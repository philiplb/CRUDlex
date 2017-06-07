Optimistic Locking
==================

By default, CRUDlex uses optimistic locking via the version field. This means that the version field is incremented on
each update of an entity. The current version is sent on submitting the edit form and then compared to the existing one
in the database. If those two values are not equal, someone else was faster in editing the entity. Now, the update is
rejected as the new values in the database sent by the other person would be lost.

This behaviour can be adjusted:


.. code-block:: yaml

    library:
        label: Library
        table: library
        optimisticLocking: true
        fields:
            name:
                type: text
                label: Name

The default value is true and so optimistic locking is activated. If it is set to false, it is switched off and the
version field is not used. Neither incremented nor checked on form submission. It then doesn't need to exist in the
database table.