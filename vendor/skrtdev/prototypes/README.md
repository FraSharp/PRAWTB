# Prototypes
> Dinamically add methods to PHP classes

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/skrtdev/prototypes/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/skrtdev/prototypes/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/skrtdev/prototypes/badges/build.png?b=master)](https://scrutinizer-ci.com/g/skrtdev/prototypes/build-status/master) [![Codacy Badge](https://app.codacy.com/project/badge/Grade/ab3a826ec45e45d7bcc910c39df1331a)](https://www.codacy.com/gh/skrtdev/prototypes/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=skrtdev/prototypes&amp;utm_campaign=Badge_Grade) ![php version](https://img.shields.io/badge/php-%3E%3D7.4-blueviolet)  
Using this library you can dinamcally add methods to classes, as in the following example:  
```php
use skrtdev\Prototypes\Prototypeable;

class MyClass{
    use Prototypeable;
}

MyClass::addMethod('wow', function () {
    return "What a nice way to use PHP";
});

$instance = new MyClass();

echo $instance->wow();
```
Output is `What a nice way to use PHP`

### Main Features

  - Closures are bound to the original object, so you can access `$this` inside closures in the same way as you do when writing a normal method for that class.
  - Supports **static** methods too, and you can access `self` and `static` too.  
  - A native-like `\Error` will be thrown when trying to call a non-existent method.  
  - A `skrtdev\Prototypes\Exception` will be thrown if class method already exists, is a prototype, class does not exist or isn't Prototypeable (when using `skrtdev\Prototypes\Prototypes::addMethod()`).
  - Ability to use any kind of `callable`s, not just `Closure`s.
  - Ability to use [named arguments](https://www.php.net/manual/en/functions.arguments.php#functions.named-arguments) in Prototypes methods.

### Check if a Class is Prototypeable

You may need to know if a class is `Prototypeable` before trying to add methods to it.

You can use `isPrototypeable` method:  
```php
use skrtdev\Prototypes\Prototypes;

var_dump(Prototypes::isPrototypeable($instance::class)); // you must pass the class name as string (use get_class() in php7.x)
var_dump(Prototypes::isPrototypeable(MyClass::class));
```

### Fun fact

The `Prototypes` class itself is `Prototypeable`, how strange.  

### Known issues

  - This library does not have `Inheritance`: you won't be able to use Prototypes methods defined for a class in his child classes. (this is going to be added soon)  
  - You can't override already-prototyped methods, but this will be added soon.  
  - Any kind of `Error/Exception`(s) look a bit strange in the Stack traces, and method name is hidden. Maybe there is a solution; if you find it, feel free to open an `Issue/Pull Request`.  

### Future scope

  - Use `class_parent()` to implement some kind of `Inheritance`. This may slow up calling prototypes in classes with a long hierarchy.  
  - Maybe add the ability to add a method to a class without adding it to his children. (does it make sense?)
  - Allow to add all methods of a normal/anonymous class into a Prototypeable one (Using `Reflection`?).  
  - Add the ability to define prototype methods that has been already defined as prototypes, overwriting them.  
  - Add the ability to define prototypes for all the Prototypeable classes. (do you see any use cases?)  
