<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Hash
 *
 * @author Jiskit
 */
class Hash {
    //put your code here

    /**
     *
     * @param string $algo The algorithm (md5, sha1, whirlpool, etc)
     * @param string $data The data to encode
     * @param string $salt The salt (This should be the same throughout the system probably)
     * @return string The hashed/salted data
     */
    public static function create($algo, $data, $salt) {

        $context = hash_init($algo, HASH_HMAC, $salt);
        hash_update($context, $data);

        return hash_final($context);
    }

}
