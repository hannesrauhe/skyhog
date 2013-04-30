<?php
/*
Copyright 2012,2013 Hannes Rauhe

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
require_once('./base.inc.php');

// require_once('libs/utf8/utf8.php');
// require_once('libs/utf8/utils/bad.php');
// require_once('libs/utf8/utils/validation.php');
// require_once('libs/utf8_to_ascii/utf8_to_ascii.php');


if($_REQUEST['action']=="Create") {
    if(!array_key_exists("name",$_REQUEST)) {
        $msg = "Give the site a name.";
    } else  {
        $site_name = $_REQUEST['name'];
        $default_site = array();
        $default_site['page_url']="";
        $default_site['preview_url']="";
        $default_site['page_dir']=DEFAULT_LIVE_DIR.$site_name."/";
        $default_site['preview_dir']=DEFAULT_PREVIEW_DIR.$site_name."/";
        $default_site['git']="";
        $site = array_merge($default_site,$_REQUEST);
        $d->insertSite($site);
    }    
} else if($_REQUEST['action']=="Update") {
    $d->updateSite($_REQUEST);
    $s->init($d,$s->getSiteID());
} else if($_REQUEST['action']=="Delete") {
    $d->deleteSite($_REQUEST['site_id']); 
}

Header("Location:sites.php?msg=".urlencode($msg));
exit(0);
