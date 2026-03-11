<?php

namespace Quantum\Tests\Unit\Session;

use Quantum\Database\Adapters\Idiorm\IdiormDbal;

trait TestCaseHelper
{
    private function _createSessionsTable(): void
    {
        IdiormDbal::execute(
            'CREATE TABLE IF NOT EXISTS sessions (
                        id INTEGER PRIMARY KEY,
                        session_id VARCHAR(255) UNIQUE,
                        data VARCHAR(255),
                        ttl INTEGER(11)
                    )'
        );

    }
}
