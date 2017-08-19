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

/**
 * Management and execution of events.
 */
class EntityEvents
{

    /**
     * Holds the events.
     * @var array
     */
    protected $events;

    /**
     * Executes the event chain of an entity.
     *
     * @param Entity $entity
     * the entity having the event chain to execute
     * @param string $moment
     * the "moment" of the event, can be either "before" or "after"
     * @param string $action
     * the "action" of the event, can be either "create", "update" or "delete"
     *
     * @return boolean
     * true on successful execution of the full chain or false if it broke at
     * any point (and stopped the execution)
     */
    public function shouldExecuteEvents(Entity $entity, $moment, $action)
    {
        if (!isset($this->events[$moment.'.'.$action])) {
            return true;
        }
        foreach ($this->events[$moment.'.'.$action] as $event) {
            $result = $event($entity);
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * Adds an event to fire for the given parameters. The event function must
     * have this signature:
     * function (Entity $entity)
     * and has to return true or false.
     * The events are executed one after another in the added order as long as
     * they return "true". The first event returning "false" will stop the
     * process.
     *
     * @param string $moment
     * the "moment" of the event, can be either "before" or "after"
     * @param string $action
     * the "action" of the event, can be either "create", "update" or "delete"
     * @param \Closure $function
     * the event function to be called if set
     */
    public function pushEvent($moment, $action, \Closure $function)
    {
        $events                            = isset($this->events[$moment.'.'.$action]) ? $this->events[$moment.'.'.$action] : [];
        $events[]                          = $function;
        $this->events[$moment.'.'.$action] = $events;
    }

    /**
     * Removes and returns the latest event for the given parameters.
     *
     * @param string $moment
     * the "moment" of the event, can be either "before" or "after"
     * @param string $action
     * the "action" of the event, can be either "create", "update" or "delete"
     *
     * @return \Closure|null
     * the popped event or null if no event was available.
     */
    public function popEvent($moment, $action)
    {
        if (array_key_exists($moment.'.'.$action, $this->events)) {
            return array_pop($this->events[$moment.'.'.$action]);
        }
        return null;
    }
}