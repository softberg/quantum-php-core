<?php

namespace Quantum\Tests\Unit\Libraries\Mailer;

use Quantum\Tests\Unit\AppTestCase;
use Quantum\Di\Di;

abstract class MailerTestCase extends AppTestCase
{
    protected $adapter;
    public function tearDown(): void
    {
        parent::tearDown();

        $coreDependencies = [
            \Quantum\Loader\Loader::class => \Quantum\Loader\Loader::class,
            \Quantum\Http\Request::class => \Quantum\Http\Request::class,
            \Quantum\Http\Response::class => \Quantum\Http\Response::class,
        ];

        Di::registerDependencies($coreDependencies);

        $emailFile = base_dir() . DS . 'shared' . DS . 'emails' . DS . $this->adapter->getMessageId() . '.eml';

        if($this->fs->exists($emailFile)) {
            $this->fs->remove($emailFile);
        }

        Di::reset();
    }
}