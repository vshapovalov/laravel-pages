<?php

namespace Vshapovalov\Pages\Http\Controllers;

use Vshapovalov\Pages\Facades\Pages;
use Illuminate\Routing\Controller;

class PagesController extends Controller
{

    function getPage($url = '/'){

	    if ($route = Pages::getRouteByUrl($url)) {

	        return view(
		        $route['page']->template,
		        [
			        'page' => $route['page'],
			        'params' => isset($route['info']['params']) ? $route['info']['params'] : []
		        ]
	        );
	    } else {

	    	return view('404');
	    }
    }
}
