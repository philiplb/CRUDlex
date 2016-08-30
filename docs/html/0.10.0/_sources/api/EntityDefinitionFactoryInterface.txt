-----------------------------------------
CRUDlex\\EntityDefinitionFactoryInterface
-----------------------------------------

.. toctree::
  :maxdepth: 1

  EntityDefinitionFactory

.. php:namespace: CRUDlex

.. php:interface:: EntityDefinitionFactoryInterface

    Interface to make the creation of the EntityDefinitions flexible. To be handed into
    the ServiceProvider registration via the key "crud.entitydefinitionfactory".

    .. php:method:: createEntityDefinition($table, $fields, $label, $localeLabels, $standardFieldLabels, ServiceProvider $serviceProvider)

        Creates an EntityDefinition instance.

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
        :returns: EntityDefinition the new instance
