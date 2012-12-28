Installation
============

Prerequisites
-------------

This bundle requires Symfony 2.1+

Installation
------------

1. Download SpFixtureDumperBundle using composer
2. Enable the Bundle
3. Dump fixtures from database

### Step 1: Download SpFixtureDumperBundle using composer

Add SpFixtureDumperBundle in your composer.json:

```json
{
    "require": {
        "sp/fixture-dumper-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

```bash
php composer.phar update sp/fixture-dumper-bundle
```

Composer will install the bundle to your project's `vendor/sp` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Sp\FixtureDumperBundle\SpFixtureDumperBundle(),
    );
}
```

### Step 3: Dump fixtures from doctrine

You can dump fixtures from existing entities via the command

```bash
php app/console sp:fixture-dumper:orm /path/to/fixtures
```

If you're using the ODM, use the ``sp:fixture-dumper:mongodb`` command instead:

```bash
php app/console sp:fixture-dumper:mongodb /path/to/fixtures
```
Both commands come with a few options:

* ``--format=yml`` - Use this option to manually specify the
  format for the dumped fixtures. There are currently three implemented
  * class (default) - Dump [class fixtures](http://symfony.com/doc/current/bundles/DoctrineFixturesBundle/index.html#writing-simple-fixtures)
  * yml - Dump yml fixtures which can be loaded with the [alice](https://github.com/nelmio/alice) library
  * array - Dump php array fixtures which can be loaded with the [alice](https://github.com/nelmio/alice) library

* ``--single-file`` - Use this flag to dump all fixtures into one file

* ``--em=manager_name`` - Manually specify the entity manager to use for
  loading the data.

**Note:** If using the ``sp:fixture-dumper:mongodb`` task, replace the ``--em=``
option with ``--dm=`` to manually specify the document manager.

A full example use might look like this:

```bash
php app/console sp:fixture-dumper:orm --format=yml --single-file --em=foo_manager /path/to/fixtures
```
