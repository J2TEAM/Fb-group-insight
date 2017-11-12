<?php
  // DON'T EDIT, PLEASE RESPECT COPYRIGHT
  define('AUTHOR', 'T-Rekt');
  define('COPYRIGHT', 'J2TEAM');

  // EDIT THIS
  define('secret','i_am_t_rekt_obey_me');
  define('cookie', '');
  define('token', '');
  define('gid','');
?>

<?php
  $GLOBALS['ONE_DAY'] = 60*60*24;
  $GLOBALS['DOC_IDS'] = [
    "engagement" => "1470044149684839",
    "member" => "1554851827859432",
    "growth" => "1761498670534891",
    "highlights" => "1378499845591554"
  ];

  function request($url = '', $headers = [] , $params = [], $post = 0)    {
    $c = curl_init();
    $opts = [
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

  function makeQuery($start_time, $end_time) {
    return [
      "groupID"=> gid,
      "startTime"=> $start_time,
      "endTime"=> $end_time,
      "ref"=> null
    ];
  }

  function getData($queries, $headers) {
    $post_data = json_encode([
      "access_token" => token,
      "batch" => $queries,
      "include_headers"=> "false"
    ]);

    return request("https://graph.facebook.com/", $headers, $post_data, 1);
  }

  function getGroupInfo($headers) {
    $html = request("https://www.facebook.com/groups/".gid, $headers);
    $group_name = preg_match('/<title id="pageTitle">(.+?)<\/title>/', $html, $matches);
    $pending_posts = preg_match('/\/pending\/">([0-9]+)/', $html, $matches1);
    return [
      "group_name" => $group_name?$matches[1]:0,
      "pending_posts" => $pending_posts?$matches1[1]:0
    ];
  }

  function buildBatch($method, $rurl, $body) {
    return [
      "method" => $method,
      "relative_url" => $rurl,
      "body" => $body
    ];
  }

  function doAll() {
    try {
      $headers = [
        "Content-Type: application/json"
      ];
      $queries = [];
      array_push($queries, buildBatch("POST", "graphql", "q=node(".gid."){name}"));
      foreach ($GLOBALS['DOC_IDS'] as $doc_name => $doc_id) {
        $data = buildBatch(
          "POST",
          "graphql", 
          "variables=". json_encode(makeQuery(time()-$GLOBALS['ONE_DAY']*30, time())) ."&doc_id=". $doc_id
        );
        array_push($queries, $data);
      }
      $data = json_decode(getData($queries, $headers),1);
      // var_dump($data);
      $full['group_name'] = json_decode($data[0]["body"],1)[gid]["name"];
      $i = 0;
      foreach ($GLOBALS['DOC_IDS'] as $doc_name => $doc_id) {
        $i++;
        $full[$doc_name] = json_decode($data[$i]["body"],1)['data']['group']['group_insights'];
      }
      var_dump($full);
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

  if (isset($_GET['secret'])) {
    $secret = $_GET['secret'];
    if ($secret!==secret) {
      echo 0;
      return;
    }
    echo doAll();
  }
  else echo("Wrong secret");