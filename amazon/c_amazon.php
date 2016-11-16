<?php
    /*
        Author: DevRoof Team
        Example: Amazon Book Search
        Description: Search a book on Amazon using Parser Broker API.
    */    
 
    //
    // Add Parser Broker Class
    //
    require_once('../__include/class/class_parserbroker.php');

    //
    // Receive search parameter
    //
    $s = @$_REQUEST['s'];
    if (empty($s)) $s = "API";

    //
    // Define Parser Parameters
    //
    $token = '<li id="result_';
    $url = "https://www.amazon.com/s/ref=nb_sb_noss_2?url=search-alias%3Dstripbooks&field-keywords=" . urlencode($s);

    //
    // Create Parser Object
    //
    $o = new HTMLListBroker($url,$token);
    $o->addField('title','a-text-normal" title="','"');
    $o->addField('author','by </span><span class="a-size-small a-color-secondary">','<');
    $o->addField('cover','"><img src="','"');
    $o->addField('link','a-text-normal" href="','"');

    //
    // Request info from Parser API server
    //
    $ret = $o->parseHTML();

    if(!empty(@$ret[0]['title'])) {
        file_put_contents('result.json',json_encode($ret));
    }

    //
    // Return result as JSON
    //
    $o->json_out($ret);
?>
