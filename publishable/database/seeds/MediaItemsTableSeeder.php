<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MediaItemsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    DB::table('media_items')->insert(
	    	[
	    		'id' => 1,
		        'title' => 'root',
	            'parent_id' => null,
	            'path' => '0001;',
	            'type' => 'folder'
		    ]);
    }
}
