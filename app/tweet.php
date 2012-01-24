<?php

require_once 'utils/db.php';

/*
 * Fetch a tweet
 * Logic:
 *    Fetch the tweet with the least number of ratings which the user hasn't
 *    already rated.
 * Query:
SELECT tweet_id, content 
FROM tweets, (SELECT tweet_id as rated_id FROM ratings WHERE ratings.user_id != %USERID%) rated_tweets
WHERE tweets.tweet_id != rated_tweets.rated_id
ORDER BY tweets.num_ratings DESC
LIMIT 50;
 */

$fetchRateableTweet = function() use ($getDatabase) {
   $db = $getDatabase(DB_AWS);
   $tweet = $db->queryFirstRow("SELECT tweet_id, content FROM DATA_survey_tweets ORDER BY times_rated DESC LIMIT 50");
   return $tweet;
};

?>
