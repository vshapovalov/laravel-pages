<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->increments('id');
	        $table->string('title');
	        $table->string('url', 191);
	        $table->index('url', 'idx_pages_url');
	        $table->string('code', 191);
	        $table->text('body')->nullable();
	        $table->string('title_image')->nullable();
	        $table->string('template');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('pages', function (Blueprint $table) {
		    $table->dropIndex('idx_pages_url');
	    });

        Schema::dropIfExists('pages');
    }
}
