<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Test\Services;

use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class PaymentMethodFactory implements PaymentMethodFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $defaultFactory;

    /**
     * @var array
     */
    private $expectedGateways = [
        'paypal_express_checkout' => 'paypal express checkout',
        'be2bill_direct' => 'be2bill direct',
        'be2bill_offsite' => 'be2bill offsite',
        'stripe_checkout' => 'stripe checkout',
        'offline' => 'offline',
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct(FactoryInterface $defaultFactory)
    {
        $this->defaultFactory = $defaultFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->defaultFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createFromArray(array $parameters)
    {
        $this->setGatewayBasedOnPaymentMethodName($parameters);
        $paymentMethod = $this->defaultFactory->createNew();

        $accessor = PropertyAccess::createPropertyAccessor();
        foreach ($parameters as $propertyName => $value) {
            $accessor->setValue($paymentMethod, $propertyName, $value);
        }

        return $paymentMethod;
    }

    /**
     * @param array $parameters
     */
    private function setGatewayBasedOnPaymentMethodName(array &$parameters)
    {
        if (!isset($parameters['name'])) {
            throw new \InvalidArgumentException(sprintf('Name cannot be empty'));
        }

        $paymentMethodName = strtolower($parameters['name']);

        if (!isset($parameters['gateway'])) {
            $parameters['gateway'] = array_search($paymentMethodName, $this->expectedGateways);
        }

        if (!array_key_exists($parameters['gateway'], $this->expectedGateways)) {
            throw new \InvalidArgumentException(sprintf('There is no %s gateway registered, or update this check', $parameters['gateway']));
        }
    }
}
