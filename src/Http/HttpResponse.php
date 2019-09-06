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
 * @since 1.0.0
 */

namespace Quantum\Http;

use Quantum\Exceptions\ExceptionMessages;

/**
 * HttpResponse Class
 * 
 * Abstract base http response class
 * 
 * @package Quantum
 * @subpackage Http
 * @category Http
 */
abstract class HttpResponse {
    
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
     * Set status
     * 
     * @param integer $status
     * @return void
     */
    public static function setStatus($status) {
        header('HTTP/1.1 ' . $status . ' ' . self::$statusTexts[$status]);
    }
    
    /**
     * Set content type
     * 
     * @param string $contentType
     * @return void
     */
    public static function setContentType($contentType) {
        self::setHeader('Content-Type', $contentType);
    }
    
    /**
     * Set Сross Site Request Forgery Token
     * 
     * @param string $csrfToken
     * @return void
     */
    public static function setCSRFToken($csrfToken) {
        self::setHeader('X-CSRF-Token', $csrfToken);
    }

    /**
     * Set header
     * 
     * @param string $key
     * @param string $value
     */
    public static function setHeader($key, $value) {
        header($key . ': ' . $value);
    }

    /**
     * JSON output
     *
     * Outputs JSON response
     *
     * @param mixed $data
     * @param integer $status
     */
    public static function json($data, $status = null) {
        if ($status) {
            self::setStatus($status);
        }

        self::setContentType('application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * XML output
     *
     * Outputs XML response
     *
     * @param array $arr
     */
    public static function xml(array $arr) {
        $simpleXML = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');
        self::arrayToXML($arr, $simpleXML);

        self::setContentType('application/xml');
        echo $simpleXML->asXML();
        exit;
    }

    /**
     * ArrayToXML
     *
     * Transforms array to XML
     *
     * @param array $arr
     * @param object $simpleXML
     * @return void
     */
    private function arrayToXML(array $arr, &$simpleXML) {
        foreach ($arr as $key => $value) {
            if (is_numeric($key)) {
                $key = 'item' . $key;
            }
            if (is_array($value)) {
                $subnode = $simpleXML->addChild($key);
                self::arrayToXML($value, $subnode);
            } else {
                $simpleXML->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }

}
