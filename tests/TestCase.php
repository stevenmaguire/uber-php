<?php

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        @session_start();
        parent::setUp();
    }
}
