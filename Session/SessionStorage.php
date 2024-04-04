<?php
/*
 * This file is part of the Vaganca project.
 *
 * (c) Benjamin Wagner <wagner@vaganca.de>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Vaganca\Component\HttpTools\Session;

class SessionStorage implements SessionStorageInterface
{

    protected array $containers = [];
    protected string $id;
    protected string $name;
    protected bool $started = false;
    protected bool $closed = false;

    public function __construct(array $options = [])
    {
        if (!\extension_loaded('session')) {
            throw new \LogicException('PHP extension "session" is required.');
        }

        $options += [
            'cache_limiter' => '',
            'cache_expire' => 0,
            'use_cookies' => 1,
            'lazy_write' => 1,
            'use_strict_mode' => 1,
        ];

        session_register_shutdown();

        $this->setOptions($options);
    }


    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (filter_var(ini_get('session.use_cookies'), \FILTER_VALIDATE_BOOL) && headers_sent($file, $line)) {
            throw new \RuntimeException(sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
        }

        $sessionId = $_COOKIE[session_name()] ?? null;

        if ($sessionId && !preg_match('/^[a-zA-Z0-9,-]{22,250}$/', $sessionId)) {
            session_id(session_create_id());
        }

        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session.');
        }

        $this->loadSession();

        return true;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function regenerate(bool $destroy = false, ?int $lifetime = null): bool
    {
        // Cannot regenerate the session ID for non-active sessions.
        if (\PHP_SESSION_ACTIVE !== session_status()) {
            return false;
        }

        if (headers_sent()) {
            return false;
        }

        if (null !== $lifetime && $lifetime != \ini_get('session.cookie_lifetime')) {
            ini_set('session.cookie_lifetime', $lifetime);
            $this->start();
        }

        return session_regenerate_id($destroy);
    }


    public function clear(): void
    {
        foreach ($this->containers as $container) {
            $container->clear();
        }

        // clear out the session
        $_SESSION = [];

        // reconnect the bags to the session
        $this->loadSession();
    }

    public function getContainer(string $name): SessionContainerInterface
    {
        if (!isset($this->containers[$name])) {
            throw new \InvalidArgumentException(sprintf('The SessionContainerInterface "%s" is not registered.', $name));
        }

        if (!$this->started) {
            $this->start();
        }

        return $this->containers[$name];
    }

    public function registerContainer(SessionContainerInterface $container): void
    {
        if ($this->started) {
            throw new \LogicException('Cannot register a container when the session is already started.');
        }

        $this->containers[$container->getName()] = $container;
    }

    private function setOptions(array $options)
    {
        if (headers_sent() || \PHP_SESSION_ACTIVE === session_status()) {
            return;
        }

        $validOptions = array_flip([
            'cache_expire', 'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure', 'cookie_samesite',
            'gc_divisor', 'gc_maxlifetime', 'gc_probability',
            'lazy_write', 'name', 'referer_check',
            'serialize_handler', 'use_strict_mode', 'use_cookies',
            'use_only_cookies', 'use_trans_sid',
            'sid_length', 'sid_bits_per_character', 'trans_sid_hosts', 'trans_sid_tags',
        ]);

        foreach ($options as $key => $value) {
            if (isset($validOptions[$key])) {
                if ('cookie_secure' === $key && 'auto' === $value) {
                    continue;
                }
                ini_set('session.'.$key, $value);
            }
        }
    }

    public function write(): void
    {
        // Store a copy so we can restore the bags in case the session was not left empty
        $session = $_SESSION;

        foreach ($this->containers as $container) {
            if (empty($_SESSION[$key = $container->getStorageKey()])) {
                unset($_SESSION[$key]);
            }
        }

        try {
            session_write_close();
        } finally {
            restore_error_handler();

            // Restore only if not empty
            if ($_SESSION) {
                $_SESSION = $session;
            }
        }

        $this->closed = true;
        $this->started = false;
    }

    protected function loadSession(?array &$session = null): void
    {
        if (null === $session) {
            $session = &$_SESSION;
        }

        foreach ($this->containers as $container) {
            $key = $container->getStorageKey();
            $session[$key] = isset($session[$key]) && \is_array($session[$key]) ? $session[$key] : [];
            $container->initialize($session[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }

}