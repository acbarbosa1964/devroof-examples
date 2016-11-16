<?php

// require_once('../../libs.php');
@define("SERVER_BROKER","https://broker.devroof.com/");
set_time_limit(0);

class HTMLListBroker {
    private $url;
    private $token;
    private $map;
    private $parser_broker;
    private $parser_model;
    //
    // Constructor
    //
    public function __construct($url, $token, $list = true) {
        $this->url = $url;
        if (filter_var($this->url, FILTER_VALIDATE_URL) === false) {
            $this->url = "";
        }
        $this->map = array(
            "url" => $url,
            "line_break" => $token,
            "list" => $list
        );
        $this->token         = $token;
        $this->parser_broker = SERVER_BROKER . 'parserbroker.php';
        $this->parser_model  = SERVER_BROKER . 'parserbrokermodel.php';
    }
    
    //
    // Public Methods
    //
    public function addField($field_name, $begin, $end, $type = "C", $prefix = null, $sufix = null, $token_begin = null, $token_end = null) {
        $this->map['fields'][] = array(
            "field_name" => $field_name,
            "begin" => $begin,
            "end" => $end,
            "type" => $type,
            "prefix" => $prefix,
            "sufix" => $sufix,
            "token_begin" => $token_begin,
            "token_end" => $token_end
        );
    }
    
    public function addNextPages($max_pages, $token, $break, $break_end = '"', $begin = null, $end = null) {
        $this->map['next_pages']['max']       = $max_pages;
        $this->map['next_pages']['token']     = $token;
        $this->map['next_pages']['break']     = $break;
        $this->map['next_pages']['break_end'] = $break_end;
        $this->map['next_pages']['begin']     = $begin;
        $this->map['next_pages']['end']       = $end;
    }
    
    public function parseHTML() {
        $j   = json_encode($this->map);
        $ret = json_decode($this->send_json($j), true);
        return $ret;
    }
    
    public function file_get_json_page() {
        $j   = json_encode($this->map);
        $ret = json_decode($this->send_json($j, false), true);
        return $ret;
    }
    
    private function send_json($jsonDataEncoded, $list = true) {
        $ch = curl_init($this->parser_broker);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function json_out($j) {
        if (is_array($j))
            $j = json_encode($j);
        header('Content-Type: application/json');
        echo $j;
        die();
    }
    
    public function parserModel() {
        if ($this->map['list'] == true) {
            $ret = file_get_contents($this->parser_model . '?u=' . urlencode($this->map['url']) . '&lb=' . urlencode($this->map['line_break']));
        } else {
            $ret = file_get_contents($this->parser_model . '?u=' . urlencode($this->map['url']) . '&lb=<HTML>');
        }
        return $ret;
    }
}
?>