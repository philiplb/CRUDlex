The CRUDServiceProvider
=======================

The CRUDServiceProvider registers itself under $app['crud'] and offers some
methods you might find useful on other places.

## getData($name)

Returns the CRUDData-instance of the given entity, "book" for the book-entity
for example.

## getEntities()

Returns a list of available entities.

## formatDate($value)

Converts the given date string from the format "Y-m-d H:i:s" or "Y-m-d" to the
format "Y-m-d". If $value is empty, an empty string is returned.

## formatDateTime($value)

Converts the given date time string from the format "Y-m-d H:i:s" or "Y-m-d H:i"
to the format "Y-m-d". If $value is empty, an empty string is returned.

## translate($key, array $placeholders = array())

Picks up the string of the given $key from the strings.yml and returns the
value. Replaces placeholder from "{0}" to "{n}" with the values given via the
array $placeholders. Example from the strings.yml:

```yml
create.success: '{0} created with id {1}'
```

This call:

```php
$app['crud']->translate('create.success', array('Book', 123));
```

Returns "Book created with id 123".

## basename($value)

Calls PHPs "[basename](http://php.net/manual/en/function.basename.php)" and
returns it's result.

---

Previous: [Various Other Features](7_miscfeatures.md)

[Table of Contents](0_manual.md)
