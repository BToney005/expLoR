<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlayerDecksUseDeckUuid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_decks', function (Blueprint $table) {
            //$table->dropUnique(['player_uuid','deck_code']);
            $table->dropColumn('deck_code');
            $table->uuid('deck_uuid')->after('player_uuid');
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
        Schema::table('player_decks', function (Blueprint $table) {
            $table->dropForeign(['deck_uuid']);
            $table->dropColumn('deck_uuid');
            $table->string('deck_code')->after('player_uuid');
            //$table->unique(['player_uuid','deck_code']);
        });
    }
}
