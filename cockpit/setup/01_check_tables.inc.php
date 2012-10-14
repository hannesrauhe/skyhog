<?php

/* nav table */
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
