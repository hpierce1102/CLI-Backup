<?php

require_once __DIR__ . '/../vendor/autoload.php';

// https://github.com/sebastianbergmann/phpunit-documentation/issues/77#issuecomment-76672259
//PHPUnit no longer provides a way to mock static methods. So we do it ourselves by changing to a
//new implementation for testing.

//This is needed because this class attempts to get user input. We can't get that during an automated
//test.
class_alias("Backup\\Tests\\Mocks\\Util\\Readline", "Backup\\Util\\Readline");