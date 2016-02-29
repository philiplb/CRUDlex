-------------------------
CRUDlex\\MySQLDataFactory
-------------------------

.. php:namespace: CRUDlex

.. php:class:: MySQLDataFactory

    A factory implementation for {@see MySQLData} instances.

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

    .. php:method:: createData(EntityDefinition $definition, FileProcessorInterface $fileProcessor)

        {@inheritdoc}

        :type $definition: EntityDefinition
        :param $definition:
        :type $fileProcessor: FileProcessorInterface
        :param $fileProcessor:
