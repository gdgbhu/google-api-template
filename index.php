<?php

session_start();


// Include / Require Autoloader + Helpers

require 'vendor/autoload.php';
require 'helpers/helpers.php';

$api_config = parse_ini_file('config/config.ini');


// Build API Client Config

$client = new Google_Client();
$client->setClientId($api_config['client_id']);
$client->setClientSecret($api_config['client_secret']);
$client->setRedirectUri($api_config['redirect_uri']);
$client->addScope('https://mail.google.com/');


// Build a Service


// Check if we are logged out
if(isset($_REQUEST['logout']))
{
    unset($_SESSION['access_token']);
}

// Check if we have an authorization code
if(isset($_GET['code'])){
    $code = $_GET['code'];
    $client->authenticate($code);
    $_SESSION['access_token'] = $client->getAccessToken();
    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    header('Location: ' . filter_var($url,FILTER_VALIDATE_URL));
}

// Check if we have an access token in the session
if(isset($_SESSION['access_token'])){
    $client->setAccessToken($_SESSION['access_token']);
} else {
    $loginUrl = $client->createAuthUrl();
    echo '<a href="'.$loginUrl.'">Signin with Google</a>';
}

// Refresh the token if it's expired.
if($client->isAccessTokenExpired()) {
    if($client->getRefreshToken()){
        $client->refreshToken($client->getRefreshToken());
        $_SESSION['access_token'] = $client->getAccessToken();
    }
}

try {

    if(isset($_SESSION['access_token']) && $client->getAccessToken()){
        // Start Hacking
        
    }

} catch (Google_Auth_Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<a href="?logout">Login Again</a>';
}