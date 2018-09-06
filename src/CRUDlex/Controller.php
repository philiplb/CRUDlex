<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlex;

use League\Flysystem\FilesystemInterface;
use League\Flysystem\Util\MimeType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Environment;


/**
 * Default implementation of the ControllerInterface.
 */
class Controller implements ControllerInterface {

    /**
     * Holds the filesystme.
     * @var FilesystemInterface
     */
    protected $filesystem;

    /**
     * Holds the session.
     * @var SessionInterface
     */
    protected $session;

    /**
     * Holds the translator.
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Holds the service.
     * @var Service
     */
    protected $service;

    /**
     * Holds the Twig instance.
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * Postprocesses the entity after modification by handling the uploaded
     * files and setting the flash.
     *
     * @param Request $request
     * the current request
     * @param AbstractData $crudData
     * the data instance of the entity
     * @param Entity $instance
     * the entity
     * @param string $entity
     * the name of the entity
     * @param string $mode
     * whether to 'edit' or to 'create' the entity
     *
     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse
     * the HTTP response of this modification
     */
    protected function modifyFilesAndSetFlashBag(Request $request, AbstractData $crudData, Entity $instance, $entity, $mode)
    {
        $id          = $instance->get('id');
        $fileHandler = new FileHandler($this->filesystem, $crudData->getDefinition());
        $result      = $mode == 'edit' ? $fileHandler->updateFiles($crudData, $request, $instance, $entity) : $fileHandler->createFiles($crudData, $request, $instance, $entity);
        if (!$result) {
            return null;
        }
        $this->session->getFlashBag()->add('success', $this->translator->trans('crudlex.'.$mode.'.success', [
            '%label%' => $crudData->getDefinition()->getLabel(),
            '%id%' => $id
        ]));
        return new RedirectResponse($this->service->generateURL('crudShow', ['entity' => $entity, 'id' => $id]));
    }

    /**
     * Sets the flashes of a failed entity modification.
     *
     * @param boolean $optimisticLocking
     * whether the optimistic locking failed
     * @param string $mode
     * the modification mode, either 'create' or 'edit'
     */
    protected function setValidationFailedFlashes($optimisticLocking, $mode)
    {
        $this->session->getFlashBag()->add('danger', $this->translator->trans('crudlex.'.$mode.'.error'));
        if ($optimisticLocking) {
            $this->session->getFlashBag()->add('danger', $this->translator->trans('crudlex.edit.locked'));
        }
    }

    /**
     * Validates and saves the new or updated entity and returns the appropriate HTTP
     * response.
     *
     * @param Request $request
     * the current request
     * @param AbstractData $crudData
     * the data instance of the entity
     * @param Entity $instance
     * the entity
     * @param string $entity
     * the name of the entity
     * @param boolean $edit
     * whether to edit (true) or to create (false) the entity
     *
     * @return Response
     * the HTTP response of this modification
     */
    protected function modifyEntity(Request $request, AbstractData $crudData, Entity $instance, $entity, $edit)
    {
        $fieldErrors = [];
        $mode        = $edit ? 'edit' : 'create';
        if ($request->getMethod() == 'POST') {
            $instance->populateViaRequest($request);
            $validator  = new EntityValidator($instance);
            $validation = $validator->validate($crudData, intval($request->get('version')));

            $fieldErrors = $validation['errors'];
            if (!$validation['valid']) {
                $optimisticLocking = isset($fieldErrors['version']);
                $this->setValidationFailedFlashes($optimisticLocking, $mode);
            } else {
                $modified = $edit ? $crudData->update($instance) : $crudData->create($instance);
                $response = $modified ? $this->modifyFilesAndSetFlashBag($request, $crudData, $instance, $entity, $mode) : false;
                if ($response) {
                    return $response;
                }
                $this->session->getFlashBag()->add('danger', $this->translator->trans('crudlex.'.$mode.'.failed'));
            }
        }

        return new Response($this->twig->render($this->service->getTemplate('template', 'form', $entity), [
            'crud' => $this->service,
            'crudEntity' => $entity,
            'crudData' => $crudData,
            'entity' => $instance,
            'mode' => $mode,
            'fieldErrors' => $fieldErrors,
            'layout' => $this->service->getTemplate('layout', $mode, $entity)
        ]));
    }

    /**
     * Gets the parameters for the redirection after deleting an entity.
     *
     * @param Request $request
     * the current request
     * @param string $entity
     * the entity name
     * @param string $redirectPage
     * reference, where the page to redirect to will be stored
     *
     * @return array<string,string>
     * the parameters of the redirection, entity and id
     */
    protected function getAfterDeleteRedirectParameters(Request $request, $entity, &$redirectPage)
    {
        $redirectPage       = 'crudList';
        $redirectParameters = ['entity' => $entity];
        $redirectEntity     = $request->get('redirectEntity');
        $redirectId         = $request->get('redirectId');
        if ($redirectEntity && $redirectId) {
            $redirectPage       = 'crudShow';
            $redirectParameters = [
                'entity' => $redirectEntity,
                'id' => $redirectId
            ];
        }
        return $redirectParameters;
    }

    /**
     * Builds up the parameters of the list page filters.
     *
     * @param Request $request
     * the current request
     * @param EntityDefinition $definition
     * the current entity definition
     * @param array &$filter
     * will hold a map of fields to request parameters for the filters
     * @param boolean $filterActive
     * reference, will be true if at least one filter is active
     * @param array $filterToUse
     * reference, will hold a map of fields to integers (0 or 1) which boolean filters are active
     * @param array $filterOperators
     * reference, will hold a map of fields to operators for AbstractData::listEntries()
     *
     * @return array
     * the raw filter query parameters
     */
    protected function buildUpListFilter(Request $request, EntityDefinition $definition, &$filter, &$filterActive, &$filterToUse, &$filterOperators)
    {
        $rawFilter = [];
        foreach ($definition->getFilter() as $filterField) {
            $type                    = $definition->getType($filterField);
            $filter[$filterField]    = $request->get('crudFilter'.$filterField);
            $rawFilter[$filterField] = $filter[$filterField];
            if ($filter[$filterField]) {
                $filterActive                  = true;
                $filterToUse[$filterField]     = $filter[$filterField];
                $filterOperators[$filterField] = '=';
                if ($type === 'boolean') {
                    $filterToUse[$filterField] = $filter[$filterField] == 'true' ? 1 : 0;
                } else if ($type === 'reference') {
                    $filter[$filterField] = ['id' => $filter[$filterField]];
                } else if ($type === 'many') {
                    $filter[$filterField] = array_map(function($value) {
                        return ['id' => $value];
                    }, $filter[$filterField]);
                    $filterToUse[$filterField] = $filter[$filterField];
                } else if (in_array($type, ['text', 'multiline', 'fixed'])) {
                    $filterToUse[$filterField]     = '%'.$filter[$filterField].'%';
                    $filterOperators[$filterField] = 'LIKE';
                }
            }
        }
        return $rawFilter;
    }

    /**
     * Generates the not found page.
     *
     * @param string $error
     * the cause of the not found error
     *
     * @return Response
     * the rendered not found page with the status code 404
     */
    protected function getNotFoundPage($error)
    {
        return new Response($this->twig->render('@crud/notFound.twig', [
            'crud' => $this->service,
            'error' => $error,
            'crudEntity' => '',
            'layout' => $this->service->getTemplate('layout', '', '')
        ]), 404);
    }

    /**
     * Controller constructor.
     *
     * @param Service $service
     * the CRUDlex service
     * @param FilesystemInterface $filesystem
     * the used filesystem
     * @param Twig_Environment $twig
     * the Twig environment
     * @param SessionInterface $session
     * the session service
     * @param TranslatorInterface $translator
     * the translation service
     */
    public function __construct(Service $service, FilesystemInterface $filesystem, Twig_Environment $twig, SessionInterface $session, TranslatorInterface $translator)
    {
        $this->service    = $service;
        $this->filesystem = $filesystem;
        $this->twig       = $twig;
        $this->session    = $session;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocaleAndCheckEntity(Request $request)
    {
        $locale = $this->translator->getLocale();
        $this->service->setLocale($locale);
        if (!$this->service->getData($request->get('entity'))) {
            return $this->getNotFoundPage($this->translator->trans('crudlex.entityNotFound'));
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function create(Request $request, $entity)
    {
        $crudData = $this->service->getData($entity);
        $instance = $crudData->createEmpty();
        $instance->populateViaRequest($request);
        return $this->modifyEntity($request, $crudData, $instance, $entity, false);
    }

    /**
     * {@inheritdoc}
     */
    public function showList(Request $request, $entity)
    {
        $crudData   = $this->service->getData($entity);
        $definition = $crudData->getDefinition();

        $filter          = [];
        $filterActive    = false;
        $filterToUse     = [];
        $filterOperators = [];
        $rawFilter       = $this->buildUpListFilter($request, $definition, $filter, $filterActive, $filterToUse, $filterOperators);

        $pageSize = $definition->getPageSize();
        $total    = $crudData->countBy($definition->getTable(), $filterToUse, $filterOperators, true);
        $page     = abs(intval($request->get('crudPage', 0)));
        $maxPage  = intval($total / $pageSize);
        if ($total % $pageSize == 0) {
            $maxPage--;
        }
        if ($page > $maxPage) {
            $page = $maxPage;
        }
        $skip = $page * $pageSize;

        $sortField            = $request->get('crudSortField', $definition->getInitialSortField());
        $sortAscendingRequest = $request->get('crudSortAscending');
        $sortAscending        = $sortAscendingRequest !== null ? $sortAscendingRequest === 'true' : $definition->isInitialSortAscending();

        $entities = $crudData->listEntries($filterToUse, $filterOperators, $skip, $pageSize, $sortField, $sortAscending);

        return new Response($this->twig->render($this->service->getTemplate('template', 'list', $entity), [
            'crud' => $this->service,
            'crudEntity' => $entity,
            'crudData' => $crudData,
            'definition' => $definition,
            'entities' => $entities,
            'pageSize' => $pageSize,
            'maxPage' => $maxPage,
            'page' => $page,
            'total' => $total,
            'filter' => $filter,
            'rawFilter' => $rawFilter,
            'filterActive' => $filterActive,
            'sortField' => $sortField,
            'sortAscending' => $sortAscending,
            'layout' => $this->service->getTemplate('layout', 'list', $entity)
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function show($entity, $id)
    {
        $crudData = $this->service->getData($entity);
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($this->translator->trans('crudlex.instanceNotFound'));
        }
        $definition = $crudData->getDefinition();

        $childrenLabelFields = $definition->getChildrenLabelFields();
        $children            = [];
        if (count($childrenLabelFields) > 0) {
            foreach ($definition->getChildren() as $child) {
                $childField      = $child[1];
                $childEntity     = $child[2];
                $childLabelField = array_key_exists($childEntity, $childrenLabelFields) ? $childrenLabelFields[$childEntity] : 'id';
                $childCrud       = $this->service->getData($childEntity);
                $children[]      = [
                    $childCrud->getDefinition()->getLabel(),
                    $childEntity,
                    $childLabelField,
                    $childCrud->listEntries([$childField => $instance->get('id')]),
                    $childField
                ];
            }
        }

        return new Response($this->twig->render($this->service->getTemplate('template', 'show', $entity), [
            'crud' => $this->service,
            'crudEntity' => $entity,
            'entity' => $instance,
            'children' => $children,
            'layout' => $this->service->getTemplate('layout', 'show', $entity)
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function edit(Request $request, $entity, $id)
    {
        $crudData = $this->service->getData($entity);
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($this->translator->trans('crudlex.instanceNotFound'));
        }

        return $this->modifyEntity($request, $crudData, $instance, $entity, true);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Request $request, $entity, $id)
    {
        $crudData = $this->service->getData($entity);
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($this->translator->trans('crudlex.instanceNotFound'));
        }

        $fileHandler  = new FileHandler($this->filesystem, $crudData->getDefinition());
        $filesDeleted = $fileHandler->deleteFiles($crudData, $instance, $entity);
        $deleted      = $filesDeleted ? $crudData->delete($instance) : AbstractData::DELETION_FAILED_EVENT;

        if ($deleted === AbstractData::DELETION_FAILED_EVENT) {
            $this->session->getFlashBag()->add('danger', $this->translator->trans('crudlex.delete.failed'));
            return new RedirectResponse($this->service->generateURL('crudShow', ['entity' => $entity, 'id' => $id]));
        } elseif ($deleted === AbstractData::DELETION_FAILED_STILL_REFERENCED) {
            $this->session->getFlashBag()->add('danger', $this->translator->trans('crudlex.delete.error', [
                '%label%' => $crudData->getDefinition()->getLabel()
            ]));
            return new RedirectResponse($this->service->generateURL('crudShow', ['entity' => $entity, 'id' => $id]));
        }

        $redirectPage       = 'crudList';
        $redirectParameters = $this->getAfterDeleteRedirectParameters($request, $entity, $redirectPage);

        $this->session->getFlashBag()->add('success', $this->translator->trans('crudlex.delete.success', [
            '%label%' => $crudData->getDefinition()->getLabel()
        ]));
        return new RedirectResponse($this->service->generateURL($redirectPage, $redirectParameters));
    }

    /**
     * {@inheritdoc}
     */
    public function renderFile($entity, $id, $field)
    {
        $crudData   = $this->service->getData($entity);
        $instance   = $crudData->get($id);
        $definition = $crudData->getDefinition();
        if (!$instance || $definition->getType($field) != 'file' || !$instance->get($field)) {
            return $this->getNotFoundPage($this->translator->trans('crudlex.instanceNotFound'));
        }
        $fileHandler = new FileHandler($this->filesystem, $definition);
        return $fileHandler->renderFile($instance, $entity, $field);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteFile($entity, $id, $field)
    {
        $crudData = $this->service->getData($entity);
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($this->translator->trans('crudlex.instanceNotFound'));
        }
        $fileHandler = new FileHandler($this->filesystem, $crudData->getDefinition());
        if (!$crudData->getDefinition()->getField($field, 'required', false) && $fileHandler->deleteFile($crudData, $instance, $entity, $field)) {
            $instance->set($field, '');
            $crudData->update($instance);
            $this->session->getFlashBag()->add('success', $this->translator->trans('crudlex.file.deleted'));
        } else {
            $this->session->getFlashBag()->add('danger', $this->translator->trans('crudlex.file.notDeleted'));
        }
        return new RedirectResponse($this->service->generateURL('crudShow', ['entity' => $entity, 'id' => $id]));
    }

    /**
     * {@inheritdoc}
     */
    public function staticFile(Request $request)
    {
        $fileParam = str_replace('..', '', $request->get('file'));
        $file      = __DIR__.'/../static/'.$fileParam;
        if (!$fileParam || !file_exists($file)) {
            return $this->getNotFoundPage($this->translator->trans('crudlex.resourceNotFound'));
        }

        $mimeType = MimeType::detectByFilename($file);
        $size     = filesize($file);

        $streamedFileResponse = new StreamedFileResponse();
        $response             = new StreamedResponse($streamedFileResponse->getStreamedFileFunction($file), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="'.basename($file).'"',
            'Content-length' => $size
        ]);

        $response->setETag(filemtime($file))->setPublic()->isNotModified($request);
        $response->send();

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocale(Request $request, $locale)
    {

        if (!in_array($locale, $this->service->getLocales())) {
            return $this->getNotFoundPage('Locale '.$locale.' not found.');
        }

        $manageI18n = $this->service->isManageI18n();
        if ($manageI18n) {
            $this->session->set('locale', $locale);
        }
        $redirect = $request->get('redirect');
        return new RedirectResponse($redirect);
    }
}
