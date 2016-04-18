<?php

namespace PhpFp\IO;

/**
 * An OO-looking implementation of IO in PHP.
 */
class IO {
    /**
     * The "unsafe" IO action.
     * @var callable
     */
    private $unsafePerform;

    /**
     * Applicative constructor.
     * @param mixed $x The IO's inner value.
     * @return IO The value wrapped with IO.
     */
    public static function of($x) : IO {
        return new IO(function () use ($x) {
            return $x;
        });
    }

    /**
     * Construct a new IO with an action function.
     * @param callable $f An unsafe function.
     */
    public function __construct(callable $f) {
        $this->unsafePerform = $f;
    }

    /**
     * Application, derived with IO::chain.
     * @param IO $that The wrapped parameter.
     * @return IO The wrapped result
     */
    public function ap(IO $that) : IO {
        return $this->chain(function ($f) use ($that) {
            return $that->map($f);
        });
    }

    /**
     * PHP implementation of Haskell IO's >>=.
     * @param callable $f a -> IO b
     * @return IO The result of the function.
     */
    public function chain(callable $f) : IO {
        return new IO(function () use ($f) {
            return $f($this->unsafePerform())
                ->unsafePerform();
        });
    }

    /**
     * Functor map for IO. Transform the inner value.
     * @param callable $f The mapping function.
     * @return IO The outer structure is preserved.
     */
    public function map(callable $f) : IO {
        return $this->chain(function ($a) use ($f) {
            return IO::of($f($a));
        });
    }

    /**
     * Run the unsafe action.
     * @return mixed Whatever the action's result!
     */
    public function unsafePerform() {
        return call_user_func(
            $this->unsafePerform
        );
    }
}
