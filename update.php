<?php
$GLOBALS['ONE_DAY'] = 60*60*24;
$GLOBALS['DOC_IDS'] = [
	"engagement" => "1470044149684839",
	"member" => "1554851827859432",
	"growth" => "1761498670534891",
	"highlights" => "1378499845591554"
];

function request($url = '', $headers = [] , $params = [], $post = 0)	{
	$c = curl_init();
	$opts = [
		// CURLOPT_PROXY => '127.0.0.1:8888',
		CURLOPT_URL => $url.(!$post && $params ? '?'.http_build_query($params) : ''),
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_HTTPHEADER =>  $headers,
		CURLOPT_SSL_VERIFYPEER => false
	];
	if($post){
		$opts[CURLOPT_POST] = true;
		$opts[CURLOPT_POSTFIELDS] = $params;
	}
	curl_setopt_array($c, $opts);
	$d = curl_exec($c);
	curl_close($c);
	return $d;
}

function getFbDtsg($headers) {
	$html = request("https://www.facebook.com/", $headers);
	$fb_dtsg = preg_match('/DTSGInitialData.+?:"(.+?)"/', $html, $matches);
	return $fb_dtsg?$matches[1]:0;
}

function makeQuery($gid, $start_time, $end_time) {
	return json_encode([
		"groupID"=> $gid,
		"startTime"=> $start_time,
		"endTime"=> $end_time,
		"ref"=> null
	]);
}

function getData($gid, $doc_id, $start_time, $end_time, $fb_dtsg, $headers) {
	$post_data = http_build_query([
		"__a" => "1",
		"fb_dtsg" => $fb_dtsg,
		"variables" => makeQuery($gid, $start_time, $end_time),
		"doc_id" => $doc_id
	]);

	return request("https://www.facebook.com/api/graphql/", $headers, $post_data, 1);
}

function getGroupName($gid, $headers) {
	$html = request("https://www.facebook.com/groups/".$gid, $headers);
	$group_name = preg_match('/<title id="pageTitle">(.+?)<\/title>/', $html, $matches);
	return $group_name?$matches[1]:0;
}

function doAll($gid, $cookie) {
	try {
		$headers = [
			"User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36",
			"Content-Type: application/x-www-form-urlencoded",
			"Cookie: ".$cookie
		];
		$fb_dtsg = getFbDtsg($headers);
		if (!$fb_dtsg) return 0;
		$full = [];
		$group_name = getGroupName($gid, $headers);
		if (!$group_name) return 0;
		$full['group_name'] = $group_name;
		// $f = fopen($gid.".json", "w");
		foreach ($GLOBALS['DOC_IDS'] as $doc_name => $doc_id) {
			$data = getData($gid, $doc_id, time()-$GLOBALS['ONE_DAY']*30, time(), $fb_dtsg, $headers);
			$full[$doc_name] = json_decode($data,1)['data']['group']['group_insights'];
		}
		$full['last_update'] = time();
		$f = fopen("full.json", "w");
		fwrite($f, json_encode($full));
		fclose($f);
		return 1;
	}
	catch (Exception $e) {
		return 0;
	}
}

const secret = "i_am_t_rekt_obey_me";
$cookie = "";
$gid = "364997627165697";

if (isset($_GET['secret'])) {
	$secret = $_GET['secret'];
	if ($secret!==secret) {
		echo 0;
		return;
	}
	// $gid = $_GET['gid'];
	// $cookie = $_GET['cookie'];
	echo doAll($gid, $cookie);
}
else echo("Wrong secret");