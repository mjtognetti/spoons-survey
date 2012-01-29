<?php

require_once 'user.php';
require_once 'exceptions.php';

class Guard {

   public static function hasLoggedInUser() {
      global $_SESSION;
      return isset($_SESSION['cpLogin']);
   }

   public static function getLoggedInUser() {
      global $_SESSION;

      if (!Guard::hasLoggedInUser()) return NULL;

      $cpLogin = $_SESSION['cpLogin'];
      $user = User::getWithLogin($cpLogin);
      return $user;
   }

   public static function authenticate($user, $lastName) {
      if ($user->getLastName() != $lastName) {
         throw new NameMismatchException();
      }
      $user->authenticate();
      return $user;
   }

   public static function register($cpLogin, $lastName) {
      $user = User::register($cpLogin, $lastName);
      $user->authenticate();   
      return $user;
   }

   public static function login($user) {
      if ($user->authenticated()) {
         $_SESSION['cpLogin'] = $user->getCPLogin();
      }
   }

   public static function logout() {
      $_SESSION['cpLogin'] = NULL;
   }

}
?>
