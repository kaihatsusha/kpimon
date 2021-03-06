<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MemCache
 *
 * @author ThuongQBD
 */
namespace app\components;

class MemCache  extends \yii\caching\MemCache{
	
	/**
     * Initializes this application component.
     * It creates the memcache instance and adds memcache servers.
     */
    public function init()
    {
		$this->keyPrefix = \Yii::$app->id;
        return parent::init();
    }
	
	/**
     * Builds a normalized cache key from a given key.
     *
     * If the given key is a string containing alphanumeric characters only and no more than 32 characters,
     * then the key will be returned back prefixed with [[keyPrefix]]. Otherwise, a normalized key
     * is generated by serializing the given key, applying MD5 hashing, and prefixing with [[keyPrefix]].
     *
     * @param mixed $key the key to be normalized
     * @return string the generated cache key
     */
    public function buildKey($key)
    {
        if (!is_string($key)) {
            $key = md5(json_encode($key));
        }
        return $this->keyPrefix . $key;
    }
}
