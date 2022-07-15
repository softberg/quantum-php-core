<?php

namespace Quantum\Libraries\Validation {

    if (!function_exists('t')) {

        function t($key, $params = null)
        {
            return $key;
        }

    }
}

namespace Quantum\Models {

    use Quantum\Mvc\QtModel;

    class VUserModel extends QtModel
    {

        public $table = 'users';

    }

}

namespace Quantum\Tests\Libraries\Validation {

    use PHPUnit\Framework\TestCase;
    use Quantum\Libraries\Validation\Validator;
    use Quantum\Libraries\Database\Idiorm\IdiormDbal;
    use Quantum\Libraries\Validation\Rule;
    use Quantum\Factory\ModelFactory; 
    use Quantum\Models\VUserModel;
    use Quantum\Http\Request;
    use Quantum\Di\Di;
    use Quantum\App;

    class ValidatorTest extends TestCase
    {

        private $request;
        private $validator;

        public function setUp(): void
        {
            App::loadCoreFunctions(dirname(__DIR__, 4) . DS . 'src' . DS . 'Helpers');

            App::setBaseDir(dirname(__DIR__, 2) . DS . '_root');

            Di::loadDefinitions();

            $this->request = new Request();

            $this->validator = new Validator();

            $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
                    . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
                    . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
                    . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';

            $img = imagecreatefromstring(base64_decode($data));

            imagepng($img, base_dir() . DS . 'php8fe2.tmp');

            imagedestroy($img);
            
            
        }

        public function tearDown(): void
        {
            unlink(base_dir() . DS . 'php8fe2.tmp');
        }

        public function testValidatorConstructor()
        {
            $this->assertInstanceOf(Validator::class, $this->validator);
        }

        public function testAddUpdateDeleteRule()
        {
            $this->validator->addRule('text', [
                Rule::set('minLen', 5),
                Rule::set('maxLen', 10),
            ]);

            $this->assertFalse($this->validator->isValid(['text' => 'some']));

            $this->validator->updateRule('text', Rule::set('minLen', 2));

            $this->validator->flushErrors();

            $this->assertTrue($this->validator->isValid(['text' => 'some']));

            $this->assertFalse($this->validator->isValid(['text' => 'some long text goes']));

            $this->validator->deleteRule('text', 'maxLen');

            $this->validator->flushErrors();

            $this->assertTrue($this->validator->isValid(['text' => 'some long text goes']));
        }

        public function testMultipleRules()
        {
            $this->validator->addRule('text', [
                Rule::set('minLen', 7),
                Rule::set('maxLen', 20),
                Rule::set('email')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'someone@gmail.com']));

            $this->assertFalse($this->validator->isValid(['text' => 'someonegmail.com']));

            $errors = $this->validator->getErrors();

            $this->assertEquals('validation.email', $errors['text'][0]);

            $this->assertFalse($this->validator->isValid(['text' => 'a@c.cc']));

            $errors = $this->validator->getErrors();

            $this->assertEquals('validation.minLen', $errors['text'][1]);

            $this->assertFalse($this->validator->isValid(['text' => 'verylongemailaddress@longdomain.cc']));

            $errors = $this->validator->getErrors();

            $this->assertEquals('validation.maxLen', $errors['text'][2]);
        }

        public function testMultipleFields()
        {
            $this->validator->addRules([
                'name' => [
                    Rule::set('required'),
                    Rule::set('maxLen', 30)
                ],
                'email' => [
                    Rule::set('required'),
                    Rule::set('email')
                ],
                'age' => [
                    Rule::set('required'),
                    Rule::set('minNumeric', 16),
                ],
            ]);

            $data = [
                'name' => 'John',
                'email' => 'john@gmail.com',
                'age' => 36
            ];

            $this->assertTrue($this->validator->isValid($data));

            $data = [
                'name' => 'Junior',
                'email' => 'johny@gc.com',
                'age' => 12
            ];

            $this->assertFalse($this->validator->isValid($data));

            $this->validator->flushRules();

            $data = [
                'name' => '',
                'email' => 'john@gmail.com',
                'age' => 0
            ];

            $this->assertTrue($this->validator->isValid($data));

            $this->validator->addRules([
                'phone' => [
                    Rule::set('required'),
                    Rule::set('minLen', 8),
                ]
            ]);

            $data = [
                'name' => 'John',
                'email' => 'john@gmail.com',
                'age' => 36,
            ];

            $this->assertFalse($this->validator->isValid($data));
        }

        public function testDifferentMultipleFields()
        {
            $this->validator->addRules([
                'name' => [
                    Rule::set('maxLen', 30)
                ],
                'email' => [
                    Rule::set('required'),
                    Rule::set('email')
                ],
                'age' => [
                    Rule::set('required'),
                    Rule::set('minNumeric', 16),
                ],
            ]);

            $data = [
                'email' => 'john@gmail.com',
                'age' => 36,
            ];

            $this->assertTrue($this->validator->isValid($data));

            $data = [
                'name' => '',
                'email' => 'john@gmail.com',
                'age' => 36,
            ];

            $this->assertTrue($this->validator->isValid($data));
        }

        public function testCustomValidator()
        {
            $this->validator->addValidation('checkName', function ($value, $param = null) {
                return $value == $param;
            });

            $this->validator->addRules([
                'name' => [
                    Rule::set('checkName', 'Wayne'),
                ]
            ]);

            $this->assertTrue($this->validator->isValid(['name' => 'Wayne']));

            $this->assertFalse($this->validator->isValid(['name' => 'John']));
        }

        public function testRequired()
        {
            $this->validator->addRule('text', [
                Rule::set('required')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'something']));

            $this->assertFalse($this->validator->isValid(['text' => '']));

            $this->assertFalse($this->validator->isValid(['custom' => 'customValue']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.required', $errors['text'][0]);
        }

        public function testEmail()
        {
            $this->validator->addRule('text', [
                Rule::set('email')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'something@mail.com']));

            $this->assertFalse($this->validator->isValid(['text' => 'something']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.email', $errors['text'][0]);
        }

        public function testMinLen()
        {
            $this->validator->addRule('text', [
                Rule::set('minLen', 3)
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'something']));

            $this->assertFalse($this->validator->isValid(['text' => 'so']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.minLen', $errors['text'][0]);
        }

        public function testMaxLen()
        {
            $this->validator->addRule('text', [
                Rule::set('maxLen', 5)
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'some']));

            $this->assertFalse($this->validator->isValid(['text' => 'something long']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.maxLen', $errors['text'][0]);
        }

        public function testExactLen()
        {
            $this->validator->addRule('text', [
                Rule::set('exactLen', 4)
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'some']));

            $this->assertFalse($this->validator->isValid(['text' => 'something long']));

            $this->assertFalse($this->validator->isValid(['text' => 'so']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.exactLen', $errors['text'][0]);
        }

        public function testAlpha()
        {
            $this->validator->addRule('text', [
                Rule::set('alpha')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'some']));

            $this->assertFalse($this->validator->isValid(['text' => 123]));

            $this->assertFalse($this->validator->isValid(['text' => 'so 456']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.alpha', $errors['text'][0]);
        }

        public function testAlphaNumeric()
        {
            $this->validator->addRule('text', [
                Rule::set('alphaNumeric')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'some']));

            $this->assertTrue($this->validator->isValid(['text' => 'num456']));

            $this->assertTrue($this->validator->isValid(['text' => 123]));

            $this->assertFalse($this->validator->isValid(['text' => '*- ']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.alphaNumeric', $errors['text'][0]);
        }

        public function testAlphaDash()
        {
            $this->validator->addRule('text', [
                Rule::set('alphaDash')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'some']));

            $this->assertTrue($this->validator->isValid(['text' => 'num-']));

            $this->assertFalse($this->validator->isValid(['text' => '55-']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.alphaDash', $errors['text'][0]);
        }

        public function testAlphaSpace()
        {
            $this->validator->addRule('text', [
                Rule::set('alphaSpace')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'some']));

            $this->assertTrue($this->validator->isValid(['text' => 'num555']));

            $this->assertTrue($this->validator->isValid(['text' => 'num 555']));

            $this->assertFalse($this->validator->isValid(['text' => 'num: 555']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.alphaSpace', $errors['text'][0]);
        }

        public function testNumeric()
        {
            $this->validator->addRule('text', [
                Rule::set('numeric')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 555]));

            $this->assertTrue($this->validator->isValid(['text' => '555']));

            $this->assertFalse($this->validator->isValid(['text' => 'num 555']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.numeric', $errors['text'][0]);
        }

        public function testInteger()
        {
            $this->validator->addRule('text', [
                Rule::set('integer')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 555]));

            $this->assertTrue($this->validator->isValid(['text' => '555']));

            $this->assertFalse($this->validator->isValid(['text' => 555.12]));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.integer', $errors['text'][0]);
        }

        public function testFloat()
        {
            $this->validator->addRule('text', [
                Rule::set('float')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 11.12]));

            $this->assertTrue($this->validator->isValid(['text' => '11.12']));

            $this->assertTrue($this->validator->isValid(['text' => 11]));

            $this->assertFalse($this->validator->isValid(['text' => 'something']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.float', $errors['text'][0]);
        }

        public function testBoolean()
        {
            $this->validator->addRule('text', [
                Rule::set('boolean')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => true]));

            $this->assertTrue($this->validator->isValid(['text' => 'true']));

            $this->assertTrue($this->validator->isValid(['text' => 1]));

            $this->assertTrue($this->validator->isValid(['text' => 0]));

            $this->assertTrue($this->validator->isValid(['text' => 'false']));

            $this->assertTrue($this->validator->isValid(['text' => false]));

            $this->assertFalse($this->validator->isValid(['text' => 'something']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.boolean', $errors['text'][0]);
        }

        public function testUrl()
        {
            $this->validator->addRule('text', [
                Rule::set('url')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'http://something.com']));

            $this->assertTrue($this->validator->isValid(['text' => 'http://www.something.com']));

            $this->assertTrue($this->validator->isValid(['text' => 'http://subdomain.something.com']));

            $this->assertFalse($this->validator->isValid(['text' => 'something']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.url', $errors['text'][0]);
        }

        public function testThatUrlExists()
        {
            $this->validator->addRule('text', [
                Rule::set('urlExists')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'http://google.com']));

            $this->assertFalse($this->validator->isValid(['text' => 'http://someunregistereddomain.com']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.urlExists', $errors['text'][0]);
        }

        public function testIp()
        {
            $this->validator->addRule('text', [
                Rule::set('ip'),
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '127.0.0.1']));

            $this->assertFalse($this->validator->isValid(['text' => '521.652.125.987']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.ip', $errors['text'][0]);
        }

        public function testIpV4()
        {
            $this->validator->addRule('text', [
                Rule::set('ipv4'),
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '127.0.0.1']));

            $this->assertFalse($this->validator->isValid(['text' => '521.652.125.987']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.ipv4', $errors['text'][0]);
        }

        public function testIpV6()
        {
            $this->validator->addRule('text', [
                Rule::set('ipv6')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '2001:0000:3238:DFE1:0063:0000:0000:FEFB']));

            $this->assertFalse($this->validator->isValid(['text' => '521.652.125.987']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.ipv6', $errors['text'][0]);
        }

        public function testCreditCard()
        {
            $this->validator->addRule('text', [
                Rule::set('creditCard')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '4111111111111111']));

            $this->assertFalse($this->validator->isValid(['text' => '123456789876541']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.creditCard', $errors['text'][0]);
        }

        public function testName()
        {
            $this->validator->addRule('text', [
                Rule::set('name')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'John']));

            $this->assertTrue($this->validator->isValid(['text' => 'jim']));

            $this->assertTrue($this->validator->isValid(['text' => 'Jane Marko']));

            $this->assertFalse($this->validator->isValid(['text' => 'jim 12']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.name', $errors['text'][0]);
        }

        public function testContains()
        {
            $this->validator->addRule('text', [
                Rule::set('contains', 'Fisherman goes out')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'Fisherman']));

            $this->assertTrue($this->validator->isValid(['text' => 'out']));

            $this->assertFalse($this->validator->isValid(['text' => 'comes']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.contains', $errors['text'][0]);
        }

        public function testContainsList()
        {
            $this->validator->addRule('text', [
                Rule::set('containsList', ['male', 'famale'])
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'male']));

            $this->assertTrue($this->validator->isValid(['text' => 'famale']));

            $this->assertFalse($this->validator->isValid(['text' => 'other']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.containsList', $errors['text'][0]);
        }

        public function testDoesntContainsList()
        {
            $this->validator->addRule('text', [
                Rule::set('doesntContainsList', ['male', 'famale'])
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'elephant']));

            $this->assertTrue($this->validator->isValid(['text' => 'other']));

            $this->assertFalse($this->validator->isValid(['text' => 'famale']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.doesntContainsList', $errors['text'][0]);
        }

        public function testStreetAddress()
        {
            $this->validator->addRule('text', [
                Rule::set('streetAddress')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'Barbara ave. 12']));

            $this->assertTrue($this->validator->isValid(['text' => 'Lincoln blvd. 22']));

            $this->assertFalse($this->validator->isValid(['text' => 'something']));

            $this->assertFalse($this->validator->isValid(['text' => 123]));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.streetAddress', $errors['text'][0]);
        }

        public function testIban()
        {
            $this->validator->addRule('text', [
                Rule::set('iban')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'DE75512108001245126199']));

            $this->assertTrue($this->validator->isValid(['text' => 'FR7630006000011234567890189']));

            $this->assertFalse($this->validator->isValid(['text' => 'XR763000']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.iban', $errors['text'][0]);
        }

        public function testMinNumeric()
        {
            $this->validator->addRule('text', [
                Rule::set('minNumeric', 20)
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 25]));

            $this->assertFalse($this->validator->isValid(['text' => 15]));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.minNumeric', $errors['text'][0]);
        }

        public function testMaxNumeric()
        {
            $this->validator->addRule('text', [
                Rule::set('maxNumeric', 20)
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 15]));

            $this->assertFalse($this->validator->isValid(['text' => 25]));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.maxNumeric', $errors['text'][0]);
        }

        public function testDate()
        {
            $this->validator->addRule('text', [
                Rule::set('date')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '2020-05-09']));

            $this->assertTrue($this->validator->isValid(['text' => '2020-05-09 01:58:00']));

            $this->validator->addRule('text', [
                Rule::set('date', 'm/d/Y')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '05/09/2020']));

            $this->validator->addRule('text', [
                Rule::set('date', 'm/d/Y H:i:s')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '05/09/2020 01:59:00']));

            $this->assertFalse($this->validator->isValid(['text' => '05.09.2020']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.date', $errors['text'][0]);
        }

        public function testStarts()
        {
            $this->validator->addRule('text', [
                Rule::set('starts', 'b')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'bike']));

            $this->validator->addRule('text', [
                Rule::set('starts', 'br')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'break']));

            $this->assertFalse($this->validator->isValid(['text' => 'about']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.starts', $errors['text'][0]);
        }

        public function testPhoneNumber()
        {
            $this->validator->addRule('text', [
                Rule::set('phoneNumber')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '1-555-555-5555']));

            $this->assertTrue($this->validator->isValid(['text' => '+1-555-555-5555']));

            $this->assertTrue($this->validator->isValid(['text' => '1 (519) 555-4422']));

            $this->assertTrue($this->validator->isValid(['text' => '+1 (519) 555-4422']));

            $this->assertTrue($this->validator->isValid(['text' => '555 555 5555']));

            $this->assertTrue($this->validator->isValid(['text' => '555-555-5555']));

            $this->assertFalse($this->validator->isValid(['text' => '55-555-5555']));

            $this->assertFalse($this->validator->isValid(['text' => '5 55 555']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.phoneNumber', $errors['text'][0]);
        }

        public function testRegex()
        {
            $this->validator->addRule('text', [
                Rule::set('regex', '/^[1-9]+$/')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 123]));

            $this->validator->addRule('text', [
                Rule::set('regex', '/^[a-zA-Z]+$/')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => 'Hello']));

            $this->assertFalse($this->validator->isValid(['text' => 'Hello world']));

            $this->assertFalse($this->validator->isValid(['text' => 'Hello 123']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.regex', $errors['text'][0]);
        }

        public function testJsonString()
        {
            $this->validator->addRule('text', [
                Rule::set('jsonString')
            ]);

            $this->assertTrue($this->validator->isValid(['text' => '{"widget": {"debug": "on", "version": "1.0"}}']));

            $this->assertFalse($this->validator->isValid(['text' => '{hello: world}']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.jsonString', $errors['text'][0]);
        }

        public function testSame()
        {
            $this->validator->addRule('password', [
                Rule::set('same', 'confrim_password')
            ]);

            $this->assertTrue($this->validator->isValid(['password' => 'qwerty', 'confrim_password' => 'qwerty']));

            $this->assertFalse($this->validator->isValid(['password' => 'qwerty', 'confrim_password' => '123456']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.same', $errors['password'][0]);
        }

        public function testUnique()
        {
            IdiormDbal::connect(['driver' => 'sqlite', 'database' => ':memory:']);

            IdiormDbal::execute("CREATE TABLE users (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        username VARCHAR(255),
                        password VARCHAR(255),
                        created_at DATETIME
                    )");

            $this->validator->addRule('username', [
                Rule::set('unique', VUserModel::class)
            ]);

            $this->assertTrue($this->validator->isValid(['username' => 'john@doe']));

            $model = ModelFactory::get(VUserModel::class);

            $model->create();
            $model->username = 'john@doe';
            $model->password = '123456';
            $model->save();

            $this->assertFalse($this->validator->isValid(['username' => 'john@doe']));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.unique', $errors['username'][0]);
        }

        public function testValidateFileSize()
        {
            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => base_dir() . DS . 'php8fe2.tmp',
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];

            $this->request->create('POST', '/upload', null, $file);

            $this->validator->addRule('image', [
                Rule::set('fileSize', 1000)
            ]);

            $this->assertTrue($this->validator->isValid($this->request->all()));

            $this->validator->addRule('image', [
                Rule::set('fileSize', [500, 9000])
            ]);

            $this->assertFalse($this->validator->isValid($this->request->all()));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.fileSize', $errors['image'][0]);
        }

        public function testFileMimeType()
        {
            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => base_dir() . DS . 'php8fe2.tmp',
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];

            $this->request->create('POST', '/upload', null, $file);

            $this->validator->addRule('image', [
                Rule::set('fileMimeType', 'image/png')
            ]);

            $this->assertTrue($this->validator->isValid($this->request->all()));

            $this->validator->addRule('image', [
                Rule::set('fileMimeType', ['image/jpg', 'image/jpeg', 'image/png'])
            ]);

            $this->assertTrue($this->validator->isValid($this->request->all()));

            $this->validator->addRule('image', [
                Rule::set('fileMimeType', ['image/gif', 'image/jpg'])
            ]);

            $this->assertFalse($this->validator->isValid($this->request->all()));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.fileMimeType', $errors['image'][0]);
        }

        public function testFileExtension()
        {
            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => base_dir() . DS . 'php8fe2.tmp',
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];

            $this->request->create('POST', '/upload', null, $file);

            $this->validator->addRule('image', [
                Rule::set('fileExtension', 'jpg')
            ]);

            $this->assertTrue($this->validator->isValid($this->request->all()));

            $this->validator->addRule('image', [
                Rule::set('fileExtension', ['png', 'jpg', 'gif'])
            ]);

            $this->assertTrue($this->validator->isValid($this->request->all()));

            $this->validator->addRule('image', [
                Rule::set('fileExtension', ['exe', 'bmp'])
            ]);

            $this->assertFalse($this->validator->isValid($this->request->all()));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.fileExtension', $errors['image'][0]);
        }

        public function testImageDimensions()
        {
            $file = [
                'image' => [
                    'size' => 500,
                    'name' => 'foo.jpg',
                    'tmp_name' => base_dir() . DS . 'php8fe2.tmp',
                    'type' => 'image/jpg',
                    'error' => 0,
                ],
            ];

            $this->request->create('POST', '/upload', null, $file);

            $this->validator->addRule('image', [
                Rule::set('imageDimensions', [28, 18])
            ]);

            $this->assertTrue($this->validator->isValid($this->request->all()));

            $this->validator->addRule('image', [
                Rule::set('imageDimensions', [300, 500])
            ]);

            $this->assertFalse($this->validator->isValid($this->request->all()));

            $errors = $this->validator->getErrors();

            $this->assertNotEmpty($errors);

            $this->assertEquals('validation.imageDimensions', $errors['image'][0]);
        }

    }

}