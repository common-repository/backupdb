<?php

function bdb_checkSecurity(){
	if(!session_id())session_start();
	if(!isset($_POST['action']) || !function_exists($_POST['action']) || !isset($_SESSION['IS_ADMIN']) ||!$_SESSION['IS_ADMIN'] || !isset($_POST['token']) || $_POST['token'] !== $_SESSION['SECURE_TOKEN'])
		die('Bad request!');	
}

function bdb_check_data(){
	if(!isset($_POST['path']) || strlen($_POST['path']) < 1)die('Bad request!');
}

function bdb_get_files(){
	bdb_checkSecurity();
	$dir = new DirectoryIterator(get_home_path().'wp-content/backups/') or die('Bad request!');
	$ret['files'] = array();
	foreach ($dir as $fileinfo){
		if (!$fileinfo->isDot() && $fileinfo->getExtension() == "sql"){
			array_push($ret['files'], array(
				'name'	=>	$fileinfo->getFilename(),
				'ctime'	=>	date('d.m.Y H:i:s',$fileinfo->getCTime()),
				'mtime'	=>	date('d.m.Y H:i:s',$fileinfo->getMTime())
			));
		}
	}
	$ret['files'] = array_reverse($ret['files']);
	echo json_encode($ret);
	die();
}

function bdb_create_mysql_dump(){
	bdb_checkSecurity();
	$user = DB_USER;
	$pass = DB_PASSWORD;
	$name = DB_NAME;
	$host = DB_HOST;
	$path = get_home_path().'wp-content/backups/'.date("Y-m-d").'_'.date("H-i-s").'_'.'backup'.'_'.$name.'.sql';
	$return = "";
	
	//Datenbankverbindung herstellen
	$db = mysqli_connect($host,$user,$pass,$name) or die('Could not connect to database');
	
	//Alle Tabellen herausfinden
	$tables = array();
	$result = $db->query("SHOW TABLES");
	while($row = mysqli_fetch_row($result)){
		$tables[] = $row[0];
	}
	
	//Durch jede Tabelle gehen
	foreach($tables as $t){
		$result = $db->query("SELECT * FROM ".$t);
		$num_fields = mysqli_num_fields($result);
		
		//Erster Teil des Outputs - Tabelle entfernen
		$return .= 'DROP TABLE IF EXISTS '.$t.';/*<|||||||>*/';
		
		//Zweiter Teil des Outputs - Tabelle erstellen
		$row2 = mysqli_fetch_row($db->query("SHOW CREATE TABLE ".$t));
		$return .= "\n\n" . $row2[1] . ";/*<|||||||>*/\n\n";
		
		//Dritter Teil des Outputs - Daten in die neue Tabelle eintragen
		for($i = 0; $i < $num_fields; $i++){
			while($row = mysqli_fetch_row($result)) {
				$return.= 'INSERT INTO '.$t.' VALUES(';
				for($j=0; $j<$num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
					$row[$j] = str_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) {
						$return .= '"' . $row[$j] . '"';
					} else {
						$return .= '""';
					}
					if ($j<($num_fields-1)) {
						$return.= ',';
					}
				}
				$return.= ");/*<|||||||>*/\n";
			}
		}
		$return .= "\n\n\n";
	}
	
	//Datei speichern
	$handle = fopen($path, "w+");
	fwrite($handle,$return);
	fclose($handle);
	die();
}

function bdb_load_mysql_dump(){
	bdb_checkSecurity();
	bdb_check_data();
	$user = DB_USER;
	$pass = DB_PASSWORD;
	$name = DB_NAME;
	$host = DB_HOST;
	$path = get_home_path().'wp-content/backups/'.$_POST['path'];
	
	$db = mysqli_connect($host,$user,$pass,$name) or die('Could not connect to database!');
	 
	$templine = '';
	$lines = file($path);
	foreach ($lines as $line){
		if (substr($line, 0, 2) == '--' || $line == '')continue;
		$templine .= $line;
		if (strpos($line,'/*<|||||||>*/') !== false || strpos($line,'<|||||||>') !== false){
			$templine = str_replace(array('/*<|||||||>*/','<|||||||>'),'',trim($templine));
			$db->query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysqli_error($db) . '<br /><br />');
			$templine = '';
		}
	}
	die();
}