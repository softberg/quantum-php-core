<?php

return [
    'name' => 'failing-task',
    'expression' => '* * * * *',
    'callback' => function () {
        throw new \Exception('Execution failed');
    },
];
