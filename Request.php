<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE.md file that was distributed with this source code.
 */

namespace Vaganca\Component\HttpTools;

class Request
{

    public string $request;

    public string $query;

    public string $server;

    public string $files;

    public string $cookies;

    public string $header;

}