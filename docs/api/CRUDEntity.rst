-------------------
CRUDlex\\CRUDEntity
-------------------

.. php:namespace: CRUDlex

.. php:class:: CRUDEntity

    Represents a single set of data in field value pairs like the row in a
    database. Depends of course on the {@see CRUDData} implementation being used.
    With this objects, the data is passed arround and validated.

    .. php:attr:: definition

        protected

        The {@see CRUDEntityDefinition} defining how this entity looks like.

    .. php:attr:: entity

        protected

        Holds the key value data of the entity.

    .. php:method:: toType($value, $type)

        Converts a given value to the given type.

        :type $value: mixed
        :param $value: the value to convert
        :type $type: string
        :param $type: the type to convert to like 'int' or 'float'
        :returns: mixed the converted value

    .. php:method:: __construct(CRUDEntityDefinition $definition)

        Constructor.

        :type $definition: CRUDEntityDefinition
        :param $definition: the definition how this entity looks

    .. php:method:: set($field, $value)

        Sets a field value pair of this entity.

        :type $field: string
        :param $field: the field
        :type $value: mixed
        :param $value: the value

    .. php:method:: getRaw($field)

        Gets the raw value of a field no matter what type it is.
        This is usefull for input validation for example.

        :type $field: string
        :param $field: the field
        :returns: mixed null on invalid field or else the raw value

    .. php:method:: get($field)

        Gets the value of a field in its specific type.

        :type $field: string
        :param $field: the field
        :returns: mixed null on invalid field, an int if the definition says that the type of the field is an int, a boolean if the field is a bool or else the raw value

    .. php:method:: getDefinition()

        Gets the entity definition.

        :returns: CRUDEntityDefinition the definition

    .. php:method:: populateViaRequest(Request $request)

        Populates the entities fields from the requests parameters.

        :type $request: Request
        :param $request: the request to take the field data from
