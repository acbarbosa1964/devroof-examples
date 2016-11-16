<?php
    $s = @$_REQUEST['s'];
    if(empty($s)) $s = "PHP_EXAMPLE";
    switch ($s) {
      case "PHP_EXAMPLE":
        $source = file_get_contents("c_booking.php");
        $mode = "ace/mode/php";
        break;
      case "HTML":
        $source = file_get_contents("index.html");
        $mode = "ace/mode/html";
        break;
      case "CSS":
        $source = file_get_contents("../__assets/css/main.css");
        $mode = "ace/mode/css";
        break;
      case "JS":
        $source = file_get_contents("js/booking.js");
        $mode = "ace/mode/javascript";
        break;
      
      default:
        $source = null;
        break;
    }
    header("Content-Type: application/json");
    echo json_encode(array("mode" =>$mode , "source" => $source));
?>