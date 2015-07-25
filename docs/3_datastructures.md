Data Structure Definition
=========================

In the previous chapter, the CRUDServiceProvider got the path to a crud.yml
told. This is where CRUDlex gets it's information about what tables with what
fields exists in your database. We will build up a valid, small example as we
continue in this chapter.

## Entities

The first items in the crud.yml are the entities. Each entity is describing a
single table with it's fields. Let's say we have two tables, libraries and
books. So we define two entities with the same name. Note that this name is
your choice, the table name of the database gets defined in a second.

```yml
library:
book:
```

In this case, the entities would be available under this URLs (assuming you
mounted the CRUDController under "/crud"):

http://.../crud/library

http://.../crud/book

Now we declare the labels and the tables. The label is used for displaying
links in the navigation and in some messages:

```yml
library:
    label: Library
    table: library
book:
    label: Book
    table: book
```

## Fields

So far, so good. In our minimal example, a library has a name and a Book has
an author, a title and the amount of pages as fields.

```yml
library:
    label: Library
    table: library
    fields:
        name:
            type: text
            label: Name
book:
    label: Book
    table: book
    fields:
        author:
            type: text
            label: Author
        title:
            type: text
            label: Title
        pages:
            type: int
            label: Title
```

Note that the yml keys "name", "author" and "title" directly name the database
column names. Each one has a type and a label here. The type defines the
database type and the label is used in various places to display the field. In
this example, only simple string types are used and an integer for the book
pages.

Beside this fields, the CRUDlex MySQL implementation assumes that you have some
more fields per table:

- id int(11) NOT NULL AUTO_INCREMENT: the id of each row
- created_at datetime NOT NULL: a timestamp when the row was created
- updated_at datetime NOT NULL: a timestamp when the row was the last time
updated
- deleted_at datetime DEFAULT NULL: defines when this entry was deleted.
CRUDlex uses a soft delete mechanism hiding all rows where this is not null
- version int(11) NOT NULL: (will be) used for optimistic locking

See the CRUDlexSample.sql in the sample project for the exact table creation.

---

Previous: [Setup](2_setup.md)

Next: [Extended Features](4_extendedfeatures.md)

[Table of Contents](0_manual.md)
