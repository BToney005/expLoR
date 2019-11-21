<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlayerCardsRenameFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_cards', function (Blueprint $table) {
            //$table->dropUnique(['player_uuid','card_code']);
            $table->dropColumn('card_code');
            $table->uuid('card_uuid')
                ->after('player_uuid');
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
        Schema::table('player_cards', function (Blueprint $table) {
            $table->dropForeign(['card_uuid']);
            $table->dropColumn('card_uuid');
            $table->string('card_code')->after('player_uuid');
            $table->unique(['player_uuid', 'card_code']);
        });
    }
}
