<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vaganca\Component\HttpTools\Session;


class AttributContainer implements AttributContainerInterface, \IteratorAggregate, \Countable
{

    protected array $attributes = [];

    private string $name = 'attributes';
    private string $storageKey;

    public function __construct(string $storageKey = '_vag1_attributes')
    {
        $this->storageKey = $storageKey;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function initialize(array &$array): void
    {
        $this->attributes = &$array;
    }

    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->attributes);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return \array_key_exists($name, $this->attributes) ? $this->attributes[$name] : $default;
    }

    public function set(string $name, mixed $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function all(): array
    {
        return $this->attributes;
    }

    public function replace(array $attributes): void
    {
        $this->attributes = [];
        foreach ($attributes as $key => $value) {
            $this->set($key, $value);
        }
    }

    public function remove(string $name): mixed
    {
        $retrievedVal = null;
        if (\array_key_exists($name, $this->attributes)) {
            $retrievedVal = $this->attributes[$name];
            unset($this->attributes[$name]);
        }

        return $retrievedVal;
    }

    public function clear(): array
    {
        $return = $this->attributes;
        $this->attributes = [];

        return $return;
    }

    /**
     * Returns an iterator for attributes.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->attributes);
    }

    /**
     * Returns the number of attributes.
     */
    public function count(): int
    {
        return \count($this->attributes);
    }

}