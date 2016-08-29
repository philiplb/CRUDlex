-------------------------------------------
CRUDlex\\EntityDefinitionValidatorInterface
-------------------------------------------

.. toctree::
   :maxdepth: 1

   EntityDefinitionValidator

.. php:namespace: CRUDlex

.. php:interface:: EntityDefinitionValidatorInterface

    An interface for validating entity definitions.

    .. php:method:: validate($data)

        Validates the given entity definition data which was parsed from the
        crud.yml.

        :type $data: array
        :param $data: the data to validate
