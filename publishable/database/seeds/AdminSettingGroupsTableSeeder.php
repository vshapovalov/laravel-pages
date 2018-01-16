<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSettingGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    DB::table('admin_setting_groups')->insert([ 'code' => 'site', 'name' => 'Сайт']);
	    DB::table('admin_setting_groups')->insert([ 'code' => 'admin', 'name' => 'Админ.панель']);
    }
}
