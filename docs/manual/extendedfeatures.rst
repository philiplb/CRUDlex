Extended Features
=================

While continuing with the library example, we add some more features as we go.

------------------
Field Descriptions
------------------

Sometimes, you want to guide the user a bit more and be more descriptive than
just a label on the details and editpage of an entity. To do so, you can set a
description per field. In this case, the author of a book is more informative:

.. code-block:: yaml

    library:
        label: Library
        table: library
        pageSize: 5
        fields:
            name:
                type: text
                label: Name
    book:
        label: Book
        table: book
        listFields: [id, created_at, updated_at, author, title]
        filter: [author, title]
        fields:
            author:
                type: text
                label: Author
                description: The Author of the Book
            title:
                type: text
                label: Title
            pages:
                type: integer
                label: Pages

----------------------------
Displayed Fields in the List
----------------------------

As an addition, we only want to show the author and title in the big list
view of the books. We can do it by using the listFields entry:

.. code-block:: yaml

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
        listFields: [id, created_at, updated_at, author, title]
        fields:
            author:
                type: text
                label: Author
            title:
                type: text
                label: Title
            pages:
                type: integer
                label: Pages

It is a simple list referencing the fields. Note the usage of the internal
fields "id", "created_at" and "update_at". "version" is not yet used and
every row where "deleted_at" is not null is marked as deleted, so this field
would make no sense to display.

----------
Pagination
----------

Per default, 25 entities are listed per page on the list view. You can change
this number by setting the **pageSize** parameter like this:

.. code-block:: yaml

    library:
        label: Library
        table: library
        pageSize: 5
        fields:
            name:
                type: text
                label: Name
    book:
        label: Book
        table: book
        listFields: [id, created_at, updated_at, author, title]
        fields:
            author:
                type: text
                label: Author
            title:
                type: text
                label: Title
            pages:
                type: integer
                label: Pages

Only strings and integers are boring, so in the next chapter, all possible
data types are presented.

-------
Filters
-------

Currently, the listview contains all entries on the pages. Often, it is desirable to filter it in order to search for specific entries. The fields to be allowed to filter on can be easily added with a filter array just like the listFields. This is how the books view could be filtered by author and title:

.. code-block:: yaml

    library:
        label: Library
        table: library
        pageSize: 5
        fields:
            name:
                type: text
                label: Name
    book:
        label: Book
        table: book
        listFields: [id, created_at, updated_at, author, title]
        filter: [author, title]
        fields:
            author:
                type: text
                label: Author
            title:
                type: text
                label: Title
            pages:
                type: integer
                label: Pages

----
I18n
----

Here are some features around the i18n support.

^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Set the Translations of Entity- and Field-Labels
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

You can translate the labels of the entities and their fields using some special
label keys: *label_(locale)* with *(locale)* being your desired locale. Example
for **de**:

.. code-block:: yaml

    book:
        label: Book
        label_de: Buch
        table: book
        listFields: [id, created_at, updated_at, author, title]
        filter: [author, title]
        fields:
            author:
                type: text
                label: Author
                label_de: Autor
            title:
                type: text
                label: Title
                label_de: Titel
            pages:
                type: integer
                label: Pages
                label_de: Seiten

^^^^^^^^^^^^^^^^^^^^^^^^^^
Switch off I18n Management
^^^^^^^^^^^^^^^^^^^^^^^^^^

Per default, CRUDlex manages i18n for you. But this might be not desired in
bigger projects, so you can disable it on registration like this:

.. code-block:: php

    $app->register(new CRUDlex\ServiceProvider(), array(
        'crud.file' => __DIR__ . '<yourCrud.yml>',
        'crud.datafactory' => $dataFactory,
        'crud.manageI18n' => false
    ));

--------------------------
Initial Sorting Parameters
--------------------------

Initially, when you visit the list page of an entity, the view is sorted ascending
by created_at. There might be cases, where you want to change that.

For this, two parameters can be set on entity level:

* **initialSortField:** Sets the field the data is sort by
* **initialSortAscending:** If set to true, the initial sort order is ascending,
  if set to false, the initial sort order is descending

Here is an example where the books are sorted by their author in an descending
order:

.. code-block:: yaml

    book:
        label: Book
        table: book
        filter: [author, title]
        initialSortField: author
        initialSortAscending: false
        fields:
            author:
                type: text
                label: Author
            title:
                type: text
                label: Title
            pages:
                type: integer
                label: Pages

---------------------------------------------------------------
Using UUIDs as Primary Key Instead of an Auto Incremented Value
---------------------------------------------------------------

CRUDMySQLData offers an option to use UUIDs as primary key instead of an auto
incremented value.

First, you have to create your id field as varchar(36):

.. code-block:: sql

    `id` varchar(36) NOT NULL


And then you have to activate it in the setup when creating the
CRUDDataFactoryInterface:

.. code-block:: php

    $dataFactory = new CRUDlex\MySQLDataFactory($app['db'], true);

------------------------------------------
Prefilled Form Fields on the Creation Page
------------------------------------------

You can set some initial values when you link the creation page from somewhere
else by handing in the appropriate GET parameter. Example for the author of a
book: .../book/create?author=MyAuthor
