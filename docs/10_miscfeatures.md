Various Other Features
======================

This chapter is a collection of features which don't fit in other chapters.

## Using UUIDs as primary key instead of an auto incremented value

CRUDMySQLData offers an option to use UUIDs as primary key instead of an auto
incremented value.

First, you have to create your id field as varchar(36):

```sql
`id` varchar(36) NOT NULL
```

And then you have to activate it in the setup when creating the
CRUDDataFactoryInterface:

```php
$dataFactory = new CRUDlex\CRUDMySQLDataFactory($app['db'], true);
```

## Prefilled Form Fields on the Creation Page

You can set some initial values when you link the creation page from somewhere
else by handing in the appropriate GET parameter. Example for the author of a
book: .../book/create?author=MyAuthor

---

Previous: [Events](9_events.md)

Next: [The CRUDServiceProvider](11_crudserviceprovider.md)

[Table of Contents](0_manual.md)
