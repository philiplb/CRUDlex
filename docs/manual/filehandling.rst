File Handling
=============

By default, CRUDlex uses the filesystem to store uploaded files. The current working directory is taken as root here.
Usually, this is the path to your index.php. So this example stores the image files under <workingDirectory>/uploads

.. code-block:: yaml

    library:
        table: lib
        label: Library
        fields:
            image:
                type: file
                path: uploads

One big drawback here is that the application is not stateless anymore according to https://12factor.net/processes. But
luckily, the file handling is done via `Flysystem <http://flysystem.thephpleague.com//>`_ and the used
FilesystemInterface can be overridden easily by setting a service called "crud.filesystem".

In any way, overriding or not, the underlying FilesystemInterface is available via

.. tabs::

   .. group-tab:: Symfony 4

      Todo

   .. group-tab:: Silex 2

      .. code-block:: php

          $app['crud.filesystem']

^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
Overriding the Default Storage with Amazon S3 as Filesystem
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Here is an example of using Amazon S3 as storage.

First, get the AwsS3Adapter:

.. code-block:: bash

    composer require league/flysystem-aws-s3-v3

And then configure it and hand it over to CRUDlex:

.. tabs::

   .. group-tab:: Symfony 4

      Todo

   .. group-tab:: Silex 2

      .. code-block:: php

          $client = S3Client::factory([
              'credentials' => [
                  'key'    => $key,
                  'secret' => $secret
              ],
              'region' => $region,
              'version' => 'latest',
          ]);
          $adapter = new \League\Flysystem\AwsS3v3\AwsS3Adapter($client, $bucket);
          $filesystem = new \League\Flysystem\Filesystem($adapter);
          $dataFactory = new \CRUDlex\MySQLDataFactory($app['db']);
          $app->register(new \CRUDlex\ServiceProvider(), [
              'crud.file' => __DIR__ . '/../crud.yml',
              'crud.datafactory' => $dataFactory,
              'crud.filesystem' => $filesystem,
          ]);

^^^^^^^^^^^^^^^^^^^^^^^^^^^
Filesystem Storage Adapters
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Many more adapters are available for Flysystem, including (as of writing):

* Local
* Azure
* AWS S3 V2
* AWS S3 V3
* Copy.com
* Dropbox
* FTP
* GridFS
* Memory
* Null / Test
* Rackspace
* ReplicateAdapter
* SFTP
* WebDAV
* PHPCR
* ZipArchive

See the `Flysystem <http://flysystem.thephpleague.com//>`_ page for their usage.
