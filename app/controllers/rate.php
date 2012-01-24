<?php
/* 
 * Store valence and classification info for a specified tweet and user.
 */
require_once '../utils/authenticate.php';
require_once '../utils/db.php';

if ($isAuthenticated) {
   $user_id = $_SESSION['USER_ID'];
   $tweet_id = $_POST['tweet_id'];
   $valence = $_POST['valence'];
   $class = $_POST['class_id'];

   $db = $getDatabase(DB_AWS_DEV);
   $db->insert('DATA_survey_ratings', array(
      'user_id' => $user_id,
      'tweet_id' => $tweet_id,
      'valence' => $valence,
      'class_id' => $class
   ));
   $db->query("UPDATE DATA_survey_tweets SET times_rated=times_rated+1 WHERE tweet_id=%i", $tweet_id);
}
else {
   throw new Exception("Not authenticated!");
}

?>
