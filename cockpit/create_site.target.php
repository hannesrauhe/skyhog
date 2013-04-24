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

$site_name = '';
if(isset($_REQUEST['site_name'])) {
    $site_name = $_REQUEST['site_name'];
    // if(!utf8_is_valid($site_name))
    // {
      // $site_name=utf8_bad_strip($site_name);
    // }
//     
    // $site_name = utf8_to_ascii($site_name, '' );
}
echo $site_name;

echo "SITE CREATED: ".$s->createSite(array("name"=>$site_name),$d);