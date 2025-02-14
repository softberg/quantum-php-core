<?php

namespace Quantum\Tests\Unit\Libraries\Mailer;

use Quantum\Libraries\Mailer\MailTrap;
use Quantum\Tests\Unit\AppTestCase;

class MailTrapTest extends AppTestCase
{

    private $mailTrap;

    private $filename;

    private $content;

    private $path;

    public function setUp(): void
    {
        parent::setUp();

        $this->mailTrap = MailTrap::getInstance();

        $this->filename = '2YILSA4zZk61tDdYEfGMw7lNznlhAQakjwNGr0QCq44';

        $this->path = base_dir() . DS . 'shared' . DS . 'emails';

        $this->content = 'Date: Wed, 5 Apr 2023 19:15:37 +0300
To: Jane Due <jane@gmail.com>
From: John Doe <john@hotmail.com>
Cc: Kevin <kevin@gmail.com>, Bill <bill@outlook.com>
Bcc: Dan <dan@hotmail.com>
Reply-To: Sam <sam@gmail.com>
Subject: Lorem
Message-ID: <2YILSA4zZk61tDdYEfGMw7lNznlhAQakjwNGr0QCq44@WIN-PO1MOI973IP>
X-Mailer: PHPMailer 6.8.0 (https://github.com/PHPMailer/PHPMailer)
MIME-Version: 1.0
Content-Type: multipart/mixed;
 boundary="b1=_2YILSA4zZk61tDdYEfGMw7lNznlhAQakjwNGr0QCq44"
Content-Transfer-Encoding: 8bit

--b1=_2YILSA4zZk61tDdYEfGMw7lNznlhAQakjwNGr0QCq44
Content-Type: text/plain; charset=us-ascii

Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
Vestibulum lobortis leo velit, et facilisis justo sollicitudin ultricies. 
Praesent quis commodo diam. Duis lacinia ut quam ut finibus.

--b1=_2YILSA4zZk61tDdYEfGMw7lNznlhAQakjwNGr0QCq44
Content-Type: text/plain; name=document.txt
Content-Transfer-Encoding: base64
Content-Disposition: attachment; filename=document.txt

Y29udGVudCBvZiB0aGUgZG9jdW1lbnQ=

--b1=_2YILSA4zZk61tDdYEfGMw7lNznlhAQakjwNGr0QCq44--';

        $this->mailTrap->saveMessage($this->filename, $this->content);

    }

    public function tearDown(): void
    {
        $this->fs->remove($this->path . DS . $this->filename . '.eml');
    }

    public function testMailTrapInstance()
    {
        $this->assertInstanceOf(MailTrap::class, $this->mailTrap);
    }

    public function testMailTrapSaveMessage()
    {
        $filename = bin2hex(random_bytes(16));

        $this->assertFalse($this->fs->exists($this->path . DS . $filename . '.eml'));

        $this->mailTrap->saveMessage($filename, $this->content);

        $this->assertTrue($this->fs->exists($this->path . DS . $filename . '.eml'));

        $this->fs->remove($this->path . DS . $filename . '.eml');
    }

    public function testMailTrapGetParsedMessageId()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedMessagedId = $message->getParsedMessageId();

        $this->assertIsString($parsedMessagedId);

        $this->assertStringContainsString($this->filename, $parsedMessagedId);
    }

    public function testMailTrapGetParsedXMailer()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedXMailer = $message->getParsedXMailer();

        $this->assertIsString($parsedXMailer);

        $this->assertStringContainsStringIgnoringCase('PHPMailer 6.8.0', $parsedXMailer);
    }

    public function testMailTrapGetParsedMimeVersion()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedMimeVersion = $message->getParsedMimeVersion();

        $this->assertIsString($parsedMimeVersion);

        $this->assertEquals('1.0', $parsedMimeVersion);
    }

    public function testMailTrapGetParsedContentType()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedContentType = $message->getParsedContentType();

        $this->assertIsString($parsedContentType);

        $this->assertEquals('multipart/mixed', $parsedContentType);
    }

    public function testMailTrapGetParsedDate()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedDate = $message->getParsedDate();

        $this->assertIsString($parsedDate);

        $this->assertEquals('Wed, 5 Apr 2023 19:15:37 +0300', $parsedDate);
    }

    public function testMailTrapGetParsedFromAddress()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedFromAddress = $message->getParsedFromAddress();

        $this->assertIsString($parsedFromAddress);

        $this->assertEquals('John Doe <john@hotmail.com>', $parsedFromAddress);
    }

    public function testMailTrapGetParsedToAddresses()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedToAddresses = $message->getParsedToAddresses();

        $this->assertIsArray($parsedToAddresses);

        $this->assertEquals('Jane Due <jane@gmail.com>', $parsedToAddresses[0]);
    }

    public function testMailTrapGetParsedCcAddresses()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedCcAddress = $message->getParsedCcAddresses();

        $this->assertIsArray($parsedCcAddress);

        $this->assertEquals('Kevin <kevin@gmail.com>', $parsedCcAddress[0]);

        $this->assertEquals('Bill <bill@outlook.com>', $parsedCcAddress[1]);
    }

    public function testMailTrapGetParsedBccAddresses()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedBccAddress = $message->getParsedBccAddresses();

        $this->assertIsArray($parsedBccAddress);

        $this->assertEquals('Dan <dan@hotmail.com>', $parsedBccAddress[0]);
    }

    public function testMailTrapGetParsedReplyToAddresses()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedReplyToAddress = $message->getParsedReplyToAddresses();

        $this->assertIsArray($parsedReplyToAddress);

        $this->assertEquals('Sam <sam@gmail.com>', $parsedReplyToAddress[0]);
    }

    public function testMailTrapGetParsedSubject()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedSubject = $message->getParsedSubject();

        $this->assertIsString($parsedSubject);

        $this->assertEquals('Lorem', $parsedSubject);
    }

    public function testMailTrapGetParsedBody()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $parsedBody = $message->getParsedBody();

        $this->assertIsString($parsedBody);

        $this->assertStringContainsString('Lorem ipsum dolor sit amet', $parsedBody);
    }

    public function testMailTrapGetParsedAttachments()
    {
        $message = $this->mailTrap->parseMessage($this->filename);

        $attachments = $message->getParsedAttachments();

        $this->assertIsArray($attachments);

        $firstAttachment = $attachments[0];

        $this->assertIsArray($firstAttachment);

        $this->assertArrayHasKey('filename', $firstAttachment);

        $this->assertArrayHasKey('content-type', $firstAttachment);

        $this->assertArrayHasKey('content', $firstAttachment);

        $this->assertEquals('document.txt', $firstAttachment['filename']);

        $this->assertEquals('text/plain', $firstAttachment['content-type']);

        $this->assertStringContainsString('content of the document', base64_decode($firstAttachment['content']));
    }
}