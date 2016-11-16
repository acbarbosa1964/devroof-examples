<?php

define("API_END_POINT", "http://broker.devroof.com/");

function newHTMLParser($url) {
    $ret = array(
        "url" => $url,
        "list" => false
    );
    return $ret;
}

function newHtmlListParser($url, $line_break = null) {
    $ret = array(
        "url" => $url,
        "line_break" => $line_break,
        "list" => true
    );
    return $ret;
}

function addField($o, $field_name, $begin, $end, $type = "C", $prefix = null, $sufix = null, $token_begin = null, $token_end = null) {
    $o['fields'][] = array(
        "field_name" => $field_name,
        "begin" => $begin,
        "end" => $end,
        "type" => $type,
        "prefix" => $prefix,
        "sufix" => $sufix,
        "token_begin" => $token_begin,
        "token_end" => $token_end
    );
    return $o;
}

function addNextPages($o, $max_pages, $token, $break, $break_end = '"', $begin = null, $end = null) {
    $o['next_pages']['max']       = $max_pages;
    $o['next_pages']['token']     = $token;
    $o['next_pages']['break']     = $break;
    $o['next_pages']['break_end'] = $break_end;
    $o['next_pages']['begin']     = $begin;
    $o['next_pages']['end']       = $end;
    return $o;
}

function parse_HTML($o) {
    return file_get_json($o);
}

function file_get_json($o) {
    $j    = json_encode($o);
    $data = send_json($j);
    $ret  = json_decode($data, true);
    return $ret;
}

function file_get_json_page($o) {
    $j   = json_encode($o);
    $ret = json_decode(send_json($j, false), true);
    return $ret;
}

function send_json($jsonDataEncoded, $list = true) {
    $url = API_END_POINT . 'parserbroker.php';
    $ch  = curl_init($url);
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
    //echo $result;
    //die();
    return $result;
}

function json_out($j) {
    if (is_array($j))
        $j = json_encode($j);
    header('Content-Type: application/json');
    echo $j;
    die();
}

function parserModel($o) {
    if ($o['list'] == true) {
        $url = API_END_POINT . 'parserbrokermodel.php?u=' . urlencode($o['url']) . '&lb=' . urlencode($o['line_break']);
    } else {
        $url = API_END_POINT . 'parserbrokermodel.php?u=' . urlencode($o['url']) . '&lb=<HTML>';
    }       
    // var_dump($url);
    $ret = file_get_contents($url);
    return $ret;
    //echo $ret;
    //die();
}

function parserModelUrl($url) {
    $url = API_END_POINT . 'parserbrokermodel.php?u=' . urlencode($url) . '&lb=<HTML>';
    $ret = file_get_contents($url);
    return $ret;
    //echo $ret;
    //die();
}


function headerCreate($f) {
    $bloco = "";
    if (!empty($f)) {
        foreach ($f as $d) {
            $bloco .= '<th class="dynatable-head" data-dynatable-column="' . $d['field_name'] . '"><a href="#" class="dynatable-sort-header">' . $d['field_name'] . '</a></th>';
        }
    }
    return $bloco;
}

function dump($o, $ret = null) {
    $table_header = headerCreate(@$o['fields']);
    $token        = $o['line_break'];
    $html         = parserModel($o);
    $items        = explode($token, $html);
    $item         = @$items[1];
    if (empty($item))
        $item = $html;
    if (empty($ret)) {
        $ret = file_get_json($o);
    }
    $json = json_encode($ret);
?>
    <!DOCTYPE html>

    <html lang="en">

    <head>
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0">
    	<link rel="stylesheet" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" media="screen" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/5.5.7/jsoneditor.css" />
        <link rel="stylesheet" media="all" href="https://s3.amazonaws.com/dynatable-docs-assets/css/jquery.dynatable.css" />
    	<style>
          body {
            padding: 30px;
          }
          #editor {
                  position: absolute;
                  width: 500px;
                  height: 400px;
          }
          .highlight {
              background-color:yellow;
          }
          .highlight_fieldname {
              background-color: blue;
              color: white;
          }
          .etabs { margin: 0; padding: 0; }
          .tab { display: inline-block; zoom:1; *display:inline; background: #eee; border: solid 1px #999; border-bottom: none; -moz-border-radius: 4px 4px 0 0; -webkit-border-radius: 4px 4px 0 0; }
          .tab a { font-size: 14px; line-height: 2em; display: block; padding: 0 10px; outline: none; }
          .tab a:hover { text-decoration: underline; }
          .tab.active { background: #fff; padding-top: 6px; position: relative; top: 1px; border-color: #666; }
          .tab a.active { font-weight: bold; }
          .tab-container .panel-container { background: #fff; border: solid #666 1px; padding: 10px; -moz-border-radius: 0 4px 4px 4px; -webkit-border-radius: 0 4px 4px 4px; }
          .html_source {
            	border-style: dotted;
              border-width: 1px;
              padding: 10px;
          }
    	</style>
    </head>
    <body>
    	<div class="container">
        <h1>HTML Parser Debug</h1>
        <p>
          <div>
            <strong>Target URL: </strong> <?php
    echo $o['url'];
?>
          </div>
          <div>
            <strong>Line breaker (token): </strong> <?php
    echo htmlentities($o['line_break']);
?>
          </div>
        </p>

        <div id="tab-container" class="tab-container">
          <ul class='etabs'>
            <li class='tab'><a href="#tabs1-html">JSON</a></li>
            <li class='tab'><a href="#tabs1-js">HTML Record</a></li>
            <li class='tab'><a href="#tabs1-css">Result as Table</a></li>
          </ul>
          <div id="tabs1-html">
            <div class="row">
                <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                    <div id="editor" style="width: 100%; height: 800px;"></div>
                </div>
                <div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">

                </div>
            </div>
          </div>
          <div id="tabs1-js">
            <div class="html_source">
                <hr>
                <code id="source_code">
                    <?php
    echo (htmlentities($item));
?>
                </code>
                <hr>
            </div>
          </div>
          <div id="tabs1-css">
            <div class="container">
                <p><br class="clear"></p>
                <table id="my-final-table" class="table table-bordered">
                    <thead>
                        <tr>
                            <?php
    echo $table_header;
?>
                        </tr>
                    </thead>
                    <tbody>
                </table>
            </div>
          </div>
        </div>
    	</div>

    	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jsoneditor/5.5.7/jsoneditor.js"></script>
        <script type='text/javascript' src='https://s3.amazonaws.com/dynatable-docs-assets/js/jquery.dynatable.js'></script>
        <script type='text/javascript' src='https://s3.amazonaws.com/dynatable-docs-assets/js/highcharts.js'></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-hashchange/1.3/jquery.ba-hashchange.js"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.easytabs/3.2.0/jquery.easytabs.min.js"></script>

    	<script>
        var container = document.getElementById('editor');
        var options = {
          mode: 'code',
          modes: ['code', 'form', 'text', 'tree', 'view'],
              onError: function (err) {
              alert(err.toString());
          },
          onModeChange: function (newMode, oldMode) {
              console.log('Mode switched from', oldMode, 'to', newMode);
          }
        };
        var json = <?php
    echo $json;
?>;
        var editor = new JSONEditor(container, options, json);
        function highlight(id,text,fieldname,field_end) {
            inputText = document.getElementById(id);
            var innerHTML = inputText.innerHTML;
            var index = innerHTML.indexOf(text);
            if ( index >= 0 ) {
               var bloco = innerHTML.substring(index + text.length);
               bloco = do_highlight(bloco,field_end);
               innerHTML = innerHTML.substring(0,index) +
               "<span class='highlight_fieldname'>["+fieldname+"]</span>" +
               "<span class='highlight'>" + innerHTML.substring(index,index + text.length) + "</span>" + bloco;
               inputText.innerHTML = innerHTML;
           }
        }
        function do_highlight(innerHTML,text) {
           var index = innerHTML.indexOf(text);
           if ( index >= 0 ) {
               innerHTML = innerHTML.substring(0,index) +
                           "<span class='highlight'>" + innerHTML.substring(index,index+text.length) + "</span><span class='highlight_fieldname'>[end]</span>" +
                           innerHTML.substring(index + text.length);
           }
           return innerHTML;
        }

        $(document).ready(function() {
          <?php
    foreach ($o['fields'] as $f) {
        $fn  = $f['field_name'];
        $fne = $f['end'];
        $ft  = str_replace('<', '&lt;', $f['begin']);
        $ft  = str_replace('>', '&gt;', $ft);
        $fne = str_replace('<', '&lt;', $fne);
        $fne = str_replace('>', '&gt;', $fne);
        echo "highlight('source_code','" . $ft . "','" . $fn . "','" . $fne . "');" . PHP_EOL;
    }
?>
           var $records = $('#json-records')

           $('#my-final-table').dynatable({
               dataset: {
                   records: json
               }
           });
           $('#tab-container').easytabs();
        });
    	</script>
    </body>
    </html>

    <?php
    die();
}

?>