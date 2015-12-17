<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Test\Services;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SharedStorage
{
    /**
     * @var array
     */
    private $clipboard = array();

    /**
     * @var string|null
     */
    private $latestKey = null;

    /**
     * {@inheritdoc}
     */
    public function setCurrentObject($object, $key = null)
    {
        $key = $this->getNormalizedObjectName($key ?: $object);

        $this->clipboard[$key] = $object;

        $this->latestKey = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentObject($object)
    {
        $key = $this->getNormalizedObjectName($object);

        if (!isset($this->clipboard[$key])) {
            throw new \InvalidArgumentException(sprintf('There is no current object for "%s"!', $key));
        }

        return $this->clipboard[$key];
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestObject()
    {
        return $this->clipboard[$this->latestKey];
    }

    /**
     * @param string|object $object
     *
     * @return string
     */
    private function getNormalizedObjectName($object)
    {
        if (is_object($object)) {
            $object = preg_replace(
                '/([^A-Z])([A-Z]+)/',
                '$1_$2',
                (new \ReflectionClass($object))->getShortName()
            );
        }

        return strtolower(str_replace(' ', '_', $object));
    }
}
