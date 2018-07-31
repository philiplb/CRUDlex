-------------------------
CRUDlex\\MySQLDataFactory
-------------------------

.. php:namespace: CRUDlex

.. php:class:: MySQLDataFactory

    A factory implementation for MySQLData instances.

    .. php:attr:: database

        protected Connection

        Holds the Doctrine DBAL instance.

    .. php:attr:: useUUIDs

        protected bool

        Flag whether to use UUIDs as primary key.

    .. php:method:: __construct(Connection $database, $useUUIDs = false)

        Constructor.

        :type $database: Connection
        :param $database:
        :param $useUUIDs:

    .. php:method:: createData(EntityDefinition $definition, FilesystemInterface $filesystem)

        {@inheritdoc}

        :type $definition: EntityDefinition
        :param $definition:
        :type $filesystem: FilesystemInterface
        :param $filesystem:
