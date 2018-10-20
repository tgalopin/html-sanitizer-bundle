<?php

/*
 * This file is part of the HTML sanitizer project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HtmlSanitizer\Bundle\DependencyInjection;

use HtmlSanitizer\Bundle\Form\TextTypeExtension;
use HtmlSanitizer\Bundle\Twig\TwigExtension;
use HtmlSanitizer\Extension\ExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Twig\Environment;

/**
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class HtmlSanitizerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('html_sanitizer.configuration', $config['sanitizer']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->registerExtensions($container);

        if (class_exists(TextType::class)) {
            $this->registerFormExtension($container);
        }

        if (class_exists(Environment::class)) {
            $this->registerTwigExtension($container);
        }
    }

    private function registerExtensions(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(ExtensionInterface::class)->addTag('html_sanitizer.extension');

        $builderDefinition = $container->getDefinition('html_sanitizer.builder');

        foreach ($container->findTaggedServiceIds('html_sanitizer.extension') as $serviceId => $tags) {
            $builderDefinition->addMethodCall('registerExtension', [new Reference($serviceId)]);
        }
    }

    private function registerFormExtension(ContainerBuilder $container)
    {
        $extension = new Definition(TextTypeExtension::class, [new Reference('html_sanitizer')]);
        $extension->addTag('form.type_extension', ['extended_type' => TextType::class]);

        $container->setDefinition('html_sanitizer.form.text_type_extension', $extension);
    }

    private function registerTwigExtension(ContainerBuilder $container)
    {
        $extension = new Definition(TwigExtension::class, [new Reference('html_sanitizer')]);
        $extension->addTag('twig.extension');

        $container->setDefinition('html_sanitizer.twig_extension', $extension);
    }
}
