<?php

function createEnvFile()
{
    if (!file_exists(PROJECT_ROOT . DS . '.env.testing')) {
        copy(
            PROJECT_ROOT . DS . '.env.example',
            PROJECT_ROOT . DS . '.env.testing'
        );
    }
}

function removeEnvFile()
{
    if (file_exists(PROJECT_ROOT . DS . '.env.testing')) {
        unlink(PROJECT_ROOT . DS . '.env.testing');
    }
}
