<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\BatchProcessing;

use Sylius\Bundle\CoreBundle\DependencyInjection\ProcessManagerRegistry;
use Sylius\Bundle\CoreBundle\Entity\Job;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProcessType extends AbstractResourceType
{
    private ProcessManagerRegistry $registry;

    public function __construct(string $dataClass, array $validationGroups, ProcessManagerRegistry  $registry)
    {
        parent::__construct($dataClass, $validationGroups);
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = array_combine(array_keys($this->registry->all()), array_keys($this->registry->all()));
        $builder
            ->add('name', TextType::class)
            ->add('code', TextType::class)
            ->add('processManagerId', ChoiceType::class, [
                'choices' => $choices,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
    }
}
