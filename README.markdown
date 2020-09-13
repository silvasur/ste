STE Template Engine
===================

The STE Template Engine (STE) is a template engine for the PHP programming
language.

Get it via composer / packagist
-------------------------------

[Composer](https://getcomposer.org) is a dependency manager and package manager for PHP. Using composer is the recommended way of installing STE.

Just put `kch42/ste` in your requirements (I suggest using the version `1.*`) and execute `composer install`. Then include composer's autoloader and you can simply use all stuff in the `\kch42\ste` namespace without further `require`s or `include`s. Awesome!


Requirements
------------

PHP >= 7.3

Why should you use it?
----------------------

* It's syntax (inspired by Textpattern's template syntax) is very easy and
  similar to the syntax of (X)HTML. So it should be easy for designers to learn
  this system.
* It has a simple, yet powerful plugin interface. You can easily define your
  own template functions / tags. It is even possible to write them in the
  template language itself, which makes it kind of a programming language...
* It can compile templates into PHP.
* You can use anonymous functions to define custom tags.

Documentation
-------------

The `docu` directory contains the documentation of the template language.
If you need the documentation of the php code / the API, you can create it with the `mkdocu.sh` script.
This is done using [phpDocumentor](https://phpdoc.org/). It will be installed as a dev-depencency by composer.

There is also a mirror of the documentation [here](http://r7r.silvasur.net/ste_docu/).
