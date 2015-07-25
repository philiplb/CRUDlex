Constraints
===========

Constraints are defined per field and control its content. Currently, two
types of constraints are supported:

- Whether a field is mandatory/required and the user needs to fill it
- Whether a field should have unique content and no two rows can exist in the
database with the same value of this field

In our library/book example, we define a name of the library to be unique and
mandatory and the title of a book should be required:

```yml
library:
    table: lib
    label: Library
    fields:
        name:
            type: text
            required: true
            unique: true
book:
    table: book
    label: Book
    fields:
        title:
            type: text
            required: true
        author:
            type: text
        library:
            type: reference
            reference:
              table: lib
              nameField: name
              entity: library
```

So it's just as easy as adding either "required: true" or "unique: true" to the
field definition. Note that both constraints are used for the field "name" in
the entity "library".

---

Previous: [Data Types](4_datatypes.md)

Next: [Overriding Layouts](6_layouts.md)

[Table of Contents](0_manual.md)
