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

    public InputContainer $request;

    public InputContainer $query;

    public ServerContainer $server;

    public InputContainer $cookies;

    public HeaderContainer $headers;

    private ParameterContainer $attributes;

    private mixed $content;

    public function __construct(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $server = [], $content = null)
    {
        $this->initialize($query, $request, $attributes, $cookies, $server, $content);
    }

    public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $server = [], $content = null): void
    {
        $this->request = new InputContainer($request);
        $this->query = new InputContainer($query);
        $this->attributes = new ParameterContainer($attributes);
        $this->cookies = new InputContainer($cookies);
        $this->server = new ServerContainer($server);
        $this->headers = new HeaderContainer($this->server->getHeaders());
        $this->content = $content;
    }

}