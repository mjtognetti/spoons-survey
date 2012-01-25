<?php

require_once '../app/guard.php';
require_once '../app/survey.php';

require '../app/tweet.php';
require '../app/utils/authenticate.php'; // $isAuthenticated(), returns true if session-authentic
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
 
// Redirect to another page.
$redirect = function($target) use ($app) {
   $rootUri = $app->request()->getRootUri();
   $app->redirect($rootUri.$target, 301);
};

$protect = function() use ($redirect) {
   if (!Guard::hasLoggedInUser()) {
      $redirect('/login');
   }
}


//=============      
// Routes
//=============

$app->get('/', $protect, function() use ($redirect) {
   $redirect('/survey');
});

$app->get('/login', function() use ($redirect) {
   global $app;

   if (Guard::hasLoggedInUser()) {
      $redirect('/survey');
   }
   else {
      $app->render('login.mustache');
   }
});

$app->post('/login', function() {
   global $app;
   $post = $app->request()->post();

   if (Guard::hasLoggedInUser()) {
      return 'already logged in';
   }
   
   $lastName = $post('lastName');
   $cpLogin = $post('cpLogin');

   try {
      $user = Guard::authenticate($lastName, $cpLogin);
      Guard::login($user);
      return 'success';
   }
   catch (NoSuchUserException e)  {
      try {
         $user = Guard::register($lastName, $cpLogin);
         Guard::login($user);
         return 'success';
      catch (UserAlreadyExistsException e) {
         return 'error';
      }
   }
   catch (NameMistmatchException) {
      return 'error'
   }            
});

$app->get('/survey', function() use ($redirect){
   global $app;

   $user = Guard::getLoggedInUser();
   $survey = new Survey($user);

   if ($survey->isComplete()) {
      $redirect('/thanks');
   }
   else {
      $tweet = $survey->fetchTweet();
      $progress = $survey->getProgress();

      $app->render('survey.mustache', array(
         'tweet' => $tweet,
         'progress' => $progress
      ));
   }
});

$app->post('/survey', $protect, function() {
   global $app;
   $post = $app->request()->post();

   $tweetId = $post('tweetId');
   $valence = $post('valence');
   $classId = $post('classId');

   $user = Guard::getLoggedInUser();
   $survey = new Survey($user);

   $survey->storeResults($tweetId, $valence, $classId);

   return 'success';
});

$app->get('/thanks', $protect, function() {
   global $app;
   $app->render('thanks.mustache');
});

$app->run();

?>
