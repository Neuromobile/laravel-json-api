<?php namespace CloudCreativity\LaravelJsonApi\Schema;

use App;
use Config;
use InvalidArgumentException;
use ReflectionClass;
use Illuminate\Support\Str;
use Neomerx\JsonApi\I18n\Translator as T;
use Neomerx\JsonApi\Schema\Container as BaseContainer;

/**
 * @package CloudCreativity\LaravelJsonApi
 */
class Container extends BaseContainer
{
    const NAMESPACES = "NAMESPACES";

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

        // If it does not exist try register using reflection searching schema in default location
        if ($this->hasProviderMapping($type) === false)
        {
            if (Config::get('json-api.generator.namespace'))
            {
                $schema = 'App\\'.Config::get('json-api.generator.namespace').
                    '\\'.Str::studly(App::make($type)->getTable()).'\\Schema';
                
                if (! class_exists($schema))
                {
                    throw new InvalidArgumentException(T::t('Schema is not registered for type \'%s\'.', [$type]));
                }

                $namespaces = (new ReflectionClass($schema))->getConstant(static::NAMESPACES);
                if (! $namespaces || (! in_array($this->namespace, $namespaces) && ! in_array('defaults', $namespaces)))
                {
                    throw new InvalidArgumentException(T::t('Schema is not registered for type \'%s\'.', [$type]));
                }

                $this->setProviderMapping($type, $schema);
            }
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