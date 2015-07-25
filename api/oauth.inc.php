<?php
require_once('oauth2-server-php/src/OAuth2/Autoloader.php');
OAuth2\Autoloader::register();

$pdo = new PDO('sqlite:oauth.sq3');
$storage = new OAuth2\Storage\Pdo($pdo);
$server = new OAuth2\Server($storage);

// create some users in memory
$users = array('hannes' => array('password' => 'brent123', 'first_name' => 'Hannes', 'last_name' => 'Rauhe'));
$user_storage = new OAuth2\Storage\Memory(array('user_credentials' => $users));
$grantType = new OAuth2\GrantType\UserCredentials($user_storage);

$server->addGrantType($grantType);