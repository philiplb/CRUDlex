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

## getTemplate(Application $app, $section, $action, $entity)

Determines the Twig template to use for the given parameters depending on
the existance of certain keys in the Application $app in this order:

* crud.$section.$action.$entity
* crud.$section.$action
* crud.$section

If nothing exists, this string is returned: "@crud/<action>.twig"

## getManageI18n()

Gets whether CRUDlex manages the i18n system.

## formatFloat($float)

Formats a float to not display in scientific notation.

---

Previous: [Various Other Features](10_miscfeatures.md)

[Table of Contents](0_manual.md)
