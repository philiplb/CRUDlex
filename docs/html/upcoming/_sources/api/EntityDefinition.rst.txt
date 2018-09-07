-------------------------
CRUDlex\\EntityDefinition
-------------------------

.. php:namespace: CRUDlex

.. php:class:: EntityDefinition

    The class for defining a single entity.

    .. php:attr:: table

        protected string

        The table where the data is stored.

    .. php:attr:: fields

        protected array

        Holds all fields in the same structure as in the CRUD YAML file.

    .. php:attr:: label

        protected string

        The label for the entity.

    .. php:attr:: localeLabels

        protected array

        The labels  of the entity in the locales.

    .. php:attr:: children

        protected array

        An array with the children referencing the entity. All entries are
        arrays with three referencing elements: table, fieldName, entity

    .. php:attr:: standardFieldLabels

        protected array

        Labels for the fields "id", "created_at" and "updated_at".

    .. php:attr:: listFields

        protected array

        An array containing the fields which should appear in the list view
        of the entity.

    .. php:attr:: childrenLabelFields

        protected array

        The fields used to display the children on the details page of an entity.
        The keys are the entity names as in the CRUD YAML and the values are the
        field names.

    .. php:attr:: deleteCascade

        protected bool

        Whether to delete its children when an instance is deleted.

    .. php:attr:: pageSize

        protected int

        The amount of items to display per page on the listview.

    .. php:attr:: filter

        protected array

        The fields offering to be filtered.

    .. php:attr:: service

        protected Service

        Holds the service.

    .. php:attr:: locale

        protected string

        Holds the locale.

    .. php:attr:: initialSortField

        protected string

        Holds the initial sort field.

    .. php:attr:: initialSortAscending

        protected bool

        Holds the initial sort order.

    .. php:attr:: hardDeletion

        protected bool

        Holds whether hard deletion is activated.

    .. php:attr:: navBarGroup

        protected string

        Holds if the entity must be displayed grouped in the nav bar.

    .. php:attr:: optimisticLocking

        protected bool

        Holds whether optimistic locking is switched on.

    .. php:method:: getFilteredFieldNames($exclude)

        Gets the field names exluding the given ones.

        :type $exclude: string[]
        :param $exclude: the field names to exclude
        :returns: array all field names excluding the given ones

    .. php:method:: checkFieldNames($reference, $fieldNames)

        Checks whether the given field names are declared and existing.

        :type $reference: string
        :param $reference: a hint towards the source of an invalid field name
        :type $fieldNames: array
        :param $fieldNames: the field names to check

    .. php:method:: __construct($table, $fields, $label, $localeLabels, $standardFieldLabels, Service $service)

        Constructor.

        :type $table: string
        :param $table: the table of the entity
        :type $fields: array
        :param $fields: the field structure just like the CRUD YAML
        :type $label: string
        :param $label: the label of the entity
        :type $localeLabels: array
        :param $localeLabels: the labels  of the entity in the locales
        :type $standardFieldLabels: array
        :param $standardFieldLabels: labels for the fields "id", "created_at" and "updated_at"
        :type $service: Service
        :param $service: The current service provider

    .. php:method:: getFieldNames($includeMany = false)

        Gets all field names, including the implicit ones like "id" or
        "created_at".

        :type $includeMany: boolean
        :param $includeMany: whether to include the many fields as well
        :returns: string[] the field names

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

    .. php:method:: getService()

        Gets the service.

        :returns: Service the service provider

    .. php:method:: setService(Service $service)

        Sets the service.

        :type $service: Service
        :param $service: the new service

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

        :returns: string[] the read only field names

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

    .. php:method:: setStandardFieldLabels($standardFieldLabels)

        Sets the labels for the fields "id", "created_at" and "updated_at".

        :type $standardFieldLabels: array
        :param $standardFieldLabels: labels for the fields "id", "created_at" and "updated_at"

    .. php:method:: setLocale($locale)

        Sets the locale to be used.

        :type $locale: string
        :param $locale: the locale to be used.

    .. php:method:: getLocale()

        Gets the locale to be used.

        :returns: null|string the locale to be used.

    .. php:method:: setInitialSortField($initialSortField)

        Sets the initial sort field.

        :type $initialSortField: string
        :param $initialSortField: the new initial sort field

    .. php:method:: getInitialSortField()

        Gets the initial sort field.

        :returns: string the initial sort field

    .. php:method:: setInitialSortAscending($initialSortAscending)

        Sets whether the initial sort order is ascending.

        :type $initialSortAscending: boolean
        :param $initialSortAscending: the initial sort order, true if ascending

    .. php:method:: isInitialSortAscending()

        Gets whether the initial sort order is ascending.

        :returns: boolean the initial sort order, true if ascending

    .. php:method:: setHardDeletion($hardDeletion)

        Sets the hard deletion state.

        :type $hardDeletion: boolean
        :param $hardDeletion: the hard deletion state

    .. php:method:: isHardDeletion()

        Gets the hard deletion state.

        :returns: boolean the hard deletion state

    .. php:method:: getNavBarGroup()

        Gets the navigation bar group where the entity belongs.

        :returns: string the navigation bar group where the entity belongs

    .. php:method:: setNavBarGroup($navBarGroup)

        Sets the navigation bar group where the entity belongs.

        :type $navBarGroup: string
        :param $navBarGroup: the navigation bar group where the entity belongs

    .. php:method:: hasOptimisticLocking()

        Returns whether optimistic locking via the version field is activated.

        :returns: boolean true if optimistic locking is activated

    .. php:method:: setOptimisticLocking($optimisticLocking)

        Sets whether optimistic locking via the version field is activated.

        :type $optimisticLocking: boolean
        :param $optimisticLocking: true if optimistic locking is activated

    .. php:method:: getSubTypeField($fieldName, $subType, $key)

        Gets a sub field of an field.

        :type $fieldName: string
        :param $fieldName: the field name of the sub type
        :type $subType: string
        :param $subType: the sub type like "reference" or "many"
        :type $key: string
        :param $key: the key of the value
        :returns: string the value of the sub field

    .. php:method:: getField($name, $key, $default = null)

        Gets the value of a field key.

        :type $name: string
        :param $name: the name of the field
        :type $key: string
        :param $key: the value of the key
        :type $default: mixed
        :param $default: the default value to return if nothing is found
        :returns: mixed the value of the field key or null if not existing

    .. php:method:: setField($name, $key, $value)

        Sets the value of a field key. If the field or the key in the field
        don't exist, they get created.

        :type $name: string
        :param $name: the name of the field
        :type $key: string
        :param $key: the value of the key
        :type $value: mixed
        :param $value: the new value
