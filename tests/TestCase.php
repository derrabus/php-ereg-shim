<?php

namespace Rabus\EregShim;

use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function expectEmptyPatternWarning($functionName)
    {
        $message = sprintf('%s(): REG_EMPTY', $functionName);

        if (method_exists($this, 'expectWarning')) {
            $this->expectWarning();
            $this->expectWarningMessage($message);
        } elseif (method_exists($this, 'expectException')) {
            $this->expectException('PHPUnit\Framework\Error\Warning');
            $this->expectExceptionMessage($message);
        } else {
            $this->setExpectedException('PHPUnit\Framework\Error\Warning', $message);
        }
    }
}

if (!class_exists('PHPUnit\Framework\Warning')) {
    class_alias('PHPUnit_Framework_Error_Warning', 'PHPUnit\Framework\Error\Warning');
}
