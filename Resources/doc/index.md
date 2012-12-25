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

``` bash
$ php composer.phar update sp/fixture-dumper-bundle
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

### Step 3: Dump fixtures from database

**TODO**
