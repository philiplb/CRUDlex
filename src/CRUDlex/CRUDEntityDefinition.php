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
class CRUDEntityDefinition {

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
     * Holds the {@see CRUDServiceProvider}.
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
     * Gets the field names exluding the given ones.
     *
     * @param array $exclude
     * the field names to exclude
     *
     * @return array
     * all field names excluding the given ones
     */
    protected function getFilteredFieldNames(array $exclude) {
        $fieldNames = $this->getFieldNames();
        $result = array();
        foreach ($fieldNames as $fieldName) {
            if (!in_array($fieldName, $exclude)) {
                $result[] = $fieldName;
            }
        }
        return $result;
    }

    /**
     * Gets the value of a field key.
     *
     * @param string $name
     * the name of the field
     * @param string $key
     * the value of the key
     *
     * @return mixed
     * the value of the field key or null if not existing
     */
    protected function getFieldValue($name, $key) {
        if (array_key_exists($name, $this->fields) && array_key_exists($key, $this->fields[$name])) {
            return $this->fields[$name][$key];
        }
        return null;
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
    protected function setFieldValue($name, $key, $value) {
        if (!array_key_exists($name, $this->fields)) {
            $this->fields[$name] = array();
        }
        $this->fields[$name][$key] = $value;
    }

    /**
     * Gets the value of a reference field.
     *
     * @param string $fieldName
     * the field name of the reference
     * @param string $key
     * the key of the reference value
     *
     * @return string
     * the value of the reference field
     */
    protected function getReferenceValue($fieldName, $key) {
        if ($this->getType($fieldName) != 'reference') {
            return null;
        }
        if (!array_key_exists('reference', $this->fields[$fieldName])) {
            return null;
        }
        if (!array_key_exists($key, $this->fields[$fieldName]['reference'])) {
            return null;
        }
        return $this->fields[$fieldName]['reference'][$key];
    }

    /**
     * Checks if the given field has the given constraint.
     *
     * @param string $fieldName
     * the field name maybe having the constraint
     * @param string $constraint
     * the constraint to check, 'required' or 'unique'
     *
     * @return boolean
     * true if the given field has the given constraint
     */
    protected function isConstraint($fieldName, $constraint) {
        $result = $this->getFieldValue($fieldName, $constraint);
        if ($result === null) {
            $result = false;
        }
        return $result;
    }

    /**
     * Constructor.
     *
     * @param string $table
     * the table of the entity
     * @param array $fields
     * the fieldstructure just like the CRUD YAML
     * @param string $label
     * the label of the entity
     * @param array $localeLabels
     * the labels  of the entity in the locales
     * @param array $standardFieldLabels
     * labels for the fields "id", "created_at" and "updated_at"
     * @param CRUDServiceProvider $serviceProvider
     * The current service provider
     */
    public function __construct($table, array $fields, $label, $localeLabels, array $standardFieldLabels, CRUDServiceProvider $serviceProvider) {
        $this->table = $table;
        $this->fields = $fields;
        $this->label = $label;
        $this->localeLabels = $localeLabels;
        $this->standardFieldLabels = $standardFieldLabels;
        $this->serviceProvider = $serviceProvider;

        $this->children = array();
        $this->listFields = array();
        $this->childrenLabelFields = array();
        $this->filter = array();
        $this->deleteCascade = false;
        $this->pageSize = 25;
        $this->locale = null;
        $this->initialSortField = 'created_at';
        $this->initialSortAscending = true;
    }

    /**
     * Gets all field names, including the implicit ones like "id" or
     * "created_at".
     *
     * @return array
     * the field names
     */
    public function getFieldNames() {
        $fieldNames = $this->getReadOnlyFields();
        foreach ($this->fields as $field => $value) {
            $fieldNames[] = $field;
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
        $this->filter = $filter;
    }

    /**
     * Gets the service provider.
     *
     * @return CRUDServiceProvider
     * the service provider
     */
    public function getServiceProvider() {
        return $this->serviceProvider;
    }

    /**
     * Sets the service provider.
     *
     * @param CRUDServiceProvider $serviceProvider
     * the new service provider
     */
    public function setServiceProvider(CRUDServiceProvider $serviceProvider) {
        return $this->serviceProvider = $serviceProvider;
    }

    /**
     * Gets the public field names. The internal fields "version" and
     * "deleted_at" are filtered.
     *
     * @return array
     * the public field names
     */
    public function getPublicFieldNames() {
        $exclude = array('version', 'deleted_at');
        $result = $this->getFilteredFieldNames($exclude);
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
     * @return array
     * the read only field names
     */
    public function getReadOnlyFields() {
        return array('id', 'created_at', 'updated_at', 'version', 'deleted_at');
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
        switch ($fieldName) {
            case 'id':
                return 'string';
            case 'created_at':
            case 'updated_at':
            case 'deleted_at':
                return 'datetime';
            case 'version':
                return 'int';
        }
        return $this->getFieldValue($fieldName, 'type');
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
        $this->setFieldValue($fieldName, 'type', $value);
    }

    /**
     * Gets whether a field is required.
     *
     * @param string $fieldName
     * the field name
     *
     * @return boolean
     * true if so
     */
    public function isRequired($fieldName) {
        return $this->isConstraint($fieldName, 'required');
    }

    /**
     * Sets whether a field is required.
     *
     * @param string $fieldName
     * the field name
     * @param boolean $fieldName
     * the new required state
     */
    public function setRequired($fieldName, $value) {
        $this->setFieldValue($fieldName, 'required', $value);
    }

    /**
     * Gets whether a field is unique.
     *
     * @param string $fieldName
     * the field name
     *
     * @return boolean
     * true if so
     */
    public function isUnique($fieldName) {
        return $this->isConstraint($fieldName, 'unique');
    }

    /**
     * Sets whether a field is unique.
     *
     * @param string $fieldName
     * the field name
     * @param boolean $value
     * true if so
     */
    public function setUnique($fieldName, $value) {
        $this->setFieldValue($fieldName, 'unique', $value);
    }

    /**
     * Gets the table field of a reference.
     *
     * @param string $fieldName
     * the field name of the reference
     *
     * @return string
     * the table field of a reference or null on invalid field name
     */
    public function getReferenceTable($fieldName) {
        return $this->getReferenceValue($fieldName, 'table');
    }

    /**
     * Gets the name field of a reference.
     *
     * @param string $fieldName
     * the field name of the reference
     *
     * @return string
     * the name field of a reference or null on invalid field name
     */
    public function getReferenceNameField($fieldName) {
        return $this->getReferenceValue($fieldName, 'nameField');
    }

    /**
     * Gets the entity field of a reference.
     *
     * @param string $fieldName
     * the field name of the reference
     *
     * @return string
     * the entity field of a reference or null on invalid field name
     */
    public function getReferenceEntity($fieldName) {
        return $this->getReferenceValue($fieldName, 'entity');
    }

    /**
     * Gets the file path of a field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the file path of a field or null on invalid field name
     */
    public function getFilePath($fieldName) {
        return $this->getFieldValue($fieldName, 'filepath');
    }

    /**
     * Sets the file path of a field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the file path of a field or null on invalid field name
     */
    public function setFilePath($fieldName, $value) {
        $this->setFieldValue($fieldName, 'filepath', $value);
    }

    /**
     * Gets the value of a fixed field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the value of a fixed field or null on invalid field name
     */
    public function getFixedValue($fieldName) {
        return $this->getFieldValue($fieldName, 'fixedvalue');
    }

    /**
     * Sets the value of a fixed field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new value for the fixed field
     */
    public function setFixedValue($fieldName, $value) {
        $this->setFieldValue($fieldName, 'fixedvalue', $value);
    }

    /**
     * Gets the items of a set field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return array
     * the items of the set field or null on invalid field name
     */
    public function getSetItems($fieldName) {
        return $this->getFieldValue($fieldName, 'setitems');
    }

    /**
     * Sets the items of a set field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new items of the set field
     */
    public function setSetItems($fieldName, $value) {
        $this->setFieldValue($fieldName, 'setitems', $value);
    }

    /**
     * Gets the step size of a float field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return array
     * the step size of a float field or null on invalid field name
     */
    public function getFloatStep($fieldName) {
        return $this->getFieldValue($fieldName, 'floatStep');
    }

    /**
     * Sets the step size of a float field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new step size of the float field
     */
    public function setFloatStep($fieldName, $value) {
        $this->setFieldValue($fieldName, 'floatStep', $value);
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

        $result = null;

        if ($this->locale) {
            $result = $this->getFieldValue($fieldName, 'label_'.$this->locale);
        }

        if ($result === null) {
            $result = $this->getFieldValue($fieldName, 'label');
        }

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
        $this->setFieldValue($fieldName, 'label', $value);
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
     * Gets the description of a field.
     *
     * @param string $fieldName
     * the field name
     *
     * @return string
     * the description of the field
     */
    public function getDescription($fieldName) {
        return $this->getFieldValue($fieldName, 'description');
    }

    /**
     * Sets the description of a field.
     *
     * @param string $fieldName
     * the field name
     * @param string $value
     * the new description of the field
     */
    public function setDescription($fieldName, $value) {
        $this->setFieldValue($fieldName, 'description', $value);
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
        $this->children[] = array($table, $fieldName, $entity);
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
    public function getInitialSortAscending() {
        return $this->initialSortAscending;
    }
}
