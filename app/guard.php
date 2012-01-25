<?php

require_once 'user.php';
require_once 'database.php';

class NoSuchUserException extends Exception {}
class UserAlreadyExistsException extends Exception {}
class LoginNameMismatchException extends Exception {}

class Guard {

   public static function hasLoggedInUser() {
      return isset($_SESSION['user_id']);
   }

   public static function getLoggedInUser() {
      if (!Guard::hasLoggedInUser()) return NULL;
      
      $db = new Database();
      $id = $_SESSION['user_id'];
      $userData = $db->getUserById($id);
      
      if (!$userData) {
         throw new NoSuchUserException();
      }

      $user = new User(
         $userData['id'],
         $userData['cp_login'],
         $userData['last_name'],
         $userData['num_tweets_rated']
      );

      return $user;
         
   }

   public static function authenticate($last_name, $login) {
      $db = new Database();
      $userData = $db->getUserByLogin($login);
      
      if (!$userData) {
         throw new NoSuchUserException();
      }
      else if ($userData['last_name'] != $last_name) {
         throw new NameMismatchException();
      }

      $user = new User(
         $userData['id'],
         $userData['cp_login'],
         $userData['last_name'],
         $userData['num_tweets_rated']
      );

      $user->authenticate();

      return $user;
   }

   public static function register($last_name, $login) {
      $db = new Database();
      $userData = $db->registerUser($login, $last_name);
      if (!$userData) {
         throw new UserAlreadyExistsException();
      }
      
      $user = new User(
         $userData['id'],
         $userData['cp_login'],
         $userData['last_name'],
         $userData['num_tweets_rated']
      );

      $user->authenticate();   

      return $user;
   }

   public static function login($user) {
      if ($user->authenticated()) {
         $_SESSION['user_id'] = $user->getId();
      }
   }

   public static function logout() {
      $_SESSION['user_id'] = NULL;
   }

}
?>
