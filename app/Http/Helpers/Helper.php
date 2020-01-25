<?php

if (!function_exists('assignRank')) {
    function assignRank($score, $ranks) {
        $i = 0;
        $rank = '';
        $lower_bound = 0;
        do {
            $lower_bound = $ranks[$i]["lower_bound"];
            $rank = $ranks[$i]["rank"];
            $i++;
        } while ($lower_bound > $score);
        return $rank;
    }
}