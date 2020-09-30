<?php

declare(strict_types=1);

namespace Chiron\Encrypter;

use Chiron\Encrypter\Config\EncrypterConfig;

//https://github.com/spiral/encrypter/blob/master/src/Exception/EncrypterException.php
//https://github.com/spiral/encrypter/blob/master/src/Exception/DecryptException.php
//https://github.com/spiral/encrypter/blob/master/src/Exception/EncryptException.php

final class Encrypter
{
    /**
     * The encryption key.
     *
     * @var string
     */
    private $key;

    public function __construct(EncrypterConfig $config)
    {
        $this->key = hex2bin($config->getKey());
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
        // implode the array.
        if (is_array($data)) {
            $data = json_encode($data);
        }

        try {
            return base64_encode(Cryptor::encrypt($data, $this->key));
        } catch (\Throwable $e) {
            // TODO : créer une EncryptException !!!!
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
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
            $result = Cryptor::decrypt(
                base64_decode($ciphertext),
                $this->key
            );

            return json_decode($result, true);
        } catch (\Throwable $e) {
            // TODO : créer une DecryptException !!!!
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
