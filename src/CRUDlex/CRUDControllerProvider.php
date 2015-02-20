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

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

use CRUDlex\CRUDEntity;

/**
 * This is the ControllerProvider offering all CRUD pages.
 *
 * It offers this routes:
 *
 * "/resource/static" serving static resources
 *
 * "/{entity}/create" creation page of the entity
 *
 * "/{entity}" list page of the entity
 *
 * "/{entity}/{id}" details page of a single entity instance
 *
 * "/{entity}/{id}/edit" edit page of a single entity instance
 *
 * "/{entity}/{id}/delete" POST only deletion route for an entity instance
 *
 * "/{entity}/{id}/{field}/file" renders a file field of an entity instance
 *
 * "/{entity}/{id}/{field}/delete" POST only deletion of a file field of an entity instance
 */
class CRUDControllerProvider implements ControllerProviderInterface {

    /**
     * Generates the not found page.
     *
     * @param Application $app
     * the Silex application
     * @param string $error
     * the cause of the not found error
     *
     * @return Response
     * the rendered not found page with the status code 404
     */
    protected function getNotFoundPage(Application $app, $error) {
        return new Response($app['twig']->render('@crud/notFound.twig', array(
            'error' => $error,
            'crudEntity' => '',
            'layout' => $app['crud.layout']
        )), 404);
    }

    /**
     * Delivers the layout for the page in the way it is described in the
     * manual.
     *
     * @param Application $app
     * the Silex application
     * @param string $action
     * the current calling action like "create" or "show"
     * @param string $entity
     * the current calling entity
     *
     * @return string
     * the best fitting layout
     */
    protected function getLayout(Application $app, $action, $entity) {
        if ($app->offsetExists('crud.layout.'.$action.'.'.$entity)) {
            return $app['crud.layout.'.$action.'.'.$entity];
        }
        if ($app->offsetExists('crud.layout.'.$entity)) {
            return $app['crud.layout.'.$entity];
        }
        if ($app->offsetExists('crud.layout.'.$action)) {
            return $app['crud.layout.'.$action];
        }
        return $app['crud.layout'];
    }


    /**
     * Implements ControllerProviderInterface::connect() connecting this
     * controller.
     *
     * @param Application $app
     * the Application instance of the Silex application
     *
     * @return SilexController\Collection
     * this method is expected to return the used ControllerCollection instance
     */
    public function connect(Application $app) {
        if ($app->offsetExists('twig.loader.filesystem')) {
            $app['twig.loader.filesystem']->addPath(__DIR__ . '/../views/', 'crud');
        }

        if (!$app->offsetExists('crud.layout')) {
            $app['crud.layout'] = '@crud/layout.twig';
        }

        $class = get_class($this);
        $factory = $app['controllers_factory'];
        $factory->get('/resource/static', $class.'::staticFile')
                ->bind('static');
        $factory->match('/{entity}/create', $class.'::create')
                ->bind('crudCreate');
        $factory->match('/{entity}', $class.'::showList')
                ->bind('crudList');
        $factory->match('/{entity}/{id}', $class.'::show')
                ->bind('crudShow');
        $factory->match('/{entity}/{id}/edit', $class.'::edit')
                ->bind('crudEdit');
        $factory->post('/{entity}/{id}/delete', $class.'::delete')
                ->bind('crudDelete');
        $factory->match('/{entity}/{id}/{field}/file', $class.'::renderFile')
                ->bind('crudRenderFile');
        $factory->post('/{entity}/{id}/{field}/delete', $class.'::deleteFile')
                ->bind('crudDeleteFile');
        return $factory;
    }

    /**
     * The controller for the "create" action.
     *
     * @param Application $app
     * the Silex application
     * @param string $entity
     * the current entity
     *
     * @return Response
     * the HTTP response of this action
     */
    public function create(Application $app, $entity) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }

        $errors = array();
        $instance = $crudData->createEmpty();
        $definition = $crudData->getDefinition();
        $fields = $definition->getEditableFieldNames();

        foreach ($fields as $field) {
            if ($definition->getType($field) == 'file') {
                $file = $app['request']->files->get($field);
                if ($file) {
                    $instance->set($field, $file->getClientOriginalName());
                }
            } else {
                $instance->set($field, $app['request']->get($field));
            }
        }
        if ($app['request']->getMethod() == 'POST') {
            $validation = $instance->validate($crudData);
            if (!$validation['valid']) {
                $errors = $validation['errors'];
                $app['session']->getFlashBag()->add('danger', $app['crud']->translate('create.error'));
            } else {
                $crudData->create($instance);
                $id = $instance->get('id');
                $crudData->createFiles($app['request'], $instance, $entity);

                $app['session']->getFlashBag()->add('success', $app['crud']->translate('create.success', array($crudData->getDefinition()->getLabel(), $id)));
                return $app->redirect($app['url_generator']->generate('crudShow', array('entity' => $entity, 'id' => $id)));
            }
        }

        $definition = $crudData->getDefinition();

        return $app['twig']->render('@crud/form.twig', array(
            'crudEntity' => $entity,
            'crudData' => $crudData,
            'entity' => $instance,
            'mode' => 'create',
            'errors' => $errors,
            'layout' => $this->getLayout($app, 'create', $entity)
        ));
    }

    /**
     * The controller for the "show list" action.
     *
     * @param Application $app
     * the Silex application
     * @param string $entity
     * the current entity
     *
     * @return Response
     * the HTTP response of this action or 404 on invalid input
     */
    public function showList(Application $app, $entity) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $definition = $crudData->getDefinition();
        $pageSize = $definition->getPageSize();
        $total = $crudData->countBy($definition->getTable(), array(), array(), true);
        $page = abs(intval($app['request']->get('crudPage', 0)));
        $maxPage = intval($total / $pageSize);
        if ($total % $pageSize == 0) {
            $maxPage--;
        }
        if ($page > $maxPage) {
            $page = $maxPage;
        }
        $skip = $page * $pageSize;
        $entities = $crudData->listEntries(array(), $skip, $pageSize);
        $crudData->fetchReferences($entities);

        return $app['twig']->render('@crud/list.twig', array(
            'crudEntity' => $entity,
            'definition' => $definition,
            'entities' => $entities,
            'pageSize' => $pageSize,
            'maxPage' => $maxPage,
            'page' => $page,
            'total' => $total,
            'layout' => $this->getLayout($app, 'list', $entity)
        ));
    }

    /**
     * The controller for the "show" action.
     *
     * @param Application $app
     * the Silex application
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id to show
     *
     * @return Response
     * the HTTP response of this action or 404 on invalid input
     */
    public function show(Application $app, $entity, $id) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }
        $instance = array($instance);
        $crudData->fetchReferences($instance);
        $instance = $instance[0];
        $definition = $crudData->getDefinition();

        $childrenLabelFields = $definition->getChildrenLabelFields();
        $children = array();
        if (count($childrenLabelFields) > 0) {
            foreach ($definition->getChildren() as $child) {
                $childField = $child[1];
                $childEntity = $child[2];
                $childLabelField = key_exists($childEntity, $childrenLabelFields) ? $childrenLabelFields[$childEntity] : 'id';
                $childCrud = $app['crud']->getData($childEntity);
                $children[] = array(
                    $childCrud->getDefinition()->getLabel(),
                    $childEntity,
                    $childLabelField,
                    $childCrud->listEntries(array($childField => $instance->get('id')))
                );
            }
        }

        return $app['twig']->render('@crud/show.twig', array(
            'crudEntity' => $entity,
            'entity' => $instance,
            'children' => $children,
            'layout' => $this->getLayout($app, 'show', $entity)
        ));
    }

    /**
     * The controller for the "edit" action.
     *
     * @param Application $app
     * the Silex application
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id to edit
     *
     * @return Response
     * the HTTP response of this action or 404 on invalid input
     */
    public function edit(Application $app, $entity, $id) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }

        $definition = $crudData->getDefinition();

        $errors = array();
        $fields = $definition->getEditableFieldNames();


        if ($app['request']->getMethod() == 'POST') {
            foreach ($fields as $field) {
                if ($definition->getType($field) == 'file') {
                    $file = $app['request']->files->get($field);
                    if ($file) {
                        $instance->set($field, $file->getClientOriginalName());
                    }
                } else {
                    $instance->set($field, $app['request']->get($field));
                }
            }
            $validation = $instance->validate($crudData);
            if (!$validation['valid']) {
                $app['session']->getFlashBag()->add('danger', $app['crud']->translate('edit.error'));
                $errors = $validation['errors'];
            } else {
                $crudData->update($instance);
                $crudData->updateFiles($app['request'], $instance, $entity);
                $app['session']->getFlashBag()->add('success', $app['crud']->translate('edit.success', array($crudData->getDefinition()->getLabel(), $id)));
                return $app->redirect($app['url_generator']->generate('crudShow', array('entity' => $entity, 'id' => $id)));
            }
        }

        return $app['twig']->render('@crud/form.twig', array(
            'crudEntity' => $entity,
            'crudData' => $crudData,
            'entity' => $instance,
            'mode' => 'edit',
            'errors' => $errors,
            'layout' => $this->getLayout($app, 'edit', $entity)
        ));
    }

    /**
     * The controller for the "delete" action.
     *
     * @param Application $app
     * the Silex application
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id to delete
     *
     * @return Response
     * redirects to the entity list page or 404 on invalid input
     */
    public function delete(Application $app, $entity, $id) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }

        $crudData->deleteFiles($instance, $entity);
        $deleted = $crudData->delete($id);
        if ($deleted) {

            $redirectPage = 'crudList';
            $redirectParameters = array(
                'entity' => $entity
            );
            $redirectEntity = $app['request']->get('redirectEntity');
            $redirectId = $app['request']->get('redirectId');
            if ($redirectEntity && $redirectId) {
                $redirectPage = 'crudShow';
                $redirectParameters = array(
                    'entity' => $redirectEntity,
                    'id' => $redirectId
                );
            }

            $app['session']->getFlashBag()->add('success', $app['crud']->translate('delete.success', array($crudData->getDefinition()->getLabel())));
            return $app->redirect($app['url_generator']->generate($redirectPage, $redirectParameters));
        } else {
            $app['session']->getFlashBag()->add('danger', $app['crud']->translate('delete.error', array($crudData->getDefinition()->getLabel())));
            return $app->redirect($app['url_generator']->generate('crudShow', array('entity' => $entity, 'id' => $id)));
        }
    }

    /**
     * The controller for the "render file" action.
     *
     * @param Application $app
     * the Silex application
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id
     * @param string $field
     * the field of the file to render of the instance
     *
     * @return Response
     * the rendered file
     */
    public function renderFile(Application $app, $entity, $id, $field) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        $definition = $crudData->getDefinition();
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }
        if ($definition->getType($field) != 'file' || !$instance->get($field)) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }
        return $crudData->renderFile($instance, $entity, $field);
    }

    /**
     * The controller for the "delete file" action.
     *
     * @param Application $app
     * the Silex application
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id
     * @param string $field
     * the field of the file to delete of the instance
     *
     * @return Response
     * redirects to the instance details page or 404 on invalid input
     */
    public function deleteFile(Application $app, $entity, $id, $field) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }
        if (!$crudData->getDefinition()->isRequired($field)) {
            $crudData->deleteFile($instance, $entity, $field);
            $instance->set($field, '');
            $crudData->update($instance);
            $app['session']->getFlashBag()->add('success', $app['crud']->translate('file.deleted'));
        } else {
            $app['session']->getFlashBag()->add('danger', $app['crud']->translate('file.notdeleted'));
        }
        return $app->redirect($app['url_generator']->generate('crudShow', array('entity' => $entity, 'id' => $id)));
    }

    /**
     * The controller for serving static files.
     *
     * @param Application $app
     * the Silex application
     *
     * @return Response
     * redirects to the instance details page or 404 on invalid input
     */
    public function staticFile(Application $app) {
        $fileParam = $app['request']->get('file');
        if (!$fileParam) {
            return $this->getNotFoundPage($app, $app['crud']->translate('resourceNotFound'));
        }

        $file = __DIR__.'/../static/'.$fileParam;
        if (!file_exists($file)) {
            return $this->getNotFoundPage($app, $app['crud']->translate('resourceNotFound'));
        }

        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $mimeType = '';
        if (strtolower($extension) === 'css') {
            $mimeType = 'text/css';
        } else {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file);
            finfo_close($finfo);
        }

        $size = filesize($file);

        $response = new StreamedResponse(function () use ($file) {
            set_time_limit(0);
            $handle = fopen($file,"rb");
            if ($handle !== false) {
                $chunkSize = 1024 * 1024;
                while (!feof($handle)) {
                    $buffer = fread($handle, $chunkSize);
                    echo $buffer;
                    flush();
                }
                fclose($handle);
            }
        }, 200, array(
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="'.basename($file).'"',
            'Content-length' => $size
        ));
        $response->send();

        return $response;
    }
}
