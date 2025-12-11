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

namespace Quantum\App\Enums;

/**
 * Class ReservedKeys
 * @package Quantum\App
 */
final class ReservedKeys
{

    /**
     * Internal response key for rendered view
     */
    public const RENDERED_VIEW = '_qt_rendered_view';

    /**
     * Internal session key for previous request
     */
    public const PREV_REQUEST = '_qt_prev_request';

}