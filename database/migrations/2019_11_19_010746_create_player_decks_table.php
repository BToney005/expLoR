<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerDecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_decks', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->uuid('player_uuid');
            $table->string('deck_code');
            $table->timestamps();
            $table->softDeletes();

            //$table->unique(['player_uuid','deck_code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_decks');
    }
}
