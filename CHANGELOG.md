CRUDlex Changelog
=================

## 0.9.2
Released: Upcoming
- Creating an entity with the DB-layer doesn't return the id anymore but updates the entity field "id"
- Correctly displaying the name of the referenced entities in the list and details page
- 100% test coverage!
- 404 if trying to delete a non existant entity
- Added a getter for the definition of an entity

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
