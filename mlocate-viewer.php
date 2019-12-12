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
# 파일 헤더 가져오기  mlocate

$conf_block_len = unpack("N", substr($data, 8, 4));
echo "mlocate Configuration File Length: ".$conf_block_len[1];
echo "\r\n";
# updatedb.conf 설정 크기 가져오기

$updatedb_conf = substr($data, 18, $conf_block_len[1]);
echo "updatedb.conf: ".$updatedb_conf;
echo "\r\n";
# 설정 부분 가져오기

$data = substr_replace($data, '', 0, $conf_block_len[1]+18); 
/*
파싱이 완료된 헤더를 제거한다.
*/


$data_arr = preg_split('[\x00\x02]', $data);
/*
Directory Entry를 0x0002로 구분자로 분리하고 배열에 저장한다.
*/


$count_arr = sizeof($data_arr)-1;
/*
배열크기 계산
*/

for($i=0; $i < $count_arr; $i++){
	
	$time_dir = unpack('N4TIME/Z*dir', $data_arr[$i]);
	$timestamp = $time_dir[TIME2];
	$nanosec = $time_dir[TIME3];
	$dir = $time_dir[dir];
	$dateString = date("Y-m-d H:i:s", $timestamp).$nanosec;
	
	echo $dir." ==> ".$dateString;
  //Parent Directory ==> 시간정보

	$subdir = substr_replace($data_arr[$i], '', 0, strlen($dir)+16); 
	//파싱한 엔트리 삭제
	
		$a = preg_replace('[\x00\x01]', "\n [D]", $subdir);
		$c = preg_replace('[\x00\x00]', "\n [F]", $a);
		// Subdirectory 디렉터리, 파일 구분 
		
	echo $c;
	// 디렉터리, 파일 출력
	echo "\n\n";	
	
}

?>
