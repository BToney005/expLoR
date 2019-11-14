<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePlayerMatchesAddFk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('player_matches', function (Blueprint $table) {
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
        Schema::table('player_matches', function (Blueprint $table) {
            $table->dropForeign(['player_uuid']); 
        });
    }
}
