<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.0.0
 */

namespace Quantum\Http;

use SimpleXMLElement;
use DOMDocument;

/**
 * Class HttpResponse
 * @package Quantum\Http
 */
abstract class HttpResponse
{

    /**
     * HTML content type
     */
    const CONTENT_HTML = 'text/html';

    /**
     * XML content type
     */
    const CONTENT_XML = 'application/xml';

    /**
     * JSON content type
     */
    const CONTENT_JSON = 'application/json';

    /**
     * Response headers
     * @var type 
     */
    private static $__headers = [];

    /**
     * Status code
     * @var int 
     */
    private static $__statusCode = 200;

    /**
     * Response
     * @var array
     */
    private static $__response = [];

    /**
     * XML root element
     * @var string 
     */
    private static $xmlRoot = '<data></data>';

    /**
     * Status texts
     *
     * @var array
     */
    public static $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Reserved for WebDAV advanced collections expired proposal',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates (Experimental)',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];

    /**
     * Sends all response data to the client and finishes the request.
     */
    public static function send()
    {
        foreach (self::$__headers as $key => $value) {
            header($key . ': ' . $value);
        }

        echo self::getContent();
    }

    /**
     * Gets the response content
     * @return string
     */
    public static function getContent(): string
    {
        $content = '';

        switch (self::getContentType()) {
            case self::CONTENT_JSON:
                $content = json_encode(self::all());
                break;
            case self::CONTENT_XML:
                $content = self::arrayToXml(self::all());
                break;
            case self::CONTENT_HTML:
                $content = self::get('_qt_rendered_view');
                break;
            default :
                break;
        }

        return $content;
    }

    public static function flush()
    {
        self::$__headers = [];
        self::$__statusCode = 200;
        self::$__response = [];
    }

    /**
     * Sets new key/value pair into response
     * @param string $key
     * @param mixed $value
     */
    public static function set($key, $value)
    {
        self::$__response[$key] = $value;
    }

    /**
     * Checks if response contains a data by given key
     * @param $key
     * @return bool
     */
    public static function has($key)
    {
        return isset(self::$__response[$key]);
    }

    /**
     * Gets the data from response by given key
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        return self::has($key) ? self::$__response[$key] : $default;
    }

    /**
     * Gets all response parameters
     * @return array
     */
    public static function all()
    {
        return self::$__response;
    }

    /**
     * Deletes the element from response by given key
     * @param type $key
     */
    public static function delete($key)
    {
        if (self::has($key)) {
            unset(self::$__response[$key]);
        }
    }

    /**
     * Sets the response header
     * @param string $key
     * @param string $value
     */
    public static function setHeader($key, $value)
    {
        self::$__headers[$key] = $value;
    }

    /**
     * Checks the response header existence by given key
     * @param string $key
     * @return bool
     */
    public static function hasHeader($key)
    {
        return isset(self::$__headers[$key]);
    }

    /**
     * Gets the response header by given key
     * @param $key
     * @return mixed|null
     */
    public static function getHeader($key)
    {
        return self::hasHeader($key) ? self::$__headers[$key] : null;
    }

    /**
     * Get all response headers
     * @return array
     */
    public static function allHeaders()
    {
        return self::$__headers;
    }

    /**
     * Deletes the header by given key
     * @param string $key
     */
    public static function deleteHeader($key)
    {
        if (self::hasHeader($key)) {
            unset(self::$__headers[$key]);
        }
    }

    /**
     * Set the status code
     * @param integer $code
     */
    public static function setStatusCode(int $code)
    {
        if (!array_key_exists($code, self::$statusTexts)) {
            throw new \InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }

        self::$__statusCode = $code;
    }

    /**
     * Gets the status code
     * @return int
     */
    public static function getStatusCode()
    {
        return self::$__statusCode;
    }

    /**
     * Gets the status text
     * @return string
     */
    public static function getStatusText()
    {
        return self::$statusTexts[self::$__statusCode];
    }

    /**
     * Sets the content type
     * @param string $contentType
     */
    public static function setContentType($contentType)
    {
        self::setHeader('Content-Type', $contentType);
    }

    /**
     * Gets the content type
     */
    public static function getContentType()
    {
        return self::getHeader('Content-Type');
    }

    /**
     * Redirect
     * @param string $url
     * @param int|null $code
     */
    public static function redirect($url, $code = null)
    {
        if ($code) {
            self::setStatusCode($code);
        }

        self::setHeader('Location', $url);

        self::send();
    }

    /**
     * Prepares the JSON response
     * @param array|null $data
     * @param int|null $code
     */
    public static function json(array $data = null, $code = null)
    {
        self::setContentType(self::CONTENT_JSON);

        if ($code) {
            self::setStatusCode($code);
        }

        if ($data) {
            foreach ($data as $key => $value) {
                self::$__response[$key] = $value;
            }
        }
    }

    /**
     * Prepares the XML response
     * @param array|null $data
     * @param int|null $code
     */
    public static function xml(array $data = null, $root = '<data></data>', $code = null)
    {
        self::setContentType(self::CONTENT_XML);

        self::$xmlRoot = $root;

        if ($code) {
            self::setStatusCode($code);
        }

        if ($data) {
            foreach ($data as $key => $value) {
                self::$__response[$key] = $value;
            }
        }
    }

    /**
     * Prepares the HTML content
     * @param string $html
     * @param int|null $code
     */
    public static function html(string $html, $code = null)
    {
        self::setContentType(self::CONTENT_HTML);

        if ($code) {
            self::setStatusCode($code);
        }

        self::$__response['_qt_rendered_view'] = $html;
    }

    /**
     * Transforms array to XML
     * @param array $arr
     * @param string $root
     * @return string
     */
    private static function arrayToXML(array $arr)
    {
//        dump(['<data></data>', self::$xmlRoot]);
        
        $simpleXML = new SimpleXMLElement(self::$xmlRoot);
        self::composeXML($arr, $simpleXML);

        $dom = new DOMDocument();
        $dom->loadXML($simpleXML->asXML());
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    private static function composeXML(array $arr, &$simpleXML)
    {
        foreach ($arr as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item' . $key;
            }

            $tag = $key;
            $attributes = null;

            if (strpos($key, '@') !== false) {
                list($tag, $attributes) = explode('@', $key);
                $attributes = json_decode($attributes);
            }

            if (is_array($value)) {
                $child = $simpleXML->addChild($tag);
                if ($attributes) {
                    foreach ($attributes as $attrKey => $attrVal) {
                        $child->addAttribute($attrKey, $attrVal);
                    }
                }

                self::composeXML($value, $child);
            } else {
                $child = $simpleXML->addChild($tag, htmlspecialchars($value));

                if ($attributes) {
                    foreach ($attributes as $attrKey => $attrVal) {
                        $child->addAttribute($attrKey, $attrVal);
                    }
                }
            }
        }
    }

}
