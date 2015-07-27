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

## basename($value)

Calls PHPs "[basename](http://php.net/manual/en/function.basename.php)" and
returns it's result.

---

Previous: [Various Other Features](8_miscfeatures.md)

[Table of Contents](0_manual.md)
