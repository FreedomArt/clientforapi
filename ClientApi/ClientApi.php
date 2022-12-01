<?php

namespace ClientApi;

use Exception;
use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use stdClass;
use Http\Request;

class ClientApi
{
    /** @var CacheInterface $instanceCache */
    protected static $instanceCache = null;

    protected $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.89 Safari/537.36';

    /**
     * ClientApi constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        Request::setHttpClient($client);
    }

    /**
     * @param ClientInterface $httpClient
     */
    public static function setHttpClient(ClientInterface $httpClient): void
    {
        Request::setHttpClient($httpClient);
    }

    /**
     * @param ClientInterface $client
     * @param string $username
     * @param string $password
     * @param CacheInterface $cache
     *
     * @return Instagram
     */
    public static function withCredentials(ClientInterface $client, $cache)
    {
        static::$instanceCache = $cache;
        $instance = new self($client);
        return $instance;
    }

    public function login()
    {
        return 'Go!';
    }

}