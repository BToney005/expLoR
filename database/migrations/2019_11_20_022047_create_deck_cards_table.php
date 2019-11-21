<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeckCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deck_cards', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('deck_uuid');
            $table->uuid('card_uuid');
            $table->unsignedInteger('count')->default(1);

            $table->foreign('deck_uuid')
                ->references('uuid')
                ->on('decks');

            $table->foreign('card_uuid')
                ->references('uuid')
                ->on('cards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deck_cards');
    }
}
