<?php

require_once 'storage.php';
require_once 'exceptions.php';

class User {
   private $id;
   private $cpLogin;
   private $lastName;
   private $course;
   private $instructor;
   private $numTweetsRated;
   private $isAuthenticated;

   function User($userData) {
      // Login and last name are guaranteed to be known.
      $this->cpLogin = $userData['cp_login'];
      $this->lastName = $userData['last_name'];
      $this->course = $userData['course'];
      $this->instructor = $userData['instructor'];
      $this->numTweetsRated = $userData['num_tweets_rated'];

      // Id will be unknown if the user was just
      // registered. In such a case, use -1 as id to indicate its unknown.
      $this->id = ( isset($userData['id']) ? $userData['id'] : -1);

      // Users are not authenticated on creation.
      $this->isAuthenticated = false;
   }

   public function getId() {
      return $this->id;
   }

   public function getCPLogin() {
      return $this->cpLogin;
   }

   public function getLastName() {
      return $this->lastName;
   }

   public function getCourse() {
      return $this->course;
   }

   public function getInstructor() {
      return $this->instructor;
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

   public static function getWithLoginAndCourse($cpLogin, $course) {
      $userData = Storage::getUserDataForLoginAndCourse($cpLogin, $course);
      if (!$userData) throw new NoSuchUserException();
      return new User($userData);
   }

   public static function register($cpLogin, $lastName, $course, $instructor) {
      $userData = Storage::registerUser($cpLogin, $lastName, $course, $instructor);
      return new User($userData); 
   }
}

?>
