CRUDlex Changelog
=================

## 0.9.5
Released: Upcoming
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
