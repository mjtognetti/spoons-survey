<?php

class User {
   private $id;
   private $lastName;
   private $cpLogin;
   private $numTweetsRated;
   private $isAuthenticated;

   function User($id, $cpLogin, $lastName, $numTweetsRated) {
      $this->id = $id;
      $this->cpLogin = $cpLogin;
      $this->lastName = $lastName;
      $this->numTweetsRated = $numTweetsRated;
   }

   public function authenticate() {
      $this->isAuthenticated = true;
   }

   public function getId() {
      return $this->id;
   }

   public function getLastName() {
      return $this->lastName;
   }

   public function getLogin() {
      return $this->cpLogin;
   }

   public function getNumTweetsRated() {
      return $this->numTweetsRated;
   }

   public function authenticated() {
      return $this->isAuthenticated;
   }
}

?>
