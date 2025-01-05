STE Template Engine
===================

The STE Template Engine (STE) is a template engine for the PHP programming
language.

## ⚠️ No longer maintained ⚠️

For now, I no longer maintain Ratatöskr / STE. I don't use it myself any more and to my knowledge nobody else does either. The time I'd need to invest to keep up with new PHP versions and other maintenance chores therefore don't seem worth it. I might revisit this in the future, but for now, I want to focus on other things.

Get it via composer / packagist
-------------------------------

[Composer](https://getcomposer.org) is a dependency manager and package manager for PHP. Using composer is the recommended way of installing STE.

Just put `r7r/ste` in your requirements (I suggest using the version `2.*`) and execute `composer install`. Then include composer's autoloader and you can simply use all stuff in the `\r7r\ste` namespace without further `require`s or `include`s. Awesome!


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
