<?php
    /*
        Author: DevRoof Team
        Example: Booking.com Search
        Description: Search Booking.com's accomodations using Parser Broker API.
    */    
 
    //
    // Add Parser Broker Class
    //
    require_once('../__include/class/class_parserbroker.php');
    require_once('../__include/parserbrokercommon.php');

    // $url = 'http://www.booking.com/searchresults.pt-br.html?aid=372769&sb=1&src=index&src_elem=sb&error_url=http%3A%2F%2Fwww.booking.com%2Findex.pt-br.html%3Faid%3D372769%3Bsid%3D40acf1dbfcd2fd6c185b74d6a9d73463%3Bsb_price_type%3Dtotal%26%3B&ss=S%C3%A3o+Paulo&checkin_monthday=26&checkin_year_month=2016-8&checkout_monthday=27&checkout_year_month=2016-8&room1=A%2CA&no_rooms=1&group_adults=2&group_children=0&ss_raw=S%C3%A3o+Paulo&dest_id=&dest_type=';
    $url = get_url();
    $token = 'sr_item sr_item_new';

    $o = new HTMLListBroker($url,$token);
    $o->addField('id','data-hotelid="','"');
    $o->addField('name','class="sr-hotel__name">','<');
    $o->addField('url','href="/hotel/','"','C','https://www.booking.com/hotel/');
    $o->addField('image','src="','"');
    $o->addField('checkin','checkin=',';');
    $o->addField('checkout','checkout=',';');
    $o->addField('class','data-class="','"');
    $o->addField('score','data-score="','"');
    $o->addField('score_text','scoreword">','<');
    $o->addField('gps','data-coords="','"');
    $o->addField('dest_id','dest_id=',';');
    $o->addField('dest_type','dest_type=',';');
    $o->addfield('price_from','><b>','<');

    //echo parserModelUrl($url);
    //die();
    
    //
    // Request info from Parser API server
    //
    $ret = $o->parseHTML();
    //$o->json_out($ret);

    //
    // Performs some sdjustments
    //
    $data = array();
    foreach($ret as $d) {
        @$d['price_from'] = str_replace('&nbsp;',' ',$d['price_from']);
        $data[] = $d;
    }
    //file_put_contents('result.json',json_encode($data));


    //
    // Return result as JSON
    //
    $o->json_out($data);

function get_url() {
    //
    // Receive search parameter
    //
    $s = @$_REQUEST['s'];
    if (empty($s)) $s = "LONDON";
    $dates = @$_REQUEST['d'];
    if (empty($d)) $d = "10/10/2017 - 10/12/2017";
    
    $ds = explode(' - ',$d);
    $d1 = @$ds[0];
    $d2 = @$ds[1];
    
    $a1 = explode('/',$d1);
    $c_m = @$a1[0];
    $c_d = @$a1[1];
    $c_y = @$a1[2];

    $a2 = explode('/',$d2);
    $o_m = @$a2[0];
    $o_d = @$a2[1];
    $o_y = @$a2[2];
    
    
    $destination = $s;
    $raw_destination = $s;
    $checkin_day = $c_d;
    $checkin_month = $c_m;
    $checkin_year = $c_y;
    $checkout_day = $o_d;
    $checkout_month = $o_m;
    $checkout_year = $o_y;
    $rooms = 1;
    $adults = 2;
    $children = 0;
    $aid = "372769";
    $url_error ="http://www.booking.com/index.pt-br.html?aid=" . $aid . ";sid=40acf1dbfcd2fd6c185b74d6a9d73463;sb_price_type=total&;";
    $url = "http://www.booking.com/searchresults.pt-br.html?aid=" . $aid .
            "&sb=1" .
            "&src=index" .
            "&src_elem=sb" .
            "&error_url=" . $url_error .
            "&ss=" . $destination .
            "&checkin_monthday=" .$checkin_day .
            "&checkin_year_month=" . $checkin_year. "-" . $checkin_month .
            "&checkout_monthday=" . $checkout_day .
            "&checkout_year_month=" . $checkout_year . "-" . $checkout_month .
            "&room1=A%2CA" .
            "&no_rooms=" . $rooms .
            "&group_adults=" . $adults .
            "&group_children=" . $children .
            "&ss_raw=" . $raw_destination .
            "&dest_id=" .
            "&dest_type=";
    return $url;
}
?>
