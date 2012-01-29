<?php

require_once '../app/exceptions.php';
require_once '../app/user.php';
require_once '../app/guard.php';
require_once '../app/survey.php';

require '../lib/Slim/Slim.php';
require '../lib/Slim/MustacheView.php';

MustacheView::$mustacheDirectory = '../lib/';

$app = new Slim(array(
   'view' => 'MustacheView',
   'templates.path' => 'views/'
));


//================
// Helper Functions
//================
 
$protect = function() {
   global $app;
   if ( !Guard::hasLoggedInUser() ) {
      $app->redirect('login');
   }
};


//=============      
// Routes
//=============

$app->get('/', $protect, function() {
   global $app;
   $app->redirect('survey');
});

// Login Page
$app->get('/login', function() {
   global $app;

   // If a user is already logged in redirect them to the survey.
   if ( Guard::hasLoggedInUser() ) {
      $app->redirect('survey');
   }
   // Otherwise render the login page.
   else {
      $app->render('login.mustache');
   }
});

// Login attempt
$app->post('/login', function() {
   global $app;
   $post = $app->request()->post();

   // Get cpLogin and lastName from post paramters.
   $cpLogin = $post['cpLogin'];
   $lastName = $post['lastName'];

   // If either is missing return an error.
   if (!$cpLogin || !$lastName) $app->error();

   // Ensure case doesn't matter.
   $cpLogin = strtolower($cpLogin);
   $lastName = strtolower($lastName);

   // Try to log the user in.
   try {
      $user = User::getWithLogin($cpLogin);
      Guard::authenticate($user, $lastName);
      Guard::login($user);
      echo 'success';
   }
   catch(NoSuchUserException $e) {
      try {
         $user = Guard::register($cpLogin, $lastName);
         Guard::login($user);
         echo 'success';
      }
      catch(UserAlreadyExistsException $e) {
         echo 'user already exists';
      }
   }
   catch(NameMismatchException $d) {
      echo 'name mismatch';
   }

});

// Logout a user.
$app->get('/logout', $protect, function() {
   global $app;
   Guard::logout();
   $app->redirect('survey');
});

// Instructions page.
$app->get('/instructions', $protect, function() {
   global $app;

   $survey = new Survey(Guard::getLoggedInUser());

   if ($survey->isComplete()) {
      $app->redirect('thanks');
   }
   else {
      $app->render('instructions.mustache');
   }
});

// Survey page
$app->get('/survey', $protect, function(){
   global $app;

   // Retrieve the currently logged in user and create
   // a Survey object for her.
   $user = Guard::getLoggedInUser();
   $survey = new Survey($user);

   // Check if the user has already completed the survey. If she
   // has redirect to the thank you page.
   if ($survey->isComplete()) {
      $app->redirect('thanks');
   }
   // If they haven't finished the survey, render the survey page.
   else {
      // Fetch a rateable tweet and the user's survey progress 
      // (the number of tweets the user has rated).
      $tweet = $survey->fetchTweet();
      $progress = $survey->getProgress() + 1;

      // Render the page.
      $app->render('survey.mustache', array(
         'tweet' => $tweet,
         'progress' => $progress
      ));
   }
});

// Survey rating submit
$app->post('/survey', $protect, function() {
   global $app;
   $post = $app->request()->post();

   // Retrieve tweet id, valence rating, and class id from the post
   // parameters.
   $tweetId = $app->request()->post('tweetId'); //$post['tweetId'];
   $valence = $post['valence'];
   $classId = $post['classId'];

   // If any of them are missing return an error.
   if (!$tweetId || !$valence || !$classId) {
      $app->error();
   }
   
   // If no user is authenticated and logged in return an error.
   if (!Guard::hasLoggedInUser()) $app->error(); 

   // Retrieve the logged in user and create a Survey object for her.
   $user = Guard::getLoggedInUser();
   $survey = new Survey($user);

   // Store the survey results for the tweet.
   $survey->storeResults($tweetId, $valence, $classId);
});

// Thank you page (survey complete)
$app->get('/thanks', $protect, function() {
   global $app;

   $user = Guard::getLoggedInUser();
   $survey = new Survey($user);

   // If the survey is complete render the thank you page.
   if ($survey->isComplete()) {
      Guard::logout();
      $app->render('thanks.mustache');
   }
   // otherwise redirect the user to the survey.
   else {
      $app->redirect('survey');
   }
});

$app->run();

?>
