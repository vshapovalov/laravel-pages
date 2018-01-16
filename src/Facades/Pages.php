<?php
/**
 * Created by PhpStorm.
 * User: dr_sharp
 * Date: 10.10.2017
 * Time: 13:18
 */

namespace Vshapovalov\Pages\Facades;

use Illuminate\Support\Facades\Facade;

class Pages extends Facade {

	protected static function getFacadeAccessor() {
		return 'pages';
	}

}