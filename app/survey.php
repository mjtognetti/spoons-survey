<?php

require_once 'database.php';

class SurveyCompletedException extends Exception {};
class TweetFetchException extends Exception {};

class Survey {
  
   // Move to configuration file 
   const MAX_TWEETS = 50;

   private $user;
   private $db;

   function Survey($user) {
      $this->user = $user;
      $db = new Database();
   }

   public function fetchTweet() {
      if ($this->isComplete()) {
         throw new SurveyCompleteException();
      }

      $tweet = $db->getTweetForUser($this->user);

      if (!$tweet) {
         throw new TweetFetchException();
      }

      return $tweet;
   }

   public function storeResults($tweetId, $valence, $classId) {
      $response = $db->storeResults($this->user, $tweetId, $valence, $classId);
   }

   public function isComplete() {
      return ($this->user->getNumTweetsRated() >= self::MAX_TWEETS);
   }

   public function getProgress() {
      return $this->user->getNumTweetsRated();
   }

   public function getUser() {
      return $this->user;
   }
}

?>
