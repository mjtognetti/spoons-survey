<?php
/**
 * MeekroDB Configuration 
 */

require_once '../lib/meekrodb.2.0.class.php';

define("DB_AWS", "aws");
define("DB_AWS_DEV", "aws");

$getDatabase = call_user_func(function() {
   $configurations = array(
   );
   return function($which) use ($configurations) {
      if (!isset($configurations[$which])) {
         throw new Exception("unknown database configuration!");
      }

      $config = $configurations[$which];
      $db = new MeekroDB(
         $config['host'],
         $config['username'],
         $config['password'],
         $config['dbName'],
         $config['port'],
         $config['encoding']
      );
      return $db;
   };
});

?>
