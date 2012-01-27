<?php

require '../app/guard.php';
require '../app/survey.php';

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
   if ( !Guard::hasLoggedInUser() ) {
      $redirect('/login');
   }
};


//=============      
// Routes
//=============

$app->get('/', $protect, function() use ($redirect) {
   $redirect('/survey');
});

$app->get('/login', function() use ($redirect) {
   global $app;
   if ( Guard::hasLoggedInUser() ) {
      $redirect('/survey');
   }
   else {
      $app->render('login.mustache');
   }
});

$app->post('/login', function() {
});

$app->get('/survey', $protect, function(){
   global $app;
   
   $user = Guard::getLoggedInUser();
   $survey = new Survey($user);
   $tweet = $survey->fetchTweet();
   $app->render('survey.mustache', $tweet);
});

$app->post('/survey', $protect, function() {
});

$app->get('/thanks', $protect, function() {
   global $app;
   $app->render('thanks.mustache');
});

$app->run();

?>
