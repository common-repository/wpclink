<?php
/**
 * CLink Encryption and Decryption Functions
 *
 * CLink encryption and decryption of security keys
 *
 * @package CLink
 * @subpackage Link Manager
 */
 
 // Direct Access Not Allowed
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/**
 * Encrypt the Keys
 * 
 * @param string $data site key data
 * @param string $key secure key
 * @param string $method type
 * 
 * @return string $encrypted complete key
 */
function wpclink_new_encrypt( $data,  $key,  $method = 'AES-256-CBC')
{
    $ivSize = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivSize);
    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    
    // For storage/transmission, we simply concatenate the IV and cipher text
    $encrypted = base64_encode($iv . $encrypted);
    return $encrypted;
}
/**
 * Decrypt Keys
 * 
 * @param string $data site key data
 * @param string $key secure key
 * @param string $method type
 * 
 * @return string $data complete key
 */
function wpclink_new_decrypt( $data,  $key,  $method = 'AES-256-CBC')
{
    $data = base64_decode($data);
    $ivSize = openssl_cipher_iv_length($method);
    $iv = substr($data, 0, $ivSize);
    $data = openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv);
    return $data;
}
/**
 * CLink Encryption Code
 * 
 * @param string $pure_string string of encryption code
 * @param string $key key of encryption code
 * 
 * @return string
 */
function wpclink_encrypt_code($pure_string,$key) {
    $dirty = array("+", "/", "=");
    $clean = array("+", "/", "=");
    $iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $encrypted_string = mcrypt_encrypt(MCRYPT_BLOWFISH, $key, utf8_encode($pure_string), MCRYPT_MODE_ECB, $iv);
    $encrypted_string = base64_encode($encrypted_string);
    return str_replace($dirty, $clean, $encrypted_string);
}
/**
 * Decrypt Keys old function
 * 
 * @param string $data site key data
 * @param string $key secure key
 * @param string $method type
 * 
 * @return string $data complete key
 */
function wpclink_decrypt($data,$key) { 
	$method = 'AES-256-CBC';
	
    $data = base64_decode($data);
    $ivSize = openssl_cipher_iv_length($method);
    $iv = substr($data, 0, $ivSize);
    $data = openssl_decrypt(substr($data, $ivSize), $method, $key, OPENSSL_RAW_DATA, $iv);
    return $data;
}
/**
 * CLink Generate Unique URL
 * 
 * @param string $length lenght of url
 * @param string $keyspace allowed characters
 * 
 * @return string unique generated url
 */
function wpclink_go_live_link($length, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
        $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
}
/**
 * Encrypt the Keys old function
 * 
 * @param string $data site key data
 * @param string $key secure key
 * @param string $method type
 * 
 * @return string $encrypted complete key
 */
function wpclink_encrypt($data,$key) {
	
	$method = 'AES-256-CBC';
    
	$ivSize = openssl_cipher_iv_length($method);
    $iv = openssl_random_pseudo_bytes($ivSize);
    $encrypted = openssl_encrypt($data, $method, $key, OPENSSL_RAW_DATA, $iv);
    
    // For storage/transmission, we simply concatenate the IV and cipher text
    $encrypted = base64_encode($iv . $encrypted);
    return $encrypted;
	
}
/**
 * CLink Decryption Code
 * 
 * @param string $encrypted_string string of decriptiion code
 * @param string $key key of decriptiion
 * 
 * @return string
 */
function wpclink_decrypt_code($encrypted_string,$key) { 
    $dirty = array("+", "/", "=");
    $clean = array("+", "/", "=");
    $string = base64_decode(str_replace($clean, $dirty, $encrypted_string));
	
	
	$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $key,$string, MCRYPT_MODE_ECB, $iv);
    return $decrypted_string;
}
