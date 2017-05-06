<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateContentsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contents_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('content_id');
            $table->string('locale');

            $table->string('slug')->nullable();
            $table->string('title');
            $table->json('metatags')->nullable();
            $table->json('document')->nullable();
            $table->json('properties')->nullable();

            $table->string('translation_status')->nullable();
            $table->unsignedInteger('edited_by')->nullable();
        });
        Schema::table('contents_translations', function (Blueprint $table) {
            $table->index('locale');
            $table->index('content_id');
            $table->index('translation_status');
            $table->unique(['content_id', 'locale']);

            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
            $table->foreign('edited_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contents_translations');
    }
}
