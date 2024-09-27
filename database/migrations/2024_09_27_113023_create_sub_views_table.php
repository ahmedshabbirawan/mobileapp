<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubViewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_views', function (Blueprint $table) {
            $table->id();

            $table->bigInteger("post_id");
            $table->string('type', 255)->nullable();
            $table->string('frame', 255)->nullable();
            $table->string('text', 255)->nullable();
            $table->string('font_name', 255)->nullable();
            $table->string('font_size', 255)->nullable();
            $table->string('text_color', 255)->nullable();
            $table->string('image_name', 255)->nullable();
            $table->string('extra', 255)->nullable();
           

            $table->string('status', 20)->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('sub_views');
    }
}
