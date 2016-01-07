<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Context\Shop;

use Sylius\Behat\Context\FeatureContext;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ProductContext extends FeatureContext
{
    /**
     * @Given catalog has a product :productName priced at $:price
     */
    public function catalogHasAProductPricedAt($productName, $price)
    {
        /** @var ProductInterface $product */
        $product = $this->getService('sylius.factory.product')->createNew();
        $product->setName($productName);
        $product->setPrice((int) $price);
        $product->setDescription('Awesome star wars mug');

        $channel = $this->clipboard->getCurrentObject('channel');
        $product->addChannel($channel);

        $this->persistObject($product);
        $this->flushEntityManager();
    }
}
