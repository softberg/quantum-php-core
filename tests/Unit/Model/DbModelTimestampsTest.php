<?php

namespace Quantum\Tests\Unit\Model;

use Quantum\Tests\_root\shared\Models\TestPostCustomTimestampModel;
use Quantum\Tests\_root\shared\Models\TestPostUnixTimestampModel;
use Quantum\Tests\_root\shared\Models\TestPostTimestampModel;
use Quantum\Libraries\Database\Adapters\Idiorm\IdiormDbal;
use Quantum\Tests\_root\shared\Models\TestPostModel;
use Quantum\Model\Factories\ModelFactory;
use Quantum\Tests\Unit\AppTestCase;

class DbModelTimestampsTest extends AppTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set('app.debug', true);

        IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

        $this->createPostsTable();
    }

    public function tearDown(): void
    {
        IdiormDbal::execute('DROP TABLE posts');
        IdiormDbal::execute('DROP TABLE posts_custom');
    }

    public function testTimestampsAreNotAppliedWhenTraitIsNotUsed()
    {
        /** @var TestPostModel $model */
        $model = ModelFactory::get(TestPostModel::class);

        $post = $model->create();

        $post->title = 'Hello';
        $post->content = 'World';
        $post->author = 'John';
        $post->published_at = '2026-01-23 10:00:00';

        $this->assertTrue($post->save());

        $saved = $model->findOne($post->id);

        $this->assertNotNull($saved);

        $data = $saved->asArray();

        $this->assertArrayHasKey('created_at', $data);
        $this->assertNull($data['created_at']);

        $this->assertArrayHasKey('updated_at', $data);
        $this->assertNull($data['updated_at']);
    }

    public function testTimestampsAreAppliedOnInsertWhenTraitIsUsed()
    {
        /** @var TestPostTimestampModel $model */
        $model = ModelFactory::get(TestPostTimestampModel::class);

        $post = $model->create();

        $post->title = 'Hello';
        $post->content = 'World';
        $post->author = 'John';
        $post->published_at = '2026-01-23 10:00:00';

        $this->assertTrue($post->save());

        $saved = $model->findOne($post->id);

        $this->assertNotNull($saved);

        $this->assertNotEmpty($saved->created_at);
        $this->assertNotEmpty($saved->updated_at);
    }

    public function testUpdatedAtChangesOnUpdateButCreatedAtStaysSame()
    {
        /** @var TestPostTimestampModel $model */
        $model = ModelFactory::get(TestPostTimestampModel::class);

        $post = $model->create();

        $post->title = 'First';
        $post->content = 'Body';
        $post->author = 'John';
        $post->published_at = '2026-01-23 10:00:00';

        $this->assertTrue($post->save());

        $saved = $model->findOne($post->id);

        $this->assertNotNull($saved);

        $createdAt1 = $saved->created_at;
        $updatedAt1 = $saved->updated_at;

        sleep(1);

        $saved->title = 'Second';

        $this->assertTrue($saved->save());

        $saved2 = $model->findOne($post->id);

        $this->assertNotNull($saved2);

        $this->assertEquals($createdAt1, $saved2->created_at);
        $this->assertNotEquals($updatedAt1, $saved2->updated_at);
    }

    public function testUnixTimestampTypeStoresIntegers()
    {
        /** @var TestPostUnixTimestampModel $model */
        $model = ModelFactory::get(TestPostUnixTimestampModel::class);

        $post = $model->create();

        $post->title = 'Unix';
        $post->content = 'Test';
        $post->author = 'John';
        $post->published_at = '2026-01-23 10:00:00';

        $this->assertTrue($post->save());

        $saved = $model->findOne($post->id);

        $this->assertNotNull($saved);

        $this->assertIsNumeric($saved->created_at);
        $this->assertIsNumeric($saved->updated_at);
    }

    public function testCustomTimestampColumnsAreApplied()
    {
        /** @var TestPostCustomTimestampModel $model */
        $model = ModelFactory::get(TestPostCustomTimestampModel::class);

        $post = $model->create();

        $post->title = 'Custom';
        $post->content = 'Columns';

        $this->assertTrue($post->save());

        $saved = $model->findOne($post->id);

        $this->assertNotNull($saved);

        $data = $saved->asArray();

        $this->assertArrayHasKey('created_on', $data);
        $this->assertArrayHasKey('modified_on', $data);

        $this->assertNotEmpty($data['created_on']);
        $this->assertNotEmpty($data['modified_on']);
    }

    private function createPostsTable(): void
    {
        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS posts (
            id INTEGER PRIMARY KEY,
            title VARCHAR(255),
            content TEXT,
            author VARCHAR(255),
            published_at TEXT NULL,
            created_at TEXT NULL,
            updated_at TEXT NULL
        )');

        IdiormDbal::execute('CREATE TABLE IF NOT EXISTS posts_custom (
        id INTEGER PRIMARY KEY,
        title VARCHAR(255),
        content TEXT,
        created_on TEXT NULL,
        modified_on TEXT NULL
    )');
    }

}
