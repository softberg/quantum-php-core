<?php

namespace Quantum\Tests\Unit\Paginator;

use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Tests\Unit\AppTestCase;

class PaginatorTestCase extends AppTestCase
{

    public function setUp(): void
    {
        parent::setUp();

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->_createPostTableWithData();
    }

    public function tearDown(): void
    {
        IdiormDbal::execute("DROP TABLE IF EXISTS posts");
    }

    private function _createPostTableWithData()
    {
        IdiormDbal::execute("CREATE TABLE IF NOT EXISTS posts (
                        id INTEGER PRIMARY KEY,
                        title VARCHAR(255),
                        content VARCHAR(255),
                        author VARCHAR(255),
                        published_at DATETIME,
                        created_at DATETIME
                    )");

        IdiormDbal::execute("INSERT INTO
            posts
                (title, content, author, published_at, created_at)
            VALUES
                ('Hi', 'First post!', 'John Doe', '2020-01-05 12:00:00', '2020-01-04 20:28:33'),
                ('Hey', 'Hello world', 'Jane Du', '2020-03-15 14:30:00', '2020-03-14 10:15:12'),
                ('News', 'Big update', 'Benjamin Gentry', '2020-04-15 09:45:00', '2020-04-14 10:15:12'),
                ('Note', 'Quick tip', 'Rosa Briggs', '2020-05-15 11:20:00', '2020-05-14 10:15:12'),
                ('FYI', 'Just info', 'Nola Ho', '2020-06-15 16:10:00', '2020-06-14 10:15:12')
        ");
    }
}