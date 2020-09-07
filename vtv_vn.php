<?php
error_reporting(0);
define('domain', 'http://localhost');

echo enc_hls();

//--------------------------------------------------------------------------------------------------------------------------------------------------------
function enc_hls()
{
	$data = get_channel_id();
	$enc = bin2hex($data[1]);
	return sprintf('%s/vtv/%s/%s.m3u8', domain, $enc, $data[0]);
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function get_channel_id()
{
	$link = 'https://vtv.vn/video/tu-may-quay-ngua-troi-den-may-quay-tren-troi-duoi-be-457721.htm';
	if(isset($_GET['link']))
	$link = $_GET['link'];
	preg_match('/https:\/\/vtv.vn\/(.*)\-(.*).htm/', $link, $id);
	if(isset($id[2]) and strlen($id[2]) <= 10)
	{
		$url = 'https://p2.cnnd.vn/vtv-api/v2/app/video/detail';
		$result = Request_URL($url, 'id='.$id[2]);
		$hls = json_decode($result, true)['data']['Video']['FileName'];
		return [$id[2], Resolution_URL($hls)];
	}
	elseif(isset($id[2]) and strlen($id[2]) >= 10)
	{
		$url = 'https://p2.cnnd.vn/vtv-api/v2/app/news/detail-native';
		$result = Request_URL($url, 'news_id='.$id[2]);
		$hls = json_decode($result, true)['data']['News']['Avatar5'];
		return [$id[2], Resolution_URL($hls)];
	}
	else
	{
		return ['457721', 'https://hls.mediacdn.vn/vtv/2020/9/3/kuvn-may-quay-ngua-troi-15991411793361033569298-659d5.mp4/master.m3u8'];
	}
}

//--------------------------------------------------------------------------------------------------------------------------------------------------------
function httpcode_hls($url) 
{
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	curl_setopt($ch, CURLOPT_USERAGENT, 'ExoPlayerDemo/3.1.5 (Linux;Android 5.0.2) ExoPlayerLib/1.5.7');
	curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $httpcode;
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function Resolution_URL($hls)
{
	$playlist = Request_HLS($hls);

	if(substr_count($playlist, 'm3u8') == 6)
	{
		$hls = str_replace('master.m3u8', '1080.m3u8', $hls);
	}
	elseif(substr_count($playlist, 'm3u8') == 5)
	{
		$hls = str_replace('master.m3u8', '720.m3u8', $hls);
	}
	elseif(substr_count($playlist, 'm3u8') == 4)
	{
		$hls = str_replace('master.m3u8', '480.m3u8', $hls);
	}
	elseif(substr_count($playlist, 'm3u8') == 3)
	{
		$hls = str_replace('master.m3u8', '360.m3u8', $hls);
	}
	elseif(substr_count($playlist, 'm3u8') == 2)
	{
		$hls = str_replace('master.m3u8', '240.m3u8', $hls);
	}
	elseif(substr_count($playlist, 'm3u8') == 1)
	{
		$hls = str_replace('master.m3u8', '144.m3u8', $hls);
	}
	return $hls;
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function Request_URL($url, $id = null)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	if ($id !== null) 
	{
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'secret_key=L$5SSAfp@7^9$F8NkUY_hbfU&'.$id);
	}
	$headers = ['Content-Type: application/x-www-form-urlencoded', 'User-Agent: okhttp/3.10.0'];
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

	$result = curl_exec($ch);
	curl_close($ch);
	return str_replace('http://', 'https://', $result);
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function Request_HLS($url)
{
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'ExoPlayerDemo/3.1.5 (Linux;Android 5.0.2) ExoPlayerLib/1.5.7');

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}
?>