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

namespace Quantum\Http\Traits\Request;

use Quantum\Libraries\Storage\Factories\FileSystemFactory;
use Quantum\Config\Exceptions\ConfigException;
use Quantum\Libraries\Storage\UploadedFile;
use Quantum\App\Exceptions\BaseException;
use Quantum\Di\Exceptions\DiException;
use Quantum\Http\Enums\ContentType;
use Quantum\Environment\Server;
use ReflectionException;

/**
 * Class RawInput
 * @package Quantum\Http
 */
trait RawInput
{

    /**
     * Parses raw input data and returns parsed parameters and files
     * @param string $rawInput
     * @return array|array[]
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    public static function parse(string $rawInput): array
    {
        $boundary = self::getBoundary();

        if (!$boundary) {
            return ['params' => [], 'files' => []];
        }

        $blocks = self::getBlocks($boundary, $rawInput);

        return self::processBlocks($blocks);
    }

    /**
     * Extracts boundary string from Content-Type header
     * @return string|null
     */
    private static function getBoundary(): ?string
    {
        $contentType = Server::getInstance()->contentType();

        if (!$contentType) {
            return null;
        }

        preg_match('/boundary=(.*)$/', $contentType, $match);

        return $match[1] ?? null;
    }

    /**
     * Splits raw input into multipart blocks
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
     * Processes multipart blocks and extracts parameters and files
     * @param array $blocks
     * @return array
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function processBlocks(array $blocks): array
    {
        $params = [];
        $files = [];

        foreach ($blocks as $block) {
            $block = trim($block);

            if ($block === '') {
                continue;
            }

            $type = self::detectBlockType($block);

            switch ($type) {
                case 'file':
                    [$nameParam, $file] = self::getParsedFile($block);

                    if (!$file) {
                        continue 2;
                    }

                    self::addFileToCollection($files, $nameParam, $file);
                    break;

                case 'stream':
                    $params += self::getParsedStream($block);
                    break;

                case 'param':
                default:
                    $params += self::getParsedParameter($block);
                    break;
            }
        }

        return ['params' => $params, 'files' => $files];
    }

    /**
     * Adds a parsed file to the files collection
     * @param array $files
     * @param string $nameParam
     * @param UploadedFile $file
     */
    private static function addFileToCollection(array &$files, string $nameParam, UploadedFile $file)
    {
        $arrayParam = self::arrayParam($nameParam);

        if (is_array($arrayParam)) {
            [$name, $key] = $arrayParam;

            if ($key === '') {
                $files[$name][] = $file;
            } else {
                $files[$name][$key] = $file;
            }
        } else {
            $files[$nameParam] = $file;
        }
    }

    /**
     * Detects the block type as a string identifier.
     * @param string $block
     * @return string One of 'file', 'stream', 'param'
     */
    private static function detectBlockType(string $block): string
    {
        if (strpos($block, 'filename') !== false) {
            return 'file';
        }

        if (strpos($block, ContentType::OCTET_STREAM) !== false) {
            return 'stream';
        }

        return 'param';
    }

    /**
     * Gets the parsed param
     * @param string $block
     * @return array
     */
    private static function getParsedStream(string $block): array
    {
        preg_match('/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s', $block, $match);

        return [$match[1] => $match[2] ?? ''];
    }

    /**
     * Gets the parsed file
     * @param string $block
     * @return array|null
     * @throws BaseException
     * @throws ConfigException
     * @throws DiException
     * @throws ReflectionException
     */
    private static function getParsedFile(string $block): ?array
    {
        [$name, $filename, $type, $content] = self::parseFileData($block);

        if (!$content) {
            return null;
        }

        $fs = FileSystemFactory::get();
        $tempName = tempnam(sys_get_temp_dir(), 'qt_');
        $fs->put($tempName, $content);

        $file = new UploadedFile([
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
     * Parses a file block into metadata and binary content
     * @param string $block
     * @return array{string, string, string, string}
     */
    private static function parseFileData(string $block): array
    {
        $block = ltrim($block, "\r\n");

        $parts = explode("\r\n\r\n", $block, 2);

        if (count($parts) < 2) {
            return ['-unknown-', '-unknown-', ContentType::OCTET_STREAM, ''];
        }

        [$rawHeaders, $content] = $parts;

        [$name, $filename, $contentType] = self::parseHeaders($rawHeaders);

        $content = substr($content, 0, strlen($content) - 2);

        return [
            $name,
            $filename,
            $contentType,
            $content
        ];
    }

    /**
     * Parses a block and extracts normal form parameters
     * @param string $block
     * @return array
     */
    private static function getParsedParameter(string $block): array
    {
        $data = [];

        $block = trim($block);

        if (preg_match('/name="([^"]+)"\s*\r?\n\r?\n(.*)/s', $block, $match)) {
            if (preg_match('/^(.*)\[\]$/i', $match[1], $tmp)) {
                $data[$tmp[1]][] = rtrim($match[2]);
            } else {
                $data[$match[1]] = rtrim($match[2]);
            }
        }

        return $data;
    }

    /**
     * Extracts name, filename, and content type from header lines
     * @param string $rawHeaders
     * @return array{string, string, string}
     */
    private static function parseHeaders(string $rawHeaders): array
    {
        $name = '-unknown-';
        $filename = '-unknown-';
        $contentType = ContentType::OCTET_STREAM;

        $rawHeaders = preg_replace("/\r\n|\r|\n/", "\n", $rawHeaders);
        $lines = explode("\n", $rawHeaders);

        foreach ($lines as $line) {
            if (stripos($line, 'Content-Disposition') !== false) {
                if (preg_match('/name="([^"]+)"/', $line, $match)) {
                    $name = $match[1];
                }

                if (preg_match('/filename="([^"]*)"/', $line, $match)) {
                    $filename = $match[1];
                }
            }

            if (stripos($line, 'Content-Type') !== false && preg_match('/Content-Type:\s*(.+)/i', $line, $match)) {
                $contentType = trim($match[1]);
            }
        }

        return [$name, $filename, $contentType];
    }

    /**
     * Parses array-like parameter names
     * @param string $parameter
     * @return array|string
     */
    private static function arrayParam(string $parameter)
    {
        if (strpos($parameter, '[') !== false && preg_match('/^([^[]*)\[([^]]*)\](.*)$/', $parameter, $match)) {
            return [$match[1], $match[2]];
        }

        return $parameter;
    }
}