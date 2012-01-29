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

   // Retrieve user data (id, cp login, last name, number of tweets rated) from the database.
   // Format of user data is (id, cp_login, last_name, num_tweets_rated). Return value will
   // be NULL if no user exists with $cpLogin.
   public static function getUserDataForLogin($cpLogin) {
      $userData = self::$db->queryFirstRow("SELECT * FROM META_survey_users WHERE cp_login = %s", $cpLogin);
      return $userData;
   }

   // Register a new user, a UserAlreadyExistsException will be thrown if
   // a user with $cpLogin already exists.
   public static function registerUser($cpLogin, $lastName) {
      $userData = array(
         'cp_login' => $cpLogin,
         'last_name' => $lastName
      );
      self::$db->insert('META_survey_users', $userData);
      return new User($userData);
   }

   // Fetch a rateable tweet for the user with $cpLogin. A 'rateable' tweet is
   // one that has not been rated by this user. In addition, the tweet should
   // have been rated by a minimal number of other users (i.e. the tweet thats
   // been rated the fewest times).
   public static function fetchTweetForUser($cpLogin) {
      $query = "SELECT * FROM DATA_survey_tweets ORDER BY num_ratings LIMIT 1";
      $tweet = self::$db->queryFirstRow($query);
      return $tweet;
   }

   // Store a user's rating of a tweet.
   public static function storeResults($cpLogin, $tweetId, $valence, $classId) {
      self::$db->insert('DATA_survey_results', array(
         'user_id' => self::$db->sqleval('(SELECT id FROM META_survey_users WHERE cp_login = %s)', $cpLogin),
         'tweet_id' => $tweetId,
         'valence' => $valence,
         'class_id' => $classId
      ));
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
