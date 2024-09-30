<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('post_author')->default(0);
                $table->dateTime('post_date')->default('0000-00-00 00:00:00');
                $table->dateTime('post_date_gmt')->default('0000-00-00 00:00:00');
                $table->longText('post_content')->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci');
                $table->text('post_title')->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci');
                $table->text('post_excerpt')->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci');
                $table->string('post_status', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('publish');
                $table->string('comment_status', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('open');
                $table->string('ping_status', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('open');
                $table->string('post_password', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('');
                $table->string('post_name', 200)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('');
                $table->text('to_ping')->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci');
                $table->text('pinged')->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci');
                $table->dateTime('post_modified')->default('0000-00-00 00:00:00');
                $table->dateTime('post_modified_gmt')->default('0000-00-00 00:00:00');
                $table->longText('post_content_filtered')->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci');
                $table->unsignedBigInteger('post_parent')->default(0);
                $table->string('guid', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('');
                $table->integer('menu_order')->default(0);
                $table->string('template_bound', 200)->nullable();
                $table->integer('thumbmail_id')->nullable();
                
                $table->string('post_type', 20)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('post');
                $table->string('post_mime_type', 100)->charset('utf8mb4')->collation('utf8mb4_unicode_520_ci')->default('');
                $table->bigInteger('comment_count')->default(0);
    
                // $table->primary('ID');
                // $table->index([DB::raw('post_name(191)')]);
                // $table->index(['post_type', 'post_status', 'post_date', 'ID'], 'type_status_date');
                // $table->index('post_parent');
                // $table->index('post_author');

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
        Schema::dropIfExists('posts');
    }
}
