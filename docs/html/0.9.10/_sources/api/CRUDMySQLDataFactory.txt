-----------------------------
CRUDlex\\CRUDMySQLDataFactory
-----------------------------

.. php:namespace: CRUDlex

.. php:class:: CRUDMySQLDataFactory

    A factory implementation for {@see CRUDMySQLData} instances.

    .. php:attr:: db

        protected

        Holds the Doctrine DBAL instance.

    .. php:attr:: useUUIDs

        protected

        Flag whether to use UUIDs as primary key.

    .. php:method:: __construct($db, $useUUIDs = false)

        Constructor.

        :param $db:
        :param $useUUIDs:

    .. php:method:: createData(CRUDEntityDefinition $definition, CRUDFileProcessorInterface $fileProcessor)

        {@inheritdoc}

        :type $definition: CRUDEntityDefinition
        :param $definition:
        :type $fileProcessor: CRUDFileProcessorInterface
        :param $fileProcessor:
