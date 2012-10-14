<?php
/*
Copyright 2012 Hannes Rauhe

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
if((include_once("./config.inc.php")) === FALSE) {
	echo "The config file does not exist... ";
	if(!array_key_exists('DOMAIN', $_POST)) {
		echo "TODO: Print form!\n"; //TODO
		exit(1);
	} else {
		//relevant data has been POSTED - replace in config-template
		$file_c = file_get_contents("./config.inc.php.template");
		if($file_c) {
		    $file = str_replace(array_keys($_POST), array_values($_POST), $file);
		    if(file_put_contents("./config.inc.php", $file_c) === FALSE) {
		    	echo "The config file hasn't been written, make the skyhog directory writable for the webserver and reload or copy this content to config.inc.php:\n";
				echo $file;
				exit(1);
		    }
		}
	}
} else {
	echo "The config file exists... ";
	if(! (defined("DOMAIN") ||	defined("PAGE_DIR") || defined("PAGE_PATH") || defined("UPLOAD_PATH") || defined("UPLOAD_DIR"))) {
		echo "but DOMAIN, PAGE_* or UPLOAD_* rule is missing. Repair by hand please!\n";
		exit(1);
	}
	if(! (defined("GIT_CMD") || defined("PYTHON_CMD"))) {
		echo "but GIT_CMD or PYTHON_CMD is missing. Repair by hand please!\n";
		exit(1);
	}
	if(! (defined("DB_NAME")) ) {
		echo "but DB_NAME is missing. Trying to add the default value automatically...";
		$cont_app = 'define("DB_NAME","scihog.db");';
	    if(file_put_contents("./config.inc.php", $cont_app,FILE_APPEND) === FALSE) {
	    	echo "The config file hasn't been written, make the skyhog directory writable for the webserver and reload or add this content to config.inc.php:\n";
			echo $cont_app;
			exit(1);
	    } else {
	    	echo "succeded. Please reload page for further tests!\n";
	    	exit(0);
	    }		
	}
	echo "and looks fine.\n";
}
