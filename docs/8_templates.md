Overriding Templates
====================

If you need to adjust the templates of single pages, you can't achieve that with
just overriding the layout. Example: You want to add a button to the action
column of the list page of the entity "book". For that, you have to override the
Twig block "content". But with just the layout, you simply can't. CRUDlex
uses for this page a template "@crud/list.twig" which extends the so far
overridable layout.

In this case, you can override the template of a single page:
```php
$app['crud.template.list.book'] = 'bookList.twig';
```

Note the changed part "template" of the key!

The rest of the internal template selection works like the layouts. You don't
need to specify the entity if you want to adjust all list pages for example.

This templates can be overridden so far:

* form: The create and edit page (@crud/form.twig)
* list: The list page (@crud/list.twig)
* show: The list page (@crud/list.twig)

---

Previous: [Constraints](7_layouts.md)

Next: [Various Other Features](9_miscfeatures.md)

[Table of Contents](0_manual.md)
