<?php
namespace app\components;

class EnDescrypt {
    const AUTH_MAGIC_SHA1 = '289a3b32d491e7bd4eea5a79ebb8d739';   // MD5 hash for FinJap

    /**
     * Encrypt by Sha1
     * @param $value
     * @return string
     */
    public static function encryptSha1($value) {
        return sha1($value . ':' . self::AUTH_MAGIC_SHA1);
    }
}