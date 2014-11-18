Data Types
==========

This is a comprehensive list of all supported data types, their parameters and
the related MySQL-types.

## Text

```yml
type: text
```

A single text line, no further parameters. Related MySQL-types:
- CHAR
- VARCHAR (recommended)
- TINYTEXT
- TEXT
- MEDIUMTEXT
- LONGTEXT

## Multiline

```yml
type: multiline
```

A multi text line allowing linebreaks, no further parameters. Related MySQL-types:
- CHAR
- VARCHAR
- TINYTEXT
- TEXT (recommended)
- MEDIUMTEXT
- LONGTEXT

If the field is shown in the list view and the value is longer than 27
characters, the rest is cut and replaced with three dots. The full text is still
available as tooltip in the list view though. Example with 50 characters:

"Lorem ipsum dolor sit amet, consetetur sadipscing"

Would be shown in the list view as:

"Lorem ipsum dolor sit amet,..."

## Url

```yml
type: url
```

A single text line representing an URL, no further parameters. Related
MySQL-types:
- CHAR
- VARCHAR (recommended)
- TINYTEXT
- TEXT
- MEDIUMTEXT
- LONGTEXT

The only difference to the type "text" is that url fields are clickable in the
list and show view. They are shortened in the list view to their base name in
order to save space. A value of "http://www.foo.com/bar.txt" would lead to
"http://www.foo.com/bar.txt" on click and is shortened to "bar.txt" in the list.

## Int

```yml
type: int
```

An integer, no further parameters. Related MySQL-types:
- TINYINT
- SMALLINT
- MEDIUMINT
- INT (recommended)
- BIGINT

## Float

```yml
type: float
floatStep: 0.1
```

An float. Related MySQL-types:
- FLOAT (recommended)
- DECIMAL
- DOUBLE (recommended)
- REAL

The parameter "floatStep" is to set the step size in the form field.

## Boolean

```yml
type: bool
```

A boolean value, either true or false, no further parameters. Related MySQL-type:
- TINYINT

Saved as 0 (false) or 1 (true).

## Date

```yml
type: date
```

A date value without time, no further parameters. Related MySQL-types:
- DATE
- DATETIME (recommended)
- TIMESTAMP

## Datetime

```yml
type: datetime
```

A date value with time, no further parameters. Related MySQL-type:
- DATETIME (recommended)
- TIMESTAMP

## Set

```yml
type: set
setitems: [red, green, blue]
```

A fixed set of elements to be chosen from, stored as text. Related MySQL-types:
- CHAR
- VARCHAR (recommended)
- TINYTEXT
- TEXT
- MEDIUMTEXT
- LONGTEXT

In this example, the user has the choice between the three colors "red", "green"
and "blue".

## Reference

```yml
type: reference
reference:
  table: otherTable
  nameField: otherName
  entity: otherEntity
```

This is the 1-side of a one-to-many relation. Related MySQL-type:
- INT

In order to display a proper selection UI and represent the the value from the
other table, a few more fields are needed. Those are the __table__ telling
CRUDlex where to look for the, representation, the __nameField__ describing
which field to use from the other table to display the selected value and last,
the referenced __entity__.

Think about a book in a library. The library is stored in the table "lib" and
has a field "name". A book belongs to a library, so it has an integer field
"library" referencing ids of libraries. Here is the needed yml for this
book-library relationship:

```yml
library:
    table: lib
    label: Library
    fields:
        name:
            type: text
book:
    table: book
    label: Book
    fields:
        title:
            type: text
        author:
            type: text
        library:
            type: reference
            reference:
              table: lib
              nameField: name
              entity: library
```

### Show Children

If you want to show the children (books in this case) on the details page of the
parent (library), you can activate it via the childrenLabelFields:

```yml
library:
    table: lib
    label: Library
    childrenLabelFields:
        book: title
    fields:
        name:
            type: text
book:
    table: book
    label: Book
    fields:
        title:
            type: text
        author:
            type: text
        library:
            type: reference
            reference:
              table: lib
              nameField: name
              entity: library
```

On a details page of a library, all of its books are now displayed by their
title field. If a library had more children and their label fields are not
defined, it falls back to the id field.

### Cascading Children Deletion

The default setup is, that referenced entities can't be deleted until their
children are deleted. In this case, a library can't be deleted until all of its
books are gone. You can force children deletion by using the __deleteCascade__
setting like this:

```yml
library:
    table: lib
    label: Library
    childrenLabelFields:
        book: title
    deleteCascade: true
    fields:
        name:
            type: text
book:
    table: book
    label: Book
    fields:
        title:
            type: text
        author:
            type: text
        library:
            type: reference
            reference:
              table: lib
              nameField: name
              entity: library
```

### MySQL Foreign Key Hint

Don't forget to set the MySQL foreign key.

```sql
ALTER TABLE `book`
ADD CONSTRAINT `book_ibfk_1` FOREIGN KEY (`library`) REFERENCES `lib` (`id`);
```

If a book still references a library, CRUDlex refuses to delete the library if
you try.

## File

CRUDlex supports the handling of files. They get uploaded with the create or
edit form, can be viewed, removed and replaced.

To have an image field for our library, you would declare it like this:

```yml
library:
    table: lib
    label: Library
    fields:
        image:
            type: file
            filepath: uploads
```

The images are stored in the filesystem relative to your index.php within the
subfolder you give with the filepath parameter.

If you edit an entity with a file and re-upload it or if you delete the file or
if you delete the entity, the current implementation is defensive and doesn't
physically delete the files.

You can override the storage-mechanism by giving an instance of a class
implementing the CRUDFileProcessorInterface:


```php
$app->register(new CRUDlex\CRUDServiceProvider(), array(
    'crud.file' => __DIR__ . '<yourCrud.yml>',
    'crud.datafactory' => $dataFactory,
    'crud.fileprocessor' => $myFileProcessor
));
```

If this parameter is not given, an instance of the
CRUDSimpleFilesystemFileProcessor is used.

There is an implementation available for storing and retrieving the files at
Amazon S3 within the [CRUDlex Addons](https://github.com/philiplb/CRUDlexAddons)
package.


## Fixed

```yml
type: fixed
fixedvalue: abc
```

Fills the db always with the defined, fixed value, not editable. Related MySQL-types:
- CHAR
- VARCHAR (recommended)
- TINYTEXT
- TEXT
- MEDIUMTEXT
- LONGTEXT
- TINYINT
- SMALLINT
- MEDIUMINT
- INT
- BIGINT

---

Previous: [Data Structure Definition](3_datastructures.md)

Next: [Constraints](5_constraints.md)

[Table of Contents](0_manual.md)
