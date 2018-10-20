<?php

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
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpKernel\Kernel;
use Tests\HtmlSanitizer\Bundle\AppKernelTestTrait;

class TextTypeExtensionTest extends TestCase
{
    public function testDefault()
    {
        $kernel = new TextTypeExtensionAppKernel('test', 'dev');
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->assertTrue($container->has('form.factory'));

        /** @var FormFactoryInterface $factory */
        $factory = $container->get('form.factory');

        $form = $factory->createBuilder(FormType::class, ['data' => null])
            ->add('data', TextType::class, ['required' => true, 'sanitize_html' => true])
            ->getForm()
        ;

        $form->submit(['data' => file_get_contents(__DIR__.'/fixtures/default/input.html')]);

        $this->assertSame(trim(file_get_contents(__DIR__.'/fixtures/default/output.html')), trim($form->getData()['data']));
    }

    public function testBasic()
    {
        $kernel = new TextTypeExtensionAppKernel('test', 'dev');
        $kernel->boot();

        $container = $kernel->getContainer();

        $this->assertTrue($container->has('form.factory'));

        /** @var FormFactoryInterface $factory */
        $factory = $container->get('form.factory');

        $form = $factory->createBuilder(FormType::class, ['data' => null])
            ->add('data', TextType::class, ['required' => true, 'sanitize_html' => true, 'sanitizer' => 'basic'])
            ->getForm()
        ;

        $form->submit(['data' => file_get_contents(__DIR__.'/fixtures/basic/input.html')]);

        $this->assertSame(trim(file_get_contents(__DIR__.'/fixtures/basic/output.html')), trim($form->getData()['data']));
    }
}

class TextTypeExtensionAppKernel extends Kernel
{
    use AppKernelTestTrait;

    public function registerBundles()
    {
        return [new FrameworkBundle(), new HtmlSanitizerBundle()];
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(function ($container) {
            $container->loadFromExtension('framework', ['secret' => '$ecret']);

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
