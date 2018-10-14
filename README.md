# html-sanitizer-bundle

[html-sanitizer](https://github.com/tgalopin/html-sanitizer)
is a library aiming at handling, cleaning and sanitizing HTML sent by external users
(who you cannot trust), allowing you to store it and display it safely. It has sensible defaults
to provide a great developer experience while still being entierely configurable.

This repository is a Symfony bundle integrating this library into Symfony applications.
It provides helpful tools on top of the sanitizer to easily use it in Symfony.

Have a look at the [library documentation](https://github.com/tgalopin/html-sanitizer) to learn more about
what you can do with the sanitizer.

- [Installation](#installation)

## Installation

html-sanitizer-bundle requires PHP 7.1+ and Symfony 4.0+.

You can install the bundle using Symfony Flex:

```
composer require tgalopin/html-sanitizer-bundle
```
