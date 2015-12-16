<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Setup;

use Sylius\Behat\Context\SetupContext;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductContext extends SetupContext
{
    /**
     * @var FactoryInterface
     */
    private $productFactory;

    /**
     * @param RepositoryInterface $entityRepository
     * @param SharedStorageInterface $clipboard
     * @param FactoryInterface $productFactory
     */
    public function __construct(RepositoryInterface $entityRepository, SharedStorageInterface $clipboard, FactoryInterface $productFactory)
    {
        parent::__construct($entityRepository, $clipboard);

        $this->productFactory = $productFactory;
    }

    /**
     * @Given catalog has a product :productName priced at $:price
     */
    public function catalogHasAProductPricedAt($productName, $price)
    {
        $product = $this->productFactory->createNew();
        $product->setName($productName);
        $product->setPrice((int) $price);
        $product->setDescription('Awesome star wars mug');

        $channel = $this->clipboard->getCurrentResource('channel');
        $product->addChannel($channel);

        $this->entityRepository->add($product);
    }
}
