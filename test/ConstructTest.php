<?php

namespace PHP\IO\Test;

use PhpFp\IO\IO;

class ConstructTest extends \PHPUnit_Framework_TestCase {
    public function testParameterCount() {
        $count = (new \ReflectionClass('PhpFp\IO\IO'))
            ->getConstructor()->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testConstruct() {
        $IO = new IO(function () {
            return 2;
        });

        $this->assertEquals(
            $IO->unsafePerform(),
            2,
            'Constructs an IO.'
        );
    }
}
