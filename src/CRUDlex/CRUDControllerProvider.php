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
use CRUDlex\CRUDEntity;

class CRUDControllerProvider implements ControllerProviderInterface {

    protected function getNotFoundPage($app, $error) {
        return new Response($app['twig']->render('@crud/notFound.twig', array(
            'error' => $error,
            'crudEntity' => '',
            'layout' => $app['crud.layout']
        )), 404);
    }

    public function connect (Application $app) {
        if ($app->offsetExists('twig.loader.filesystem')) {
            $app['twig.loader.filesystem']->addPath(__DIR__ . '/../views/', 'crud');
        }

        if (!$app->offsetExists('crud.layout')) {
            $app['crud.layout'] = '@crud/layout.twig';
        }

        $factory = $app['controllers_factory'];
        $factory->match('/{entity}/create', 'CRUDlex\CRUDControllerProvider::create')
                ->bind('crudCreate');
        $factory->match('/{entity}', 'CRUDlex\CRUDControllerProvider::showList')
                ->bind('crudList');
        $factory->match('/{entity}/{id}', 'CRUDlex\CRUDControllerProvider::show')
                ->bind('crudShow');
        $factory->match('/{entity}/{id}/edit', 'CRUDlex\CRUDControllerProvider::edit')
                ->bind('crudEdit');
        $factory->post('/{entity}/{id}/delete', 'CRUDlex\CRUDControllerProvider::delete')
                ->bind('crudDelete');
        return $factory;
    }

    public function create(Application $app, $entity) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }

        $errors = array();
        $instance = $crudData->createEmpty();
        $fields = $crudData->getDefinition()->getEditableFieldNames();

        if ($app['request']->getMethod() == 'POST') {
            foreach ($fields as $field) {
                $instance->set($field, $app['request']->get($field));
            }
            $validation = $instance->validate($crudData);
            if (!$validation['valid']) {
                $errors = $validation['errors'];
                $app['session']->getFlashBag()->add('danger', $app['crud']->translate('create.error'));
            } else {
                $crudData->create($instance);
                $id = $instance->get('id');
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
            'layout' => $app['crud.layout']
        ));
    }

    public function showList(Application $app, $entity) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $entitiesRaw = $crudData->listEntries();
        $entities = array();
        foreach ($entitiesRaw as $curEntity) {
            $crudData->fetchReferences($curEntity);
            $entities[] = $curEntity;
        }
        $definition = $crudData->getDefinition();
        return $app['twig']->render('@crud/list.twig', array(
            'crudEntity' => $entity,
            'definition' => $definition,
            'entities' => $entities,
            'layout' => $app['crud.layout']
        ));
    }

    public function show(Application $app, $entity, $id) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        $crudData->fetchReferences($instance);
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }
        $definition = $crudData->getDefinition();
        return $app['twig']->render('@crud/show.twig', array(
            'crudEntity' => $entity,
            'entity' => $instance,
            'layout' => $app['crud.layout']
        ));
    }

    public function edit(Application $app, $entity, $id) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }

        $errors = array();
        $fields = $crudData->getDefinition()->getEditableFieldNames();

        if ($app['request']->getMethod() == 'POST') {
            foreach ($fields as $field) {
                $instance->set($field, $app['request']->get($field));
            }
            $validation = $instance->validate($crudData);
            if (!$validation['valid']) {
                $app['session']->getFlashBag()->add('danger', $app['crud']->translate('edit.error'));
                $errors = $validation['errors'];
            } else {
                $crudData->update($instance);
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
            'layout' => $app['crud.layout']
        ));
    }

    public function delete(Application $app, $entity, $id) {
        $crudData = $app['crud']->getData($entity);
        if (!$crudData) {
            return $this->getNotFoundPage($app, $app['crud']->translate('entityNotFound'));
        }
        $instance = $crudData->get($id);
        if (!$instance) {
            return $this->getNotFoundPage($app, $app['crud']->translate('instanceNotFound'));
        }

        $deleted = $crudData->delete($id);
        if ($deleted) {
            $app['session']->getFlashBag()->add('success', $app['crud']->translate('delete.success', array($crudData->getDefinition()->getLabel())));
            return $app->redirect($app['url_generator']->generate('crudList', array('entity' => $entity)));
        } else {
            $app['session']->getFlashBag()->add('danger', $app['crud']->translate('delete.error', array($crudData->getDefinition()->getLabel())));
            return $app->redirect($app['url_generator']->generate('crudShow', array('entity' => $entity, 'id' => $id)));
        }
    }
}
