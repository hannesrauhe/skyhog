<?php
/*
Copyright 2012-2015 Hannes Rauhe

This file is part of Skyhog.

Skyhog is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.
	
Skyhog is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Skyhog.  If not, see <http://www.gnu.org/licenses/>.
*/
require_once("./config.inc.php");

if(session_status() !== PHP_SESSION_ACTIVE) {session_start();}
if(defined(ENABLE_DEBUG) && ENABLE_DEBUG=="true") {
	header('Content-type: text/html; charset=utf-8');
	ini_set('display_errors', '1');
	error_reporting(E_ALL);
} else {
	header('Content-type: application/json; charset=utf-8');
}

require_once("./classes.inc.php");
require_once("./oauth.inc.php")

// Handle a request to a resource and authenticate the access token
if (!$server->verifyResourceRequest(OAuth2\Request::createFromGlobals())) {
	$server->getResponse()->send();
	echo json_encode(array('status' => 'not authenticated', 'message' => 'You are not authenticated!'));
	die;
}


/*
$d = new skyhog_db();
$a = new auth();
$oid = '';

if(array_key_exists("oid", $_COOKIE) && !empty($_COOKIE['oid'])) {
	$oid = $_COOKIE['oid'];
}

if(array_key_exists("openid_identifier", $_POST) && !empty($_POST["openid_identifier"])) {
	$oid = $_POST['openid_identifier'];
}

try {
    if(!$a->auth($d,$oid)) {
        $msg = "Please provide a valid OpenID";	
        Header("Location: index.php?msg=".urlencode($msg)."&redirect=".urlencode($_SERVER['SCRIPT_NAME']));
        exit(0);
    }

    if($a->isInactiveUser()) {
        if(isset($_REQUEST['redirect']) && $_REQUEST['redirect']=="/setup.php") {
            Header("Location: setup.php");
            exit(0);
        }            
        $msg = "your account needs to be activated by the administrator";
        Header("Location: index.php?msg=".urlencode($msg)."&redirect=".urlencode($_SERVER['SCRIPT_NAME']));
        exit(0);
    }
} catch (Exception $e) {
    $msg = 'An error occured: '.$e->getMessage();
    Header("Location: index.php?msg=".urlencode($msg));
    exit(0);
}

$msg = '';

$site_id = -1;
if(isset($_REQUEST['site_id'])) {
    $site_id = $_REQUEST['site_id'];
}
$s = new site($d,$site_id);
$l = new skylog("system.log",$a->getAuthUserName());

if($site_id!=-1 && $site_id!=$s->getSiteID()) {
    $msg="The requested site does not exist! Check the maintenance script, if that seems to be wrong.";
}

if($s->getSiteID()==-1 && FALSE===array_search(basename($_SERVER['SCRIPT_NAME']),array("sites.php","setup.php","edit_site.redirect.php"))) {
    if(!isset($msg) || empty($msg)) {
        $msg = "because of an unknown error the site could not be changed";
    }    
    Header("Location: sites.php?msg=".urlencode($msg)."&redirect=".urlencode($_SERVER['SCRIPT_NAME']));
    exit(0);
}
*/