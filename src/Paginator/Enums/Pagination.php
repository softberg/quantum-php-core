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

namespace Quantum\Paginator\Enums;

/**
 * Class Pagination
 * @package Quantum\Paginator
 */
class Pagination
{
    /**
     * Pagination class name
     */
    public const PAGINATION_CLASS = 'pagination';

    /**
     * Active class name
     */
    public const PAGINATION_CLASS_ACTIVE = 'active';

    /**
     * Parameter name for per page
     */
    public const PER_PAGE = 'per_page';

    /**
     * Parameter name for page number
     */
    public const PAGE = 'page';

    /**
     * First page number
     */
    public const FIRST_PAGE_NUMBER = 1;

    /**
     * Minimum page items count
     */
    public const MINIMUM_PAGE_ITEMS_COUNT = 3;

    /**
     * Edge padding
     */
    public const EDGE_PADDING = 3;
}
