Overriding Templates
====================

If you need to adjust the templates of single pages, you can't achieve that with
just overriding the layout. Example: You want to add a button to the action
column of the list page of the entity "book". For that, you have to override the
Twig block "content". But with just the layout, you simply can't. CRUDlex
uses for this page a template "@crud/list.twig" which extends the so far
overridable layout.

In this case, you can override the template of a single page:

.. code-block:: php

    $app['crud.template.list.book'] = 'bookList.twig';

Note the changed part "template" of the key!

The rest of the internal template selection works like the layouts. You don't
need to specify the entity if you want to adjust all list pages for example.

Note that you find all used templates in "src/views/", they give a good starting
point to tweak.

--------------------------------
Templates Included by the Layout
--------------------------------

The layout.twig includes those templates:

* header: The included header (@crud/header.twig)
* footer: The included footer (@crud/footer.twig)

-------------------------------------
Templates for the Single Action Pages
-------------------------------------

This are the "root"-templates directly included in the layout.twig:

* form: The create and edit page (@crud/form.twig)
* list: The list page (@crud/list.twig)
* show: The show page (@crud/show.twig)

--------------------
Form Field Templates
--------------------

And this are the form field templates for each type:

* booleanField: The form field for the type bool (@crud/boolField.twig)
* dateField: The form field for the type date (@crud/dateField.twig)
* datetimeField: The form field for the type datetime (@crud/datetimeField.twig)
* fileField: The form field for the type file (@crud/fileField.twig)
* fixedField: The form field for the type fixed (@crud/fixedField.twig)
* floatField: The form field for the type float (@crud/floatField.twig)
* integerField: The form field for the type int (@crud/intField.twig)
* multilineField: The form field for the type multiline (@crud/multilineField.twig)
* referenceField: The form field for the type reference (@crud/referenceField.twig)
* setField: The form field for the type set (@crud/setField.twig)
* textField: The form field for the type text (@crud/textField.twig)
* urlField: The form field for the type url (@crud/urlField.twig)

--------------------
Additional Templates
--------------------

In addition, these templates exists:

* fieldLabel: A label of a form field (@crud/fieldLabel.twig)
* renderField: The rendering of a field value on the list or the show page (@crud/renderField.twig)
