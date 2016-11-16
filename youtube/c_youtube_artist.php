<?php
    /*
    
        Author: DevRoof Team
        Example: Youtube Artist Search
        Description: Search artist info in Youtube using Parser Broker API.
        
    */    
    define('YOUTUBE','https://www.youtube.com/');
    //
    // Add Parser Broker Class
    //
    require_once('../__include/class/class_parserbroker.php');

    //
    // Receive search parameter
    //
    $s = @$_REQUEST['s'];
    if (empty($s)) $s = "SIMPLY RED";

    //
    // Define Parser Parameters
    //
    $url = YOUTUBE . "/results?search_query=" . urlencode($s);
    $token = "watch-card-header";

    //
    // Create Parser Object
    //
    $o = new HTMLListBroker($url,$token);
    $o->addField('image','yt-thumb-simple','</img>');
    $o->addField('playlist','href="/playlist?list=','"');
    $o->addField('artist','data-ytid="','<');

    //
    // Request info from Parser API server
    //
    $data = $o->parseHTML();
    //
    // Further adjustments
    //
    $img = explode('//',@$data[0]['image']);
    $img = explode('"',@$img[1]);
    if (!empty($img[0])) {
        $data[0]['image'] = 'http://' . @$img[0];
        $art = explode('>',$data[0]['artist']);
        $data[0]['artist'] = @$art[1];
    }
    $data = @$data[0];

    //
    // Get Playlist
    //
    if(!empty($data['playlist'])) {
      $url = YOUTUBE . 'playlist?list='. $data['playlist'];
      $token = '<tr class="pl-video yt-uix-tile "';
      $o = new HTMLListBroker($url,$token);
      $o->addField('id','data-video-id="','"');
      $o->addField('user','/user/','"');
      $o->addField('title','data-title="','"');
      $o->addField('thumb','data-thumb="','?');
      $o->addField('time','<div class="timestamp"><span aria-label="','<');
      $data['videos'] = $o->parseHTML();
      // file_put_contents('result.json',json_encode($data));
    }


    //
    // Return result as JSON
    //
    $o->json_out($data);

?>