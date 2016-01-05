<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Extension;

use Behat\Testwork\ServiceContainer\Extension as TestworkExtension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SyliusPageObjectExtension implements TestworkExtension
{
    /**
     * {@inheritdoc}
     */
    public function getConfigKey()
    {
        return 'sylius_page_object';
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $classNameResolver = $container->get('sensio_labs.page_object_extension.class_name_resolver.camelcased');
        $defaultFactory = $container->get('sensio_labs.page_object_extension.page_factory.default');
        $kernel = $container->get('symfony2_extension.kernel');
        $appContainer = clone $kernel->getContainer();
        $router = $appContainer->get('router');

        $definition = new Definition('Sylius\Behat\Factory\PageObjectFactory');
        $definition->setArguments(array($classNameResolver, $defaultFactory, new Reference('mink'), $router, '%mink.parameters%'));
        $container->setDefinition('sylius.page_object.factory', $definition);
    }
}
