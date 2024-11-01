<?php
/**
 * @package LESS compiler
 * @version 1.0
 * @author  Gabor Zsoter - Zsitro.com
 */
/*
Plugin Name: LESS Compiler
Plugin URI: http://wordpress.org/extend/plugins/lesscompiler/
Description: This plugin compiles .less files in the active theme folder.
Author: Gabor Zsoter - Zsitro.com
Version: 1.0
Author URI: http://zsitro.com/
*/

//  ============
//  = Defaults =
//  ============

		// Project settings
		define(LESS_FOLDER, get_stylesheet_directory() ); // or change it to whatever:  define(LESS_FOLDER, 'styles/');

		//Debug ON/OFF
		define(DEBUG_MODE, 0);

		$lessFileList = array();

//  ========================
//  = Complie LESS file(s) =
//  ========================

function collectLessFiles($rootPath) {
	$pathStack=array($rootPath);
	while($path=array_pop($pathStack)){
		foreach(scandir($path) as $filename){
			if('.'!=substr($filename,0,1)){
				$newPath = $path.'/'.$filename;
				if (is_dir($newPath)){
					array_push($pathStack,$newPath);
				}else{
					if(end(explode(".",$newPath))=="less")
						$contents[basename($filename)] = $newPath;
				}
			}
		}
	}
	/*
	echo '<pre>';
	var_dump($contents);
	echo '</pre>';
	*/
	return $contents;
}

function OnlyFilename($strName){
     $ext = strrchr($strName, '.');
     if($ext !== false){
         $strName = substr($strName, 0, -strlen($ext));
     }
     return $strName;
}

function processLESSfiles(){
	// read files to an array
	$files = collectLessFiles( LESS_FOLDER );

	require realpath(dirname(__FILE__)).'/lessc.inc.php';

	foreach ($files as $lessFile) {
		try {
		    $cssFile = OnlyFilename($lessFile).".css";
		    lessc::ccompile( $lessFile, $cssFile );
			//echo "***".$value." is CONVERTED!<br>";

		} catch (exception $ex) {
			//echo "***".$value." is NOT CONVERTED!<br>";
		    exit($ex->getMessage());
		}
		//echo "***".$value."<br>";
	} // end of foreach

}

if (!is_admin()){
	add_action( 'wp_head', 'processLESSfiles' );
}

?>