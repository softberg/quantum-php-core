<?php
namespace Quantum\Libraries\Encryption;

class Encryption
{
    private $res;
    private $digest_alg = 'sha512';
    private $private_key_bits = 1024;
    private $private_key_type = OPENSSL_KEYTYPE_RSA;
    private $config = [];
    private $privKey = '';
    private $pubKey = '';
    private $keys = [];
    private $encryptedData;
    private $decryptedData;
    public static $cipher = 'AES-128-CBC';

    public function __construct()
    {
        $this->set_config();
        $this->set_res();
        $this->set_priv_key();
    }

    public function set_config()
    {
        $this->config = [
            "digest_alg" => $this->digest_alg,
            "private_key_type" => $this->private_key_type,
            "private_key_bits" => $this->private_key_bits
        ];
    }

    private function set_res()
    {
        $this->res = openssl_pkey_new($this->config);
    }

    private function set_priv_key()
    {
        openssl_pkey_export($this->res, $this->privKey);
        
        $pubKey = openssl_pkey_get_details($this->res);
        $this->pubKey = $pubKey["key"];
        
        $this->keys = [
            'public_key' => base64_encode($this->pubKey),
            'private_key' => base64_encode($this->privKey)
        ];
    }
    
    public function get_keys($obj = true)
    {
        return $obj ? (object) $this->keys : $this->keys;
    }

    private function public_encrypt($data, $pubKey)
    {
        openssl_public_encrypt($data, $this->encrypedData, $pubKey);
    }

    public function encrypt_data($data, $pubKey)
    {
        $this->public_encrypt($data, base64_decode($pubKey));
        return base64_encode($this->encrypedData);
    }

    private function private_decrypt($encrypted_data, $privKey)
    {
        openssl_private_decrypt($encrypted_data, $this->decryptedData, $privKey);
    }

    public function decrypt_data($encrypted_data, $privKey)
    {
        $this->private_decrypt(base64_decode($encrypted_data), base64_decode($privKey));
        return $this->decryptedData;
    }

    private static function encrypt($plaintext)
    {
        $key = env('APP_KEY');
        $ciphertext = '';

        if (in_array(self::$cipher, openssl_get_cipher_methods()))
        {
            $ivlen = openssl_cipher_iv_length(self::$cipher);
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext = openssl_encrypt($plaintext, self::$cipher, $key, $options=0, $iv);
            $ciphertext =  base64_encode(base64_encode($ciphertext) . '::' . base64_encode($iv));
        }

        return $ciphertext;
    }

    public static function encryptString($plaintext)
    {
       return self::encrypt($plaintext);
    }

    private static function decrypt($ciphertext)
    {
        $key = env('APP_KEY');
        $data = explode('::', base64_decode($ciphertext), 2);
        if (in_array(self::$cipher, openssl_get_cipher_methods()))
        {
            $plaintext = openssl_decrypt(base64_decode($data[0]), self::$cipher, $key, $options=0, base64_decode($data[1]));
        }

        return $plaintext;
    }

    public static function decryptString($ciphertext)
    {
        return self::decrypt($ciphertext);
    }
}