----------------------------
CRUDlex\\CRUDEntityValidator
----------------------------

.. php:namespace: CRUDlex

.. php:class:: CRUDEntityValidator

    Performs validation of the field values of the given {@see CRUDEntity}.

    .. php:attr:: entity

        protected

        The entity to validate.

    .. php:attr:: definition

        protected

        The entities definition.

    .. php:method:: validateRequired($field, $fieldErrors, $valid)

        Validates the given field for the required constraint.

        :type $field: string
        :param $field: the field to validate
        :param $fieldErrors:
        :param $valid:

    .. php:method:: validateUnique($field, CRUDData $data, $fieldErrors, $valid)

        Validates the given field for the unique constraint.

        :type $field: string
        :param $field: the field to validate
        :type $data: CRUDData
        :param $data: the data instance to work with
        :param $fieldErrors:
        :param $valid:

    .. php:method:: validateSet($field, $fieldErrors, $valid)

        Validates the given field for the set type.

        :type $field: string
        :param $field: the field to validate
        :param $fieldErrors:
        :param $valid:

    .. php:method:: validateNumber($field, $numberType, $fieldErrors, $valid)

        Validates the given field for a number type.

        :type $field: string
        :param $field: the field to validate
        :type $numberType: string
        :param $numberType: the type, might be 'int' or 'float'
        :param $fieldErrors:
        :param $valid:

    .. php:method:: validateDate($field, $fieldErrors, $valid)

        Validates the given field for the date type.

        :type $field: string
        :param $field: the field to validate
        :param $fieldErrors:
        :param $valid:

    .. php:method:: validateDateTime($field, $fieldErrors, $valid)

        Validates the given field for the datetime type.

        :type $field: string
        :param $field: the field to validate
        :param $fieldErrors:
        :param $valid:

    .. php:method:: validateReference($field, CRUDData $data, $fieldErrors, $valid)

        Validates the given field for the reference type.

        :type $field: string
        :param $field: the field to validate
        :type $data: CRUDData
        :param $data: the data instance to work with
        :param $fieldErrors:
        :param $valid:

    .. php:method:: __construct(CRUDEntity $entity)

        Constructor.

        :type $entity: CRUDEntity
        :param $entity: the entity to validate

    .. php:method:: validate(CRUDData $data, $expectedVersion)

        Validates the entity against the definition.

        :type $data: CRUDData
        :param $data: the data access instance used for counting things
        :type $expectedVersion: integer
        :param $expectedVersion: the version to perform the optimistic locking check on
        :returns: array an array with the fields "valid" and "fields"; valid provides a quick check whether the given entity passes the validation and fields is an array with all fields as keys and arrays as values; this field arrays contain three keys: required, unique and input; each of them represents with a boolean whether the input is ok in that way; if "required" is true, the field wasn't set, unique means the uniqueness of the field in the datasource and input is used to indicate whether the form of the value is correct (a valid int, date, depending on the type in the definition)
