#!/usr/bin/php
<?php
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('memory_limit','-1');
ini_set('max_execution_time', 0); 

if($argv[1] && is_file($argv[1])){
	$filename = $argv[1]; 
}else{
	$filename = "/var/lib/mlocate/mlocate.db";
}

$handle = fopen($filename, "rb");
$data = fread($handle, filesize($filename));
fclose($handle);

$header = substr($data, 1, 7);
echo "File Header Find: ".$header;
echo "\r\n";
# Get file header information

$conf_block_len = unpack("N", substr($data, 8, 4));
echo "mlocate Configuration File Length: ".$conf_block_len[1];
echo "\r\n";
# Get the size updatedb.conf block

$updatedb_conf = substr($data, 18, $conf_block_len[1]);
echo "updatedb.conf: ".$updatedb_conf;
echo "\r\n";
# Get the updatedb.conf contents

$data = substr_replace($data, '', 0, $conf_block_len[1]+18); 
/*
Remove parsing completed header
*/


$data_arr = preg_split('[\x00\x02]', $data);
/*
Separate Directory Entry with a delimiter of 0x0002 and save it in an array.
*/


$count_arr = sizeof($data_arr)-1;
/*
Array Size Calculation
*/

for($i=0; $i < $count_arr; $i++){
	
	$time_dir = unpack('N4TIME/Z*dir', $data_arr[$i]);
	$timestamp = $time_dir[TIME2];
	$nanosec = $time_dir[TIME3];
	$dir = $time_dir[dir];
	$dateString = date("Y-m-d H:i:s", $timestamp).$nanosec;
	
	echo $dir." ==> ".$dateString;
  	//Parent Directory ==> Timestamp

	$subdir = substr_replace($data_arr[$i], '', 0, strlen($dir)+16); 
	//Remove Parsed Entry
	
		$a = preg_replace('[\x00\x01]', "\n [D]", $subdir);
		$c = preg_replace('[\x00\x00]', "\n [F]", $a);
		//directory, file classification
		
	echo $c;
	//output
	echo "\n\n";	
	
}

?>
