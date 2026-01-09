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

use Quantum\Http\Enums\StatusCode;
use InvalidArgumentException;

/**
 * Trait Status
 * @package Quantum\Http\Response
 */
trait Status
{
    /**
     * Status code
     * @var int
     */
    private static $__statusCode = StatusCode::OK;

    /**
     * Status texts
     * @var string[]
     */
    private static $texts = [
        StatusCode::CONTINUE => 'Continue',
        StatusCode::SWITCHING_PROTOCOLS => 'Switching Protocols',
        StatusCode::PROCESSING => 'Processing',
        StatusCode::EARLY_HINTS => 'Early Hints',

        StatusCode::OK => 'OK',
        StatusCode::CREATED => 'Created',
        StatusCode::ACCEPTED => 'Accepted',
        StatusCode::NON_AUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        StatusCode::NO_CONTENT => 'No Content',
        StatusCode::RESET_CONTENT => 'Reset Content',
        StatusCode::PARTIAL_CONTENT => 'Partial Content',
        StatusCode::MULTI_STATUS => 'Multi-Status',
        StatusCode::ALREADY_REPORTED => 'Already Reported',
        StatusCode::IM_USED => 'IM Used',

        StatusCode::MULTIPLE_CHOICES => 'Multiple Choices',
        StatusCode::MOVED_PERMANENTLY => 'Moved Permanently',
        StatusCode::FOUND => 'Found',
        StatusCode::SEE_OTHER => 'See Other',
        StatusCode::NOT_MODIFIED => 'Not Modified',
        StatusCode::USE_PROXY => 'Use Proxy',
        StatusCode::SWITCH_PROXY => 'Switch Proxy',
        StatusCode::TEMPORARY_REDIRECT => 'Temporary Redirect',
        StatusCode::PERMANENT_REDIRECT => 'Permanent Redirect',

        StatusCode::BAD_REQUEST => 'Bad Request',
        StatusCode::UNAUTHORIZED => 'Unauthorized',
        StatusCode::PAYMENT_REQUIRED => 'Payment Required',
        StatusCode::FORBIDDEN => 'Forbidden',
        StatusCode::NOT_FOUND => 'Not Found',
        StatusCode::METHOD_NOT_ALLOWED => 'Method Not Allowed',
        StatusCode::NOT_ACCEPTABLE => 'Not Acceptable',
        StatusCode::PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        StatusCode::REQUEST_TIMEOUT => 'Request Timeout',
        StatusCode::CONFLICT => 'Conflict',
        StatusCode::GONE => 'Gone',
        StatusCode::LENGTH_REQUIRED => 'Length Required',
        StatusCode::PRECONDITION_FAILED => 'Precondition Failed',
        StatusCode::PAYLOAD_TOO_LARGE => 'Payload Too Large',
        StatusCode::URI_TOO_LONG => 'URI Too Long',
        StatusCode::UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        StatusCode::RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
        StatusCode::EXPECTATION_FAILED => 'Expectation Failed',
        StatusCode::MISDIRECTED_REQUEST => 'Misdirected Request',
        StatusCode::UNPROCESSABLE_ENTITY => 'Unprocessable Entity',
        StatusCode::LOCKED => 'Locked',
        StatusCode::FAILED_DEPENDENCY => 'Failed Dependency',
        StatusCode::TOO_EARLY => 'Too Early',
        StatusCode::UPGRADE_REQUIRED => 'Upgrade Required',
        StatusCode::PRECONDITION_REQUIRED => 'Precondition Required',
        StatusCode::TOO_MANY_REQUESTS => 'Too Many Requests',
        StatusCode::REQUEST_HEADER_FIELDS_TOO_LARGE => 'Request Header Fields Too Large',
        StatusCode::UNAVAILABLE_FOR_LEGAL_REASONS => 'Unavailable For Legal Reasons',

        StatusCode::INTERNAL_SERVER_ERROR => 'Internal Server Error',
        StatusCode::NOT_IMPLEMENTED => 'Not Implemented',
        StatusCode::BAD_GATEWAY => 'Bad Gateway',
        StatusCode::SERVICE_UNAVAILABLE => 'Service Unavailable',
        StatusCode::GATEWAY_TIMEOUT => 'Gateway Timeout',
        StatusCode::HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not Supported',
        StatusCode::VARIANT_ALSO_NEGOTIATES => 'Variant Also Negotiates',
        StatusCode::INSUFFICIENT_STORAGE => 'Insufficient Storage',
        StatusCode::LOOP_DETECTED => 'Loop Detected',
        StatusCode::NOT_EXTENDED => 'Not Extended',
        StatusCode::NETWORK_AUTHENTICATION_REQUIRED => 'Network Authentication Required',
    ];

    /**
     * Gets the reason phrase for a given HTTP status code.
     * @param int $code
     * @return string
     */
    public static function getText(int $code): string
    {
        if (!isset(self::$texts[$code])) {
            throw new InvalidArgumentException("Unknown HTTP status code: {$code}");
        }

        return self::$texts[$code];
    }

    /**
     * Sets the status code
     * @param int $code
     */
    public static function setStatusCode(int $code)
    {
        if (!isset(self::$texts[$code])) {
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
        return self::getText(self::$__statusCode);
    }
}
