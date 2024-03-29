<?php

/*
 * This file is part of the HTML sanitizer project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\HtmlSanitizer\Bundle\Kernel;

use HtmlSanitizer\Bundle\HtmlSanitizerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @internal
 */
class EmptyAppKernel extends Kernel
{
    use KernelTestTrait;

    public function registerBundles(): iterable
    {
        return [new HtmlSanitizerBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function (ContainerBuilder $container) {
            $container->loadFromExtension('html_sanitizer', [
                'default_sanitizer' => 'default',
                'sanitizers' => ['default' => ['extensions' => ['basic']]],
            ]);
        });
    }
}
