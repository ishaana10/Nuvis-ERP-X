<?php

namespace Webkul\Payroll\Tests;

use Tests\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (class_exists('TestBootstrapHelper')) {
            \TestBootstrapHelper::ensurePluginInstalled('employees');
            \TestBootstrapHelper::ensurePluginInstalled('accounts');
            \TestBootstrapHelper::ensurePluginInstalled('payroll');
        }
    }
}
