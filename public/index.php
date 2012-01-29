<?php

require_once '../app/exceptions.php';
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

   // Get cpLogin and lastName from post paramters.
   $cpLogin = $app->request()->post('username');
   $lastName = $app->request()->post('lastName');

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
      }
      catch(UserAlreadyExistsException $e) {
         $app->error();
      }
   }
   catch(NameMismatchException $d) {
      $app->error();
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
   $app->render('instructions.mustache');
});

// Survey page
$app->get('/survey', $protect, function(){
   global $app;

   $user = Guard::getLoggedInUser();
   $survey = new Survey($user);

   if ($survey->isComplete()) {
      $app->redirect('thanks');
   }
   else {
      $tweet = $survey->fetchTweet();
      $app->render('survey.mustache', $tweet);
   }
});

// Survey rating submit
$app->post('/survey', $protect, function() {
   global $app;
});

// Thank you page (survey complete)
$app->get('/thanks', $protect, function() {
   global $app;
   $app->render('thanks.mustache');
});

$app->run();

?>
