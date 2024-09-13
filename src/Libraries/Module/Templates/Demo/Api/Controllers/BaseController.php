<?php

return '<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 2.9.0
 */

namespace Modules\Api\Controllers;

use Quantum\Mvc\QtController;

/**
 * Class BaseController
 * @package Modules\Api\Controllers
 */
abstract class BaseController extends QtController
{

    /**
     * Status error
     */
    const STATUS_ERROR = \'error\';

    /**
     * Status success
     */
    const STATUS_SUCCESS = \'success\';

    /**
     * CSRF verification
     * @var bool
     */
    public $csrfVerification = false;

}';
