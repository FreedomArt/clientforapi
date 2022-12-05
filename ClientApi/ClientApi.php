<?php

namespace ClientApi;

use Exception;
use Psr\Http\Client\ClientInterface;
use Psr\SimpleCache\CacheInterface;
use stdClass;
use Http\Request;

class ClientApi
{
    const HTTP_NOT_FOUND = 404;
    const HTTP_OK = 200;
    const HTTP_FOUND = 302;
    const HTTP_FORBIDDEN = 403;
    const HTTP_BAD_REQUEST = 400;

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
     * @param CacheInterface $cache
     *
     * @return ClientApi
     */
    public static function withCredentials(ClientInterface $client, $cache)
    {
        static::$instanceCache = $cache;
        $instance = new self($client);
        return $instance;
    }

    public function run($link, $token = NULL)
    {
        if ($token == NULL){
            $response = Request::get($link);
        }else{
            $response = Request::get($link, $this->generateHeaders($token));
        }

        if (static::HTTP_NOT_FOUND === $response->code) {
            throw new \Exception('NOT_FOUND');
        }
        if (static::HTTP_OK !== $response->code) {
            throw new \Exception('Response code is ' . $response->code . ': ' . static::httpCodeToString($response->code) . '.' . 'Something went wrong. Please report issue.', $response->code, static::getErrorBody($response->body));
        }

        $jsonResponse = $this->decodeRawBodyToJson($response->raw_body);

        return $jsonResponse;
    }

    /**
     * @param $session
     * @param $gisToken
     *
     * @return array
     */
    private function generateHeaders($token)
    {
        $headers = [];
        if ($token) {
            $headers = [
                'token' => $token,
            ];
        }

        if ($this->getUserAgent()) {
            $headers['user-agent'] = $this->getUserAgent();
        }

        return $headers;
    }

    /**
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * @param $userAgent
     *
     * @return string
     */
    public function setUserAgent($userAgent)
    {
        return $this->userAgent = $userAgent;
    }

    /**
     * @param $rawBody
     * @return mixed
     */
    private function decodeRawBodyToJson($rawBody)
    {
        return json_decode($rawBody, true, 512, JSON_BIGINT_AS_STRING);
    }

    /**
     * @return null
     */
    public function resetUserAgent()
    {
        return $this->userAgent = null;
    }

    /**
     * @param stdClass|string $rawError
     *
     * @return string
     */
    private static function getErrorBody($rawError)
    {
        if (is_string($rawError)) {
            return $rawError;
        }
        if (is_object($rawError)) {
            $str = '';
            foreach ($rawError as $key => $value) {
                if (is_array($value))  $value=json_encode((array)$value);
                if (is_object($value)) $value=json_encode((array)$value);
                if (is_array($key))    $key=json_encode((array)$key);
                if (is_object($key))   $key=json_encode((array)$key);
                $str .= ' ' . $key . ' => ' . $value . ';';
            }
            return $str;
        } else {
            return 'Unknown body format';
        }

    }

    public static function httpCodeToString($code)
    {
        switch($code) {
            case 100:
                return "Continue";
            case 101:
                return "Switching Protocols";
            case 102:
                return "Processing";
            case 200:
                return "OK";
            case 201:
                return "Created";
            case 202:
                return "Accepted";
            case 203:
                return "Non-Authoritative Information";
            case 204:
                return "No Content";
            case 205:
                return "Reset Content";
            case 206:
                return "Partial Content";
            case 207:
                return "Multi-Status";
            case 208:
                return "Already Reported";
            case 226:
                return "IM Used";
            case 300:
                return "Multiple Choices";
            case 301:
                return "Moved Permanently";
            case 302:
                return "Found";
            case 303:
                return "See Other";
            case 304:
                return "Not Modified";
            case 305:
                return "Use Proxy";
            case 306:
                return "Switch Proxy";
            case 307:
                return "Temporary Redirect";
            case 308:
                return "Permanent Redirect";
            case 400:
                return "Bad Request";
            case 401:
                return "Unauthorized";
            case 402:
                return "Payment Required";
            case 403:
                return "Forbidden";
            case 404:
                return "Not Found";
            case 405:
                return "Method Not Allowed";
            case 406:
                return "Not Acceptable";
            case 407:
                return "Proxy Authentication Required";
            case 408:
                return "Request Timeout";
            case 409:
                return "Conflict";
            case 410:
                return "Gone";
            case 411:
                return "Length Required";
            case 412:
                return "Precondition Failed";
            case 413:
                return "Payload Too Large";
            case 414:
                return "URI Too Long";
            case 415:
                return "Unsupported Media Type";
            case 416:
                return "Range Not Satisfiable";
            case 417:
                return "Expectation Failed";
            case 418:
                return "I'm a teapot";
            case 421:
                return "Misdirected Request";
            case 422:
                return "Unprocessable Entity";
            case 423:
                return "Locked";
            case 424:
                return "Failed Dependency";
            case 426:
                return "Upgrade Required";
            case 428:
                return "Precondition Required";
            case 429:
                return "Too Many Requests";
            case 431:
                return "Request Header Fields Too Large";
            case 450:
                return "Blocked by Windows Parental Controls";
            case 451:
                return "Unavailable For Legal Reasons";
            case 500:
                return "Internal Server Error";
            case 501:
                return "Not Implemented";
            case 502:
                return "Bad Gateway";
            case 503:
                return "Service Unavailable";
            case 504:
                return "Gateway Time-out";
            case 505:
                return "HTTP Version Not Supported";
            case 506:
                return "Variant Also Negotiaties";
            case 507:
                return "Insufficient Storage";
            case 508:
                return "Loop Detected";
            case 510:
                return "Not Extended";
            case 511:
                return "Network Authentication Required";
            default:
                return "Not implemented or invalid HTTP code";
        }
    }

}