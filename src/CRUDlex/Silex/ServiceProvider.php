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

use CRUDlex\EntityDefinitionFactory;
use CRUDlex\EntityDefinitionValidator;
use CRUDlex\Service;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Api\BootableProviderInterface;
use Silex\Application;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;

/**
 * The ServiceProvider setups and initializes the service for Silex.
 */
class ServiceProvider implements ServiceProviderInterface, BootableProviderInterface
{

    /**
     * Initializes needed but yet missing service providers.
     *
     * @param Container $app
     * the application container
     */
    protected function initMissingServiceProviders(Container $app)
    {

        if (!$app->offsetExists('translator')) {
            $app->register(new LocaleServiceProvider());
            $app->register(new TranslationServiceProvider(), [
                'locale_fallbacks' => ['en'],
            ]);
        }

        if (!$app->offsetExists('session')) {
            $app->register(new SessionServiceProvider());
        }

        if (!$app->offsetExists('twig')) {
            $app->register(new TwigServiceProvider());
        }
        $app['twig.loader.filesystem']->addPath(__DIR__.'/../../views/', 'crud');
    }

    /**
     * Implements ServiceProviderInterface::register() registering $app['crud'].
     * $app['crud'] contains an instance of the ServiceProvider afterwards.
     *
     * @param Container $app
     * the Container instance of the Silex application
     */
    public function register(Container $app)
    {
        if (!$app->offsetExists('crud.filesystem')) {
            $app['crud.filesystem'] = new Filesystem(new Local(getcwd()));
        }

        $doValidate = !$app->offsetExists('crud.validateentitydefinition') || $app['crud.validateentitydefinition'] === true;
        $validator  = null;
        if ($doValidate) {
            $validator = $app->offsetExists('crud.entitydefinitionvalidator')
                ? $app['crud.entitydefinitionvalidator']
                : new EntityDefinitionValidator();
        }

        $app['crud'] = function() use ($app, $validator) {
            $crudFileCachingDirectory = $app->offsetExists('crud.filecachingdirectory') ? $app['crud.filecachingdirectory'] : null;
            $entityDefinitionFactory  = $app->offsetExists('crud.entitydefinitionfactory') ? $app['crud.entitydefinitionfactory'] : new EntityDefinitionFactory();
            $result                   = new Service($app['crud.file'], $crudFileCachingDirectory, $app['url_generator'], $app['translator'], $app['crud.datafactory'], $entityDefinitionFactory, $app['crud.filesystem'], $validator);
            $result->setTemplate('layout', '@crud/layout.twig');
            return $result;
        };
    }

    /**
     * Initializes the crud service right after boot.
     *
     * @param Application $app
     * the Container instance of the Silex application
     */
    public function boot(Application $app)
    {
        $this->initMissingServiceProviders($app);
        $twigSetup = new TwigSetup();
        $twigSetup->registerTwigExtensions($app);
    }

}
