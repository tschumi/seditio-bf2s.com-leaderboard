<?php

function startTag($parser, $name, $attrs) {
 global $stack;

 $tag=array("name"=>$name,"attrs"=>$attrs);
 array_push($stack,$tag);
}

function cdata($parser, $cdata) {
 global $stack;

 $stack[count($stack)-1]['cdata'] .= $cdata;
}

function endTag($parser, $name) {
 global $stack;

 $stack[count($stack)-2]['children'][] = $stack[count($stack)-1];
 array_pop($stack);
}

function fgc_wrapper($url) {
    // wrapper to replease file_get_contents()

    $dest = parse_url($url);
    if(!$dest['port'] && $dest['scheme'] == 'http' )
        $dest['port'] = 80;

    $fp = @fsockopen($dest['host'], $dest['port'], $errno, $errstr, 5);
    if (!$fp) {
        //echo "could not open XML input";
        return false;
    } else {
        stream_set_timeout($fp, 30);
        $get = $dest['path'];
        if( $dest['query'] )
            $get .= "?" . $dest['query'];

        $out  = "GET " . $get . " HTTP/1.0\r\n";
        $out .= "Host: " . $dest['host'] . "\r\n";
        $out .= "Connection: Close\r\n\r\n";

        $start = time();

        fwrite($fp, $out);
        while (!feof($fp)) {
            $res .= fgets($fp, 2048);
            if( time() - $start > 30 ) break; // too many seconds passed -- the hard way, damnit.
        }
        fclose($fp);

        $lastFullDownload = trim(str_replace("\r", '', $res));
        $res = trim(strstr($lastFullDownload,"\n\n"));

    }

    return $res;
}

function get_bf2tracker_data($clanid) {

global $stack;

$stack = array();
$playerstats = array();

$xml_parser = xml_parser_create();
xml_set_element_handler($xml_parser, "startTag", "endTag");
xml_set_character_data_handler($xml_parser, "cdata");

$xmllink="http://bf2tracker.com/livefeed/xml_clanprofile.php?clanid=".$clanid;

$xmlfeed = fgc_wrapper($xmllink);

$data = xml_parse($xml_parser,$xmlfeed);
if(!$data) die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser)));

xml_parser_free($xml_parser);

// Get Data

 // Get Player Data
 for($i = 0; $i < sizeof($stack[0][children][2][children]); $i++) {
  for($x = 0; $x < sizeof($stack[0][children][2][children][$i][children]); $x++) {
   $valname=$stack[0][children][2][children][$i][children][$x][name];
   $value=$stack[0][children][2][children][$i][children][$x][cdata];
   if($valname=="PLAYERID") $pid=$value;
   $playerstats[$pid][$valname]=$value;
  }
 }

return $playerstats;
}

?>