<?php
error_reporting(0);
header("Content-Type: binary/octet-stream");
print(get_data(dec_key()));


//--------------------------------------------------------------------------------------------------------------------------------------------------------
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function dec_key()
{
	if(isset($_GET['token']))
	{
		$link = hex2bin($_GET['token']);
	}
	if(isset($link) and substr_count($link, '/drm/') != 0)
		return $link;
	else
		return 'https://kms.sohatv.vn/drm/b5272dea-d587-49f6-b6da-71d1d76558e6.key';
}
//--------------------------------------------------------------------------------------------------------------------------------------------------------
function get_data($link)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $link);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, 'ExoPlayerDemo/3.1.5 (Linux;Android 5.0.2) ExoPlayerLib/1.5.7');

	$result = curl_exec($ch);
	$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	return $result;
}

?>
