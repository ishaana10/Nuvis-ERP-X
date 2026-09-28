<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

if (! class_exists('TestBootstrapHelper')) {
    $helper = __DIR__ . '/../plugins/webkul/support/tests/Helpers/TestBootstrapHelper.php';
    if (file_exists($helper)) {
        require_once $helper;
    }
}

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        if (! $this->app) {
            $this->refreshApplication();
        }

        \TestBootstrapHelper::ensureERPInstalled();

        parent::setUp();
    }
}
