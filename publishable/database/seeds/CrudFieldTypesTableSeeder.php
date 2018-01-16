<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CrudFieldTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    DB::table('crud_field_types')->insert([ 'code' => 'textbox', 'name' => 'textbox']);
	    DB::table('crud_field_types')->insert([ 'code' => 'checkbox', 'name' => 'checkbox']);
	    DB::table('crud_field_types')->insert([ 'code' => 'colorbox', 'name' => 'colorbox']);
	    DB::table('crud_field_types')->insert([ 'code' => 'textarea', 'name' => 'textarea']);
	    DB::table('crud_field_types')->insert([ 'code' => 'datepicker', 'name' => 'datepicker']);
	    DB::table('crud_field_types')->insert([ 'code' => 'richedit', 'name' => 'richedit']);
	    DB::table('crud_field_types')->insert([ 'code' => 'image', 'name' => 'image']);
    }
}
