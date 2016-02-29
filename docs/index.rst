Welcome to CRUDlex's documentation!
===================================

This is the documentation of CRUDlex, an easy to use CRUD generator for `Silex <http://silex.sensiolabs.org/>`_.
It describes every feature being available.

Requirements:

* PHP >= 5.3.2
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
   manual/extendedfeatures
   manual/datatypes
   manual/constraints
   manual/layouts
   manual/templates
   manual/events

.. toctree::
   :maxdepth: 2
   :caption: API

   api/ControllerProvider
   api/Data
   api/DataFactoryInterface
   api/Entity
   api/EntityDefinition
   api/EntityValidator
   api/FileProcessorInterface
   api/ServiceProvider
   api/StreamedFileResponse

Indices and tables
==================

* :ref:`genindex`
* :ref:`search`
