Data Structure Definition
=========================

In the previous chapter, the CRUDServiceProvider got the path to a crud.yml
told. This is where CRUDlex gets it's information about what tables with what
fields exists in your database. We will build up a valid, small example as we
continue in this chapter.

The first items in the crud.yml are the entities. Each entity is describing a
single table with it's fields. Let's say we have two tables, libraries and
books. So we define two entities with the same name. Note that this name is
your choice, the table name of the database gets defined in a second.

```yml
library:
book:
```

---

Previous: [Setup](2_setup.md)

Next: [Data Types](4_datatypes.md)
