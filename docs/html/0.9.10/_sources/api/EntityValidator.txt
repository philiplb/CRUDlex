------------------------
CRUDlex\\EntityValidator
------------------------

.. php:namespace: CRUDlex

.. php:class:: EntityValidator

    Performs validation of the field values of the given {@see Entity}.

    .. php:attr:: entity

        protected

        The entity to validate.

    .. php:attr:: definition

        protected

        The entities definition.

    .. php:method:: fieldToRules($field, AbstractData $data, Valdi\Validator $validator)

        Builds up the validation rules for a single field according to the
        entity definition.

        :type $field: string
        :param $field: the field for the rules
        :type $data: AbstractData
        :param $data: the data instance to use for validation
        :type $validator: Valdi\Validator
        :param $validator:
        :returns: array the validation rules for the field

    .. php:method:: buildUpRules(AbstractData $data, Valdi\Validator $validator)

        Builds up the validation rules for the entity according to its
        definition.

        :type $data: AbstractData
        :param $data: the data instance to use for validation
        :type $validator: Valdi\Validator
        :param $validator:
        :returns: array the validation rules for the entity

    .. php:method:: buildUpData()

        Builds up the data to validate from the entity.

        :returns: array a map field to raw value

    .. php:method:: __construct(Entity $entity)

        Constructor.

        :type $entity: Entity
        :param $entity: the entity to validate

    .. php:method:: validate(AbstractData $data, $expectedVersion)

        Validates the entity against the definition.

        :type $data: AbstractData
        :param $data: the data access instance used for counting things
        :type $expectedVersion: integer
        :param $expectedVersion: the version to perform the optimistic locking check on
        :returns: array an array with the fields "valid" and "errors"; valid provides a quick check whether the given entity passes the validation and errors is an array with all errored fields as keys and arrays as values; this field arrays contains the actual errors on the field: "boolean", "floating", "integer", "dateTime" (for dates and datetime fields), "inSet", "reference", "required", "unique", "value" (only for the version field, set if the optimistic locking failed).
