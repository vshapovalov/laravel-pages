<?php

Route::any('/{url?}', ['as' => 'page', 'uses' => '\\Vshapovalov\\Pages\\Http\\Controllers\\PagesController@getPage'])->where(['url' => '^[a-zA-Z0-9_-\/.]+$']);