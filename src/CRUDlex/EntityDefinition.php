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
 * The class for defining a single entity.
 */
class EntityDefinition {

    /**
     * The table where the data is stored.
     */
    protected $table;

    /**
     * Holds all fields in the same structure as in the CRUD YAML file.
     */
    protected $fields;

    /**
     * The label for the entity.
     */
    protected $label;

    /**
     * The labels  of the entity in the locales.
     */
    protected $localeLabels;

    /**
     * An array with the children referencing the entity. All entries are
     * arrays with three referencing elements: table, fieldName, entity
     */
    protected $children;

    /**
     * Labels for the fields "id", "created_at" and "updated_at".
     */
    protected $standardFieldLabels;

    /**
     * An array containing the fields which should appear in the list view
     * of the entity.
     */
    protected $listFields;

    /**
     * The fields used to display the children on the details page of an entity.
     * The keys are the entity names as in the CRUD YAML and the values are the
     * field names.
     */
    protected $childrenLabelFields;

    /**
     * Whether to delete its children when an instance is deleted.
     */
    protected $deleteCascade;

    /**
     * The amount of items to display per page on the listview.
     */
    protected $pageSize;

    /**
     * The fields offering to be filtered.
     */
    protected $filter;

    /**
     * Holds the {@see ServiceProvider}.
     */
    protected $serviceProvider;

    /**
     * Holds the locale.
     */
    protected $locale;

    /**
     * Holds the initial sort field.
     */
    protected $initialSortField;

    /**
     * Holds the initial sort order.
     */
    protected $initialSortAscending;

    /**
     * Holds if the entity must be displayed grouped in the nav bar.
     */
    protected $navBarGroup;

    /**
     * Holds whether optimistic locking is switched on.
     */
    protected $optimisticLocking;

    /**
     * Gets the field names exluding the given ones.
     *
     * @param string[] $exclude
     * the field names to exclude
     *
     * @return array
     * all field names excluding the given ones
     */
    protected function getFilteredFieldNames(array $exclude) {
        $fieldNames = $this->getFieldNames(true);
        $result     = [];
        foreach ($fieldNames as $fieldName) {
            if (!in_array($fieldName, $exclude)) {
                $result[] = $fieldName;
            }
        }
        return $result;
    }

    /**
     * Checks whether the given field names are declared and existing.
     *
     * @param string $reference
     * a hint towards the source of an invalid field name
     * @param array $fieldNames
     * the field names to check
     * @throws \InvalidArgumentException
     * thrown with all invalid field names
     */
    protected function checkFieldNames($reference, $fieldNames) {
        $validFieldNames   = $this->getPublicFieldNames();
        $invalidFieldNames = [];
        foreach ($fieldNames as $fieldName) {
            if (!in_array($fieldName, $validFieldNames)) {
                $invalidFieldNames[] = $fieldName;
            }
        }
        if (!empty($invalidFieldNames)) {
            throw new \InvalidArgumentException('Invalid fields ('.join(', ', $invalidFieldNames).') in '.$reference.', valid ones are: '.join(', ', $validFieldNames));
        }
    }

    /**
     * Constructor.
     *
     * @param string $table
     * the table of the entity
     * @param array $fields
     * the field structure just like the CRUD YAML
     * @param string $label
     * the label of the entity
     * @param array $localeLabels
     * the labels  of the entity in the locales
     * @param array $standardFieldLabels
     * labels for the fields "id", "created_at" and "updated_at"
     * @param ServiceProvider $serviceProvider
     * The current service provider
     */
    public function __construct($table, array $fields, $label, $localeLabels, array $standardFieldLabels, ServiceProvider $serviceProvider) {
        $this->table               = $table;
        $this->fields              = $fields;
        $this->label               = $label;
        $this->localeLabels        = $localeLabels;
        $this->standardFieldLabels = $standardFieldLabels;
        $this->serviceProvider     = $serviceProvider;

        $this->children             = [];
        $this->listFields           = [];
        $this->childrenLabelFields  = [];
        $this->filter               = [];
        $this->deleteCascade        = false;
        $this->pageSize             = 25;
        $this->locale               = null;
        $this->initialSortField     = 'created_at';
        $this->initialSortAscending = true;
        $this->navBarGroup          = 'main';
        $this->optimisticLocking    = true;
    }

    /**
     * Gets all field names, including the implicit ones like "id" or
     * "created_at".
     *
     * @param boolean $includeMany
     * whether to include the many fields as well
     *
     * @return string[]
     * the field names
     */
    public function getFieldNames($includeMany = false) {
        $fieldNames = $this->getReadOnlyFields();
        foreach ($this->fields as $field => $value) {
            if ($includeMany || $this->getType($field) !== 'many') {
                $fieldNames[] = $field;
            }
        }
        return $fieldNames;
    }

    /**
     * Sets the field names to be used in the listview.
     *
     * @param array $listFields
     * the field names to be used in the listview
     */
    public function setListFields(array $listFields) {
        $this->checkFieldNames('listFields', $listFields);
        $this->listFields = $listFields;
    }

    /**
     * Gets the field names to be used in the listview. If they were not specified,
     * all public field names are returned.
     *
     * @return array
     * the field names to be used in the listview
     */
    public function getListFields() {
        if (!empty($this->listFields)) {
            return $this->listFields;
        }
        return $this->getPublicFieldNames();
    }

    /**
     * Gets the fields used to display the children on the details page of an
     * entity. The keys are the entity names as in the CRUD YAML and the values
     * are the field names.
     *
     * @return array
     * the fields used to display the children on the details page
     */
    public function getChildrenLabelFields() {
        return $this->childrenLabelFields;
    }

    /**
     * Sets the fields used to display the children on the details page of an
     * entity. The keys are the entity names as in the CRUD YAML and the values
     * are the field names.
     *
     * @param array $childrenLabelFields
     * the fields used to display the children on the details page
     */
    public function setChildrenLabelFields(array $childrenLabelFields) {
        $this->childrenLabelFields = $childrenLabelFields;
    }

    /**
     * Gets whether to delete its children when an instance is deleted.
     *
     * @return boolean
     * true if so
     */
    public function isDeleteCascade() {
        return $this->deleteCascade;
    }

    /**
     * Sets whether to delete its children when an instance is deleted.
     *
     * @param boolean $deleteCascade
     * whether to delete its children when an instance is deleted
     */
    public function setDeleteCascade($deleteCascade) {
        $this->deleteCascade = $deleteCascade;
    }

    /**
     * Gets the amount of items to display per page on the listview.
     *
     * @return integer
     * the amount of items to display per page on the listview
     */
    public function getPageSize() {
        return $this->pageSize;
    }

    /**
     * Sets the amount of items to display per page on the listview.
     *
     * @param integer $pageSize
     * the amount of items to display per page on the listview
     */
    public function setPageSize($pageSize) {
        $this->pageSize = $pageSize;
    }

    /**
     * Gets the fields offering a filter.
     *
     * @return array
     * the fields to filter
     */
    public function getFilter() {
        return $this->filter;
    }

    /**
     * Sets the fields offering a filter.
     *
     * @param array $filter
     * the fields to filter
     */
    public function setFilter(array $filter) {
        $this->checkFieldNames('filter', $filter);
        $this->filter = $filter;
    }

    /**
     * Gets the service provider.
     *
     * @return ServiceProvider
     * the service provider
     */
    public function getServiceProvider() {
        return $this->serviceProvider;
    }

    /**
     * Sets the service provider.
     *
     * @param ServiceProvider $serviceProvider
     * the new service provider
     */
    public function setServiceProvider(ServiceProvider $serviceProvider) {
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * Gets the public field names. The internal fields "version" and
     * "deleted_at" are filtered.
     *
     * @return array
     * the public field names
     */
    public function getPublicFieldNames() {
        $exclude = ['version', 'deleted_at'];
        $result  = $this->getFilteredFieldNames($exclude);
        return $result;
    }

    /**
     * Gets the field names which are editable. Not editable are fields like the
     * id or the created_at.
     *
     * @return array
     * the editable field names
     */
    public function getEditableFieldNames() {
        $result = $this->getFilteredFieldNames($this->getReadOnlyFields());
        return $result;
    }

    /**
     * Gets the read only field names like the id or the created_at.
     *
     * @return string[]
     * the read only field names
     */
    public function getReadOnlyFields() {
        $result = ['id', 'created_at', 'updated_at', 'deleted_at'];
        if ($this->optimisticLocking) {
            $result[] = 'version';
        }
        return $result;
    }

    /**
     * Gets the type of a field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the type or null on invalid field name
     */
    public function getType($fieldName) {
        if ($fieldName === 'id') {
            return 'string';
        }
        if ($fieldName === 'version') {
            return 'integer';
        }
        if (in_array($fieldName, ['created_at', 'updated_at', 'deleted_at'])) {
            return 'datetime';
        }
        return $this->getField($fieldName, 'type');
    }

    /**
     * Sets the type of a field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new field type
     */
    public function setType($fieldName, $value) {
        $this->setField($fieldName, 'type', $value);
    }

    /**
     * Gets the label of a field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the label of the field or the field name if no label is set in the CRUD
     * YAML
     */
    public function getFieldLabel($fieldName) {

        $result = $this->getField($fieldName, 'label_'.$this->locale, $this->getField($fieldName, 'label'));

        if ($result === null && array_key_exists($fieldName, $this->standardFieldLabels)) {
            $result = $this->standardFieldLabels[$fieldName];
        }

        if ($result === null) {
            $result = $fieldName;
        }

        return $result;
    }

    /**
     * Gets the label of a field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new label of the field
     */
    public function setFieldLabel($fieldName, $value) {
        $this->setField($fieldName, 'label', $value);
    }

    /**
     * Gets the table where the data is stored.
     *
     * @return string
     * the table where the data is stored
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Sets the table where the data is stored.
     *
     * @param string $table
     * the new table where the data is stored
     */
    public function setTable($table) {
        $this->table = $table;
    }

    /**
     * Gets the label for the entity.
     *
     * @return string
     * the label for the entity
     */
    public function getLabel() {
        if ($this->locale && array_key_exists($this->locale, $this->localeLabels)) {
            return $this->localeLabels[$this->locale];
        }
        return $this->label;
    }

    /**
     * Sets the label for the entity.
     *
     * @param string $label
     * the new label for the entity
     */
    public function setLabel($label) {
        $this->label = $label;
    }

    /**
     * Adds a child to this definition in case the other
     * definition has a reference to this one.
     *
     * @param string $table
     * the table of the referencing definition
     * @param string $fieldName
     * the field name of the referencing definition
     * @param string $entity
     * the entity of the referencing definition
     */
    public function addChild($table, $fieldName, $entity) {
        $this->children[] = [$table, $fieldName, $entity];
    }

    /**
     * Gets the referencing children to this definition.
     *
     * @return array
     * an array with the children referencing the entity. All entries are arrays
     * with three referencing elements: table, fieldName, entity
     */
    public function getChildren() {
        return $this->children;
    }

    /**
     * Sets the locale to be used.
     *
     * @param string $locale
     * the locale to be used.
     */
    public function setLocale($locale) {
        $this->locale = $locale;
    }

    /**
     * Gets the locale to be used.
     *
     * @return null|string
     * the locale to be used.
     */
    public function getLocale() {
        return $this->locale;
    }

    /**
     * Sets the initial sort field.
     *
     * @param string $initialSortField
     * the new initial sort field
     */
    public function setInitialSortField($initialSortField) {
        $this->initialSortField = $initialSortField;
    }

    /**
     * Gets the initial sort field.
     *
     * @return string
     * the initial sort field
     */
    public function getInitialSortField() {
        return $this->initialSortField;
    }

    /**
     * Sets the initial sort order.
     *
     * @param boolean $initialSortAscending
     * the initial sort order, true if ascending
     */
    public function setInitialSortAscending($initialSortAscending) {
        $this->initialSortAscending = $initialSortAscending;
    }

    /**
     * Gets the initial sort order.
     *
     * @return boolean
     * the initial sort order, true if ascending
     */
    public function isInitialSortAscending() {
        return $this->initialSortAscending;
    }

    /**
     * Gets the navigation bar group where the entity belongs.
     *
     * @return string
     * the navigation bar group where the entity belongs
     */
    public function getNavBarGroup() {
        return $this->navBarGroup;
    }

    /**
     * Sets the navigation bar group where the entity belongs.
     *
     * @param string $navBarGroup
     * the navigation bar group where the entity belongs
     */
    public function setNavBarGroup($navBarGroup) {
        $this->navBarGroup = $navBarGroup;
    }

    /**
     * Returns whether optimistic locking via the version field is activated.
     * @return boolean
     * true if optimistic locking is activated
     */
    public function getOptimisticLocking() {
        return $this->optimisticLocking;
    }


    /**
     * Sets whether optimistic locking via the version field is activated.
     * @param boolean $optimisticLocking
     * true if optimistic locking is activated
     */
    public function setOptimisticLocking($optimisticLocking) {
        $this->optimisticLocking = $optimisticLocking;
    }

    /**
     * Gets a sub field of an field.
     *
     * @param string $fieldName
     * the field name of the sub type
     * @param string $subType
     * the sub type like "reference" or "many"
     * @param string $key
     * the key of the value
     *
     * @return string
     * the value of the sub field
     */
    public function getSubTypeField($fieldName, $subType, $key) {

        if (!isset($this->fields[$fieldName][$subType][$key])) {
            return null;
        }

        return $this->fields[$fieldName][$subType][$key];
    }

    /**
     * Gets the value of a field key.
     *
     * @param string $name
     * the name of the field
     * @param string $key
     * the value of the key
     * @param mixed $default
     * the default value to return if nothing is found
     *
     * @return mixed
     * the value of the field key or null if not existing
     */
    public function getField($name, $key, $default = null) {
        if (array_key_exists($name, $this->fields) && array_key_exists($key, $this->fields[$name])) {
            return $this->fields[$name][$key];
        }
        return $default;
    }

    /**
     * Sets the value of a field key. If the field or the key in the field
     * don't exist, they get created.
     *
     * @param string $name
     * the name of the field
     * @param string $key
     * the value of the key
     * @param mixed $value
     * the new value
     */
    public function setField($name, $key, $value) {
        if (!array_key_exists($name, $this->fields)) {
            $this->fields[$name] = [];
        }
        $this->fields[$name][$key] = $value;
    }

}
