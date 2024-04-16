<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vaganca\Component\HttpTools\Session;

interface AttributContainerInterface extends SessionContainerInterface
{

    public function has(string $name): bool;

    public function get(string $name, mixed $default = null): mixed;

    public function set(string $name, mixed $value): void;

    public function all(): array;

    public function replace(array $attributes): void;

    public function remove(string $name): mixed;

    public function add(string $name, mixed $value);

}