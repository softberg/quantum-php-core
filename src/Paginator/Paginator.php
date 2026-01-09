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

namespace Quantum\Paginator;

use Quantum\Paginator\Exceptions\PaginatorException;
use Quantum\Paginator\Contracts\PaginatorInterface;
use Quantum\App\Exceptions\BaseException;

/**
 * Class Paginator
 * @package Quantum\Paginator
 * @method mixed data()
 * @method mixed firstItem()
 * @method mixed lastItem()
 * @method int currentPageNumber()
 * @method int|null previousPageNumber()
 * @method int|null nextPageNumber()
 * @method int lastPageNumber()
 * @method string|null currentPageLink(bool $withBaseUrl = false)
 * @method string|null firstPageLink(bool $withBaseUrl = false)
 * @method string|null previousPageLink(bool $withBaseUrl = false)
 * @method string|null nextPageLink(bool $withBaseUrl = false)
 * @method string|null lastPageLink(bool $withBaseUrl = false)
 * @method int perPage()
 * @method int total()
 * @method array links(bool $withBaseUrl = false)
 * @method string|null getPagination(bool $withBaseUrl = false, ?int $pageItemsCount = null)
 */
class Paginator
{
    /**
     * Array paginator type
     */
    public const ARRAY = 'array';

    /**
     * Model paginator type
     */
    public const MODEL = 'model';

    /**
     * @var PaginatorInterface
     */
    private $adapter;

    /**
     * @param PaginatorInterface $adapter
     */
    public function __construct(PaginatorInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * Get the adapter instance
     * @return PaginatorInterface
     */
    public function getAdapter(): PaginatorInterface
    {
        return $this->adapter;
    }

    /**
     * @param string $method
     * @param array|null $arguments
     * @return mixed
     * @throws BaseException
     */
    public function __call(string $method, ?array $arguments)
    {
        if (!method_exists($this->adapter, $method)) {
            throw PaginatorException::methodNotSupported($method, get_class($this->adapter));
        }

        return $this->adapter->$method(...$arguments);
    }
}
