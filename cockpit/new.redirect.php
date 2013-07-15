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
require_once 'base.inc.php';

$msg="";
$format = "";

try {
  if(array_key_exists("file",$_REQUEST)) {
	  $file = $_REQUEST['file'];
  } else {
      throw new MyException("No Data submitted");   
  }

  if(strpos($file,"..")!==FALSE) {
      throw new MyException("ERROR: Paths with .. not allowed: ".$file);        
  }
      
  if(array_key_exists("format",$_REQUEST)) {
	  $format = $_REQUEST['format'];
  }

  if($format=="html") {
	  if(substr($file,0,1)!='_') {
		  $file="_".$file;
	  } 
	
	  if(substr($file,1,1)=='_') {
		  $file="_a".substr($file,2);
	  }	
	
	  if(substr($file,-5)!='.html') {
		  $file=$file.".html";
	  }
	
	  if(is_file($s->getPreviewDir().$file)) {	  
      throw new MyException("No Data submitted");
	  }
	  fclose(fopen($s->getPreviewDir().$file, 'a'));
  }
} catch(MyException $e) {
    $msg = $e->getMessage();
}

Header("Location:pages.php?msg=$msg&file=".urlencode($file));
exit(0);

