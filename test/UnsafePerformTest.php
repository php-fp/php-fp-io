<?php

namespace PhpFp\IO\Test;

use PhpFp\IO\IO;

class UnsafePerformTest extends \PHPUnit_Framework_TestCase {
    public function testParameterCount() {
        $count = (new \ReflectionMethod('PhpFp\IO\IO::unsafePerform'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            0,
            'Takes no parameters.'
        );
    }

    public function testUnsafePerform() {
        $this->assertEquals(
            IO::of(2)->unsafePerform(),
            2,
            'Forks the monad.'
        );
    }
}
