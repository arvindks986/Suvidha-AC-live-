<?php namespace App\models\Common;

use Illuminate\Database\Eloquent\Model;

class FileModel extends Model
{
 	
	public static function get_file_path($full_path = 'uploads'){
		$path = '';
		foreach(explode('/',$full_path) as $folder_name){
			$path .= $folder_name;
			if (!file_exists($path)) {
		      mkdir($path, 0777, true);
		    }
		    $path .= '/';
		}
		return $full_path;
    }

}