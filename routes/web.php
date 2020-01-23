<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['prefix' => 'api'], function () use ($router) {
    $router->post('register', 'AuthController@register');
    $router->post('login', 'AuthController@login');

    $router->group(['prefix' => 'my'], function () use ($router) {
        $router->get('cards', 'MyController@cards');
        $router->post('cards/add', 'MyController@addCard');
        $router->get('decks', 'MyController@getFavoriteDecks');
        $router->post('decks/favorite', 'MyController@addDeckToFavorites');
        $router->post('decks/unfavorite', 'MyController@removeDeckFromFavorites');
        $router->post('decks/filter', 'MyController@filterDecks');
    });

    $router->group(['prefix' => 'players'], function () use ($router) {
        $router->post('record-match', 'PlayerController@recordMatch');
        $router->get('stats', 'PlayerController@stats');
    });

    $router->group(['prefix' => 'decks'], function () use ($router) {
        $router->get('top-decks', 'DeckController@topDecks');
    });

    $router->group(['prefix' => 'ranks'], function () use ($router) {
        $router->post('set-ranks', 'RankController@setRanks');
        $router->post('deck-rank', 'RankController@getDeckRank');
    });
});
