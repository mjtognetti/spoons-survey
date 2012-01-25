<?php
/**
 * MeekroDB Configuration 
 */

require_once '../lib/meekrodb.2.0.class.php';

define("DB_AWS", "aws");
define("DB_AWS_DEV", "aws");

class Database {

   private static $configurations = array(
   );

   private function getDatabase($which) {
      if (!isset(self::$configurations[$which])) {
         throw new Exception("unknown database configuration!");
      }

      $config = self::$configurations[$which];
      $db = new MeekroDB(
         $config['host'],
         $config['username'],
         $config['password'],
         $config['dbName'],
         $config['port'],
         $config['encoding']
      );
      return $db;
   }

   public function getUserById($id) {
   }

   public function getUserByLogin($login) {
   }

   public function registerUser($login, $lastName) {
   }

   public function getTweetForUser($user) {
      $db = $this->getDatabase('aws-dev');
      $tweet = $db->queryFirstRow('SELECT * FROM DATA_survey_tweets ORDER BY times_rated DESC LIMIT 50');
      return $tweet;
   }

   public function storeResults($user, $tweetId, $valence, $class) {
   }

};

?>
