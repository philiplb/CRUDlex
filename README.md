CRUDlex
=======

CRUDlex is an easy to use, well documented and tested CRUD generator for Silex. It is very useful to generate admin pages for example.

[![Support via Gratipay](https://cdn.rawgit.com/gratipay/gratipay-badge/2.3.0/dist/gratipay.png)](https://gratipay.com/CRUDlex)

![List View of CRUDlex](docs/_static/01_List.png)

## Features

- Generates a web application for managing MySQL stored data
    - **C** reate entries
    - **R** ead entries in a list and the details of single ones
    - **U** pdate entries
    - **D** elete entries
- The list of entries is paginated, sortable and filterable
- The entries can be relational, one-to-many and many-to-many is supported
- Managing files is supported, either stored in the filesystem or at AWS S3 as addon
- The UI is available in multiple languages
- File storage is abstract, implementing other systems than the filesystem and AWS S3 is easy
- Data storage is abstract, implementing other backends than MySQL is easy

[![Total Downloads](https://poser.pugx.org/philiplb/crudlex/downloads.svg)](https://packagist.org/packages/philiplb/crudlex)
[![Latest Stable Version](https://poser.pugx.org/philiplb/crudlex/v/stable.svg)](https://packagist.org/packages/philiplb/crudlex)
[![Latest Unstable Version](https://poser.pugx.org/philiplb/crudlex/v/unstable.svg)](https://packagist.org/packages/philiplb/crudlex) [![License](https://poser.pugx.org/philiplb/crudlex/license.svg)](https://packagist.org/packages/philiplb/crudlex)

## Documentation

- [Documentation 0.13.0](http://philiplb.github.io/CRUDlex/docs/html/0.13.0/) (upcoming)
- [Documentation 0.12.0](http://philiplb.github.io/CRUDlex/docs/html/0.12.0/)
- [Documentation 0.11.0](http://philiplb.github.io/CRUDlex/docs/html/0.11.0/)
- [Documentation 0.10.0](http://philiplb.github.io/CRUDlex/docs/html/0.10.0/)
- [Documentation 0.9.10](http://philiplb.github.io/CRUDlex/docs/html/0.9.10/)

How to build the documentation:

```bash
# Install dependencies
pip install Sphinx
pip install tk.phpautodoc
# Generate:
cd docs
make html
```

## Package

CRUDlex uses [SemVer](http://semver.org/) for versioning. Currently, the API changes quickly due to be < 1.0.0, so take
care about notes in the changelog when upgrading.

### Stable

```json
"require": {
    "philiplb/crudlex": "0.12.0"
}
```

### Bleeding Edge

```json
"require": {
    "philiplb/crudlex": "0.13.x-dev"
}
```

### Sample Project

For a complete example, checkout the sample project with the classic libraries
and books:

[CRUDlex Sample](https://github.com/philiplb/CRUDlexSample)

### Addons

There are several surrounding projects around CRUDlex:

* [CRUDlexAmazonS3FileProcessor](https://github.com/philiplb/CRUDlexAmazonS3FileProcessor):
  Handling the file uploads via Amazon S3
* [CRUDlexUser](https://github.com/philiplb/CRUDlexUser):
  A library offering an user provider for symfony/security

### Roadmap

The project roadmap is organized via milestones:

[CRUDlex Milestones](https://github.com/philiplb/CRUDlex/milestones)

Beware that not each new feature will get its own ticket there. Some are
implemented on the fly when needed.

Each milestone is loosely organized as project in the columns "Backlog"
(Todo), "Staging" (Being worked on (next)) and "Done" (done):

[CRUDlex Milestone Projects](https://github.com/philiplb/CRUDlex/projects)

## Older Versions Documentation

### Manual

The manual is a reference describing every feature of CRUDlex:

* [0.9.9](https://github.com/philiplb/CRUDlex/blob/0.9.9/docs/0_manual.md)
* [0.9.8](https://github.com/philiplb/CRUDlex/blob/0.9.8/docs/0_manual.md)
* [0.9.7](https://github.com/philiplb/CRUDlex/blob/0.9.7/docs/0_manual.md)
* [0.9.6](https://github.com/philiplb/CRUDlex/blob/0.9.6/docs/0_manual.md)
* [0.9.5](https://github.com/philiplb/CRUDlex/blob/0.9.5/docs/0_manual.md)
* [0.9.4](https://github.com/philiplb/CRUDlex/blob/0.9.4/docs/0_manual.md)
* [0.9.3](https://github.com/philiplb/CRUDlex/blob/0.9.3/docs/0_manual.md)

### API Documentation

The CRUDlex API itself is documented here:

* [0.9.9](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.9/)
* [0.9.8](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.8/)
* [0.9.7](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.7/)
* [0.9.6](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.6/)
* [0.9.5](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.5/)

## Build Status

[![Build Status](https://travis-ci.org/philiplb/CRUDlex.svg?branch=master)](https://travis-ci.org/philiplb/CRUDlex)
[![Code Coverage](https://scrutinizer-ci.com/g/philiplb/CRUDlex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/philiplb/CRUDlex/?branch=master)

## Code Quality

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/97dc69bd-12df-430e-ad5b-c9335ff401fa/mini.png)](https://insight.sensiolabs.com/projects/97dc69bd-12df-430e-ad5b-c9335ff401fa)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/philiplb/CRUDlex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/philiplb/CRUDlex/?branch=master)
