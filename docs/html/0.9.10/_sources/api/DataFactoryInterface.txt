-----------------------------
CRUDlex\\DataFactoryInterface
-----------------------------

.. toctree::
   :maxdepth: 1

   MySQLDataFactory

.. php:namespace: CRUDlex

.. php:interface:: DataFactoryInterface

    An interface used by the {@see ServiceProvider} to construct
    {@see Data} instances. By implementing this and handing it into
    the service provider, the user can control what database (-variation) he
    wants to use.

    .. php:method:: createData(EntityDefinition $definition, FileProcessorInterface $fileProcessor)

        Creates instances.

        :type $definition: EntityDefinition
        :param $definition: the definition of the entities managed by the to be created instance
        :type $fileProcessor: FileProcessorInterface
        :param $fileProcessor: the file processor managing uploaded files
        :returns: Data the newly created instance
