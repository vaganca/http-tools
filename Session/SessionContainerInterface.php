<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vaganca\Component\HttpTools\Session;

interface SessionContainerInterface
{

    public function getName(): string;

    public function initialize(array &$array): void;

    public function getStorageKey(): string;

    public function clear(): mixed;

}