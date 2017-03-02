<?php

namespace CloudCreativity\LaravelJsonApi\Repositories;

use CloudCreativity\JsonApi\Repositories\SchemasRepository as BaseSchemasRepository;

class SchemasRepository extends BaseSchemasRepository
{
    /**
     * @inheritdoc
     */
    public function getSchemas($name = null)
    {
        $container = parent::getSchemas($name);
        $container->setNamespace($name);
        return $container;
    }
}