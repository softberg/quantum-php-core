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
 * @since 3.0.0
 */

namespace Quantum\Http\Traits\Response;

use Quantum\Http\Exceptions\HttpException;
use Quantum\App\Enums\ReservedKeys;
use Quantum\Http\Enums\ContentType;
use SimpleXMLElement;
use DOMDocument;
use Exception;

/**
 * Trait Body
 * @package Quantum\Http\Response
 */
trait Body
{
    /**
     * Response
     */
    private static array $__response = [];

    /**
     * @var string[]
     */
    private static array $formatters = [
        ContentType::HTML => 'formatHtml',
        ContentType::XML => 'formatXml',
        ContentType::JSON => 'formatJson',
        ContentType::JSONP => 'formatJsonp',
    ];

    /**
     * Checks if response contains a data by given key
     */
    public static function has(string $key): bool
    {
        return isset(self::$__response[$key]);
    }

    /**
     * Gets the data from response by given key
     * @return mixed
     */
    public static function get(string $key, ?string $default = null)
    {
        return self::has($key) ? self::$__response[$key] : $default;
    }

    /**
     * Sets new key/value pair into response
     * @param mixed $value
     */
    public static function set(string $key, $value): void
    {
        self::$__response[$key] = $value;
    }

    /**
     * Gets all response parameters
     */
    public static function all(): array
    {
        return self::$__response;
    }

    /**
     * Deletes the element from response by given key
     */
    public static function delete(string $key): void
    {
        if (self::has($key)) {
            unset(self::$__response[$key]);
        }
    }

    /**
     * Prepares the JSON response
     */
    public static function json(?array $data = null, ?int $code = null): void
    {
        self::setContentType(ContentType::JSON);

        if (!is_null($code)) {
            self::setStatusCode($code);
        }

        if ($data) {
            self::$__response = array_merge(self::$__response, $data);
        }
    }

    /**
     * Prepares the JSONP response
     */
    public static function jsonp(string $callback, ?array $data = null, ?int $code = null): void
    {
        self::setContentType(ContentType::JSONP);

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
     */
    public static function getJsonPData(array $data): string
    {
        return self::$callbackFunction . '(' . json_encode($data) . ')';
    }

    /**
     * Prepares the XML response
     */
    public static function xml(?array $data = null, $root = '<data></data>', ?int $code = null): void
    {
        self::setContentType(ContentType::XML);

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
     */
    public static function html(string $html, ?int $code = null): void
    {
        self::setContentType(ContentType::HTML);

        if (!is_null($code)) {
            self::setStatusCode($code);
        }

        self::$__response[ReservedKeys::RENDERED_VIEW] = $html;
    }

    /**
     * Gets the response content
     * @throws HttpException
     */
    public static function getContent(): string
    {
        $contentType = self::getContentType();

        if (!isset(self::$formatters[$contentType])) {
            throw new HttpException("Unsupported content type: {$contentType}");
        }

        $formatterMethod = self::$formatters[$contentType];

        return self::$formatterMethod();
    }

    /**
     * Transforms array to XML
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
     */
    private static function composeXML(array $arr, SimpleXMLElement &$simpleXML): void
    {
        foreach ($arr as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item' . $key;
            }

            $tag = $key;
            $attributes = null;

            if (strpos($key, '@') !== false) {
                [$tag, $attributes] = explode('@', $key);
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

    /**
     * Formats data as JSON
     */
    private static function formatJson(): string
    {
        return json_encode(self::all(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Formats data as XML
     * @throws Exception
     */
    private static function formatXml(): string
    {
        return self::arrayToXml(self::all());
    }

    /**
     * Formats data as HTML
     */
    private static function formatHtml(): string
    {
        return self::get(ReservedKeys::RENDERED_VIEW) ?? '';
    }

    /**
     * Formats data as JSONP
     */
    private static function formatJsonp(): string
    {
        return self::getJsonPData(self::all());
    }
}
