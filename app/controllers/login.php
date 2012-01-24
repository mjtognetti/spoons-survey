<?php

require_once '../utils/db.php';

/*
 * User login and registration.
 * Recieves $last_name and $cp_login (cal poly login).
 * Treats $cp_login as a username and $last_name as a password.
 *
 * If a user exists with $cp_login attempt to log them in, 
 * else automatically create a new user account.
 */

$db = $getDatabase(DB_AWS);

function authenticate() {}

function register($last_name, $cp_login) {
   // insert new account into user table
   // authenticate the user
}

function login($last_name, $cp_login) {
   // query db for cp_login
   // if login exists
   //    if last_name is correct
   //       authenticate user
   //    else
   //       wrong login info!
   // else
   //    register new user
}

function loginOrRegister() use ($getDatabase) {
   $last_name, $cp_login;
   
}
   
   

?>
