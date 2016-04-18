<?php

namespace PHP\IO\Test;

use PhpFp\IO\IO;

class ApTest extends \PHPUnit_Framework_TestCase {
    public function testParameterCount() {
        $count = (new \ReflectionMethod('PhpFp\IO\IO::ap'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testAp() {
        $add = function ($x) {
            return function ($y) use ($x) {
                return $x + $y;
            };
        };

        $a = IO::of(2);
        $b = IO::of(4);

        $this->assertEquals(
            IO::of($add)->ap($a)->ap($b)
                ->unsafePerform(),
            6,
            'Applies parameters.'
        );
    }
}
