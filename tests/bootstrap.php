<?php

require_once __DIR__ . '/../vendor/autoload.php';

// https://github.com/sebastianbergmann/phpunit-documentation/issues/77#issuecomment-76672259
//PHPUnit no longer provides a way to mock static methods. So we do it ourselves by changing to a
//new implementation for testing.

//This is needed because this class attempts to get user input. We can't get that during an automated
//test.
class_alias("Backup\\Tests\\Mocks\\Util\\Readline", "Backup\\Util\\Readline");

//http://php.net/manual/en/class.errorexception.php#errorexception.examples
function exception_error_handler($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        // This error code is not included in error_reporting
        return;
    }
    throw new ErrorException($message, 0, $severity, $file, $line);
}
set_error_handler("exception_error_handler");
