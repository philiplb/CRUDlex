CRUDlex Changelog
=================

## 0.15.0
Released: Upcoming
- Fixed a crash where the namefield of a many relation is a MySQL keyword, thanks to https://github.com/th-lange
- Fixed a timeout within the fetching of the language names
- Fixed a crash if a referenced entity had hard deletion
- Fixed a crash if a route of a non existent entity was requested, properly returning an HTTP 404 now
- Updated dependencies:
    - "philiplb/valdi": "^1.0"

## 0.14.0
Released: 2018-09-07
- Added PHP 7.2 as test target
- Attention: Dropped support for PHP <= 7.1
- Attention: CRUDlex is now not anymore dependent on Silex as framework; the Silex implementation got split to https://github.com/philiplb/CRUDlexSilex2; a Symfony 4 implementation is done at https://github.com/philiplb/CRUDlexSymfony4Bundle
- Attention: Splitted the class ControllerProvider into Controller and Silex\ControllerProvider with Controller being customizable and implementing the new ControllerInterface
- Attention: Splitted the class ServiceProvider into Service and Silex\ServiceProvider
- Attention: Changed the mechanism to define custom layouts and templates from Pimple keys like $app['crud.layout'] = 'myLayout.twig' to the Provider function $app['crud']->setTemplate('layout', 'myLayout.twig')
- Attention: Changed the i18n management flag from the Pimple key "crud.manageI18n" to the Provider functions $app['crud']->isManageI18n() and $app['crud']->setManageI18n()
- Attention: Moved the class "ServiceProvider" from the namespace "CRUDlex" to the namespace "CRUDlex\Silex"
- Attention: Prefixed all Twig functions and filters with "crudlex_"
- Replaced all Pimple calls to "app." in the templates with calls to the crud instance or new Twig functions
- Updated dependencies:
    - "phpunit/phpunit": "~7.2"
    - "eloquent/phony": "~3.0"
    - "eloquent/phony-phpunit": "~4.0"

## 0.13.0
Released: 2018-02-12
- The soft deletion is now optional (but switched on by default)
- Fixed content type of static files by detecting the mimetype by their name

## 0.12.0
Released: 2017-08-22
- Attention: Switched from the own abstraction "FileProcessorInterface" to the library Flysystem for file handling, API changes:
    - All implementations of the FileProcessorInterface are gone now
    - The class "MimeTypes" is gone now
    - DataFactoryInterface::createData now needs a League\Flysystem\FilesystemInterface as last parameter
    - The constructor of MySQLData now takes a FilesystemInterface instead of a FileProcessorInterface
    - ServiceProvider::init now takes only two parameters: $crudFileCachingDirectory, Container $app
    - AbstractData::shouldExecuteEvents is now public
    - AbstractData::createFiles, ::updateFiles, ::deleteFile, ::deleteFiles and ::renderFile are moved to an own class: FileHandler
- Attention: Moved the event handling to an own class and so the API changed
- Added a caching mechanism for the parsing of the CRUD YAML files
- Added the possibility to group entities in the navigation bar, thanks to https://github.com/dmaciel
- Added an optional hideId parameter for references and many relations so the id is hidden in the reference buttons, thanks to https://github.com/jmfayard
- Optimistic locking can now be turned off per entity
- Attention: Prefixed the name of the route for static assets from "static" to "crudStatic" just like the other routes
- Added documentation about the routes added by ControllerProvider
- Attention: Changed the following routes from "match" to just "get", so only HTTP GET is allowed on them:
    - crudList
    - crudShow
    - crudRenderFile
- Added documentation about how to optimize serving the static content
- Attention: Moved the events code up to the abstract data class which changed the signatures a bit
- "before" "delete" Events of cascade deleted children are now properly taken into account
- added @var annotations to class members for better IDE usage
- switched code formatting from Javaish style to the PSR-2 standard
- Updated dependencies:
    - "silex/silex": "~2.2"
    - "doctrine/dbal": "~2.5"
    - "symfony/twig-bridge": "~3.2"
    - "symfony/yaml": "~3.3"
    - "symfony/translation": "~3.3"
    - "symfony/intl": "~3.3"
    - "symfony/browser-kit": "~3.3"
    - "symfony/css-selector": "~3.3"
    - "eloquent/phony": "~1.0"
    - Quill Editor v1.3.1
    - flatpickr v3.0.7

## 0.11.0
Released: 2017-04-02
- Added a french translation, thanks to https://github.com/k20human
- Added a new data type: WYSIWYM (What You See Is What You Mean), with a visual editor producing HTML
- Added a "CRUD YAML Reference" chapter in the documentation
- The referencing children list of an entity has now an "Create New" button, thanks to https://github.com/k20human
- The endpoint for static files now uses ETag caching speeding up the rendering of the UI
- Fixed the initialization of the TwigServiceProvider using the Silex 2 API now
- Attention: Removed the method AbstractData::fetchReferences and so simplified further implementations
- Fixed the feature of prepopulated creation forms via GET parameter
- Nicer visualization of boolean values using icons
- Fixed the initialization of the TwigServiceProvider if it wasn't present yet
- Fixed adding the YAML loader and languages to the translator by moving it to the boot phase of the service provider
- Fixed a crash if a many field was a reserved MySQL word
- Filters now only do a LIKE-comparison if the field is a text, multiline or fixed field, else they use strict equals
- Restructured the i18n handling and initialization a bit so the 'crud' provider is properly lazily initialized and the YaML not parsed for routes outside CRUDlex
- Nullable fields with empty form input are now properly stored as null
- Removed dependencies:
    - Moment.js
- Updated dependencies:
    - "eloquent/phony": "~0.14"
    - "symfony/browser-kit": "~3.2"
    - "symfony/css-selector": "~3.2"
    - "symfony/twig-bridge": "~3.2"
    - "symfony/yaml": "~3.2"
    - flatpickr 2.4.8
    - jQuery 3.2.1

## 0.10.0
Released: 2016-09-18
- Added a new data type implementing a many-to-many relationship called "many", sponsored by [italic](https://github.com/italic)
- Switched to SemVer
- Added validation of the entity definition YAML file
- Replaced handwritten mocks with Phony
- Moved the mime type reading into an own class
- Added a meaningful exception if invalid field names are given in "fieldList" or "filter"
- Attention: The minimum PHP version is now 5.5
- Attention: Updated to Silex 2.0
- Attention: Switched from PSR-0 to PSR-4
- Attention: The field entity.field.reference.table is not needed anymore
- Attention: Renamed entity definition YAML fields:
    - setitems -> items
    - filepath -> path
    - fixedvalue -> value
- Attention: Moved the following functions from the ServiceProvider to Twig extensions:
    - arrayColumn -> Twig Filter arrayColumn
    - getLanguageName -> Twig Filter languageName
    - formatFloat -> Twig Filter float
    - basename -> basename
    - formatDate -> formatDate
    - formatDateTime -> formatDateTime
- Attention: Replaced the following functions of the class EntityDefinition with getSubTypeField:
    - getReferenceNameField
    - getReferenceEntity
- Attention: Replaced the following functions of the class EntityDefinition with getField:
    - getDescription
    - getFloatStep
    - getItems
    - getValue
    - getPath
    - isUnique
    - isRequired
- Attention: Replaced the following functions of the class EntityDefinition with setField:
    - setDescription
    - setFloatStep
    - setItems
    - setValue
    - setPath
    - setUnique
    - setRequired
- Switched to a flag-sprites.com generated css sprite for the language flags
- Updated dependencies:
    - "silex/silex": "~2.0"
    - "symfony/twig-bridge": "~3.1"
    - "philiplb/valdi": "0.10.0"
    - "symfony/yaml": "~3.1"
    - "symfony/translation": "~3.1"
    - "symfony/intl": "~3.1"
    - "symfony/browser-kit": "~3.1"
    - "symfony/css-selector": "~3.1"
    - "eloquent/phony": "~0.13"
    - Bootstrap 3.3.7
- Switched to the array shorthand
- Correctly saving null if not required date time fields are not filled

## 0.9.10
Released: 2016-06-19
- Attention: Removed the prefix "CRUD" from all classes as they live in their own namespace anyway
- Attention: The data types "int" and "bool" got renamed to "integer" and "boolean"
- Attention, API changes:
    - CRUDlex\Data -> CRUDlex\AbstractData
    - EntityDefinition::getInitialSortAscending() -> EntityDefinition::isInitialSortAscending()
    - ServiceProvider::getMangeI18N() -> ServiceProvider::isManagingI18n()
    - Show-Page: The id "crudEntityShowTable" is now a class
    - CRUDlex\EntityValidator changed its return structure to the one of Valdi:
      http://philiplb.github.io/Valdi/docs/html/0.9.0/manual/gettingstarted.html#validation
    - The date and datetime fields changed moved their classes to the input fields and changed their names to "crudDate" and "crudDateTime"
- Attention: Fixed a security issue in the static file provider
- Changed the entity validation to https://github.com/philiplb/Valdi
- Changed the date and date time pickers to https://github.com/chmln/flatpickr
- Replaced the markdown manual and the APIGen documentation with an unified Sphinx version
- Added RTL support in the i18n system
- Added file handling events
- Made a base path configurable for the SimpleFilesystemFileProcessor
- The ServiceProvider uses now static instantiation instead of calling his own class making it easier to override
- Added some more IDs and classes in the HTML to be more tweakable
- Fixed a crash if the table name of an entity is a MySQL keyword
- Fixed a crash if the field name of an entity is a MySQL keyword
- Fixed a crash if the sort field name of an entity is a MySQL keyword
- Fixed a crash if non required reference fields where not given
- Fixed a crash if referenced entities got soft deleted by a third party
- Fixed the sort order being properly handled in the pagination buttons now
- Fixed and refactored a lot of things revealed by static code analysis

## 0.9.9
Released: 2016-02-11
- Attention: From now on, the created_at and updated_at timestamps are stored as UTC values in the MySQL data provider
- The list views can be sorted now
- Added optimistic locking for editing an entity
- CRUDMySQLData now offers an option to use UUIDs as primary key instead of an auto incremented value
- Added a function to the service provider to get the available locales
- Added a function to the service provider to get the name of the language of a locale
- Generating the language picker based on the available translation files instead of being hard coded
- Fixed and refactored a lot of things revealed by static code analysis
- Updated dependencies:
    - Symfony-Components to the current LTS version 2.8
    - "symfony/...": "~2.8" (current LTS version)
    - "phpunit/phpunit": "~4.8"
    - "satooshi/php-coveralls": "1.0.1"
    - "apigen/apigen": "4.1.2"
    - Eonasdan/bootstrap-datetimepicker V4.17.37
    - Bootstrap 3.3.6
    - moment.js 2.11.2
    - jQuery 2.2.0

## 0.9.8
Released: 2015-09-28
- Added complete i18n support, initially with en, de and gr
- Added events for reactions before or after creating, updating or deleting an entity
- Added the possibility to override every single template
- Initializing all needed providers in the CRUDServiceProvider if not done yet by the application
- Made the nameField of a reference optional
- Fixed a crash when choosing "created_at", "updated_at", "id", "deleted_at" or "version" as filter field
- Some potential crashes in CRUDEntityDefinition fixed revealed by new unit tests
- Fixed the display of very small float values which where converted to scientific notation

## 0.9.7
Released: 2015-07-26
- Added an optional description per field
- CRUDData::listEntries() can take now operators for the filter parameter
- Added filters for the list views
- Big cleanup of the CRUDEntityDefinition constructor
- Not required number fields not being entered by the user end up the database as NULL instead of as 0
- null is a valid value when validating an int, float or reference which is not required in an entity
- Added classes to the create, edit and delete forms so they can be hooked easily with JavaScript
- Using the DBAL querybuilder in the CRUDMySQLData class instead of constructing the queries via string concatenation; this might solve some unknown security issues and is more readable
- Fixed all issues revealed by SensioLabsInsight
- The requirements are now mentioned in the README.md
- Set the preferred-install in the composer package
- Moved some features to an own chapter in the documentation
- Added setters to CRUDEntityDefinition:
    - setServiceProvider
    - setUnique
    - setFilePath
    - setSetItems
    - setFloatStep
    - setFieldLabel
    - setTable
    - setLabel
- Updated dependencies to:
    - "silex/silex": "~1.3"
    - "symfony/twig-bridge": "~2.7"
    - "symfony/yaml": "~2.7"
    - "phpunit/phpunit": "4.7.6"
    - "symfony/browser-kit": "~2.7"
    - "symfony/css-selector": "~2.7"
    - "apigen/apigen": "4.1.1"
    - Eonasdan/bootstrap-datetimepicker V4.14.30
    - Bootstrap 3.3.5
    - moment.js 2.10.3
    - jQuery 2.1.4

## 0.9.6
Released: 2015-02-20
- Added the possibility to cascade delete children
- Added ids to the rendered fields of an entity details page so they can be hooked easily with JavaScript
- Added ids and classes to the rows and columns of the entity list page so they can be hooked easily with JavaScript
- Added ids and classes all buttons so they can be hooked easily with JavaScript
- Added an entity related id to the page wrapping div so everything can be hooked easily with JavaScript
- Fixed a SQL error for not required date and datetime fields where no value was selected
- Fixed a SQL error for not required reference fields where no value was selected
- Updated dependencies to:
    - Bootstrap 3.3.1
    - moment.js 2.8.4
    - "symfony/twig-bridge": "~2.6"
    - "doctrine/dbal": "~2.5"
    - "symfony/yaml": "~2.6"
    - "phpunit/phpunit": "~4.5",
    - "satooshi/php-coveralls": "0.7.*@dev",
    - "symfony/browser-kit": "~2.6",
    - "symfony/css-selector": "~2.6",
    - "apigen/apigen": "4.0.0"
- Added pagination to the list view
- Added the ability to hand in default values to the create form
- Added edit and delete buttons to the children table of the entities show page
- CRUDMySQLData::listEntries handles null filter values now correctly
- Fixed values are also returned in CRUDEntity::get() now
- Huge performance optimization when listing entities referencing other entities

## 0.9.5
Released: 2014-11-07
- Changed CRUDEntityDefinition::addParent() to CRUDEntityDefinition::addChild() and CRUDEntityDefinition::getParents() to CRUDEntityDefinition::getChildren() as it was confusing
- Added the option to show the referencing children on the parents details page
- Nicer requirements in the composer.json, less strict (~1.2 instead of 1.2.1 for example)
- Modifications of the entity definition (changing the type) doesn't require the field to exist anymore; this prepares the runtime modification of the whole definition
- API docs are written now
- Updated dependencies to: Bootstrap 3.3.0, moment.js 2.8.3
- Removed all usages of CDNs for CSS and JS resources and delivering them from the filesystem
- Added support for float fields.

## 0.9.4
Released: 2014-10-07
- The input placeholders now use the field label as well and don't use just the fieldlabel anymore
- url fields in the list view get cut after 27 characters now with "..." at the end, the full url is still available as tooltip
- A first version of file uploads
- Introducing the addons repository with Amazon S3 file support
- Added the possibility to extend the CRUDControllerProvider which had a hard coded reference to itself before
- Added the type "fixed" which always has a fixed value
- A type of a field can be changed in runtime now via a setter of the CRUDEntityDefinition
- A required constraint of a field can be changed in runtime now via a setter of the CRUDEntityDefinition

## 0.9.3
Released: 2014-09-09
- Rearranged the button positions, sizes and colors
- Multiline fields get cut after 27 characters now with "..." at the end, the full text is still available as tooltip
- URL fields show only their base name in the list view, but are still clickable to the full URL
- Custom layouts for the sections create, list, show and edit
- Custom layouts for the sections create, list, show and edit of specific entities
- Custom layouts for specific entities
- Supporting bool fields
- CRUDEntitity::get($fieldName) casts the fields now to int and booleans if the type is int or bool
- Added a manual

## 0.9.2
Released: 2014-09-04
- Creating an entity with the DB-layer doesn't return the id anymore but updates the entity field "id"
- Correctly displaying the name of the referenced entities in the list and details page
- 100% test coverage!
- 404 if trying to delete a non existant entity
- Added the possibility to define the fields shown in the list view
- Added a getter for the definition of an entity
- Supporting multiline text fields
- New datetime picker, used for dates and datetimes
- Supporting datetime fields
- Supporting set fields
- Supporting URL fields

## 0.9.1
Released: 2014-08-31
- Fixed an exception in PHP 5.3.3 coming from the visibility of an internal function being called from a closure
- Added the missing requirement "symfony/yaml" to the composer.json
- Reorganized source and resources like templates
- The entities can have labels now
- The fields can have labels now
- Added first PHPUnit tests

## 0.9
Released: 2014-08-29

First release.
