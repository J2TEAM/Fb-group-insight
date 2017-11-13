<?php
  // DON'T EDIT, PLEASE RESPECT COPYRIGHT
  define('AUTHOR', 'T-Rekt');
  define('COPYRIGHT', 'J2TEAM');

  // EDIT THIS
  define('token', '');
  define('gid', '');
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
      $full['group_name'] = json_decode($data[0]["body"],1)[gid]["name"];
      $i = 0;
      foreach ($GLOBALS['DOC_IDS'] as $doc_name => $doc_id) {
        $i++;
        $full[$doc_name] = json_decode($data[$i]["body"],1)['data']['group']['group_insights'];
      }
      $full['last_update'] = time();
      return json_encode($full);
    }
    catch (Exception $e) {
      return 0;
    }
  }

  if (isset($_GET['action'])) {
    $action = $_GET['action'];
    if ($action = 'getData') {
    	echo doAll();
    	exit;
    }
  }

  ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Your image links below -->
    <meta property="og:image" content="https://i.imgur.com/mdSSfuQ.png">
    <meta name="twitter:image:src" content="https://i.imgur.com/mdSSfuQ.png">
    <meta itemprop="image" content="https://i.imgur.com/mdSSfuQ.png">
    
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta http-equiv="cache-control" content="max-age=0">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
    <meta http-equiv="pragma" content="no-cache">

    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta http-equiv="content-language" content="en">
    <meta http-equiv="revisit-after" content="1 days">

    <!-- DO NOT EDIT -->
    <!-- <meta name="keywords" content="juno_okyo,j2team,j2team community,ranking"> -->
    <!-- <meta name="description" content="Top active members in J2TeaM Community from last 30 days"> -->
    <meta name="author" content="Juno_okyo">
    <meta name="copyright" content="Juno_okyo">
    <meta name="generator" content="Juno_okyo">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="Community Ranking by J2TeaM">
    <!-- <meta property="og:description" content="Top active members in J2TeaM Community from last 30 days"> -->
    <!-- <meta property="og:url" content="http://code.junookyo.xyz/apps/j2team-community-ranking/index.php"> -->
    <meta property="og:type" content="website">

    <meta property="og:site_name" content="Community Ranking">
    <meta property="og:locale" content="vi_VN">
    <!-- <meta property="fb:admins" content="100003880469096"> -->
    <!-- <meta property="fb:app_id" content="458084867627529"> -->

    <!-- <meta name="twitter:card" content="summary"> -->
    <!-- <meta name="twitter:site" content="J2TeaM Community Ranking"> -->
    <!-- <meta name="twitter:title" content="J2TeaM Community Ranking"> -->
    <!-- <meta name="twitter:description" content="Top active members in J2TeaM Community from last 30 days"> -->
    <meta name="twitter:creator" content="@juno_okyos">
    <!-- <meta name="twitter:url" content="http://code.junookyo.xyz/apps/j2team-community-ranking/index.php"> -->

    <meta itemprop="name" content="Community Ranking">
    <meta itemprop="description" content="Top active members from last 30 days">

    <title>Community Ranking by J2TeaM</title>

    <link rel="author" href="https://plus.google.com/u/0/108443613096446306111/">
    <link rel="publisher" href="https://plus.google.com/u/0/108443613096446306111/">
    <!-- <link rel="canonical" href="http://code.junookyo.xyz/apps/j2team-community-ranking/index.php"> -->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
    body {
      background-color: #e9ebee;
    }
    .profile-picture {
      border-radius: 100%;
      width: 32px;
      height: 32px;
    }
    .readmore {
      margin-top: 8px;
    }
    </style>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  </head>
  <body>
    <div class="container" id="rankTable" v-if="full">
      <div class="row">
        <div class="col-xs-12 text-center">
          <h1>{{full.group_name}} Ranking</h1>
          <p>
            Total: <strong>{{full.growth.total_member_count}}</strong> members, <strong>{{full.engagement.active_member_count}}</strong> active member, <strong>{{full.growth.pending_member_current_count}}</strong> pending members, <strong>{{full.engagement.total_post_count}}</strong> posts, <strong>{{full.pending_posts}}</strong> pending posts, <strong>{{full.engagement.total_comment_count}}</strong> comments and <strong>{{full.engagement.total_like_count}}</strong> reactions. Updated at: {{full?dateFormat(new Date(full.last_update*1000)):""}}
          </p>
        </div>
      </div>
          
      <div class="row">
        <div class="col-xs-12 col-md-6">
          <div class="panel panel-default">
            <div class="panel-heading">
              <h3 class="panel-title"><span class="glyphicon glyphicon-flash" aria-hidden="true"></span> Trending Posts</h3>
            </div>
            <div class="panel-body">
              <div class="media" v-for="post in full.engagement.top_posts_breakdown" v-if="post.story.message && post.author">
                <div class="media-left">
                  <a :href="post.story.url" target="_blank">
                    <img class="media-object" :src="post.author.profile_picture.uri" alt="post.author.name">
                  </a>
                </div>
                <div class="media-body">
                  <h4 class="media-heading">
                    <a :href="post.author.url" target="_blank" v-text="post.author.name"></a>
                  </h4>
                  <p v-text="post.story.message.text"></p>
                  <a class="btn btn-default btn-sm readmore" :href="post.story.url" target="_blank"><span class="glyphicon glyphicon-new-window" aria-hidden="true"></span> Read on Facebook</a>
                  <a class="btn btn-default btn-sm readmore disabled" href="#" disabled>{{post.like_count}} Likes</a>
                  <a class="btn btn-default btn-sm readmore disabled" href="#" disabled>{{post.comment_count}} Comments</a>
                  <a class="btn btn-default btn-sm readmore disabled" href="#" disabled>{{post.view_count}} Views</a>
                </div>
              </div>
              <hr>
            </div>
          </div>
        </div>

        <div class="col-xs-12 col-md-6">
          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h3 class="panel-title"><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> Hall of Fame</h3>
                </div>
                <table class="table table-bordered table-hover table-striped">
                  <thead>
                    <tr>
                      <th class="text-center">Rank</th>
                      <th>Name</th>
                      <th class="text-center">Posts</th>
                      <th class="text-center">Likes</th>
                      <th class="text-center">Comments</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(member, index) in full.member.top_contributors_breakdown">
                      <td class="text-center"><img :src="`./assets/images/${index<=6?badges[index]:badges[7]}`" :alt="`Rank #${index+1}`" :title="`Rank #${index+1}`" width="32px" height="32px"></td>
                      <td><img class="profile-picture" :src="member.user.profile_picture.uri" alt="profile-picture" width="32px" height="32px"> <a :href="member.user.url" target="_blank" v-text="member.user.name"></a></td>
                      <td class="text-center" v-text="member.post_count"></td>
                      <td class="text-center" v-text="member.like_count"></td>
                      <td class="text-center" v-text="member.comment_count"></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h3 class="panel-title">Daily active members</h3>
                </div>
                <div id="activeMemberCountChart" style="width: 100%;"></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="panel panel-primary">
                <div class="panel-heading">
                  <h3 class="panel-title">Weekly activity breakdown</h3>
                </div>
                <div id="weeklyActivityChart" style="width: 100%;"></div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.4.2/vue.min.js"></script>
    <script src="assets/js/main.js"></script>
  </body>
</html>