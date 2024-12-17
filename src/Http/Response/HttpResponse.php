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
 * @since 2.9.5
 */

namespace Quantum\Http\Response;

use Quantum\Exceptions\StopExecutionException;
use Quantum\Exceptions\LangException;
use Quantum\Exceptions\HttpException;
use InvalidArgumentException;
use SimpleXMLElement;
use DOMDocument;
use Exception;

/**
 * Class HttpResponse
 * @package Quantum\Http\Response
 */
abstract class HttpResponse
{

    use Header;
    use Body;

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
     * JSONP content type
     */
    const CONTENT_JSONP = 'application/javascript';

    /**
     * Status code
     * @var int
     */
    private static $__statusCode = 200;

    /**
     * XML root element
     * @var string
     */
    private static $xmlRoot = '<data></data>';

    /**
     * Status texts
     * @var array
     */
    public static $statusTexts = [];

    /**
     * Callback function
     * @var string
     */
    private static $callbackFunction = '';


    /**
     * @var bool
     */
    private static $initialized = false;

    /**
     * Initialize the Response
     * @throws HttpException
     * @throws LangException
     */
    public static function init()
    {
        if (self::$initialized) {
            return;
        }

        if (empty(self::$statusTexts)) {
            self::$statusTexts = require_once 'statuses.php';
        }

        self::$initialized = true;
    }

    /**
     * Flushes the response header and body
     */
    public static function flush()
    {
        self::$__statusCode = 200;
        self::$__headers = [];
        self::$__response = [];
    }

    /**
     * Sends all response data to the client and finishes the request.
     * @throws Exception
     */
    public static function send()
    {
        foreach (self::$__headers as $key => $value) {
            header($key . ': ' . $value);
        }

        http_response_code(self::getStatusCode());

        echo self::getContent();
    }

    /**
     * Gets the response content
     * @return string
     * @throws Exception
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
            case self::CONTENT_JSONP:
                $content = self::getJsonPData(self::all());
                break;
            default :
                break;
        }

        return $content;
    }

    /**
     * Set the status code
     * @param int $code
     */
    public static function setStatusCode(int $code)
    {
        if (!array_key_exists($code, self::$statusTexts)) {
            throw new InvalidArgumentException(sprintf('The HTTP status code "%s" is not valid.', $code));
        }

        self::$__statusCode = $code;
    }

    /**
     * Gets the status code
     * @return int
     */
    public static function getStatusCode(): int
    {
        return self::$__statusCode;
    }

    /**
     * Gets the status text
     * @return string
     */
    public static function getStatusText(): string
    {
        return self::$statusTexts[self::$__statusCode];
    }

    /**
     * Redirect
     * @param string $url
     * @param int $code
     * @throws StopExecutionException
     */
    public static function redirect(string $url, int $code = 302)
    {
        self::setStatusCode($code);
        self::setHeader('Location', $url);
        stop();
    }

    /**
     * Prepares the JSON response
     * @param array|null $data
     * @param int|null $code
     */
    public static function json(array $data = null, int $code = null)
    {
        self::setContentType(self::CONTENT_JSON);

        if (!is_null($code)) {
            self::setStatusCode($code);
        }

        if ($data) {
            self::$__response = array_merge(self::$__response, $data);
        }
    }

    /**
     * Prepares the JSONP response
     * @param string $callback
     * @param array|null $data
     * @param int|null $code
     */
    public static function jsonp(string $callback, ?array $data = null, int $code = null)
    {
        self::setContentType(self::CONTENT_JSONP);

        self::$callbackFunction = $callback;

        if (!is_null($code)) {
            self::setStatusCode($code);
        }

        if ($data) {
            self::$__response = array_merge(self::$__response, $data);
        }
    }

    /**
     * Returns response with function
     * @param array $data
     * @return string
     */
    public static function getJsonPData(array $data): string
    {
        return self::$callbackFunction . '(' . json_encode($data) . ")";
    }

    /**
     * Prepares the XML response
     * @param array|null $data
     * @param int|null $code
     */
    public static function xml(array $data = null, $root = '<data></data>', int $code = null)
    {
        self::setContentType(self::CONTENT_XML);

        if (!is_null($code)) {
            self::setStatusCode($code);
        }

        self::$xmlRoot = $root;

        if ($data) {
            self::$__response = array_merge(self::$__response, $data);
        }
    }

    /**
     * Prepares the HTML content
     * @param string $html
     * @param int|null $code
     */
    public static function html(string $html, int $code = null)
    {
        self::setContentType(self::CONTENT_HTML);

        if (!is_null($code)) {
            self::setStatusCode($code);
        }

        self::$__response['_qt_rendered_view'] = $html;
    }

    /**
     * Transforms array to XML
     * @param array $arr
     * @return string
     * @throws Exception
     */
    private static function arrayToXML(array $arr): string
    {
        $simpleXML = new SimpleXMLElement(self::$xmlRoot);
        self::composeXML($arr, $simpleXML);

        $dom = new DOMDocument();
        $dom->loadXML($simpleXML->asXML());
        $dom->formatOutput = true;
        return $dom->saveXML();
    }

    /**
     * Compose XML
     * @param array $arr
     * @param SimpleXMLElement $simpleXML
     */
    private static function composeXML(array $arr, SimpleXMLElement &$simpleXML)
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
