# mlocate-viewer
preg_match_all('/[\x00\x00\x00\x00](?<timestamp>.{16})[\0x00\x00\x00\x00](?<entry>.*)[\0x00\0x02]/', $data, $out, PREG_SET_ORDER);
preg_match_all('/[\x00{4}](?<timestamp>.{16})[\0x00{4}](?<parent>.*)[\0x00]/', $data, $out, PREG_OFFSET_CAPTURE);

$time_dir = unpack('N4TIME/Z*dir', $data);
//unpack에서 배열은 1부터 00000000/00000000/00000000/00000000
//											   1        2        3         4	
	$timestamp = $time_dir[TIME2];
	$nanosec = $time_dir[TIME3];
	$dir = $time_dir[dir];
