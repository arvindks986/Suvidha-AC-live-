<?php namespace App\Http\Controllers\Common;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Redirect,Session,Response,Input;
use Image, CropImage, Auth, Validator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use App\Classes\xssClean;

class ExceptionHandlerController extends Controller{

	public $upload_folder = '';

	public function __construct(){
		$this->xssClean = new xssClean;
    	$this->upload_folder = 'uploads1';   
  	}

	public function upload($request, $size = 1024, $is_image = 'image', $destination_path = ''){

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
           // $filename   =   time().$this->xssClean->clean_input($file->getClientOriginalName());
            
           // $filetype   =   $file->getMimeType();

           
            $extension  =   $file->getClientOriginalExtension();

            $string     =   time().$this->xssClean->clean_input($file->getClientOriginalName());
            $without_extension = pathinfo($string, PATHINFO_FILENAME);
            $newname    =   preg_replace('/[^A-Za-z0-9\-]/', '', $without_extension);
            $filename   =   $newname.'.'.$extension;
             $filetype   =   $file->getMimeType();

              if($is_image == 'image'){
           $allowed_mime = array(
                'image/jpeg',
                //'image/pjpeg',
                //'image/png',
                //'image/x-png',
            );
           // $allowed_error = "Please upload a valid jpg file.";
            if (!in_array($filetype, $allowed_mime)) {
            return Response::json([
                'success'   => false,
                'errors'    => "Allowed .jpg File Type Exception"
            ]);
        }
        else if($file->getSize() > $size*1024){
            return Response::json([
                'success'   => false,
                'errors'    => "Please upload a file less than ".$allowed_size." MB size ."
            ]);
        }

        }else if($is_image == 'pdf'){
            $allowed_mime = array(
                'application/pdf',
            );
          //  $allowed_error = "Please upload a valid jpg file.";

            if (!in_array($filetype, $allowed_mime)) {
            return Response::json([
                'success'   => false,
                'errors'    => "Allowed pdf File only"
            ]);
        }

        }

       
                 if($is_image == 'image'){
               if($extension!='jpg')
                  {
                    return Response::json([
                   'success'   => false,
                   'errors'    => "Please upload Only jpg file."
                   ]);
                  }
              }




              
        
         try{
          if (!file_exists($destination_path)) {
            mkdir($destination_path, 0777, true);
        }

           $file->move($destination_path,$filename);
        }catch(\Exception $e){
            return Response::json([
                'success'   => false,
                'errors'    => "Destination path does not exist."
            ]);
        }


        }catch(\Exception $e){
            return Response::json([
                'success'   => false,
                'errors'    => "Please upload a file less than 1 MB size."
            ]);
        }


       

       

       
        
        
      	
      	return Response::json([
        	'success' 	=> true,
        	'path' 	=> $destination_path.'/'.$filename
        ]);
        
  }

}