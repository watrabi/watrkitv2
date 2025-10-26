<?php

namespace watrlabs;
use Pixie\Connection;
use Pixie\QueryBuilder\QueryBuilderHandler;
use watrlabs\users\getinfo;
use watrlabs\watrkit\pagebuilder;

class sitefunctions {
    
    private $key = "";
    private $method = 'aes-256-gcm'; 
    
    public function __construct() {
        $this->key = $_ENV["ENCRYPTION_KEY"];
    }

        public function encrypt($text) {
            $ivLength = openssl_cipher_iv_length($this->method);
            $iv = random_bytes($ivLength);

            $tag = '';
            $ciphertext = openssl_encrypt($text, $this->method, $this->key, OPENSSL_RAW_DATA, $iv, $tag);

            return base64_encode($iv . $tag . $ciphertext);
        }
    
    public function decrypt($data) {
        $data = base64_decode($data);

        $ivLength = openssl_cipher_iv_length($this->method);
        $iv = substr($data, 0, $ivLength);
        $tag = substr($data, $ivLength, 16); 
        $ciphertext = substr($data, $ivLength + 16);

        return openssl_decrypt($ciphertext, $this->method, $this->key, OPENSSL_RAW_DATA, $iv, $tag);
    }

    public function format_number($int){

        if($int >= 1000000000){
            $formated = number_format($int / 1000000000, 1);
            $suffix = "B+";
        } elseif($int >= 1000000){
            $formated = number_format($int / 1000000, 1);
            $suffix = "M+";
        } elseif ($int >= 1000){
            $formated = number_format($int / 1000, 1);
            $suffix = "K+";
        } else {
            $formated = $int;
            $suffix = "";
        }

        if (substr($formated, -2) === '.0') {
            $formated = substr($formated, 0, -2);
        } else {
        }

        return $formated . $suffix;

    }

    public function get_setting($name) {
        global $db;

        $dbvalue = $db->table("site_config")->where("thekey", $name)->first();

        if($dbvalue == null){
            return $_ENV["CONFIG_" . $name] ?? null;
        } else {
            return $dbvalue->value;
        }
    }
    
    public function getip($encrypt = false) {
        
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            return "127.0.0.1";
        }
        
        if(isset($ip)){
            if($encrypt){
                return $this->encrypt($ip);
            } else {
                return $ip;
            }
        }
        
        return false; // fallback
        
    }
    
    public function genstring($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    // https://stackoverflow.com/questions/14649645/resize-image-in-php - ty
    public function resize_image($file, $w, $h, $crop=FALSE) {
        list($width, $height) = getimagesize($file);
        $r = $width / $height;
        if ($crop) {
            if ($width > $height) {
                $width = ceil($width-($width*abs($r-$w/$h)));
            } else {
                $height = ceil($height-($height*abs($r-$w/$h)));
            }
            $newwidth = $w;
            $newheight = $h;
        } else {
            if ($w/$h > $r) {
                $newwidth = $h*$r;
                $newheight = $h;
            } else {
                $newheight = $w/$r;
                $newwidth = $w;
            }
        }
        $src = imagecreatefrompng($file);
        $dst = imagecreatetruecolor($newwidth, $newheight);


        // Fix transparency
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
        imagefill($dst, 0, 0, $transparent);

        // Fix black outlining
        imagealphablending($src, true);
        imagesavealpha($src, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

        return imagepng($dst);
    }

    static function filter_text($text) 
    {
        $badlist = array_filter(explode(",", file_get_contents("../storage/bad_words.txt")));
        $filterCount = count($badlist);

        for ($i = 0; $i < $filterCount; $i++) {
            $pattern = '/' . preg_quote($badlist[$i], '/') . '/i';
            $text = preg_replace_callback($pattern, function($matches) {
                return str_repeat('#', strlen($matches[0]));
            }, $text);
        }

        return $text;
    }

    static function isbadtext($text){
        $badlist = array_map('trim', array_filter(explode(",", file_get_contents("../storage/bad_words.txt"))));
                
        foreach ($badlist as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }
        
        return false;

    }

    public function set_message($message, $type = "error") {
        $message = array(
            "type" => $type,
            "message" => $message
        );
        
        $encoded = json_encode($message);
        $encrypted = $this->encrypt($encoded);
        setcookie("msg", $encrypted, time() + 500, '');
        return $encrypted;
    }
    
    public function get_message(){

        $page = new pagebuilder;

        if(isset($_COOKIE["msg"])){
            
            $msg = $_COOKIE["msg"];
            
            $decrypted = $this->decrypt($msg);
            $decoded = json_decode($decrypted, true);
            
            if($decoded["type"] == "error"){
                $page->build_component("status", ["status"=>"error", "msg"=>$decoded["message"]]);
                setcookie("msg", $msg, time() - 500, '');
            } elseif($decoded["type"] == "notice"){
                $page->build_component("status", ["status"=>"confirm", "msg"=>$decoded["message"]]);
                setcookie("msg", $msg, time() - 500, '');
            } else {
                //throw new Exception('Invalid message type!');
            }
            
        } else {
            return false;
        }
    }
    
}