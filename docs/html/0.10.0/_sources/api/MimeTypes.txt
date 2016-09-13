------------------
CRUDlex\\MimeTypes
------------------

.. php:namespace: CRUDlex

.. php:class:: MimeTypes

    Class to get a mimetype from a file.

    .. php:attr:: mimeTypes

        protected

        Map from file extension to mimetype.
        THX to
        http://stackoverflow.com/questions/134833/how-do-i-find-the-mime-type-of-a-file-with-php

    .. php:method:: getMimeTypeByExtension($file)

        Gets the mime type by just looking at the extension.

        :type $file: string
        :param $file: the file to get the mimetype from
        :returns: string the mimetype

    .. php:method:: getMimeTypeByFileInfo($file)

        Gets the mime type by looking at the file info.

        :type $file: string
        :param $file: the file to get the mimetype from
        :returns: mixed|string the mimetype

    .. php:method:: getMimeType($file)

        Function to get the mimetype of a file.

        :type $file: string
        :param $file: the file to get the mimetype from
        :returns: string the mimetype
