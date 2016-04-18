# The IO Monad for PHP. [![Build Status](https://travis-ci.org/php-fp/php-fp-io.svg?branch=master)](https://travis-ci.org/php-fp/php-fp-io)

_The implementation for this looks more object-oriented than I'd have liked, but it satisfies the monad laws nevertheless._

## Intro

The IO monad is a concept that exists in functional programming, and was made 'famous' by Haskell. It is a way of purifying IO actions: in essence, this monad provides a method for building a **description** of an impure program, without actually running it.

Typically, when writing functional programs, you would have a main entry point file (e.g. `src/Main.php`) that produces an IO instance. Something like this:

```php
<?php

use PhpFp\IO\IO;

// There are no true first-class functions in PHP.
return IO::of('Hello, world!')->map('var_dump');
```

This program doesn't do any IO, or have any side effects - it simply constructs this IO instance. Then, another file (e.g. `public/index.php`) would look like this:

```php
<?php

(include '../src/Main.php')
    ->unsafePerform();
```

Thus, the program can remain pure (and totally unit testable, etc), and your true entry point only has to run the program described by your main file.

The monad works like a chain of dominoes: until `unsafePerform` is called, this monad essentially just builds up a pipeline of deferred functions. Once it _has_ been called, all the actions happen in the specified sequence.

## API

In the following type signatures, constructors and static functions are written as one would see in pure languages such as Haskell. The others contain a pipe, where the type before the pipe represents the type of the current IO instance, and the type after the pipe represents the function.

### `of :: a -> IO a`

This is the applicative constructor for the IO monad. The value supplied to this function will be the result of running `unsafePerform` - the appropriate wrapping is done for you, which isn't the case for the standard constructor.

```php
<?php

use PhpFp\IO\IO;

assert(IO::of(2)->unsafePerform() == 2);
```

### `__construct :: (-> a) -> IO a`

A bit of an odd type signature, but probably the best match that I could come up with in PHP. The parameter to the constructor must be a zero-parameter function that returns a value. It is this value that is the "inner value" for the IO instance. See the two examples:

```php
<?php

use PhpFp\IO\IO;

// With the applicative constructor.
$x = IO::of(2);

// With the OOP constructor.
$y = new IO(function () { return 2; });

// The result of both is the same - the former is neater.
assert($x->unsafePerform() == $y->unsafePerform());
```

The constructor may seem useless, but it has use when producing impure IO actions: see the `chain` example below, where the constructor is used to defer the execution of `fgets` until the impure action is performed.

### `chain :: IO a | (a -> IO b) -> IO b`

This is the equivalent of Haskell's >>= (bind) operation for this IO implementation. Sometimes referred to as a 'flat map', this will transform the inner value into an IO type, and then "flatten" the two IO wrappers into one. Compare this with the `map` type: what would we get if we mapped over an IO with a chaining function?

```php
<?php

use PhpFp\IO\IO;

//+ $getLine :: IO String
    $getLine = new IO(function () {
        return fgets(STDIN);
    });

//+ $putStrLn :: String -> IO ()
    $putStrLn = function ($str) {
        return new IO(function () use ($str) {
            echo $str; // Newline picked up by fgets
        });
    };

// This function reads a line from STDIN, and prints it to STDOUT.
return $getLine->chain($putStrLn); // ->unsafePerform() to run.
```

### `map :: IO a | (a -> b) -> IO b`

This is the usual functor mapping, though the implementation is derived from `chain`. All this does is transform the inner value of the monad according to some function. Note that the function won't actually run until `unsafePerform` is called.

```php
<?php

use PhpFp\IO\IO;

$mapper = function ($x) {
    return $x + 2;
};

assert(IO::of(2)->map($mapper)->unsafePerform() == 4);
```

### `ap :: IO (a -> b) | IO a -> IO b`

Apply an IO-wrapped argument to this IO-wrapped function. The implementation of this is also derived from `chain` - it's a pretty big deal, that function.

```php
<?php

$add = function ($x) {
    return function ($y) use ($x) {
        return $x + $y;
    };
};

$a = IO::of(2);
$b = IO::of(4);

// Look up the `liftA2` function in a functional library.
assert (IO::of($add)->ap($a)->ap($b)->unsafePerform() == 6);
```

### `unsafePerform :: IO a | a`

This function will run the IO action and return the result. As mentioned in the intro, it is not in the spirit of functional programming for you to run this during normal program flow; ideally, this should be run once, at the end of your program.

## Contributing

Given the relative stability of this monad in other languages, I'm not expecting loads of code PRs, but do feel free to submit them if you see anything you'd like to change! I see documentation changes being far more likely, and they are also more than welcome: the aim of the PhpFp project is not only to produce a set of functional utilities, but also to provide a learning resource for programmers wishing to learn more about functional programming.
