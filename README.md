# html-sanitizer-bundle

[html-sanitizer](https://github.com/tgalopin/html-sanitizer)
is a library aiming at handling, cleaning and sanitizing HTML sent by external users
(who you cannot trust), allowing you to store it and display it safely. It has sensible defaults
to provide a great developer experience while still being entierely configurable.

This repository is a Symfony bundle integrating this library into Symfony applications.
It provides helpful tools on top of the sanitizer to easily use it in Symfony.

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage in services](#usage-in-services)
- [Usage in forms](#usage-in-forms)
- [Usage in Twig](#usage-in-twig)

## Installation

html-sanitizer-bundle requires PHP 7.1+ and Symfony 3.4+.

You can install the bundle using Symfony Flex:

```
composer require tgalopin/html-sanitizer-bundle
```

## Configuration

You can configure the sanitizer using the `html_sanitizer` configuration key:

```yaml
html_sanitizer:
    extensions: ['basic', 'image', 'list']
    tags:
        img:
            allowed_hosts: ['127.0.0.1', 'mywebsite.com', 'youtube.com']
            force_https: true
```

Have a look at the [library documentation](https://github.com/tgalopin/html-sanitizer) to learn all the available
configuration options.

## Usage in services

This bundle provides the configured sanitizer for autowiring using the interface 
`HtmlSanitizer\SanitizerInterface`. This means that if you are using autowiring, you can simply
typehint the sanitizer in any of your services to get it:

```php
use HtmlSanitizer\SanitizerInterface;

class MyService
{
    private $sanitizer;
    
    public function __construct(SanitizerInterface $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }
    
    // ...
}
```

The same goes for controllers:

```php
use HtmlSanitizer\SanitizerInterface;

class MyController
{
    public function index(SanitizerInterface $sanitizer)
    {
        // ...
    }
}
```

If you are not using autowiring, you can inject the `html_sanitizer` service into your services
manually too.

## Usage in forms

> This applies only if you have installed the Symfony Form component. 

The main usage of the html-sanitizer is in combination with forms. This bundle provides a TextType extension
which allows you to automatically sanitize HTML of any text field or any field based on the TextType
(TextareaType, SearchType, etc.). 

To use it in any of your forms, you can use the `sanitize_html` option:

```php
class MyFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, ['sanitize_html' => true])
        ;
    }
}
``` 

## Usage in Twig

> This applies only if you have installed the Twig bundle.

A `sanitize_html` Twig filter is provided through an extension, letting you filter HTML inside your views.

```twig
<div>
    {{ html|sanitize_html }}
</div>
```
