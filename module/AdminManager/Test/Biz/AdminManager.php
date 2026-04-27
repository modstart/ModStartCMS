<?php
use ModStart\Test\TestCase;

TestCase::assertTrue(class_exists('Module\AdminManager\Util\ModuleUtil'), 'AdminManager Biz: 主工具类可加载');
TestCase::assertTrue(true, 'AdminManager Biz: 完成');
