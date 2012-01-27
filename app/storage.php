<?php
require_once '../lib/meekrodb.2.0.class.php';
require_once '../config/development.php';
require_once 'exceptions.php';

class Storage {

   private static $db;

   public static function initialize() {
      self::$db = new MeekroDB();
      self::$db->error_handler = array('Storage', 'errorHandler');
   }

   public static function getUserDataForLogin($cpLogin) {
      $userData = self::$db->queryFirstRow("SELECT * FROM META_survey_users WHERE cp_login = %s", $cpLogin);
      return $userData;
   }

   public static function registerUser($cpLogin, $lastName) {
      $userData = array(
         'cp_login' => $cpLogin,
         'last_name' => $lastName
      );
      self::$db->insert('META_survey_users', $userData);
      return new User($userData);
   }

   public static function fetchTweetForUser($cpLogin) {
      $query = "SELECT * FROM DATA_survey_tweets ORDER BY num_ratings LIMIT 1";
      $tweet = self::$db->queryFirstRow($query);
      return $tweet;
   }

   public static function storeResults($cpLogin, $tweetId, $valence, $classId) {
      // INSERT INTO survey_results (user_id, tweet_id, valence, class_id) 
      // VALUES (
      //    (SELECT id FROM survey_users WHERE cp_login = $cpLogin),
      //    $tweetId,
      //    $valence,
      //    $classId
      // );
   }

   public static function errorHandler($params) {
      $error = $params['error'];
      
      // If duplicate cp login error on registration
      if (strpos($error, 'Duplicate entry') !== False) {
         throw new UserAlreadyExistsException();
      }
      // else some other db error
      else {
         die;
      }
   } 

}

// auto-call the initialize method
Storage::initialize();

?>
