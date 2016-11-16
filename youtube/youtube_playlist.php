<?php

  define('YOUTUBE','https://www.youtube.com/');
  require_once('../__include/class/class_parserbroker.php');

  $s = request('p',null,false,true);
  $output = request('format','cards');
  $type = request('type','playlist');
  $url = youtube_link($s);

  if (empty($url)) die();

  if ($output == 'cards') {
     echo playlist_video_cards(get_playlist_page($url));
  } else {
    echo json_encode(get_playlist_page($url));
  }

  // $playlist_header = get_youtube_header($url);
  // $playlist_data = get_playlist_page($url);
  // var_dump($playlist_header);
  // var_dump($playlist_data);


function playlist_video_cards($data) {
  $HTMLBlock = "";
  $v = "";
  if (!empty($data)) {
    foreach($data as $d) {
      $HTMLBlock .= $v . base64_encode(playlist_HTML_block($d));
      $v = '|';
    }
  }
  return $HTMLBlock;
}

function playlist_HTML_block($d) {
  $block = '<section class="search-result-item">' .
            '    <a class="image-link" href="#"><img class="image cover img-responsive" src="' . $d['thumb'] . '">' .
            '    </a>' .
            '    <div class="search-result-item-body">' .
            '        <div class="row">' .
            '            <div class="col-sm-9">' .
            '                 <div class="row">' .
            '                     <div class="col-sm-1">' .
            '                     </div>' .
            '                     <div class="col-sm-10">' .
            '                         <h4 class="search-result-item-heading">'.$d['title'].'</h4><br>' .
            '                     </div>' .
            '                     <div class="col-sm-1">' . $d['time'] .
            '                     </div>' .
            '                 </div>' .
            '            </div>' .
            '            <div class="col-sm-3 text-align-center">' .
            '                <a class="btn-down btn btn-primary btn-info btn-sm" href="' .$d['id'] .'">Download MP3</a>' .
            '            </div>' .
            '        </div>' .
            '    </div>' .
            '</section>';
  return $block;
}

function get_playlist_page($url) {
  $token = '<tr class="pl-video yt-uix-tile "';
  $o = new HTMLListBroker($url,$token);
  $o->addField('id','data-video-id="','"');
  $o->addField('user','/user/','"');
  $o->addField('title','data-title="','"');
  $o->addField('thumb','data-thumb="','?');
  $o->addField('time','<div class="timestamp"><span aria-label="','<');
  $data = $o->parseHTML();
  return data_adjustments($data);
}

function get_youtube_header($url) {
  $token = '<title>';
  $o = new HTMLListBroker($url,$token);
  $o->addField('title','<meta name="title" content="','"');
  $o->addField('description','<meta name="description" content="','"');
  $o->addField('user','<a href="/user/','"');
  $o->addField('cover','<div class="pl-header-thumb" style="height:126px;"><img src="','?');
  $o->addField('avatar','class="appbar-nav-avatar" src="','"');
  $o->addField('next_page','data-uix-load-more-href="','"');
  $data = $o->parseHTML();
  return $data;
}

function data_adjustments($data) {
  $res = array();
  foreach ($data as $d) {
    $t = explode('">',@$d['time']);
    if(!empty($t[1])) {
      $d['watch'] = YOUTUBE . 'watch?v=' . $d['id'];
      $d['time_text'] = @$t[0];
      $d['time'] = @$t[1];
      $str_time = $d['time'];
      if (strlen($str_time) <= 5) {
        $str_time = '00:' . $str_time;
      }
      $str_time = preg_replace("/^([\d]{1,2})\:([\d]{2})$/", "00:$1:$2", $str_time);
      sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);
      $time_seconds = $hours * 3600 + $minutes * 60 + $seconds;
      $d['minutes'] = ceil($time_seconds/60);
      if ($d['minutes'] < 90) {
        $res[] = $d;
      }
    }
  }
  return $res;
}
// https://query.yahooapis.com/v1/yql?q=

function youtube_link($s) {
  if (empty($s)) return "";
  $link = get_playlist($s);
  if (empty($link)) {
    $link = get_video_id($s);
    if (empty($link)) {
      $link = get_youtube_link($s);
    }
  }
  return $link;
}
function get_youtube_link($s) {
  if (strlen($s) > 12) {
    return YOUTUBE . 'playlist?list='.$s;
  }
  return YOUTUBE . 'watch?v='.$s;
}

function get_playlist($s) {
  $arr = explode('list=',$s);
  if (!empty($arr[1])) {
    $arr = explode('&',$arr[1].'&');
    return YOUTUBE . 'playlist?list='.$arr[0];
  }
  return "";
}

function get_video_id($s) {
  $arr = explode('v=',$s);
  if (!empty($arr[1])) {
    $arr = explode('&',$arr[1].'&');
    return YOUTUBE .'watch?v=' . $arr[0];
  }
  return "";
}
function request($r,$default=null,$lower=true,$die=false) {
  $req = urldecode(@$_REQUEST[$r]);
  if (empty($req)) {
    if ($die) die();
    $req = $default;
  }
  if ($lower) {
    $req = strtolower($req);
  }
  return $req;
}
