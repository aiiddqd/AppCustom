<?php

namespace Modules\AppCustom\Http\Controllers;

use Illuminate\Routing\Controller;

class AppCustomController extends Controller
{

    /**
     * @return \Illuminate\Http\Response
     */
    public  static function index(){
        return \Response::json(['test ok' => 1]);
    }
}
