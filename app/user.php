<?php

require_once 'storage.php';
require_once 'exceptions.php';

class User {
   private $cpLogin;
   private $lastName;
   private $numTweetsRated;
   private $isAuthenticated;

   function User($userData) {
      $this->cpLogin = $userData['cp_login'];
      $this->lastName = $userData['last_name'];
      $this->numTweetsRated = (array_key_exists('num_tweets_rated', $userData) ? $userData['num_tweets_rated'] : 0);
      $this->isAuthenticated = false;
   }

   public function getCPLogin() {
      return $this->cpLogin;
   }

   public function getLastName() {
      return $this->lastName;
   }

   public function getNumTweetsRated() {
      return $this->numTweetsRated;
   }

   public function authenticated() {
      return $this->isAuthenticated;
   }

   public function authenticate() {
      $this->isAuthenticated = true;
   }

   public static function getWithLogin($cpLogin) {
      $userData = Storage::getUserDataForLogin($cpLogin);
      if (!$userData) throw new NoSuchUserException();
      return new User($userData);
   }

   public static function register($cpLogin, $lastName) {
      $userData = Storage::registerUser($cpLogin, $lastName);
      return new User($userData); 
   }
}

?>
