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
 * @since 2.9.9
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
    const PAGINATION_CLASS = 'pagination';

    /**
     * Active class name
     */
    const PAGINATION_CLASS_ACTIVE = 'active';

    /**
     * Parameter name for per page
     */
    const PER_PAGE = 'per_page';

    /**
     * Parameter name for page number
     */
    const PAGE = 'page';

    /**
     * First page number
     */
    const FIRST_PAGE_NUMBER = 1;

    /**
     * Minimum page items count
     */
    const MINIMUM_PAGE_ITEMS_COUNT = 3;

    /**
     * Edge padding
     */
    const EDGE_PADDING = 3;
}