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

/*site table */ 
$sites_schema = "CREATE TABLE sites (id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT, page_url TEXT,page_dir TEXT,preview_url TEXT,preview_dir TEXT,git TEXT)";

$query_result = $d->querySingle('SELECT * FROM sites', true);
if($query_result===FALSE) {
	$r = $d->exec($sites_schema.";");
	if(!$r) {
		echo "Nav table cannot be created, because: \n";
		echo $d->lastErrorMsg();
		exit(1);
	}
	echo "Created sites table.\n";
} else {
	echo "Sites table exists... ";
	$r = $d->query("select sql from sqlite_master where type = 'table' and name = 'sites';");
	$res = $r->fetchArray(SQLITE3_ASSOC);
	if($sites_schema!=$res['sql']) {
		echo "but its schema is\n".$res['sql']." and should be\n".$sites_schema."\n";
		echo "Please use PHPsqlite to export the table, delete it afterwards and rerun the maintenance script! Try to import the data to get it back!\n";
		echo "This script continues now, because this error might not be critical! \n\n";
	} else {
		echo "and looks fine\n";
	}
}

/* nav table
$nav_schema = "CREATE TABLE nav (link TEXT, id TEXT PRIMARY KEY, name TEXT, menu_order INTEGER)";

$query_result = $d->querySingle('SELECT * FROM nav', true);
if($query_result===FALSE) {
	$r = $d->exec($nav_schema.";");
	if(!$r) {
		echo "Nav table cannot be created, because: \n";
		echo $d->lastErrorMsg();
		exit(1);
	}
	echo "Created nav table.\n";
} else {
	echo "Nav table exists... ";
	$r = $d->query("select sql from sqlite_master where type = 'table' and name = 'nav';");
	$res = $r->fetchArray(SQLITE3_ASSOC);
	if($nav_schema!=$res['sql']) {
		echo "but its schema is\n".$res['sql']." and should be\n".$nav_schema."\n";
		echo "Please use PHPsqlite to export the table, delete it afterwards and rerun the maintenance script! Try to import the data to get it back!\n";
		echo "This script continues now, because this error might not be critical! \n\n";
	} else {
		echo "and looks fine\n";
	}
}
*/
