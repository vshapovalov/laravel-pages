<?php

namespace Vshapovalov\Pages;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Page extends Model
{
	public function save( array $options = [] ) {
		Cache::forget('pages');

		return parent::save( $options );
	}

}
