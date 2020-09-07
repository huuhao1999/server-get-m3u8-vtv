<?php
error_reporting(0);
define('domain', 'http://localhost');
header("Content-type: application/vnd.apple.mpegurl");

print(Request_URL(dec_hls()));

//--------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function dec_hls()
{
	if(isset($_GET['token']))
	{
		$link = hex2bin($_GET['token']);
	}
	if(isset($link) and substr_count($link, '/vtv/') != 0 and substr_count($link, '.m3u8') != 0)
		return $link;
	else
		return 'https://hls.mediacdn.vn/vtv/2020/9/3/kuvn-may-quay-ngua-troi-15991411793361033569298-659d5.mp4/1080.m3u8';
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function Request_URL($url)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'ExoPlayerDemo/3.1.5 (Linux;Android 5.0.2) ExoPlayerLib/1.5.7');

	$result = curl_exec($ch);
	curl_close($ch);
	return Rewrite_HLS($result);
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function Rewrite_HLS($playlist)
{
	$dataHLS = explode("\n", $playlist);
	$data_index = array();
	foreach($dataHLS as $key => $row) 
	{
		if(substr_count($row, 'METHOD=AES-128') != 0) 
		{
			$row = Fake_keyDRM($row);
		}
		$data_index[] = $row."\n";
	}
	$data_index = implode('', $data_index);
	return $data_index;
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function Fake_keyDRM($data)
{
	$real_key = explode('"', $data)[1];
	if(substr_count($real_key, 'sohatv.vn/drm') != 0)
	{
		$token = bin2hex($real_key);
		$fake_key = sprintf('%s/vtv/%s/secure.key', domain, $token);
		return str_replace($real_key, $fake_key, $data);
	}
	else
	{
		return $real_key;
	}
}
?>