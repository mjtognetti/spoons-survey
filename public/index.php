<?php

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

// Middleware used to ensure only authenticated users can access
// a page. If not authenticated, redirects to login page.
$protect = function() use ($isAuthenticated, $redirect) {
   if ( !$isAuthenticated() ) {
      $redirect('/login');
   }
};


//=============      
// Routes
//=============

$app->get('/', $protect, function() use ($redirect) {
   $redirect('/survey');
});

$app->get('/login', function() use ($isAuthenticated, $redirect) {
   global $app;
   if ( $isAuthenticated() ) {
      $redirect('/survey');
   }
   else {
      $app->render('login.mustache');
   }
});

$app->get('/survey', function() use ($fetchRateableTweet){
   global $app;
   $app->render('survey.mustache', $fetchRateableTweet());
});

$app->post('/survey', $protect, function() {
});

$app->get('/thanks', $protect, function() {
   global $app;
   $app->render('thanks.mustache');
});

$app->run();

?>
