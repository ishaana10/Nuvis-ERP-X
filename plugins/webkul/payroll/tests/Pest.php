<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Webkul\Payroll\Tests\TestCase;

uses(TestCase::class, DatabaseTransactions::class)->in(__DIR__);
