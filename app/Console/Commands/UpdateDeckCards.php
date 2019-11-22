<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Models\Card;
use App\Models\Deck;
use App\Models\Deck\Card as DeckCard;


class UpdateDeckCards extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'decks:update:cards';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cards in decks.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('hello world.');
        $decks = Deck::all();
        foreach ($decks as $index => $deck) {
            $ret = exec("cd ". base_path("resources/assets/js") ."; node runeterra.js {$deck->code} 2>&1", $out, $err);
            $dcs = collect(json_decode($ret))->each(function ($card) use ($deck) {
                $card = Card::firstOrCreate([
                    'code' => $card->code
                ]);

                $deckCard = \DB::table('deck_cards')
                    ->where('deck_uuid', $deck->uuid)
                    ->where('card_uuid', $card->uuid)
                    ->first();

                if (!$deckCard) {
                    \DB::table('deck_cards')
                        ->insert([
                            'uuid' => Str::uuid()->toString(),
                            'deck_uuid' => $deck->uuid,
                            'card_uuid' => $card->uuid
                        ]);
                }
            });
        }
    }
}