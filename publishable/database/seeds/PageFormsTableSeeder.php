<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PageFormsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	try {
		    if (! \Vshapovalov\Crud\Models\CrudForm::where('code', 'pages')->first()) {

			    $fields = [];

			    $fields[0] = new \Vshapovalov\Crud\Models\CrudField();

			    $fields[0]->name       = 'title';
			    $fields[0]->caption    = 'заголовок';
			    $fields[0]->type       = 'textbox';
			    $fields[0]->visibility = '[ "browse", "add", "edit"]';
			    $fields[0]->validation = 'required|string|max:191';
			    $fields[0]->tab        = 'Основные параметры';
			    $fields[0]->order      = '1';
			    $fields[0]->columns    = '6';

			    $fields[1] = new \Vshapovalov\Crud\Models\CrudField();

			    $fields[1]->name       = 'code';
			    $fields[1]->caption    = 'Код';
			    $fields[1]->type       = 'textbox';
			    $fields[1]->visibility = '[ "browse", "add", "edit"]';
			    $fields[1]->validation = 'required|string|max:191';
			    $fields[1]->tab        = 'Основные параметры';
			    $fields[1]->order      = '2';
			    $fields[1]->columns    = '6';


			    $fields[2] = new \Vshapovalov\Crud\Models\CrudField();

			    $fields[2]->name       = 'url';
			    $fields[2]->caption    = 'Ссылка';
			    $fields[2]->type       = 'textbox';
			    $fields[2]->visibility = '[ "browse", "add", "edit"]';
			    $fields[2]->validation = 'required|string|max:191';
			    $fields[2]->tab        = 'Основные параметры';
			    $fields[2]->order      = '3';
			    $fields[2]->columns    = '6';


			    $fields[3] = new \Vshapovalov\Crud\Models\CrudField();

			    $fields[3]->name       = 'title_image';
			    $fields[3]->caption    = 'Изображение';
			    $fields[3]->type       = 'image';
			    $fields[3]->visibility = '[ "add", "edit"]';
			    $fields[3]->validation = '';
			    $fields[3]->tab        = 'Основные параметры';
			    $fields[3]->order      = '4';
			    $fields[3]->columns    = '6';
			    $fields[3]->additional = '{"mode":"single", "type":"image"}';


			    $fields[4] = new \Vshapovalov\Crud\Models\CrudField();

			    $fields[4]->name       = 'template';
			    $fields[4]->caption    = 'Шаблон';
			    $fields[4]->type       = 'textbox';
			    $fields[4]->visibility = '[ "browse", "add", "edit"]';
			    $fields[4]->validation = 'required|string|max:191';
			    $fields[4]->tab        = 'Основные параметры';
			    $fields[4]->order      = '5';
			    $fields[4]->columns    = '6';


			    $fields[5] = new \Vshapovalov\Crud\Models\CrudField();

			    $fields[5]->name       = 'body';
			    $fields[5]->caption    = 'Контент';
			    $fields[5]->type       = 'richedit';
			    $fields[5]->visibility = '[ "add", "edit"]';
			    $fields[5]->validation = '';
			    $fields[5]->tab        = 'Контент';
			    $fields[5]->order      = '10';
			    $fields[5]->columns    = '12';

			    $form = new \Vshapovalov\Crud\Models\CrudForm();

			    $form->code    = 'pages';
			    $form->name    = 'Страницы сайта';
			    $form->model   = 'Vshapovalov\Pages\Page';
			    $form->id      = 'id';
			    $form->display = 'title';
			    $form->type    = 'list';

			    $form->save();

			    $form->fields()->saveMany( $fields );
		    }
	    } catch (\Exception $e) {

	    }

	    try {
		    if (! \Vshapovalov\Crud\Models\CrudMenu::where('name', 'pages')->first()) {
			    $menu = new \Vshapovalov\Crud\Models\CrudMenu();

			    $menu->name = 'pages';
			    $menu->caption = 'Страницы сайта';
			    $menu->action = 'crud:pages:mount';
			    $menu->order = 0;
			    $menu->status = 'enabled';
			    $menu->save();
		    }
	    } catch( \Exception $e) {

	    }
    }
}
