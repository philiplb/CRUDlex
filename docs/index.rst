Welcome to CRUDlex's documentation!
===================================

This is the documentation of CRUDlex, an easy to use CRUD generator for `Silex <http://silex.sensiolabs.org/>`_.
It describes every feature being available.

Requirements:

* PHP >= 7.2
* For the MySQL driver: MySQL >= 5.1
* For the file uploads: PECL fileinfo >= 0.1.0

.. image:: _static/01_List.png

Contents:

.. toctree::
   :maxdepth: 3
   :caption: Manual

   manual/introduction
   manual/setup
   manual/datastructures
   manual/datatypes
   manual/constraints
   manual/filehandling
   manual/layouts
   manual/templates
   manual/events
   manual/optimisticlocking
   manual/definitionvalidation
   manual/routes
   manual/extendedfeatures
   manual/addons
   manual/crudyamlreference

.. toctree::
   :maxdepth: 2
   :caption: API

   api/AbstractData
   api/ControllerInterface
   api/DataFactoryInterface
   api/Entity
   api/EntityDefinition
   api/EntityDefinitionFactoryInterface
   api/EntityDefinitionValidatorInterface
   api/EntityEvents
   api/EntityValidator
   api/FileHandler
   api/ManyValidator
   api/ReferenceValidator
   api/Service
   api/StreamedFileResponse
   api/TwigExtensions
   api/UniqueValidator
   api/YamlReader

Indices and tables
==================

* :ref:`genindex`
* :ref:`search`
