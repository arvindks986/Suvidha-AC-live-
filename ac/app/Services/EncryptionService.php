<?php
namespace App\Services;

class EncryptionService
{
    public static function decrypt($encrypted, $passphrase = 'AwdL2cXoGHtULolv')
    {
        if (!$encrypted) return null;

        $data = base64_decode($encrypted);

        // Must start with "Salted__"
        if (substr($data, 0, 8) !== "Salted__") {
            return null;
        }

        $salt = substr($data, 8, 8);
        $ciphertext = substr($data, 16);

        // Derive key & IV (CryptoJS compatible)
        [$key, $iv] = self::evpBytesToKey($passphrase, $salt);

        $decrypted = openssl_decrypt(
            $ciphertext,
            'AES-256-CBC',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return json_decode($decrypted, true);
    }

    private static function evpBytesToKey($password, $salt)
    {
        $dt = '';
        $d = '';

        while (strlen($dt) < 48) { // 32 key + 16 iv
            $d = md5($d . $password . $salt, true);
            $dt .= $d;
        }

        return [
            substr($dt, 0, 32),   // key
            substr($dt, 32, 16),  // iv
        ];
    }
}
