<?php

declare(strict_types=1);

namespace Chiron\Encrypter;

use Chiron\Config\SecurityConfig;
use Throwable;
use RuntimeException;

//https://github.com/spiral/encrypter/blob/master/src/Exception/EncrypterException.php
//https://github.com/spiral/encrypter/blob/master/src/Exception/DecryptException.php
//https://github.com/spiral/encrypter/blob/master/src/Exception/EncryptException.php

//https://github.com/spiral/encrypter/blob/master/src/Encrypter.php
//https://github.com/cakephp/cakephp/blob/42353085a8911745090024e2a4f43215d38d6af0/src/Utility/CookieCryptTrait.php

final class Encrypter
{
    /**
     * The 256 bit/32 byte binary key to use as a cipher key.
     *
     * @var string
     */
    private $key;

    public function __construct(SecurityConfig $config)
    {
        $this->key = $config->getRawKey();
    }

     /**
     * Encrypts $value
     *
     * @param string|array $value Value to encrypt
     *
     * @return string Encoded values
     */
    public function encrypt($data): string
    {
        $data = json_encode($data);

        try {
            return base64_encode(Cryptor::encrypt($data, $this->key));
        } catch (Throwable $e) {
            // TODO : créer une EncryptException !!!!
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Decrypts $value
     *
     * @param string $ciphertext Values to decrypt
     *
     * @return string|array Decrypted values
     */
    public function decrypt(string $ciphertext)
    {
        try {
            $result = Cryptor::decrypt(base64_decode($ciphertext), $this->key);

            return json_decode($result, true);
        } catch (Throwable $e) {
            // TODO : créer une DecryptException !!!!
            throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
