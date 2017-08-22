CRUD YAML Reference
===================

Here is an overview of all possible CRUD YAML instructions. Be it optional or
required, along with the default value or behaviour.


.. code-block:: yaml

    # an entity named "library" starts here, used in URLs and in some API functions
    library:
      # the label used in the UI like the navigation and flashes
      label: Library
      # the table of this entity in the data source
      table: library
      # if this property is given, then the named, referencing children of this
      # entity are listed on the details page
      childrenLabelFields:
        # list the book children of a specific library entity, represented by
        # their title field
        book: title
      # activates the filter feature for all given fields
      filter: [name, isOpenOnSundays]
      # if this is set to true, the deletion of a parent with children referencing
      # it, causes no error but the deletion of the children, defaults to false
      deleteCascade: true
      # the size of one page on the list view, defaults to 25
      pageSize: 10
      # the initial sort field on the list view, defaults to created_at
      initialSortField: name
      # whether the list view is initially ascending (true) or descending (false)
      # sorted, defaults to true
      initialSortAscending: false
      # This will create a top level item in the navigation bar called "learning"
      # and put the library entity in a dropdown below the option. Defaults to
      # 'main' (The entity will be in the top level of the navigation bar)
      navBarGroup: learning
      # the displayed fields on the list view, defaults to id, created_at, updated_at,
      # and all given ones
      listFields: [id, created_at, updated_at, name, type, opening, isOpenOnSundays]
      # Whether the optimistic locking should be enabled. If this is false, the
      # version field is unused and so not needed. Defaults to true.
      optimisticLocking: true
      # the fields of this entity
      fields:
        # the name of the field, must be identical to the column of the data source
        name:
          # the type of the field, in this case a single line text
          type: text
          # how the field is named in the UI, defaults to the field key if not given
          label: Name
          # whether this field is a required one when entering data in the UI
          required: true
          # whether the value of this field should occur only once, soft deleted rows
          # not counting
          unique: true
        type:
          # this field can have one value of a given set
          type: set
          # the allowed and presented values
          items: [small,medium,large]
        opening:
          # this field represents a date and a time
          type: datetime
        isOpenOnSundays:
          # this field can either be true or false
          type: boolean
        planet:
          # the user can't change the value, a predefined one is always taken,
          # useful for default values which might change to be editable in the
          # future
          type: fixed
          # the fixed value to take
          value: Earth
        # as this is one side of a many-to-many relationship, this field key
        # must be the name of the cross table
        libraryBook:
          # this field is one end of a many-to-many relationship
          type: many
          many:
            # the related entity
            entity: book
            # what field to take from the related entity to visualize it
            nameField: title
            # the column of the cross table pointing to this entity
            thisField: library
            # the column of the cross table pointing to the related entity
            thatField: book
            # if set to true, the reference will be displayed without its id
            hideId : false
        homepage:
          # this field is a text represented as URL in the UI
          type: url
    book:
      # the label which should be displayed if the "de" is selected as language
      label_de: Bücher
      table: book
      fields:
        title:
          type: text
          # the label which should be displayed if "de" is selected as language
          label_de: Titel
        author:
          type: text
          label: Author
          # the longer description explaining the field to the user on the details
          # and form page
          description: The Author of the Book
        abstract:
          # a multi line text field
          type: multiline
        frontpage:
          # a visual WYSIWYM editor
          type: wysiwym
        pages:
          # an integer field
          type: integer
        release:
          # a date field
          type: date
        library:
          # this field references another entity and so builds up an one-to-many relationship
          type: reference
          # the reference data
          reference:
            # the referenced entity
            entity: library
            # what field to take from the related entity to visualize it, defaults to the id
            # if not given
            nameField: name
            # if set to true, the reference will be displayed without its id
            hideId : false
        price:
          # this field is a floating point number
          type: float
          # the precision of a single step in the UI
          floatStep: 0.1
        cover:
            # this fields points to an uploaded file
            type: file
            # the sub path of the file processor to store the uploads of this field in
            path: uploads
