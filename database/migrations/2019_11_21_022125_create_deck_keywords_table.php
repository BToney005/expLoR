<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeckKeywordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deck_keywords', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('deck_uuid');
            $table->string('keyword');

            $table->unique(['deck_uuid', 'keyword']);

            $table->foreign('deck_uuid')
                ->references('uuid')
                ->on('decks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deck_keywords');
    }
}
