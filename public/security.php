<?php
// File: security.php

class SecurityHelper {
    
    // Kunci Rahasia (AES-256 requires strong key)
    private static $secret_key = 'HungerSwitch_Secret_Key_2025_Secure!'; 
    private static $cipher_method = 'AES-256-CBC';
    
    //session
    public static function secureSessionStart() {
        if (session_status() === PHP_SESSION_NONE) {
            // Setting cookie parameter agar aman
            $cookieParams = session_get_cookie_params();
            session_set_cookie_params([
                'lifetime' => $cookieParams['lifetime'],
                'path' => '/',
                'domain' => $cookieParams['domain'],
                'secure' => isset($_SERVER['HTTPS']), 
                'httponly' => true, 
                'samesite' => 'Lax'
            ]);
            
            session_start();
        }
    }

    //enskripsi
    public static function encrypt($data) {
        $key = hash('sha256', self::$secret_key);
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::$cipher_method));
        $encrypted = openssl_encrypt($data, self::$cipher_method, $key, 0, $iv);
        return base64_encode($encrypted . '::' . $iv);
    }

    public static function decrypt($data) {
        try {
            $key = hash('sha256', self::$secret_key);
            $payload = base64_decode($data);
            if (strpos($payload, '::') === false) return false;
            list($encrypted_data, $iv) = explode('::', $payload, 2);
            return openssl_decrypt($encrypted_data, self::$cipher_method, $key, 0, $iv);
        } catch (Exception $e) {
            return false;
        }
    }

    
    public static function setSecureCookie($name, $value, $days = 7) {
        $encryptedValue = self::encrypt($value); // Enkripsi isi cookie
        $expires = time() + ($days * 24 * 60 * 60);
        setcookie($name, $encryptedValue, $expires, "/", "", isset($_SERVER['HTTPS']), true);
    }

    public static function getSecureCookie($name) {
        if (isset($_COOKIE[$name])) {
            return self::decrypt($_COOKIE[$name]); // Dekripsi saat dibaca
        }
        return null;
    }

    // Generator Token untuk keamanan form
    public static function generateCSRFToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
?>