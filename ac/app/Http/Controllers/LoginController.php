<?php namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Auth;

class LoginController extends Controller
{

    public function getLogin(){
        $data = [];
        return view("welcome1", $data);
    }



}
