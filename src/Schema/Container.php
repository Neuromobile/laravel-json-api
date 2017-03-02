<?php namespace CloudCreativity\LaravelJsonApi\Schema;

use InvalidArgumentException;
use Neomerx\JsonApi\I18n\Translator as T;
use Neomerx\JsonApi\Schema\Container as BaseContainer;
use ReflectionClass;

/**
 * @package CloudCreativity\LaravelJsonApi
 */
class Container extends BaseContainer
{
    const JSON_API_SCHEMAS = "JSON_API_SCHEMAS";

    protected $namespace;

    /**
     * @inheritdoc
     */
    public function getSchemaByType($type)
    {
        is_string($type) === true ?: Exceptions::throwInvalidArgument('type', $type);
        if ($this->hasCreatedProvider($type) === true)
        {
            return $this->getCreatedProvider($type);
        }

        // If it does not exist try register using reflection
        if ($this->hasProviderMapping($type) === false)
        {
            $schemas = (new ReflectionClass($type))->getConstant(static::JSON_API_SCHEMAS);
            if (! $schemas || ! isset($schemas[$this->namespace]))
            {
                throw new InvalidArgumentException(T::t('Schema is not registered for type \'%s\'.', [$type]));
            }

            $this->setProviderMapping($type, $schemas[$this->namespace]);
        }

        $classNameOrClosure = $this->getProviderMapping($type);
        if ($classNameOrClosure instanceof Closure)
        {
            $schema = $this->createSchemaFromClosure($classNameOrClosure);
        }
        else
        {
            $schema = $this->createSchemaFromClassName($classNameOrClosure);
        }

        $this->setCreatedProvider($type, $schema);
        /** @var SchemaProviderInterface $schema */
        $this->setResourceToJsonTypeMapping($schema->getResourceType(), $type);
        return $schema;
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }
}