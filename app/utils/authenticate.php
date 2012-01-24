<?php

// Authenticate a request, return true if user is authenticates, else false.
$isAuthenticated = function() {
   return isset($_SESSION['USER_ID']) && trim($_SESSION['USER_ID']) == '';
};

?>
