<?php

declare(strict_types=1);

namespace Chiron\Encrypter\Tests;

use Chiron\Encrypter\Encrypter;
use Chiron\Container\Container;
use Chiron\Security\Config\SecurityConfig;
use Chiron\Security\Security;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chiron\Encrypter\Encrypter
 */
class EncrypterTest extends TestCase
{
    private $container;
    private $encrypter;

    public function setUp(): void
    {
        $this->container = new Container();

        $securityConfig = new SecurityConfig(['key' => bin2hex(Security::generateKey())]);

        $this->encrypter = new Encrypter($securityConfig);
    }

    /**
     * @dataProvider dataProvider
     */
    public function testEncryptAndDecrypt($source): void
    {
        $encrypted = $this->encrypter->encrypt($source);
        $decrypted = $this->encrypter->decrypt($encrypted);

        self::assertEquals($source, $decrypted);
    }

    /**
     * @return array<array<int>>
     */
    public function dataProvider(): array
    {
        return [
            ['string'],
            ['{'],
            [''],
            ['true'],
            ['false'],
            ['null'],
            [true],
            [false],
            [null],
            [123],
            [1.23],
            ['123'],
            ['1.23'],
            [['key' => 'value']]
        ];
    }

    /**
     * @dataProvider dataObjectProvider
     */
    public function testEncryptAndDecryptObject($source): void
    {
        $encrypted = $this->encrypter->encrypt($source);
        $decrypted = $this->encrypter->decrypt($encrypted);

        // Objects are converted to array during the json_encode() inside the encrypt() method
        self::assertNotEquals($source, $decrypted);
        self::assertIsArray($decrypted);
    }

    /**
     * @return array<array<int>>
     */
    public function dataObjectProvider(): array
    {
        return [
            [new \StdClass()],
            [['key' => new \StdClass()]],
            [function() { return 'callable';}],
            //[tmpfile()] // resource is not encoded in json !!!
        ];
    }
}
