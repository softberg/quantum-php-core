<?php

function createEnvFile(): void
{
    if (!file_exists(PROJECT_ROOT . DS . '.env.testing')) {
        copy(
            PROJECT_ROOT . DS . '.env.example',
            PROJECT_ROOT . DS . '.env.testing'
        );
    }
}

function removeEnvFile(): void
{
    if (file_exists(PROJECT_ROOT . DS . '.env.testing')) {
        unlink(PROJECT_ROOT . DS . '.env.testing');
    }
}
