#!/usr/bin/php
<?php
/*
Extract Unallocated Area Using Sleuth Kit
Unallocated Area Carving
*/

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0); 

if($argv[1] && is_file($argv[1])){
	$filename = $argv[1]; 
}else{
	$filename = "/var/lib/mlocate/mlocate.db";
}

$fp = popen("blkls -A /dev/sda1", "r");
	while(!feof($fp)){
	$data = fread($fp, 8192);

	//preg_match_all('/[\x00{4}](?<timestamp>.{16})[\0x00{4}](?<parent>.*)[\0x00]/', $data, $out, PREG_OFFSET_CAPTURE);
	preg_match_all('/[\x00\x00\x00\x00](?<timestamp>.{16})[\0x00\x00\x00\x00](?<entry>.*)[\0x00\0x02]/', $data, $out, PREG_SET_ORDER);
	echo $out[0][timestamp];
	echo $out[0][entry];
	
	flush();
}

?>