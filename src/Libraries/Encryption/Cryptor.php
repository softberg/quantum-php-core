<?php
namespace Quantum\Libraries\Encryption;

class Cryptor
{
    private $res;
    private $digest_alg = 'sha512';
    private $private_key_bits;
    private $private_key_type = OPENSSL_KEYTYPE_RSA;
    private $config = [];
    private $keys = [];
    private $encryptedData;
    private $decryptedData;
    private static $cipher = 'AES-256-CBC';

    public function __construct($cipher = null, $key_bits = 1024)
    {
        self::setCiper($cipher);
        $this->setKeyBits($key_bits);
        $this->set_config();
        $this->set_res();
        $this->set_priv_key();
    }

    private function set_config()
    {
        $this->config = [
            "digest_alg" => $this->digest_alg,
            "private_key_type" => $this->private_key_type,
            "private_key_bits" => $this->private_key_bits
        ];
    }

    private function setKeyBits($key_bits)
    {
        $this->private_key_bits = $key_bits;
    }

    private static function setCiper($cipher)
    {
        if($cipher) {
            if (!in_array($cipher, openssl_get_cipher_methods()))
            {
                throw new \Exception('The ciper is invalid.');
            }

            self::$cipher = $cipher;
        }
    }

    private function set_res()
    {
        $this->res = openssl_pkey_new($this->config);

        if(!$this->res) {
            throw new \Exception('Could not load openssl.cnf properly.');
        }
    }

    private function set_priv_key()
    {
        openssl_pkey_export($this->res, $privKey);
        $pubKey = openssl_pkey_get_details($this->res);

        $this->keys = [
            'public_key' => base64_encode($pubKey["key"]),
            'private_key' =>  base64_encode($privKey),
        ];
    }

    public function get_keys($obj = true)
    {
        return $obj ? (object) $this->keys : $this->keys;
    }

    public function encrypt_data(string $data, $pubKey)
    {
        openssl_public_encrypt($data, $this->encryptedData, base64_decode($pubKey));
        return base64_encode($this->encryptedData);
    }

    public function decrypt_data(string $encrypted_data, $privKey)
    {
        openssl_private_decrypt(base64_decode($encrypted_data), $this->decryptedData, base64_decode($privKey));
        return $this->decryptedData;
    }

    public static function encrypt(string $plaintext)
    {
        $key = env('APP_KEY');
        $ivlen = openssl_cipher_iv_length(self::$cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $ciphertext = openssl_encrypt($plaintext, self::$cipher, $key, $options=0, $iv);
        $ciphertext =  base64_encode(base64_encode($ciphertext) . '::' . base64_encode($iv));

        return $ciphertext;
    }

    public static function decrypt(string $ciphertext)
    {
        $key = env('APP_KEY');
        $data = explode('::', base64_decode($ciphertext), 2);

        if (!$data || count($data) < 2)
        {
            throw new \Exception('The ciphertext is invalid.');
        }

        $plaintext = openssl_decrypt(base64_decode($data[0]), self::$cipher, $key, $options=0, base64_decode($data[1]));

        return  $plaintext;
    }
}