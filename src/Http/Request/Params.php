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
 * @since 2.5.0
 */

namespace Quantum\Http\Request;

use Quantum\Libraries\Upload\File as FileUpload;
use Quantum\Libraries\Storage\FileSystem;
use Quantum\Exceptions\HttpException;
use Quantum\Environment\Server;
use Quantum\Di\Di;

/**
 * Trait Params
 * @package Quantum\Http\Request
 */
trait Params
{

    /**
     * Gets the GET params
     * @return array
     */
    private static function getParams(): array
    {
        $getParams = [];

        if (!empty($_GET)) {
            $getParams = filter_input_array(INPUT_GET, FILTER_DEFAULT) ?: [] ;
        }

        return $getParams;
    }

    /**
     * Gets the POST params
     * @return array
     */
    private static function postParams(): array
    {
        $postParams = [];

        if (!empty($_POST)) {
            $postParams = filter_input_array(INPUT_POST, FILTER_DEFAULT) ?: [];
        }

        return $postParams;
    }
    
    /**
     * Parses the raw input parameters sent via PUT, PATCH or DELETE methods
     * @return array[]
     * @throws \Quantum\Exceptions\DiException
     * @throws \Quantum\Exceptions\HttpException
     * @throws \ReflectionException
     */
    private static function parsedParams(): array
    {
        $parsedParams = [
            'params' => [],
            'files' => []
        ];

        if (in_array(self::$__method, ['PUT', 'PATCH'])) {

            $rawInput = file_get_contents('php://input');

            switch (self::$server->contentType(true)) {
                case self::CONTENT_FORM_DATA:
                    $parsedParams = self::parseRawInput($rawInput);
                    break;
                case self::CONTENT_JSON_PAYLOAD:
                    $parsedParams['params'] = json_decode($rawInput, true);
                    break;
                case self::CONTENT_URL_ENCODED:
                    parse_str(urldecode($rawInput), $result);
                    $parsedParams['params'] = $result;
                    break;
                default:
                    throw HttpException::contentTypeNotSupported();
            }
        }

        return $parsedParams;
    }

    /**
     * Parses the raw input
     * @param string $rawInput
     * @return array[]
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private static function parseRawInput(string $rawInput): array
    {
        $boundary = self::getBoundary();

        if ($boundary) {
            $blocks = self::getBlocks($boundary, $rawInput);

            return self::processBlocks($blocks);
        }

        return [
            'params' => [],
            'files' => []
        ];

    }

    /**
     * Gets the boundary
     * @return string|null
     */
    private static function getBoundary(): ?string
    {
        $contentType = (new Server)->contentType();

        if (!$contentType) {
            return null;
        }

        preg_match('/boundary=(.*)$/', $contentType, $match);

        if (!count($match)) {
            return null;
        }

        return $match[1];
    }

    /**
     * Gets the blocks
     * @param string $boundary
     * @param string $rawInput
     * @return array
     */
    private static function getBlocks(string $boundary, string $rawInput): array
    {
        $result = preg_split("/-+$boundary/", $rawInput);
        array_pop($result);
        return $result;
    }

    /**
     * Process blocks
     * @param array $blocks
     * @return array
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private static function processBlocks(array $blocks): array
    {
        $params = [];
        $files = [];

        foreach ($blocks as $id => $block) {
            if (empty($block)) {
                continue;
            }

            if (strpos($block, 'filename') !== false) {
                list($nameParam, $file) = self::getParsedFile($block);

                if(!$file) {
                    continue;
                }

                $arrayParam = self::arrayParam($nameParam);

                if (is_array($arrayParam)) {
                    list($name, $key) = $arrayParam;

                    if ($key === '') {
                        $files[$name][] = $file;
                    } else {
                        $files[$name][$key] = $file;
                    }
                } else {
                    $files[$nameParam] = $file;
                }
            } else if (strpos($block, 'application/octet-stream') !== false) {
                $params = array_merge($params, self::getParsedStream($block));
            } else {
                $params = array_merge($params, self::getParsedParameter($block));
            }
        }

        return [
            'params' => $params,
            'files' => $files
        ];
    }

    /**
     * @param string $block
     * @return array|string[]
     */
    private static function getParsedStream(string $block): array
    {
        preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $block, $match);

        return [$match[1] => $match[2] ?: ''];
    }

    /**
     * Gets the parsed file
     * @param string $block
     * @return array|\Quantum\Libraries\Upload\File[]
     * @throws \Quantum\Exceptions\DiException
     * @throws \ReflectionException
     */
    private static function getParsedFile(string $block): ?array
    {
        list($name, $filename, $type, $content) = self::parseFileData($block);

        if(!$content) {
            return null;
        }

        $fs = Di::get(FileSystem::class);

        $tempName = tempnam(sys_get_temp_dir(), 'qt_');

        $fs->put($tempName, $content);

        $file = new FileUpload([
            'name' => $filename,
            'type' => $type,
            'tmp_name' => $tempName,
            'error' => UPLOAD_ERR_OK,
            'size' => $fs->size($tempName),
        ]);

        register_shutdown_function(function () use ($fs, $tempName) {
            $fs->remove($tempName);
        });

        return [$name, $file];
    }

    /**
     * Parses the file string
     * @param string $block
     * @return array
     */
    private static function parseFileData(string $block): array
    {
        $block = ltrim($block, "\r\n");

        list($rawHeaders, $content) = explode("\r\n\r\n", $block);

        list($name, $filename, $contentType) = self::parseHeaders($rawHeaders);

        $content = substr($content, 0, strlen($content) - 2);

        return [
            $name,
            $filename,
            $contentType,
            $content
        ];
    }

    /**
     * Parses and returns the parameter
     * @param string $block
     * @return array
     */
    private static function getParsedParameter(string $block): array
    {
        $data = [];

        if (preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $match)) {
            if (preg_match('/^(.*)\[\]$/i', $match[1], $tmp)) {
                $data[$tmp[1]][] = $match[2] ?: '';
            } else {
                $data[$match[1]] = $match[2] ?: '';
            }
        }

        return $data;
    }

    /**
     * Parses the header information of the file string
     * @param string $rawHeaders
     * @return array
     */
    private static function parseHeaders(string $rawHeaders): array
    {
        $rawHeaders = explode("\r\n", $rawHeaders);

        $name = '-unknown-';
        $filename = '-unknown-';
        $contentType = 'application/octet-stream';

        foreach ($rawHeaders as $header) {

            list($key, $value) = explode(':', $header);

            if ($key == 'Content-Type') {
                $contentType = ltrim($value, ' ');
            }

            if (preg_match('/name=\"([^\"]*)\"/', $header, $match)) {
                $name = $match[1];
            }

            if (preg_match('/filename=\"([^\"]*)\"/', $header, $match)) {
                $filename = $match[1];
            }
        }

        return [$name, $filename, $contentType];
    }

    /**
     * Finds if the param is array type or not
     * @param string $parameter
     * @return array|string
     */
    private static function arrayParam(string $parameter)
    {
        if (strpos($parameter, '[') !== false) {
            if (preg_match('/^([^[]*)\[([^]]*)\](.*)$/', $parameter, $match)) {
                return [$match[1], $match[2]];
            }
        }

        return $parameter;
    }

}