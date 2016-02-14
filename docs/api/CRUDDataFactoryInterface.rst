---------------------------------
CRUDlex\\CRUDDataFactoryInterface
---------------------------------

.. toctree::
   :maxdepth: 1

   CRUDMySQLDataFactory

.. php:namespace: CRUDlex

.. php:interface:: CRUDDataFactoryInterface

    An interface used by the {@see CRUDServiceProvider} to construct
    {@see CRUDData} instances. By implementing this and handing it into
    the service provider, the user can control what database (-variation) he
    wants to use.

    .. php:method:: createData(CRUDEntityDefinition $definition, CRUDFileProcessorInterface $fileProcessor)

        Creates instances.

        :type $definition: CRUDEntityDefinition
        :param $definition: the definition of the entities managed by the to be created instance
        :type $fileProcessor: CRUDFileProcessorInterface
        :param $fileProcessor: the file processor managing uploaded files
        :returns: CRUDData the newly created instance
