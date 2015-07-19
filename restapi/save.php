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
require_once('./base.inc.php');

ob_start();
$json_return = array("status"=>"success","retval"=>0,"rettext"=>"");
try {
    if(!array_key_exists("file",$_POST)) {
    	throw new MyException("wrong data submitted (filename missing)");
    }
    
    $file = $_POST['file'];
    if(strpos($file,"..")!==FALSE) {
        throw new MyException("ERROR: Paths with .. not allowed: ".$file);        
    }
    if(!is_file($s->getPreviewDir().$file) && !array_key_exists("new",$_POST)) {
        throw new MyException("ERROR: file does not exist");
    }
    
    if(array_key_exists('elm1',$_POST) && !empty($_POST['elm1'])) {
    	file_put_contents ($s->getPreviewDir().$file,trim($_POST['elm1']));
    } else {
        throw new MyException("no content for $file submitted!");
    }
    
    chdir($s->getPreviewDir());
    git::add($file);
    git::commit($a->getAuthUserName()." <".$a->getAuthUserMail().">", "Commit from webinterface");
} catch(MyException $e) {
    $json_return = array("status"=>"error","retval"=>-1,"rettext"=>$e->getMessage());
}

$json_return["rettext"].="\n".ob_get_clean();

echo json_encode($json_return);
?>
