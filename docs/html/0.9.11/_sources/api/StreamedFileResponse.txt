-----------------------------
CRUDlex\\StreamedFileResponse
-----------------------------

.. php:namespace: CRUDlex

.. php:class:: StreamedFileResponse

    Small utility class to generate functions for streamed responses returning
    a file.

    .. php:method:: getStreamedFileFunction($file)

        Generates a lambda which is streaming the given file to standard output.

        :type $file: string
        :param $file: the filename to stream
