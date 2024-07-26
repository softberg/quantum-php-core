<?php

return '<?php

use Quantum\Factory\ViewFactory;
use Quantum\Http\Response;

return function ($route) {
    $route->get(\'/\', \'MainController\', \'index\');
};';