<?php

/** @noinspection PhpParamsInspection */

/*
 * This file is part of the HTML sanitizer project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\HtmlSanitizer\Bundle\Form;

use HtmlSanitizer\Bundle\HtmlSanitizerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Tests\HtmlSanitizer\Bundle\Kernel\KernelTestTrait;

/**
 * @internal
 */
class TextTypeExtensionAppKernel extends Kernel
{
    use KernelTestTrait;

    public function registerBundles(): iterable
    {
        return [new FrameworkBundle(), new HtmlSanitizerBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $frameworkConfig = ['secret' => '$ecret'];
            if (Kernel::VERSION_ID > 50000) {
                $frameworkConfig['form'] = ['enabled' => true];
                $frameworkConfig['property_access'] = ['enabled' => true];
            }

            $container->loadFromExtension('framework', $frameworkConfig);

            $container->loadFromExtension('html_sanitizer', [
                'default_sanitizer' => 'default',
                'sanitizers' => [
                    'default' => [
                        'extensions' => ['basic', 'image'],
                        'tags' => ['img' => ['allowed_hosts' => ['trusted.com']]],
                    ],
                    'basic' => [
                        'extensions' => ['basic'],
                    ],
                ],
            ]);

            $container->setAlias('test.form.factory', 'form.factory')->setPublic(true);
        });
    }
}
