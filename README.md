CRUDlex
=======

CRUDlex is an easy to use, well documented and tested CRUD generator for Silex. It is very useful to generate admin pages for example.

![List View of CRUDlex](docs/_static/01_List.png)

## Documentation

For the upcoming version 0.9.10, the manual and API docs got merged to an unified
documentation:

[Documentation](http://philiplb.github.io/CRUDlex/docs/html/0.9.10/)

The manual is a reference describing every feature of CRUDlex:

[Manual 0.9.9](https://github.com/philiplb/CRUDlex/blob/0.9.9/docs/0_manual.md)

The CRUDlex API itself is documented here:

[API 0.9.9](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.9/)

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

### Stable

```json
"require": {
    "philiplb/crudlex": "0.9.9"
}
```

### Bleeding Edge

```json
"require": {
    "philiplb/crudlex": "0.9.x-dev"
}
```

### Sample Project

For a complete example, checkout the sample project with the classic libraries
and books:

[CRUDlex Sample](https://github.com/philiplb/CRUDlexSample)

### Addons

Checkout the CRUDlex addons project for more features:

[CRUDlex Addons](https://github.com/philiplb/CRUDlexAddons)

### Roadmap

The project roadmap is organized via milestones:

[CRUDlex Milestones](https://github.com/philiplb/CRUDlex/milestones)

Beware that not each new feature will get its own ticket there. Some are
implemented on the fly when needed.

[![Total Downloads](https://poser.pugx.org/philiplb/crudlex/downloads.svg)](https://packagist.org/packages/philiplb/crudlex)
[![Latest Stable Version](https://poser.pugx.org/philiplb/crudlex/v/stable.svg)](https://packagist.org/packages/philiplb/crudlex)
[![Latest Unstable Version](https://poser.pugx.org/philiplb/crudlex/v/unstable.svg)](https://packagist.org/packages/philiplb/crudlex) [![License](https://poser.pugx.org/philiplb/crudlex/license.svg)](https://packagist.org/packages/philiplb/crudlex)

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
* [Latest Unstable](docs/0_manual.md)

### API Documentation

The CRUDlex API itself is documented here:

* [0.9.9](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.9/)
* [0.9.8](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.8/)
* [0.9.7](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.7/)
* [0.9.6](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.6/)
* [0.9.5](http://philiplb.github.io/CRUDlex/docs/html/api/0.9.5/)

## Build Status

[![Build Status](https://travis-ci.org/philiplb/CRUDlex.svg?branch=master)](https://travis-ci.org/philiplb/CRUDlex)
[![Coverage Status](https://coveralls.io/repos/philiplb/CRUDlex/badge.png?branch=master)](https://coveralls.io/r/philiplb/CRUDlex?branch=master)

## Code Quality

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/97dc69bd-12df-430e-ad5b-c9335ff401fa/mini.png)](https://insight.sensiolabs.com/projects/97dc69bd-12df-430e-ad5b-c9335ff401fa)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/philiplb/CRUDlex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/philiplb/CRUDlex/?branch=master)
