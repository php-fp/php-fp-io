<?php

namespace PhpFp\IO\Test;

use PhpFp\IO\IO;

class MapTest extends \PHPUnit_Framework_TestCase {
    public function testParameterCount() {
        $count = (new \ReflectionMethod('PhpFp\IO\IO::map'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testMap() {
        $mapper = function ($x) {
            return $x + 2;
        };

        $this->assertEquals(
            IO::of(2)
                ->map($mapper)
                ->unsafePerform(),
            4,
            'Maps the inner value.'
        );
    }
}
