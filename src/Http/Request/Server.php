<?php


namespace Quantum\Http\Request;


trait Server
{
    /**
     * Scheme
     * @var string
     */
    private static $__protocol = null;

    /**
     * Host name
     * @var string
     */
    private static $__host = null;

    /**
     * Server port
     * @var string
     */
    private static $__port = null;

    /**
     * Request URI
     * @var string
     */
    private static $__uri = null;

    /**
     * Gets the protocol
     * @return string
     */
    public static function getProtocol()
    {
        return self::$__protocol;
    }

    /**
     * Sets the protocol
     * @param string $protocol
     */
    public static function setProtocol(string $protocol)
    {
        self::$__protocol = $protocol;
    }

    /**
     * Gets the host name
     * @return string
     */
    public static function getHost(): ?string
    {
        return self::$__host;
    }

    /**
     * Sets the host name
     * @param string $host
     */
    public static function setHost(string $host)
    {
        self::$__host = $host;
    }

    /**
     * Gets the port
     * @return string
     */
    public static function getPort()
    {
        return self::$__port;
    }

    /**
     * Sets the port
     * @param string $port
     */
    public static function setPort($port)
    {
        self::$__port = $port;
    }

    /**
     * Gets the URI
     * @return string|null
     */
    public static function getUri()
    {
        return self::$__uri;
    }

    /**
     * Sets the URI
     * @param string $uri
     */
    public static function setUri(string $uri)
    {
        self::$__uri = ltrim($uri, '/');
    }
}