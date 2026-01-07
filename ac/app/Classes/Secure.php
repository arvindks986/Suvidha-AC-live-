<?php
/**
 * Class to Encrypt And Decrypt data
 *
 */
namespace App\Classes;
use Closure,Response,Redirect,Crypt,Request;
use App\Http\Controllers\API\ResponseController;

class Secure{
    const METHOD = 'AES-256-CBC';

    /**
     * Encrypts the data
     * 
     * @param string $message - plaintext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encode - set to FALSE to prevent base64-encoded 
     * @return string (raw binary)
     */
    
    public const CRYPTOKEY = '2d040eb61304332fa737f4e27880c3394293394d10b24d036c78017f4c147054';

    public const HASHKEY = '2d040eb61304332fa737f4e27880c3394293394d10b24d036c78017f4c147054';

    public const IV =  '0000000000000000';

    public function __construct() {

        $this->ResponseMethod = new ResponseController;
        $this->okStatus = "success";
        $this->errStatus = "error";
        $this->bad_response = $this->ResponseMethod::HTTP_BAD_REQUEST;
        $this->ok_response = $this->ResponseMethod::HTTP_ACCEPTED;      

    }


    public function encrypt($message, $encode = true){
        $ivSize = openssl_cipher_iv_length(self::METHOD);
        $iv = openssl_random_pseudo_bytes($ivSize);
        $key = hash('sha256', self::CRYPTOKEY, true);

        try{
         $ciphertext = openssl_encrypt(
            $message,
            self::METHOD,
            self::CRYPTOKEY,
            OPENSSL_RAW_DATA,
            $iv
         );
         
         if(!$ciphertext)throw new Exception('Unable To Encrypt Data');

         $hash = hash_hmac('sha256', $ciphertext.$iv, self::HASHKEY, true);

             if ($hash === false){
                self::CRYPTOKEY ;
                self::HASHKEY;
                throw new \Exception("Internal error: hash_hmac() failed");
            }

         $sendEncrypt=$iv.$hash.$ciphertext;

        }catch(Exception $e){
            $encode=false;
            $sendEncrypt=$e->getMessage();
        }

        // Now let's pack the IV and the ciphertext together
        // Naively, we can just concatenate
        if ($encode) {
            $sendEncrypt=base64_encode($sendEncrypt);
        }
        return $sendEncrypt;
    }

    /**
     * Decrypts the data
     * 
     * @param string $message - ciphertext message
     * @param string $key - encryption key (raw binary expected)
     * @param boolean $encoded - set to FALSE to prevent base64-decode
     * @return string
     */
    public function decrypt($message, $encoded = true){

        if ($encoded) {
        // try{

            $message = base64_decode($message, true);
            if ($message === false) {

                /*$data = 'Inputs data manimulated. Re-enter correct data.';
                return $this->ResponseMethod->get_http_response($this->errStatus, $data, $this->bad_response);*/

             throw new \Exception('Unable To Decrypt Data');
            }else{
               
                $key = hash('sha256', self::CRYPTOKEY, true);
                $iv = substr($message, 0, 16);
                $hash = substr($message, 16, 32);
                $ciphertext = substr($message, 48);

                if (!hash_equals(hash_hmac('sha256', $ciphertext.$iv, self::HASHKEY, true), $hash)){    

                    throw new \Exception("Internal error: Hash verification failed");
                }

                $plaintext = openssl_decrypt(
                    $ciphertext,
                    self::METHOD,
                    self::CRYPTOKEY,
                    OPENSSL_RAW_DATA,
                    $iv
                );
            }
         /*}catch(\Exception $e){
            $data = 'Inputs data not encrypted. Re-enter correct data.';
            return $this->ResponseMethod->get_http_response($this->errStatus, $data, $this->bad_response);
         }*/

        }

        return $plaintext;
    }

    // USAGE

/*$data_to_encrypt = 'Hello';

$encrypted = Secure::encrypt($data_to_encrypt);
// OUtput - Encrypted data

$decrypted = Secure::decrypt($encrypted);*/
// Output - Hello



/**
 * simple method to encrypt or decrypt a plain text string
 * initialization vector(IV) has to be the same when encrypting and decrypting
 * 
 * @param string $action: can be 'encrypt' or 'decrypt'
 * @param string $string: string to encrypt or decrypt
 *
 * @return string
 */

 public function encrypt_decrypt($action, $string) {
        $output = false;

        $encrypt_method = "AES-256-CBC";
        $secret_key = '2d040eb61304332fa737f4e27880c3394293394d10b24d036c78017f4c147054';
        $secret_iv = '0000000000000000';

        // hash
        //$key = hash('sha256', $secret_key);
        $key = substr(hash('sha256', $secret_key), 0, 32);
       
        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
         
        if ( $action == 'encrypt' ) {

            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
                    
                if($output === false){
                    
                   throw new \Exception('Unable To Encrypt Data');
                    $data = 'Inputs data manimulated. Re-enter correct data.';
                    return $this->ResponseMethod->get_http_response($this->errStatus, $data, $this->bad_response); 
                 }

        } else if( $action == 'decrypt' ) {

            $output = openssl_decrypt($string, $encrypt_method, $key, 0, $iv);
              
                if($output === false){

                    throw new \Exception('Unable To Decrypt Data');

                    $data = 'Inputs data manimulated. Re-enter correct data.';
                    return $this->ResponseMethod->get_http_response($this->errStatus, $data, $this->bad_response); 
                 }
                     
        }

        return $output;
    }


/*USAGE
$plain_txt = "This is my plain text";
echo "Plain Text =" .$plain_txt. "\n";

$encrypted_txt = encrypt_decrypt('encrypt', $plain_txt);
echo "Encrypted Text = " .$encrypted_txt. "\n";

$decrypted_txt = encrypt_decrypt('decrypt', $encrypted_txt);
echo "Decrypted Text =" .$decrypted_txt. "\n";

if ( $plain_txt === $decrypted_txt ) echo "SUCCESS";
else echo "FAILED";
*/

}



