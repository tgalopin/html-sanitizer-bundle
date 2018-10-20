<?php

/*
 * This file is part of the HTML sanitizer project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\HtmlSanitizer\Bundle\Twig;

use HtmlSanitizer\Bundle\HtmlSanitizerBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;
use Tests\HtmlSanitizer\Bundle\AppKernelTestTrait;

class TwigExtensionTest extends TestCase
{
    public function testUseTwigExtension()
    {
        $kernel = new TwigExtensionAppKernel('test', 'dev');
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->assertTrue($container->has('twig'));

        $this->assertSame(
            trim(file_get_contents(__DIR__.'/templates/output.html')),
            trim($container->get('twig')->render('input.html.twig'))
        );
    }
}

class TwigExtensionAppKernel extends Kernel
{
    use AppKernelTestTrait;

    public function registerBundles()
    {
        return [new FrameworkBundle(), new TwigBundle(), new HtmlSanitizerBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function ($container) {
            $container->loadFromExtension('framework', ['secret' => '$ecret']);
            $container->loadFromExtension('twig', ['paths' => [__DIR__.'/templates']]);

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
        });
    }
}
