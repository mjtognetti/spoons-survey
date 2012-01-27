<?php

require_once 'exceptions.php';
require_once 'storage.php';

class Survey {
   private $user;

   function Survey($user) {
      $this->user = $user;
   }

   public function fetchTweet() {
      if ( $this->isComplete() ) throw new SurveyCompleteException();
      $tweet = Storage::getTweetForUser($this->user->getCPLogin);
      return $tweet;
   }

   public function storeResults($tweetId, $valence, $classId) {
      $response = Storage::storeResults(
         $this->user,
         $tweetId,
         $valence,
         $classId
      );
      return $response;
   }

   public function isComplete() {
      $maxTweets = 50; // use config file.
      return ($this->user->getNumTweetsRated() >= $maxTweets);
   }

   public function getProgress() {
      return $this->user->getNumTweetsRated();
   }

   public function getUser() {
      return $this->user;
   }
}

?>
