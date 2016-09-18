------------------------
CRUDlex\\UniqueValidator
------------------------

.. php:namespace: CRUDlex

.. php:class:: UniqueValidator

    A validator to check for an unique field.

    .. php:method:: isValidUniqueMany($value, AbstractData $data, Entity $entity, $field)

        Checks whether the unique constraint is valid for a many-to-many field.

        :type $value: array
        :param $value: the value to check
        :type $data: AbstractData
        :param $data: the data to perform the check with
        :type $entity: Entity
        :param $entity: the entity to perform the check on
        :param $field:
        :returns: boolean true if it is a valid unique many-to-many constraint

    .. php:method:: isValidUnique($value, AbstractData $data, Entity $entity, $field)

        Performs the regular unique validation.

        :param $value:
        :type $data: AbstractData
        :param $data:
        :type $entity: Entity
        :param $entity:
        :param $field:
        :returns: boolean true if everything is valid

    .. php:method:: isValid($value, $parameters)

        {@inheritdoc}

        :param $value:
        :param $parameters:

    .. php:method:: getInvalidDetails()

        {@inheritdoc}
