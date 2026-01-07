<?php
/*
 * Copyright (c) 2011 Le Lag 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.

 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
namespace App;

  class Base32 {

   private static $map = array(
        'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', //  7
        'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', // 15
        'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', // 23
        'Y', 'Z', '2', '3', '4', '5', '6', '7', // 31
        '='  // padding char
    );
    
   private static $flippedMap = array(
        'A'=>'0', 'B'=>'1', 'C'=>'2', 'D'=>'3', 'E'=>'4', 'F'=>'5', 'G'=>'6', 'H'=>'7',
        'I'=>'8', 'J'=>'9', 'K'=>'10', 'L'=>'11', 'M'=>'12', 'N'=>'13', 'O'=>'14', 'P'=>'15',
        'Q'=>'16', 'R'=>'17', 'S'=>'18', 'T'=>'19', 'U'=>'20', 'V'=>'21', 'W'=>'22', 'X'=>'23',
        'Y'=>'24', 'Z'=>'25', '2'=>'26', '3'=>'27', '4'=>'28', '5'=>'29', '6'=>'30', '7'=>'31'
    );
    
    /**
     *    Use padding false when encoding for urls
     *
     * @return base32 encoded string
     * @author Bryan Ruiz
     **/
    public static function encode($input, $padding = true) {
        if(empty($input)) return "";
        $input = str_split($input);
        $binaryString = "";
        for($i = 0; $i < count($input); $i++) {
            $binaryString .= str_pad(base_convert(ord($input[$i]), 10, 2), 8, '0', STR_PAD_LEFT);
        }
        $fiveBitBinaryArray = str_split($binaryString, 5);
        $base32 = "";
        $i=0;
        while($i < count($fiveBitBinaryArray)) {    
            $base32 .= self::$map[base_convert(str_pad($fiveBitBinaryArray[$i], 5,'0'), 2, 10)];
            $i++;
        }
        if($padding && ($x = strlen($binaryString) % 40) != 0) {
            if($x == 8) $base32 .= str_repeat(self::$map[32], 6);
            else if($x == 16) $base32 .= str_repeat(self::$map[32], 4);
            else if($x == 24) $base32 .= str_repeat(self::$map[32], 3);
            else if($x == 32) $base32 .= self::$map[32];
        }
        return $base32;
    }
    
    public static function decode($input) {
        if(empty($input)) return;
        $paddingCharCount = substr_count($input, self::$map[32]);
        $allowedValues = array(6,4,3,1,0);
        if(!in_array($paddingCharCount, $allowedValues)) return false;
        for($i=0; $i<4; $i++){ 
            if($paddingCharCount == $allowedValues[$i] && 
                substr($input, -($allowedValues[$i])) != str_repeat(self::$map[32], $allowedValues[$i])) return false;
        }
        $input = str_replace('=','', $input);
        $input = str_split($input);
        $binaryString = "";
        for($i=0; $i < count($input); $i = $i+8) {
            $x = "";
            if(!in_array($input[$i], self::$map)) return false;
            for($j=0; $j < 8; $j++) {
                $x .= str_pad(base_convert(@self::$flippedMap[@$input[$i + $j]], 10, 2), 5, '0', STR_PAD_LEFT);
            }
            $eightBits = str_split($x, 8);
            for($z = 0; $z < count($eightBits); $z++) {
                $binaryString .= ( ($y = chr(base_convert($eightBits[$z], 2, 10))) || ord($y) == 48 ) ? $y:"";
            }
        }
        return $binaryString;
    }
}
class OTP {
    /**
     * The base32 encoded secret key
     * @var string
     */
    public $secret;

    /**
     * The algorithm used for the hmac hash function
     * @var string
     */
    public $digest;

    /**
     * The number of digits in the one-time password
     * @var integer
     */ 
    public $digits;

    /**
     * Constructor for the OTP class
     * @param string $secret the secret key
     * @param array $opt options array can contain the
     * following keys :
     *   @param integer digits : the number of digits in the one time password
     *   Currently Google Authenticator only support 6. Defaults to 6.
     *   @param string digest : the algorithm used for the hmac hash function
     *   Google Authenticator only support sha1. Defaults to sha1
     *
     * @return new OTP class.
     */
    public function __construct($secret, $opt = Array()) {
      $this->digits = isset($opt['digits']) ? $opt['digits'] : 6;
      $this->digest = 'sha1';//isset($opt['digest']) ? $opt['digest'] : 'sha1';//';
      $this->secret = $secret;
    }

    /**
     * Generate a one-time password
     *
     * @param integer $input : number used to seed the hmac hash function.
     * This number is usually a counter (HOTP) or calculated based on the current
     * timestamp (see TOTP class).
     * @return integer the one-time password 
     */
    public function generateOTP($input) {
      $hash = hash_hmac($this->digest, $this->intToBytestring($input), $this->byteSecret());
      foreach(str_split($hash, 2) as $hex) { // stupid PHP has bin2hex but no hex2bin WTF
        $hmac[] = hexdec($hex);
      }
      $offset = $hmac[19] & 0xf;
      $code = ($hmac[$offset+0] & 0x7F) << 24 |
        ($hmac[$offset + 1] & 0xFF) << 16 |
        ($hmac[$offset + 2] & 0xFF) << 8 |
        ($hmac[$offset + 3] & 0xFF);
      return $code % pow(10, $this->digits);
    }

    /**
     * Returns the binary value of the base32 encoded secret
     * @access private
     * This method should be private but was left public for
     * phpunit tests to work.
     * @return binary secret key
     */
    public function byteSecret() {
      return \App\Base32::decode($this->secret);
    }

    public function encodeSecret() {
      return \App\Base32::encode($this->secret);
    }

    public function decodeSecret($encodeSecret) {
      return \App\Base32::decode($encodeSecret);
    }

    /**
     * Turns an integer in a OATH bytestring
     * @param integer $int
     * @access private
     * @return string bytestring
     */
    public function intToBytestring($int) {
      $result = Array();
      while($int != 0) {
        $result[] = chr($int & 0xFF);
        $int >>= 8;
      }
      return str_pad(join(array_reverse($result)), 8, "\000", STR_PAD_LEFT);
    }
  }

  /**
   * HOTP - One time password generator 
   * 
   * The HOTP class allow for the generation 
   * and verification of one-time password using 
   * the HOTP specified algorithm.
   *
   * This class is meant to be compatible with 
   * Google Authenticator
   *
   * This class was originally ported from the rotp

   */
  class TOTP extends OTP {
    /**
     * The interval in seconds for a one-time password timeframe
     * Defaults to 30
     * @var integer
     */
    public $interval;

    public function __construct($s, $opt = Array()) {
      $this->interval = isset($opt['interval']) ? $opt['interval'] : 30;
      parent::__construct($s, $opt);
    }

    /**
     *  Get the password for a specific timestamp value 
     *
     *  @param integer $timestamp the timestamp which is timecoded and 
     *  used to seed the hmac hash function.
     *  @return integer the One Time Password
     */
    public function at($timestamp) {
      return $this->generateOTP($this->timecode($timestamp));
    }

    /**
     *  Get the password for the current timestamp value 
     *
     *  @return integer the current One Time Password
     */
    public function now() {
      return $this->generateOTP($this->timecode(time()));
    }

    /**
     * Verify if a password is valid for a specific counter value
     *
     * @param integer $otp the one-time password 
     * @param integer $timestamp the timestamp for the a given time, defaults to current time.
     * @return  bool true if the counter is valid, false otherwise
     */
    public function verify($otp, $timestamp = null) {
      if($timestamp === null)
        $timestamp = time();
      return ($otp == $this->at($timestamp));
    }

    /**
     * Returns the uri for a specific secret for totp method.
     * Can be encoded as a image for simple configuration in 
     * Google Authenticator.
     *
     * @param string $name the name of the account / profile
     * @return string the uri for the hmac secret
     */
    public function provisioning_uri($name) {
      return "otpauth://totp/".urlencode($name)."?secret={$this->secret}";
    }

    /**
     * Transform a timestamp in a counter based on specified internal
     *
     * @param integer $timestamp
     * @return integer the timecode
     */
    protected function timecode($timestamp) {
      return (int)( (((int)$timestamp * 1000) / ($this->interval * 1000)));
    }
  }
  // class HOTP extends OTP {
  //   /**
  //    *  Get the password for a specific counter value
  //    *  @param integer $count the counter which is used to
  //    *  seed the hmac hash function.
  //    *  @return integer the One Time Password
  //    */
  //   public function at($count) {
  //       return $this->generateOTP($count);
  //   }


  //   /**
  //    * Verify if a password is valid for a specific counter value
  //    *
  //    * @param integer $otp the one-time password 
  //    * @param integer $counter the counter value
  //    * @return  bool true if the counter is valid, false otherwise
  //    */
  //   public function verify($otp, $counter) {
  //     return ($otp == $this->at($counter));
  //   }

  //   public function verify_otp($otp, $key) {
  //      $hotp = new HOTP($key);
  //      $ls = [];       
  //      for($i=0; $i < 1000; $i++){
  //         $ls[] = $hotp->at($i);
  //      }
  //      if (in_array($otp, $ls)){
  //       return true;
  //      }else{
  //       return false;
  //      }
  //   }

  //   /**
  //    * Returns the uri for a specific secret for hotp method.
  //    * Can be encoded as a image for simple configuration in 
  //    * Google Authenticator.
  //    *
  //    * @param string $name the name of the account / profile
  //    * @param integer $initial_count the initial counter 
  //    * @return string the uri for the hmac secret
  //    */
  //   public function provisioning_uri($name, $initial_count) {
  //     return "otpauth://hotp/".urlencode($name)."?secret={$this->secret}&counter=$initial_count";
  //   }
  // }


