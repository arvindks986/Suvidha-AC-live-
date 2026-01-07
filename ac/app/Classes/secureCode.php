<?php

namespace App\Classes;

class secureCode {
    
            //DECRYPTION FUNCTION STARTS HERE
    public function decrypt($code) {     
        $hex_iv = '00000000000000000000000000000000';
        $sessionid = '2d040eb61304332fa737f4e27880c3394293394d10b24d036c78017f4c147054';
        $key = hash('sha256', $sessionid, true);
        $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
            mcrypt_generic_init($td, $key, $this->hexToStr($hex_iv));
        $str = mdecrypt_generic($td, base64_decode($code));
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
            mcrypt_generic_deinit($td);
            mcrypt_module_close($td);        
        return $this->strippadding($str);               
        }
            //DECRYPTION FUNCTION ENDS HERE

            
    public function strippadding($string) {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
                return $string;
        } else {
                return false;
            }
        }
    
            
    public function hexToStr($hex)
        {
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2)
            {
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
            }
            return $string;
        }

}
