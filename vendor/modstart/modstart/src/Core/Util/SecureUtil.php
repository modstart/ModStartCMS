<?php

namespace ModStart\Core\Util;

use ModStart\Core\Exception\BizException;

class SecureUtil
{
    const DEFAULT_CIPHER_ALGO = 'aes-256-cbc';

    /**
     * 默认模式
     */
    const MODE_DEFAULT = 'default';
    /**
     * 适配encrypt-js模式
     */
    const MODE_SALTED = 'salted';

    /**
     * @param $key string base64后的key
     * @param $data string 需要加密的数据
     * @param $keyIsBase64 bool key是否是base64后的
     * @param $mode string 加密模式，default默认模式
     * @return string base64后的加密数据
     */
    public static function aesEncode($key, $data, $keyIsBase64 = false, $mode = 'default')
    {
        $encryptionKey = $key;
        if ($keyIsBase64) {
            $encryptionKey = base64_decode($encryptionKey);
        }
        switch ($mode) {
            case self::MODE_SALTED:
                $method = "AES-256-CBC";
                $salt = openssl_random_pseudo_bytes(8);
                $keyIv = self::evpBytesToKey($salt, $key);
                $key = substr($keyIv, 0, 32);
                $iv = substr($keyIv, 32, 16);
                $ciphertext = openssl_encrypt(urlencode($data), $method, $key, OPENSSL_RAW_DATA, $iv);
                return base64_encode("Salted__" . $salt . $ciphertext);
            default:
                $ivLength = openssl_cipher_iv_length(self::DEFAULT_CIPHER_ALGO);
                $iv = openssl_random_pseudo_bytes($ivLength);
                $encrypted = openssl_encrypt($data, self::DEFAULT_CIPHER_ALGO, $encryptionKey, 0, $iv);
                return base64_encode($encrypted . '::' . $iv);
        }
    }

    /**
     * @param $key string base64后的key
     * @param $data string base64后的加密数据
     * @param $keyIsBase64 bool key是否是base64后的
     * @return false|string 解密后的数据
     */
    public static function aesDecode($key, $data, $keyIsBase64 = false)
    {
        BizException::throwsIfEmpty('SecureUtil.Key Empty', $key);
        $encryptionKey = $key;
        if ($keyIsBase64) {
            $encryptionKey = base64_decode($encryptionKey);
        }
        $data = base64_decode($data);
        if (strpos($data, 'Salted__') === 0) {
            $salt = substr($data, 8, 8);
            $ciphertext = substr($data, 16);
            $keyIv = self::evpBytesToKey($salt, $key);
            $key = substr($keyIv, 0, 32);
            $iv = substr($keyIv, 32, 16);
            $encryptedData = urldecode(openssl_decrypt($ciphertext, self::DEFAULT_CIPHER_ALGO, $key, OPENSSL_RAW_DATA, $iv));
            return $encryptedData;
        }
        $pcs = explode('::', $data, 2);
        if (count($pcs) != 2) {
            return null;
        }
        list($encryptedData, $iv) = $pcs;
        return openssl_decrypt($encryptedData, self::DEFAULT_CIPHER_ALGO, $encryptionKey, 0, $iv);
    }

    private static function evpBytesToKey($salt, $password)
    {
        $bytes = '';
        $last = '';
        while (strlen($bytes) < 48) {
            $last = hash('md5', $last . $password . $salt, true);
            $bytes .= $last;
        }
        return $bytes;
    }

    public static function encryptKey()
    {
        return config('env.ENCRYPT_KEY');
    }
}
