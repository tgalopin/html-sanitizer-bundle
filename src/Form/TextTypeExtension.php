<?php

/*
 * This file is part of the HTML sanitizer project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace HtmlSanitizer\Bundle\Form;

use HtmlSanitizer\SanitizerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextTypeExtension extends AbstractTypeExtension implements EventSubscriberInterface
{
    private $sanitizer;

    public function __construct(SanitizerInterface $sanitizer)
    {
        $this->sanitizer = $sanitizer;
    }

    public function getExtendedType()
    {
        return TextType::class;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SUBMIT => ['sanitize', 999999 /* as soon as possible */],
        ];
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(['sanitize_html' => false])
            ->setAllowedTypes('sanitize_html', 'bool')
        ;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['sanitize_html']) {
            $builder->addEventSubscriber($this);
        }
    }

    public function sanitize(FormEvent $event)
    {
        if (!is_scalar($data = $event->getData())) {
            return;
        }

        if (0 === mb_strlen($html = trim($data))) {
            return;
        }

        $event->setData($this->sanitizer->sanitize($html));
    }
}
