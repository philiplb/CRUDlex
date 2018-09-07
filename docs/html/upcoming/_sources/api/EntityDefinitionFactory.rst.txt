--------------------------------
CRUDlex\\EntityDefinitionFactory
--------------------------------

.. php:namespace: CRUDlex

.. php:class:: EntityDefinitionFactory

    Default implementation of the EntiyDefinitionFactoryInterface being used if the key "crud.entitydefinitionfactory" is
    not given during the registration of the ServiceProvider.

    .. php:method:: createEntityDefinition($table, $fields, $label, $localeLabels, $standardFieldLabels, Service $serviceProvider)

        {@inheritdoc}

        :param $table:
        :param $fields:
        :param $label:
        :param $localeLabels:
        :param $standardFieldLabels:
        :type $serviceProvider: Service
        :param $serviceProvider:
