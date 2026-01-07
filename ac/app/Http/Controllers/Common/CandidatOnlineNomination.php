<?php namespace App\Http\Controllers\Common;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect,Session,Response,Input;
use Image, CropImage, Auth, Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Classes\xssClean;

class CandidatOnlineNomination extends Controller{

	public $upload_folder = '';

	public function __construct(){
		$this->xssClean = new xssClean;
    	$this->upload_folder = config("public_config.upload_folder");   
  	}

	public function upload($request, $size = 2048, $is_image = 'image', $destination_path = ''){

        // ini_set('max_execution_time', 0);
        // ini_set("pcre.backtrack_limit", "50000000000000000000000");
        // ini_set('memory_limit', '-1');

        if(!$request->has('file')){
            return Response::json([
                'success'   => false,
                'errors'    => "Please upload a file less than ".$allowed_size."MB size."
            ]);
        }

        $tmp_folder = '';
        $destination_path = $this->upload_folder.'/'.$destination_path;
        foreach (explode('/',$destination_path) as $itr_folder) {
            if(empty($tmp_folder)){
                $tmp_folder = $itr_folder;
            }else{
                $tmp_folder = $tmp_folder.'/'.$itr_folder;
            }
            if (!file_exists($tmp_folder)) {
              mkdir($tmp_folder, 0777, true);
            }
        }

        try{
            $file       =   $request->file('file');
            $filename   =   time().$this->xssClean->clean_input($file->getClientOriginalName());
            $filetype   =   $file->getMimeType();
        }catch(\Exception $e){
            return Response::json([
                'success'   => false,
                'errors'    => "Please upload a file less than 1 MB size."
            ]);
        }


        $allowed_size = $size/1024;
        if($file->getSize() > $size*1024){
            return Response::json([
        		'success' 	=> false,
        		'errors' 	=> "Please upload a file less than ".$allowed_size."MB size."
        	]);
        }

        if($is_image == 'image'){
            $allowed_mime = array(
                'image/jpeg',
                'image/pjpeg',
                'image/png',
                'image/x-png',
            );
            $allowed_error = "Please upload a valid jpeg, jpg, png file.";
        }else if($is_image == 'pdf'){
            $allowed_mime = array(
                'application/pdf',
                'application/octet-stream',
            );
            $allowed_error = "Please upload a valid jpeg, jpg, png file.";
        }

        if (!in_array($filetype, $allowed_mime)) {
            return Response::json([
        		'success' 	=> false,
        		'errors' 	=> "File Type Not Allowed"
        	]);
        }

        if (!file_exists($destination_path)) {
            mkdir($destination_path, 0777, true);
        }
        
        try{
           $file->move($destination_path,$filename);
        }catch(\Exception $e){
        	return Response::json([
        		'success' 	=> false,
        		'errors' 	=> "Destination path does not exist."
        	]);
        }
      	
      	return Response::json([
        	'success' 	=> true,
        	'path' 	=> $destination_path.'/'.$filename
        ]);
        
  }

}