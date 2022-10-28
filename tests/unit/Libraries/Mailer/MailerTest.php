<?php

namespace Quantum\Tests\Libraries\Mailer;

use Quantum\Libraries\Mailer\Mailer;
use Quantum\Tests\AppTestCase;

class MailerTest extends AppTestCase
{

    private $mailer;

    public function setUp(): void
    {
        parent::setUp();

        $this->mailer = new Mailer();
    }

    public function testMailerConstructor()
    {
        $this->assertInstanceOf(Mailer::class, $this->mailer);
    }

    public function testSetGetFrom()
    {
        $this->mailer->setFrom('john@gmail.com');

        $this->assertEquals('john@gmail.com', $this->mailer->getFrom()['email']);

        $this->assertEmpty($this->mailer->getFrom()['name']);

        $this->mailer->setFrom('john@gmail.com', 'John Doe');

        $this->assertNotEmpty($this->mailer->getFrom()['name']);

        $this->assertEquals('John Doe', $this->mailer->getFrom()['name']);
    }

    public function testSetGetAddresses()
    {
        $this->mailer->setAddress('jonny@hotmail.com', 'Jonny')->setAddress('jane@gmail.com');

        $addresses = $this->mailer->getAddresses();

        $this->assertIsArray($addresses);

        $this->assertEquals('jonny@hotmail.com', $addresses[0]['email']);

        $this->assertEquals('Jonny', $addresses[0]['name']);

        $this->assertEquals('jane@gmail.com', $addresses[1]['email']);

        $this->assertNull($addresses[1]['name']);
    }

    public function testSetGetReplays()
    {
        $this->mailer->setReplay('jonny@hotmail.com', 'Jonny')->setReplay('jane@gmail.com');

        $replays = $this->mailer->getReplays();

        $this->assertIsArray($replays);

        $this->assertEquals('jonny@hotmail.com', $replays[0]['email']);

        $this->assertEquals('Jonny', $replays[0]['name']);

        $this->assertEquals('jane@gmail.com', $replays[1]['email']);

        $this->assertNull($replays[1]['name']);
    }

    public function testSetGetCCs()
    {
        $this->mailer->setCC('jonny@hotmail.com', 'Jonny')->setCC('jane@gmail.com');

        $CCs = $this->mailer->getCCs();

        $this->assertIsArray($CCs);

        $this->assertEquals('jonny@hotmail.com', $CCs[0]['email']);

        $this->assertEquals('Jonny', $CCs[0]['name']);

        $this->assertEquals('jane@gmail.com', $CCs[1]['email']);

        $this->assertNull($CCs[1]['name']);
    }

    public function testSetGetBCCs()
    {
        $this->mailer->setBCC('jonny@hotmail.com', 'Jonny')->setBCC('jane@gmail.com');

        $BCCs = $this->mailer->getBCCs();

        $this->assertIsArray($BCCs);

        $this->assertEquals('jonny@hotmail.com', $BCCs[0]['email']);

        $this->assertEquals('Jonny', $BCCs[0]['name']);

        $this->assertEquals('jane@gmail.com', $BCCs[1]['email']);

        $this->assertNull($BCCs[1]['name']);
    }

    public function testSetGetSubject()
    {
        $this->mailer->setSubject('Lorem ipsum');

        $this->assertIsString($this->mailer->getSubject());

        $this->assertEquals('Lorem ipsum', $this->mailer->getSubject());
    }

    public function testSetGetTemplate()
    {
        $this->mailer->setTemplate('fakepath.php');

        $this->assertIsString($this->mailer->getTemplate());

        $this->assertEquals('fakepath.php', $this->mailer->getTemplate());
    }

    public function testSetGetBody()
    {
        $this->mailer->setBody('Lorem ipsum dolor sit amet');

        $this->assertIsString($this->mailer->getBody());

        $this->assertEquals('Lorem ipsum dolor sit amet', $this->mailer->getBody());

        $this->mailer->setBody(['Lorem ipsum', 'dolor sit amet']);

        $this->assertIsArray($this->mailer->getBody());

        $this->assertEquals('Lorem ipsum', $this->mailer->getBody()[0]);
    }

    public function testSetGetAttachments()
    {
        $this->mailer->setAttachment('image.jpg', 'animation.gif');

        $this->assertIsArray($this->mailer->getAttachments());

        $this->assertEquals('image.jpg', current($this->mailer->getAttachments()));
    }

    public function testSetGetStringAttachments()
    {
        $this->mailer->setStringAttachment('content of the document', 'document.txt');

        $attachment = $this->mailer->getStringAttachments();

        $this->assertIsArray($attachment);

        $this->assertEquals('content of the document', current($attachment)['content']);

        $this->assertEquals('document.txt', current($attachment)['filename']);
    }

    public function testSendWithSetters()
    {
        config()->set('mail_trap', true);

        $this->mailer->setFrom('john@hotmail.com', 'John Doe');

        $this->mailer->setAddress('benny@gmail.com', 'Benny');

        $this->mailer->setSubject('Lorem');

        $this->mailer->setCC('katty@mail.com', 'Ketty');

        $this->mailer->setBody('Lorem ipsum dolor sit amet');

        $this->mailer->setAttachment(base_dir() . DS . 'journal.log');

        $this->mailer->setStringAttachment('content of the document', 'document.txt');

        $this->assertTrue($this->mailer->send());
    }

    public function testSendWithOptions()
    {
        config()->set('mail_trap', true);

        $from = ['john@hotmail.com', 'John Doe'];

        $to = ['benny@gmail.com', 'Benny'];

        $message = 'Lorem ipsum dolor sit amet';

        $options = [];

        $options['subject'] = 'Lorem';

        $options['cc'] = ['katty@mail.com', 'Ketty'];

        $options['attachment'] = [base_dir() . DS . 'journal.log'];

        $options['stringAttachment'] = ['content of the document', 'document.txt'];

        $this->assertTrue($this->mailer->send($from, $to, $message, $options));
    }

}
