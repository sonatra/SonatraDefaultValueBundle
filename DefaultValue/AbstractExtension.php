<?php

/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonatra\Bundle\DefaultValueBundle\DefaultValue;

use Sonatra\Bundle\DefaultValueBundle\DefaultValue\Exception\InvalidArgumentException;
use Sonatra\Bundle\DefaultValueBundle\DefaultValue\Exception\UnexpectedTypeException;

/**
 * @author François Pluchino <francois.pluchino@sonatra.com>
 */
abstract class AbstractExtension implements ObjectExtensionInterface
{
    /**
     * The types provided by this extension.
     * @var array An array of ObjectTypeInterface
     */
    protected $types;

    /**
     * The type extensions provided by this extension.
     * @var array An array of ObjectTypeExtensionInterface
     */
    protected $typeExtensions;

    /**
     * {@inheritdoc}
     */
    public function getType($name)
    {
        if (null === $this->types) {
            $this->initTypes();
        }

        if (!isset($this->types[$name])) {
            throw new InvalidArgumentException(sprintf('The object default value type "%s" can not be loaded by this extension', $name));
        }

        return $this->types[$name];
    }

    /**
     * {@inheritdoc}
     */
    public function hasType($name)
    {
        if (null === $this->types) {
            $this->initTypes();
        }

        return isset($this->types[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeExtensions($name)
    {
        if (null === $this->typeExtensions) {
            $this->initTypeExtensions();
        }

        return isset($this->typeExtensions[$name])
            ? $this->typeExtensions[$name]
            : array();
    }

    /**
     * {@inheritdoc}
     */
    public function hasTypeExtensions($name)
    {
        if (null === $this->typeExtensions) {
            $this->initTypeExtensions();
        }

        return isset($this->typeExtensions[$name]) && count($this->typeExtensions[$name]) > 0;
    }

    /**
     * Registers the types.
     *
     * @return array An array of ObjectTypeInterface instances
     */
    protected function loadTypes()
    {
        return array();
    }

    /**
     * Registers the type extensions.
     *
     * @return array An array of ObjectTypeExtensionInterface instances
     */
    protected function loadTypeExtensions()
    {
        return array();
    }

    /**
     * Initializes the types.
     *
     * @throws UnexpectedTypeException if any registered type is not an instance of ObjectTypeInterface
     */
    private function initTypes()
    {
        $this->types = array();

        foreach ($this->loadTypes() as $type) {
            if (!$type instanceof ObjectTypeInterface) {
                throw new UnexpectedTypeException($type, 'Sonatra\Bundle\DefaultValueBundle\DefaultValue\ObjectTypeInterface');
            }

            $this->types[$type->getClass()] = $type;
        }
    }

    /**
     * Initializes the type extensions.
     *
     * @throws UnexpectedTypeException if any registered type extension is not
     *                                 an instance of ObjectTypeExtensionInterface
     */
    private function initTypeExtensions()
    {
        $this->typeExtensions = array();

        foreach ($this->loadTypeExtensions() as $extension) {
            if (!$extension instanceof ObjectTypeExtensionInterface) {
                throw new UnexpectedTypeException($extension, 'Sonatra\Bundle\DefaultValueBundle\DefaultValue\ObjectTypeExtensionInterface');
            }

            $type = $extension->getExtendedType();

            $this->typeExtensions[$type][] = $extension;
        }
    }
}
