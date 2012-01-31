<?php

require_once 'user.php';
require_once 'exceptions.php';

class Guard {

   public static function hasLoggedInUser() {
      global $_SESSION;
      return isset($_SESSION['cpLogin']) && isset($_SESSION['course']);
   }

   public static function getLoggedInUser() {
      global $_SESSION;

      if (!Guard::hasLoggedInUser()) return NULL;

      $cpLogin = $_SESSION['cpLogin'];
      $course = $_SESSION['course'];
      $user = User::getWithLoginAndCourse($cpLogin, $course);
      return $user;
   }

   public static function authenticate($user, $lastName) {
      if ($user->getLastName() != $lastName) { 
         throw new IncorrectLoginDetailsException();
      }

      $user->authenticate();
      return $user;
   }

   public static function register($cpLogin, $lastName, $course, $instructor) {
      $user = User::register($cpLogin, $lastName, $course, $instructor);
      $user->authenticate();   
      return $user;
   }

   public static function login($user) {
      if ($user->authenticated()) {
         $_SESSION['cpLogin'] = $user->getCPLogin();
         $_SESSION['course'] = $user->getCourse();
      }
   }

   public static function logout() {
      $_SESSION['cpLogin'] = NULL;
   }

}
?>
