<?php
require_once '../lib/meekrodb.2.0.class.php';
require_once '../config/production.php';
require_once 'exceptions.php';

class Storage {

   private static $db;
   private static $databases;

   public static function initialize() {
      global $_CONFIG;
      $dbConfigs = $_CONFIG['databases'];

      self::$databases = array();      
      self::$databases['aws-dev'] = self::initDatabase($dbConfigs['aws-dev']);
      self::$databases['aws'] = self::initDatabase($dbConfigs['aws']);
      //self::$databases['abra'] = self::initDatabase($dbConfigs['abra']);
      //self::$databases['abra2'] = self::initDatabase($dbConfigs['abra2']);
   }

   private static function initDatabase($config) {
      $db = new MeekroDB(
         $config['host'],
         $config['username'],
         $config['password'],
         $config['dbName'],
         $config['port'],
         $config['encoding']
      );
      $db->error_handler = array('Storage', 'errorHandler');
      return $db;
   }

   // Retrieve user data (id, cp login, last name, number of tweets rated)
   // from the database.
   // Format of user data is (id, cp_login, last_name, num_tweets_rated).
   // Return value will be NULL if no user exists with $cpLogin.
   public static function getUserDataForLoginAndCourse($cpLogin, $course) {
      $db = self::$databases['aws'];
      $userData = $db->queryFirstRow("SELECT * FROM META_survey_users WHERE cp_login = %s AND course = %i", $cpLogin, $course);
      return $userData;
   }

   // Register a new user, a UserAlreadyExistsException will be thrown if
   // a user with $cpLogin already exists.
   public static function registerUser($cpLogin, $lastName, $course, $instructor) {
      $userData = array(
         'cp_login' => $cpLogin,
         'last_name' => $lastName,
         'course' => $course,
         'instructor' => $instructor,
         'num_tweets_rated' => 0
      );

      self::$databases['aws']->insert('META_survey_users', $userData);

      return $userData;
   }

   // Fetch a rateable tweet for the user with $cpLogin. A 'rateable' tweet is
   // one that has not been rated by this user. In addition, the tweet should
   // have been rated by a minimal number of other users (i.e. the tweet thats
   // been rated the fewest times).
   public static function fetchTweetForUser($id) {
      $query = "SELECT * FROM DATA_survey_tweets WHERE tweet_id NOT IN (SELECT tweet_id FROM DATA_survey_results WHERE user_id = %i) ORDER BY num_ratings";
      $tweet = self::$databases['aws']->queryFirstRow($query, $id);
      return $tweet;
   }

   // Store a user's rating of a tweet.
   // NOTE: This is the only function that uses user id. I would prefer to use
   // cpLogin instead, but it breaks the database trigger. The trigger
   // attempts to update num_tweets_rated in META_survey_users, but isn't
   // able to because that table is used in the query (to determine user id
   // from cpLogin).
   public static function storeResults($userId, $tweetId, $valence, $classId) {
      // NOTE: This query is here because some of the databases do not have
      // the DATA_tweets table, but ALL need the twitterId.
      $twitterId = self::$databases['aws']->queryFirstField('SELECT twitter_id FROM DATA_tweets where id = %i', $tweetId);


      // Loop through each database, inserting into each.
      foreach(self::$databases as $db) {
         $db->insert('DATA_survey_results', array(
            'user_id' => $userId,
            'tweet_id' => $tweetId,
            'twitter_id' => $twitterId,
            'valence' => $valence,
            'class_id' => $classId
         ));
      }
   }

   public static function errorHandler($params) {
      $error = $params['error'];
      
      // If duplicate cp login error on registration
      if (strpos($error, 'Duplicate entry') !== False) {
         throw new UserAlreadyExistsException();
      }
      // else some other db error
      else {
         echo $error;
         die;
      }
   } 

}

// auto-call the initialize method
Storage::initialize();

?>
