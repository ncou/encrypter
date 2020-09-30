<?php

declare(strict_types=1);

namespace Chiron\Encrypter\Tests;

use Chiron\Encrypter\Cryptor;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Chiron\Encrypter\Cryptor
 */
class CryptorTest extends TestCase
{
    public function testWithEmptyString(): void
    {
        $str = '';
        $key = random_bytes(32);

        $ciphertext = Cryptor::encrypt($str, $key);

        self::assertSame($str, Cryptor::decrypt($ciphertext, $key));
    }

    public function testSuccessEncryptAndDecrypt(): void
    {
        $str = 'MySecretMessageToCrypt';
        $key = random_bytes(32);

        $ciphertext = Cryptor::encrypt($str, $key);

        self::assertSame($str, Cryptor::decrypt($ciphertext, $key));
    }

    public function testExceptionDecryptWithBadKey(): void
    {
        $str = 'MySecretMessageToCrypt';
        $key = random_bytes(32);
        $badKey = random_bytes(32);

        $ciphertext = Cryptor::encrypt($str, $key);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Decryption can not proceed due to invalid ciphertext integrity.');

        Cryptor::decrypt($ciphertext, $badKey);
    }

    public function testExceptionEncryptWithKeyTooShort(): void
    {
        $str = 'MySecretMessageToCrypt';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad key length [expecting 32 bytes].');

        $ciphertext = Cryptor::encrypt($str, random_bytes(30));
    }

    public function testExceptionDecryptWithKeyTooShort(): void
    {
        $str = 'MySecretMessageToCrypt';

        $ciphertext = Cryptor::encrypt($str, random_bytes(32));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad key length [expecting 32 bytes].');

        Cryptor::decrypt($ciphertext, random_bytes(30));
    }

    public function testExceptionEncryptWithKeyTooLong(): void
    {
        $str = 'MySecretMessageToCrypt';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad key length [expecting 32 bytes].');

        $ciphertext = Cryptor::encrypt($str, random_bytes(34));
    }

    public function testExceptionDecryptWithKeyTooLong(): void
    {
        $str = 'MySecretMessageToCrypt';

        $ciphertext = Cryptor::encrypt($str, random_bytes(32));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Bad key length [expecting 32 bytes].');

        Cryptor::decrypt($ciphertext, random_bytes(34));
    }

    public function testExceptionDecryptWithBadCipherText(): void
    {
        $str = 'MySecretMessageToCrypt';
        $key = random_bytes(32);

        $ciphertext = Cryptor::encrypt($str, $key);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Decryption can not proceed due to invalid ciphertext integrity.');

        Cryptor::decrypt($ciphertext . 'a', $key);
    }

    public function testExceptionDecryptWithCipherTooSmall(): void
    {
        $str = 'MySecretMessageToCrypt';
        $key = random_bytes(32);

        $ciphertext = str_repeat('A', Cryptor::MINIMUM_CIPHERTEXT_SIZE - 1);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Decryption can not proceed due to invalid ciphertext length.');

        Cryptor::decrypt($ciphertext, $key);
    }

    /**
     * @dataProvider headerPositions
     */
    public function testExceptionDecryptWithBadCipherHeader(int $index): void
    {
        $str = 'MySecretMessageToCrypt';
        $key = random_bytes(32);

        $ciphertext = Cryptor::encrypt($str, $key);
        $ciphertext[$index] = chr((ord($ciphertext[$index]) + 1) % 256);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Decryption can not proceed due to invalid ciphertext integrity.');

        Cryptor::decrypt($ciphertext, $key);
    }

    /**
     * @return array<array<int>>
     */
    public function headerPositions(): array
    {
        return [
            [0], // the hmac.
            [Cryptor::MAC_BYTE_SIZE + 1], // the salt
            [Cryptor::MAC_BYTE_SIZE + Cryptor::SALT_BYTE_SIZE + 1], // the IV
            [Cryptor::MAC_BYTE_SIZE + Cryptor::SALT_BYTE_SIZE + Cryptor::IV_BYTE_SIZE + 1], // the ciphertext
        ];
    }
}
