<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle\Form\Type\Filter;

use Sylius\Component\Grid\Filter\ElasticaFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Nicolas Adler <nicolas.adler@openizi.com>
 */
class ElasticaFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'sylius.ui.contains' => ElasticaFilter::TYPE_CONTAINS,
                    'sylius.ui.not_contains' => ElasticaFilter::TYPE_NOT_CONTAINS,
                    'sylius.ui.equal' => ElasticaFilter::TYPE_EQUAL,
                    'sylius.ui.not_equal' => ElasticaFilter::TYPE_NOT_EQUAL,
                    'sylius.ui.empty' => ElasticaFilter::TYPE_EMPTY,
                    'sylius.ui.not_empty' => ElasticaFilter::TYPE_NOT_EMPTY,
                    'sylius.ui.starts_with' => ElasticaFilter::TYPE_STARTS_WITH,
                    'sylius.ui.ends_with' => ElasticaFilter::TYPE_ENDS_WITH,
                    'sylius.ui.in' => ElasticaFilter::TYPE_IN,
                    'sylius.ui.not_in' => ElasticaFilter::TYPE_NOT_IN,
                ]
            ])
            ->add('value', TextType::class, ['required' => false])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_grid_filter_elastica';
    }
}
