-------------------------
CRUDlex\\EntityDefinition
-------------------------

.. php:namespace: CRUDlex

.. php:class:: EntityDefinition

    The class for defining a single entity.

    .. php:attr:: table

        protected

        The table where the data is stored.

    .. php:attr:: fields

        protected

        Holds all fields in the same structure as in the CRUD YAML file.

    .. php:attr:: label

        protected

        The label for the entity.

    .. php:attr:: localeLabels

        protected

        The labels  of the entity in the locales.

    .. php:attr:: children

        protected

        An array with the children referencing the entity. All entries are
        arrays with three referencing elements: table, fieldName, entity

    .. php:attr:: standardFieldLabels

        protected

        Labels for the fields "id", "created_at" and "updated_at".

    .. php:attr:: listFields

        protected

        An array containing the fields which should appear in the list view
        of the entity.

    .. php:attr:: childrenLabelFields

        protected

        The fields used to display the children on the details page of an entity.
        The keys are the entity names as in the CRUD YAML and the values are the
        field names.

    .. php:attr:: deleteCascade

        protected

        Whether to delete its children when an instance is deleted.

    .. php:attr:: pageSize

        protected

        The amount of items to display per page on the listview.

    .. php:attr:: filter

        protected

        The fields offering to be filtered.

    .. php:attr:: serviceProvider

        protected

        Holds the {@see ServiceProvider}.

    .. php:attr:: locale

        protected

        Holds the locale.

    .. php:attr:: initialSortField

        protected

        Holds the initial sort field.

    .. php:attr:: initialSortAscending

        protected

        Holds the initial sort order.

    .. php:method:: getFilteredFieldNames($exclude)

        Gets the field names exluding the given ones.

        :type $exclude: array
        :param $exclude: the field names to exclude
        :returns: array all field names excluding the given ones

    .. php:method:: getFieldValue($name, $key)

        Gets the value of a field key.

        :type $name: string
        :param $name: the name of the field
        :type $key: string
        :param $key: the value of the key
        :returns: mixed the value of the field key or null if not existing

    .. php:method:: setFieldValue($name, $key, $value)

        Sets the value of a field key. If the field or the key in the field
        don't exist, they get created.

        :type $name: string
        :param $name: the name of the field
        :type $key: string
        :param $key: the value of the key
        :type $value: mixed
        :param $value: the new value

    .. php:method:: getReferenceValue($fieldName, $key)

        Gets the value of a reference field.

        :type $fieldName: string
        :param $fieldName: the field name of the reference
        :type $key: string
        :param $key: the key of the reference value
        :returns: string the value of the reference field

    .. php:method:: isConstraint($fieldName, $constraint)

        Checks if the given field has the given constraint.

        :type $fieldName: string
        :param $fieldName: the field name maybe having the constraint
        :type $constraint: string
        :param $constraint: the constraint to check, 'required' or 'unique'
        :returns: boolean true if the given field has the given constraint

    .. php:method:: __construct($table, $fields, $label, $localeLabels, $standardFieldLabels, ServiceProvider $serviceProvider)

        Constructor.

        :type $table: string
        :param $table: the table of the entity
        :type $fields: array
        :param $fields: the fieldstructure just like the CRUD YAML
        :type $label: string
        :param $label: the label of the entity
        :type $localeLabels: array
        :param $localeLabels: the labels  of the entity in the locales
        :type $standardFieldLabels: array
        :param $standardFieldLabels: labels for the fields "id", "created_at" and "updated_at"
        :type $serviceProvider: ServiceProvider
        :param $serviceProvider: The current service provider

    .. php:method:: getFieldNames()

        Gets all field names, including the implicit ones like "id" or
        "created_at".

        :returns: array the field names

    .. php:method:: setListFields($listFields)

        Sets the field names to be used in the listview.

        :type $listFields: array
        :param $listFields: the field names to be used in the listview

    .. php:method:: getListFields()

        Gets the field names to be used in the listview. If they were not
        specified,
        all public field names are returned.

        :returns: array the field names to be used in the listview

    .. php:method:: getChildrenLabelFields()

        Gets the fields used to display the children on the details page of an
        entity. The keys are the entity names as in the CRUD YAML and the values
        are the field names.

        :returns: array the fields used to display the children on the details page

    .. php:method:: setChildrenLabelFields($childrenLabelFields)

        Sets the fields used to display the children on the details page of an
        entity. The keys are the entity names as in the CRUD YAML and the values
        are the field names.

        :type $childrenLabelFields: array
        :param $childrenLabelFields: the fields used to display the children on the details page

    .. php:method:: isDeleteCascade()

        Gets whether to delete its children when an instance is deleted.

        :returns: boolean true if so

    .. php:method:: setDeleteCascade($deleteCascade)

        Sets whether to delete its children when an instance is deleted.

        :type $deleteCascade: boolean
        :param $deleteCascade: whether to delete its children when an instance is deleted

    .. php:method:: getPageSize()

        Gets the amount of items to display per page on the listview.

        :returns: integer the amount of items to display per page on the listview

    .. php:method:: setPageSize($pageSize)

        Sets the amount of items to display per page on the listview.

        :type $pageSize: integer
        :param $pageSize: the amount of items to display per page on the listview

    .. php:method:: getFilter()

        Gets the fields offering a filter.

        :returns: array the fields to filter

    .. php:method:: setFilter($filter)

        Sets the fields offering a filter.

        :type $filter: array
        :param $filter: the fields to filter

    .. php:method:: getServiceProvider()

        Gets the service provider.

        :returns: ServiceProvider the service provider

    .. php:method:: setServiceProvider(ServiceProvider $serviceProvider)

        Sets the service provider.

        :type $serviceProvider: ServiceProvider
        :param $serviceProvider: the new service provider

    .. php:method:: getPublicFieldNames()

        Gets the public field names. The internal fields "version" and
        "deleted_at" are filtered.

        :returns: array the public field names

    .. php:method:: getEditableFieldNames()

        Gets the field names which are editable. Not editable are fields like the
        id or the created_at.

        :returns: array the editable field names

    .. php:method:: getReadOnlyFields()

        Gets the read only field names like the id or the created_at.

        :returns: array the read only field names

    .. php:method:: getType($fieldName)

        Gets the type of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: string the type or null on invalid field name

    .. php:method:: setType($fieldName, $value)

        Sets the type of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: string
        :param $value: the new field type

    .. php:method:: isRequired($fieldName)

        Gets whether a field is required.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: boolean true if so

    .. php:method:: setRequired($fieldName, $value)

        Sets whether a field is required.

        :type $fieldName: string
        :param $fieldName: the new required state
        :param $value:

    .. php:method:: isUnique($fieldName)

        Gets whether a field is unique.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: boolean true if so

    .. php:method:: setUnique($fieldName, $value)

        Sets whether a field is unique.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: boolean
        :param $value: true if so

    .. php:method:: getReferenceTable($fieldName)

        Gets the table field of a reference.

        :type $fieldName: string
        :param $fieldName: the field name of the reference
        :returns: string the table field of a reference or null on invalid field name

    .. php:method:: getReferenceNameField($fieldName)

        Gets the name field of a reference.

        :type $fieldName: string
        :param $fieldName: the field name of the reference
        :returns: string the name field of a reference or null on invalid field name

    .. php:method:: getReferenceEntity($fieldName)

        Gets the entity field of a reference.

        :type $fieldName: string
        :param $fieldName: the field name of the reference
        :returns: string the entity field of a reference or null on invalid field name

    .. php:method:: getFilePath($fieldName)

        Gets the file path of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: string the file path of a field or null on invalid field name

    .. php:method:: setFilePath($fieldName, $value)

        Sets the file path of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: string
        :param $value: the file path of a field or null on invalid field name

    .. php:method:: getFixedValue($fieldName)

        Gets the value of a fixed field.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: string the value of a fixed field or null on invalid field name

    .. php:method:: setFixedValue($fieldName, $value)

        Sets the value of a fixed field.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: string
        :param $value: the new value for the fixed field

    .. php:method:: getSetItems($fieldName)

        Gets the items of a set field.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: array the items of the set field or null on invalid field name

    .. php:method:: setSetItems($fieldName, $value)

        Sets the items of a set field.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: string
        :param $value: the new items of the set field

    .. php:method:: getFloatStep($fieldName)

        Gets the step size of a float field.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: array the step size of a float field or null on invalid field name

    .. php:method:: setFloatStep($fieldName, $value)

        Sets the step size of a float field.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: string
        :param $value: the new step size of the float field

    .. php:method:: getFieldLabel($fieldName)

        Gets the label of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: string the label of the field or the field name if no label is set in the CRUD YAML

    .. php:method:: setFieldLabel($fieldName, $value)

        Gets the label of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: string
        :param $value: the new label of the field

    .. php:method:: getTable()

        Gets the table where the data is stored.

        :returns: string the table where the data is stored

    .. php:method:: setTable($table)

        Sets the table where the data is stored.

        :type $table: string
        :param $table: the new table where the data is stored

    .. php:method:: getLabel()

        Gets the label for the entity.

        :returns: string the label for the entity

    .. php:method:: setLabel($label)

        Sets the label for the entity.

        :type $label: string
        :param $label: the new label for the entity

    .. php:method:: getDescription($fieldName)

        Gets the description of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :returns: string the description of the field

    .. php:method:: setDescription($fieldName, $value)

        Sets the description of a field.

        :type $fieldName: string
        :param $fieldName: the field name
        :type $value: string
        :param $value: the new description of the field

    .. php:method:: addChild($table, $fieldName, $entity)

        Adds a child to this definition in case the other
        definition has a reference to this one.

        :type $table: string
        :param $table: the table of the referencing definition
        :type $fieldName: string
        :param $fieldName: the field name of the referencing definition
        :type $entity: string
        :param $entity: the entity of the referencing definition

    .. php:method:: getChildren()

        Gets the referencing children to this definition.

        :returns: array an array with the children referencing the entity. All entries are arrays with three referencing elements: table, fieldName, entity

    .. php:method:: setLocale($locale)

        Sets the locale to be used.

        :type $locale: string
        :param $locale: the locale to be used.

    .. php:method:: setInitialSortField($initialSortField)

        Sets the initial sort field.

        :type $initialSortField: string
        :param $initialSortField: the new initial sort field

    .. php:method:: getInitialSortField()

        Gets the initial sort field.

        :returns: string the initial sort field

    .. php:method:: setInitialSortAscending($initialSortAscending)

        Sets the initial sort order.

        :type $initialSortAscending: boolean
        :param $initialSortAscending: the initial sort order, true if ascending

    .. php:method:: getInitialSortAscending()

        Gets the initial sort order.

        :returns: boolean the initial sort order, true if ascending
