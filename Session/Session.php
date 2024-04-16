<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vaganca\Component\HttpTools\Session;


class_exists(AttributContainer::class);

class Session implements \IteratorAggregate, \Countable
{

    protected SessionStorageInterface $storage;
    private string $attributeName;
    private array $data = [];


    public function __construct(?SessionStorageInterface $storage = null, ?AttributContainerInterface $attributes = null)
    {
        $this->storage = $storage ?? new SessionStorage();

        $attributes ??= new AttributContainer();
        $this->attributeName = $attributes->getName();
        $this->registerContainer($attributes);
    }

    public function start(): bool
    {
        return $this->storage->start();
    }

    public function has(string $name): bool
    {
        return $this->getAttributeContainer()->has($name);
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $this->getAttributeContainer()->get($name, $default);
    }

    public function set(string $name, mixed $value): void
    {
        $this->getAttributeContainer()->set($name, $value);
    }

    public function add(string $name, mixed $value): void
    {
        $this->getAttributeContainer()->add($name, $value);
    }

    public function all(): array
    {
        return $this->getAttributeContainer()->all();
    }

    public function replace(array $attributes): void
    {
        $this->getAttributeContainer()->replace($attributes);
    }

    public function remove(string $name): mixed
    {
        return $this->getAttributeContainer()->remove($name);
    }

    public function clear(): void
    {
        $this->getAttributeContainer()->clear();
    }

    public function isStarted(): bool
    {
        return $this->storage->isStarted();
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getAttributeContainer()->all());
    }

    public function count(): int
    {
        return \count($this->getAttributeContainer()->all());
    }

    public function isEmpty(): bool
    {
        foreach ($this->data as &$data) {
            if (!empty($data)) {
                return false;
            }
        }

        return true;
    }

    public function invalidate(?int $lifetime = null): bool
    {
        $this->storage->clear();

        return $this->migrate(true, $lifetime);
    }

    public function migrate(bool $destroy = false, ?int $lifetime = null): bool
    {
        return $this->storage->regenerate($destroy, $lifetime);
    }

    public function getId(): string
    {
        return $this->storage->getId();
    }

    public function setId(string $id): void
    {
        $this->storage->setId($id);
    }

    public function getName(): string
    {
        return $this->storage->getName();
    }

    public function setName(string $name): void
    {
        $this->storage->setName($name);
    }


    public function registerContainer(SessionContainerInterface $container): void
    {
        $this->storage->registerContainer($container);
    }

    public function getContainer(string $name): SessionContainerInterface
    {
        $container = $this->storage->getContainer($name);

        return method_exists($container, 'getContainer') ? $container->getContainer() : $container;
    }

    private function getAttributeContainer(): ?AttributContainerInterface
    {
        return $this->getContainer($this->attributeName);
    }

}