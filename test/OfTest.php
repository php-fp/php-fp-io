<?php

namespace PhpFp\IO\Test;

use PhpFp\IO\IO;

class OfTest extends \PHPUnit_Framework_TestCase
{
    public function testParameterCount()
    {
        $count = (new \ReflectionMethod('PhpFp\IO\IO::of'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testApplicativeConstructor()
    {
        $this->assertEquals(
            IO::of(2)->unsafePerform(),
            2,
            'Constructs an applicative.'
        );
    }
}
