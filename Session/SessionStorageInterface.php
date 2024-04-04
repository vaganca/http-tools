<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vaganca\Component\HttpTools\Session;

interface SessionStorageInterface
{

    public function start(): bool;

    public function isStarted(): bool;

    public function getId(): string;

    public function setId(string $id): void;

    public function regenerate(bool $destroy = false, ?int $lifetime = null): bool;

    public function clear(): void;

    public function getContainer(string $name): SessionContainerInterface;

    public function registerContainer(SessionContainerInterface $container): void;

}