<?php

Route::any('/{url?}', ['as' => 'page', 'uses' => '\\Vshapovalov\\Pages\\Http\\Controllers\\PagesController@getPage'])->where(['url' => '^[a-z�-�A-Z0-9_-\/.\s]+$']);