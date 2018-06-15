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
use Symfony\Component\HttpFoundation\Request;


/**
 * This represents the Controller offering all CRUD pages.
 *
 * It offers functions for this routes:
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
interface ControllerInterface {

    /**
     * Transfers the locale from the translator to CRUDlex and
     *
     * @param Request $request
     * the current request
     * @return Response|null
     * null if everything is ok, a 404 response else
     */
    public function setLocaleAndCheckEntity(Request $request);

    /**
     * The controller for the "create" action.
     *
     * @param Request $request
     * the current request
     * @param string $entity
     * the current entity
     *
     * @return Response
     * the HTTP response of this action
     */
    public function create(Request $request, $entity);

    /**
     * The controller for the "show list" action.
     *
     * @param Request $request
     * the current request
     * @param string $entity
     * the current entity
     *
     * @return Response
     * the HTTP response of this action or 404 on invalid input
     */
    public function showList(Request $request, $entity);

    /**
     * The controller for the "show" action.
     *
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id to show
     *
     * @return Response
     * the HTTP response of this action or 404 on invalid input
     */
    public function show($entity, $id);

    /**
     * The controller for the "edit" action.
     *
     * @param Request $request
     * the current request
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id to edit
     *
     * @return Response
     * the HTTP response of this action or 404 on invalid input
     */
    public function edit(Request $request, $entity, $id);

    /**
     * The controller for the "delete" action.
     *
     * @param Request $request
     * the current request
     * @param string $entity
     * the current entity
     * @param string $id
     * the instance id to delete
     *
     * @return Response
     * redirects to the entity list page or 404 on invalid input
     */
    public function delete(Request $request, $entity, $id);

    /**
     * The controller for the "render file" action.
     *
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
    public function renderFile($entity, $id, $field);

    /**
     * The controller for the "delete file" action.
     *
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
    public function deleteFile($entity, $id, $field);

    /**
     * The controller for serving static files.
     *
     * @param Request $request
     * the current request
     *
     * @return Response
     * redirects to the instance details page or 404 on invalid input
     */
    public function staticFile(Request $request);

    /**
     * The controller for setting the locale.
     *
     * @param Request $request
     * the current request
     * @param string $locale
     * the new locale
     *
     * @return Response
     * redirects to the instance details page or 404 on invalid input
     */
    public function setLocale(Request $request, $locale);
}