# README

[![Build Status](https://secure.travis-ci.org/fridge-project/dbal.png?branch=master)](http://travis-ci.org/fridge-project/dbal)

## What is the Fridge DBAL?

It's a modern PHP 5.3+ Database Abstraction Layer created by Eric GELOEN. It is written around PDO implementation
with speed & adaptability in mind. It offers a lot of features like lazy connection, nested transactions,
advanced type support, debugging, schema introspection/manipulation & more.

## Requirements

The Fridge DBAL is only supported on PHP 5.3.3 and up.

It has 2 dependencies:

 - [EventDispatcher](https://github.com/symfony/EventDispatcher) (Symfony2)
 - [Monolog](https://github.com/Seldaek/monolog)

## Overview

[![Class Diagram](https://www.lucidchart.com/publicSegments/view/5039194d-f12c-4c67-8462-0c0c0ad3924f/image.jpeg)](https://www.lucidchart.com/publicSegments/view/5039194d-f12c-4c67-8462-0c0c0ad3924f/image.jpeg)

## Documentation

``` php
<?php

use Fridge\DBAL\Factory;

$connection = Factory::getConnection(array(
    'driver'   => 'pdo_mysql',
    'username' => 'root',
    'password' => 'pass',
    'dbname'   => 'dbname',
));

$query = 'SELECT firstname, lastname FROM users WHERE created_at >= ?';
$users = $connection->executeQuery($query, array(new \DateTime('-2 months')), array('datetime'));

foreach ($users as $user) {
    echo $user['firstname'].' '.$user['lastname'];
}
```

The full documentation is available at [fridge-project.org/dbal](http://fridge-project.org/dbal).

## Installation

The easy way to install the Fridge DBAL is [Composer](http://getcomposer.org/), an awesome PHP dependency manager.
If you prefer install it manually, the library follows the [PSR-0 standad](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md).

If you want to learn more, you can read the [installation](http://fridge-project.org/dbal/installation) section of
the documentation.

## Testing

The library is fully unit tested by [PHPUnit](http://www.phpunit.de/) with a code coverage close to **100%**.

If you want to learn more, you can read the [testing](http://fridge-project.org/dbal/dbal/testing) section of
the documentation.

## Contribution

We love contributors! Fridge is an open source project. If you'd like to contribute, please read the
[contributing](http://fridge-project.org/contributing) section of the documentation.
