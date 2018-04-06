<?php

Route::any('/{url?}', ['as' => 'page', 'uses' => '\\Vshapovalov\\Pages\\Http\\Controllers\\PagesController@getPage'])->where(['url' => '^[\s/.a-zа-яА-ЯA-Z0-9_-]+$']);