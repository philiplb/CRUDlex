<?php

/*
 * This file is part of the CRUDlex package.
 *
 * (c) Philip Lehmann-Böhm <philip@philiplb.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CRUDlex\Silex;

use CRUDlex\Controller;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

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
class ControllerProvider implements ControllerProviderInterface
{

    /**
     * Setups the templates.
     *
     * @param Application $app
     * the Application instance of the Silex application
     */
    protected function setupTemplates(Application $app)
    {
        if ($app->offsetExists('twig.loader.filesystem')) {
            $app['twig.loader.filesystem']->addPath(__DIR__.'/../../views/', 'crud');
        }
    }

    /**
     * Setups the routes.
     *
     * @param Application $app
     * the Application instance of the Silex application
     *
     * @return mixed
     * the created controller factory
     */
    protected function setupRoutes(Application $app)
    {
        $controller           = new Controller($app['crud'], $app['crud.filesystem'], $app['twig'], $app['session'], $app['translator']);
        $localeAndCheckEntity = [$controller, 'setLocaleAndCheckEntity'];
        $factory              = $app['controllers_factory'];
        $factory->get('/resource/static', [$controller, 'staticFile'])->bind('crudStatic');
        $factory->match('/{entity}/create', [$controller, 'create'])->bind('crudCreate')->before($localeAndCheckEntity, 10);
        $factory->get('/{entity}', [$controller, 'showList'])->bind('crudList')->before($localeAndCheckEntity, 10);
        $factory->get('/{entity}/{id}', [$controller, 'show'])->bind('crudShow')->before($localeAndCheckEntity, 10);
        $factory->match('/{entity}/{id}/edit', [$controller, 'edit'])->bind('crudEdit')->before($localeAndCheckEntity, 10);
        $factory->post('/{entity}/{id}/delete', [$controller, 'delete'])->bind('crudDelete')->before($localeAndCheckEntity, 10);
        $factory->get('/{entity}/{id}/{field}/file', [$controller, 'renderFile'])->bind('crudRenderFile')->before($localeAndCheckEntity, 10);
        $factory->post('/{entity}/{id}/{field}/delete', [$controller, 'deleteFile'])->bind('crudDeleteFile')->before($localeAndCheckEntity, 10);
        $factory->get('/setting/locale/{locale}', [$controller, 'setLocale'])->bind('crudSetLocale');

        return $factory;
    }

    /**
     * Setups i18n.
     *
     * @param Application $app
     * the Application instance of the Silex application
     */
    protected function setupI18n(Application $app)
    {
        $app->before(function(Request $request, Application $app) {
            $manageI18n = $app['crud']->isManageI18n();
            if ($manageI18n) {
                $locale = $app['session']->get('locale', 'en');
                $app['translator']->setLocale($locale);
            }
        }, 1);
    }

    /**
     * Implements ControllerProviderInterface::connect() connecting this
     * controller.
     *
     * @param Application $app
     * the Application instance of the Silex application
     *
     * @return \Silex\ControllerCollection
     * this method is expected to return the used ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $this->setupTemplates($app);
        $factory = $this->setupRoutes($app);
        $this->setupI18n($app);
        return $factory;
    }

}
