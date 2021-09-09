[![MIT license](http://img.shields.io/badge/license-MIT-brightgreen.svg)](http://opensource.org/licenses/MIT)
[![GitHub version](https://badge.fury.io/gh/arvenil%2Fninja-mutex.svg)](http://badge.fury.io/gh/arvenil%2Fninja-mutex)
[![Build Status](https://travis-ci.org/arvenil/ninja-mutex.svg?branch=master)](https://travis-ci.org/arvenil/ninja-mutex)
[![HHVM Status](http://hhvm.h4cc.de/badge/arvenil/ninja-mutex.svg)](http://hhvm.h4cc.de/package/arvenil/ninja-mutex)
[![Code Climate](https://codeclimate.com/github/arvenil/ninja-mutex/badges/gpa.svg)](https://codeclimate.com/github/arvenil/ninja-mutex)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/arvenil/ninja-mutex/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/arvenil/ninja-mutex/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/arvenil/ninja-mutex/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/arvenil/ninja-mutex/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/15c5c748-f8d8-4b56-b536-a29a151aac6c/mini.png)](https://insight.sensiolabs.com/projects/15c5c748-f8d8-4b56-b536-a29a151aac6c)
[![Dependency Status](https://gemnasium.com/arvenil/ninja-mutex.svg)](https://gemnasium.com/arvenil/ninja-mutex)
[![Total Downloads](https://img.shields.io/packagist/dt/arvenil/ninja-mutex.svg)](https://packagist.org/packages/arvenil/ninja-mutex)

## About

ninja-mutex is a simple to use mutex implementation for php. It supports different adapters (flock, memcache, mysql, redis, ...) so you can setup it as you wish. All adapters (if set up properly) can be used in multi server environment - in other words lock is shared between web servers.

## Usage

### Mutex

First you need to choose adapter and setup it properly. For example if you choose flock implementation first you need to setup NFS filesystem and mount it on web servers. In this example we will choose memcache adapter:

```php
<?php
require 'vendor/autoload.php';

use NinjaMutex\Lock\MemcacheLock;
use NinjaMutex\Mutex;

$memcache = new Memcache();
$memcache->connect('127.0.0.1', 11211);
$lock = new MemcacheLock($memcache);
$mutex = new Mutex('very-critical-stuff', $lock);
if ($mutex->acquireLock(1000)) {
    // Do some very critical stuff

    // and release lock after you finish
    $mutex->releaseLock();
} else {
    throw new Exception('Unable to gain lock!');
}
```

### Mutex Fabric

If you want to use multiple mutexes in your project then MutexFabric is the right solution. You setup lock implementor once and you can use as many mutexes as you want!

```php
<?php
require 'vendor/autoload.php';

use NinjaMutex\Lock\MemcacheLock;
use NinjaMutex\MutexFabric;

$memcache = new Memcache();
$memcache->connect('127.0.0.1', 11211);
$lock = new MemcacheLock($memcache);
$mutexFabric = new MutexFabric('memcache', $lock);
if ($mutexFabric->get('very-critical-stuff')->acquireLock(1000)) {
    // Do some very critical stuff

    // and release lock after you finish
    $mutexFabric->get('very-critical-stuff')->releaseLock();
} else {
    throw new Exception('Unable to gain lock for very critical stuff!');
}

if ($mutexFabric->get('also-very-critical-stuff')->acquireLock(0)) {
    // Do some also very critical stuff

    // and release lock after you finish
    $mutexFabric->get('also-very-critical-stuff')->releaseLock();
} else {
    throw new Exception('Unable to gain lock for also very critical stuff!');
}
```

## Installation

### Composer

Download composer:

    wget -nc http://getcomposer.org/composer.phar

and add dependency to your project:

    php composer.phar require arvenil/ninja-mutex:*

## Running tests

Tests require vfsStream to work. To install it, simply run in project dir:

    wget -nc http://getcomposer.org/composer.phar && php composer.phar install --dev

To run tests type in console:

    phpunit

## Something doesn't work

Feel free to fork project, fix bugs and finally request for pull
