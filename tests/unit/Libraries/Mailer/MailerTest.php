<?php

namespace Quantum\Libraries\Mailer {

    function env($key)
    {
        return false;
    }

    function get_config($key)
    {
        return false;
    }

}

namespace Quantum\Test\Unit {

    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Mailer\Mailer;

    class MailerTest extends TestCase
    {

        private $mailer;

        public function setUp(): void
        {
            $this->mailer = new Mailer();
        }

        public function testFileConstructor()
        {
            $this->assertInstanceOf('Quantum\Libraries\Mailer\Mailer', $this->mailer);
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

        public function testSetGetReplyes()
        {
            $this->mailer->setReplay('jonny@hotmail.com', 'Jonny')->setReplay('jane@gmail.com');

            $replayes = $this->mailer->getReplayes();

            $this->assertIsArray($replayes);

            $this->assertEquals('jonny@hotmail.com', $replayes[0]['email']);

            $this->assertEquals('Jonny', $replayes[0]['name']);

            $this->assertEquals('jane@gmail.com', $replayes[1]['email']);

            $this->assertNull($replayes[1]['name']);
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
            $this->mailer->setAttachments(['image.jpg', 'animation.gif']);

            $this->assertIsArray($this->mailer->getAttachments());

            $this->assertEquals('image.jpg', current($this->mailer->getAttachments()));
        }

        public function testSetGetStringAttachment()
        {
            $this->mailer->setStringAttachment('content of the document', 'document.txt');

            $attachment = $this->mailer->getStringAttachment();

            $this->assertIsArray($attachment);

            $this->assertEquals('content of the document', current($attachment)['content']);

            $this->assertEquals('document.txt', current($attachment)['filename']);
        }

        public function testEmailSend()
        {
            $this->mailer->setFrom('john@hotmail.com', 'John Doe');

            $this->mailer->setAddress('benny@gmail.com', 'Benny');

            $this->mailer->setSubject('Lorem');

            $this->mailer->setBody('Lorem ipsum dolor sit amet');

            $this->assertTrue($this->mailer->send());

            $from = ['john@hotmail.com', 'John Doe'];

            $to = ['benny@gmail.com', 'Benny'];

            $message = 'Lorem ipsum dolor sit amet';

            $options = ['subject' => 'Lorem'];
            
            $this->assertTrue($this->mailer->send($from, $to, $message, $options));
        }

    }

}