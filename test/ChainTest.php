<?php

namespace PhpFp\IO\Test;

use PhpFp\IO\IO;

class ChainTest extends \PHPUnit_Framework_TestCase {
    public function testParameterCount() {
        $count = (new \ReflectionMethod('PhpFp\IO\IO::chain'))
            ->getNumberOfParameters();

        $this->assertEquals(
            $count,
            1,
            'Takes one parameter.'
        );
    }

    public function testChain() {
        $getLine = new IO(function () {
            return 'Hello, world!';
        });

        $putStrLn = function ($str) {
            return new IO(function () use ($str) {
                return $str;
            });
        };

        return $this->assertEquals(
            $getLine
                ->chain($putStrLn)
                ->unsafePerform(),
            'Hello, world!',
            'Chains correctly.'
        );
    }
}
