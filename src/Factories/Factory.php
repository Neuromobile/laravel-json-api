<?php

namespace CloudCreativity\LaravelJsonApi\Factories;

use CloudCreativity\JsonApi\Factories\Factory as BaseFactory;
use CloudCreativity\LaravelJsonApi\Schema\Container;

/**
 * Class Factory
 *
 * @package CloudCreativity\LaravelJsonApi
 */
class Factory extends BaseFactory
{

    /**
     * @inheritdoc
     */
    public function createContainer(array $providers = [])
    {
        $container = new Container($this, $providers);

        $container->setLogger($this->logger);

        return $container;
    }
}
