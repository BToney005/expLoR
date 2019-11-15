<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_cards', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->uuid('player_uuid');
            $table->string('card_code');
            $table->unsignedInteger('quantity')->default(1);

            $table->unique(['player_uuid', 'card_code']);
            $table->foreign('player_uuid')
                ->references('uuid')
                ->on('players');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('player_cards');
    }
}
