<?php
namespace Omnipay\Sermepa\Encryptor;

/**
 * BadSignatureException
 *
 * @author Javier Sampedro <jsampedro77@gmail.com>
 */
class Encryptor
{
    static public function encrypt_3DES($message, $key)
    {
        return mcrypt_encrypt(
            MCRYPT_3DES,
            $key,
            $message,
            MCRYPT_MODE_CBC,
            implode(array_map("chr", array(0, 0, 0, 0, 0, 0, 0, 0)))
        );
    }
}
