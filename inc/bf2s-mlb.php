<?php

/*
 * BF2S XML Leader Board API
 * You can use this class to download and output a leader board of players on your own site using any formatting you so choose.
 *
 * 		Version: 1.5
 * 		Author: Jeff Minard
 * 		URL: http://jrm.cc/extras/mlb/ & http://bf2s.com/
 *
 */

class BF2S_MLB {

	var $cachedir;
	var $has_cache;

	var $debug;

	var $myleaderboard;

	function BF2S_MLB() {
		$this->cachedir = dirname(__FILE__) . '/cache';
	}


	function log($msg, $err=false) {
		if( $this->debug == true )
			$this->mlog['t'][] = htmlspecialchars($msg);
		if($err) $this->errors .= htmlspecialchars($msg) . '<br />';
	}

	function printLog() {

		if( $this->errors )
			echo "<b>ERRORS:</b>\n $this->errors\n";

		if( $this->mlog ) {
			echo '<strong>Message Log</strong><ol>';
			foreach( $this->mlog as $type => $logSet ) {
				echo "<li>LOG[$type] (" .$this->timelog[$type]. "):<ol><li><pre>";
				echo implode("</pre></li><li><pre>", $logSet);
				echo '</pre></li></ol></li>';
			}
			echo '</ol>';
		}

	}

	/*
	 * 'Testing' Functions
	 */

	function test_cache() {
		//$this->log("test_cache()");

		if( isset($this->has_cache) )
			return $this->has_cache;

		if( is_writable( $this->cachedir ) && is_executable( $this->cachedir ) ) {
			// simplest test -- can we write to the dir?

			$this->has_cache = true;

		} else {
			// could not write. See if it even exists.

			if( file_exists($this->cachedir) ) {
				// the folder is there, but isn't writeable. damn, try to make it so

				$dir = @chmod( $this->cachedir, 0777 );
				if($dir == false) {
					// tried to chmod cache folder and failed.
					$this->log("Your cache directory (<code>" . $this->cachedir . "</code>) exists, but is not world writable (777). I couldn't make it so. Please do this by hand.",1);
					$this->has_cache = false;
				} else {
					// yay, it was chmoded
					$this->has_cache = true;
				}

			} else {
				//ack, no cache dir at all - try to make one with perms

				$dir = @mkdir( $this->cachedir, 0777);
				if($dir == false) { // tried to make cache folder and failed.
					$this->log("Your cache directory (<code>" . $this->cachedir . "</code>) needs to be created and world writable (777). I couldn't make it so. Please do this by hand.",1);
					$this->has_cache = false;
				} else {
					// hey, we did it. Made the folder, and chmoded it
					$this->has_cache = true;
				}

			}

		}

		$this->log("Cache file testing/creation complete: $this->has_cache");
		return $this->has_cache;

	}


	/*
	 * Caching Functions
	 */

	function cache_recall($func_call, $stale_age=15) {
		$this->log("cache_recall($func_call, $stale_age)");

		if( $this->test_cache() == false ) return false;

		$filename = $this->cachedir . '/'. md5($func_call) . '.txt';

		if( !file_exists($filename) ) {
			$this->log("The cache ( $filename ) does not exist.");
			return false;
		}

		if ( filemtime($filename) < strtotime("$stale_age minutes ago") ) {
			$this->log("Cache file ( $filename ) is too old.");
			return false;
		}

		$cached_content = trim(file_get_contents($filename));

		if( $cached_content == '' ) {
			$this->log("Blank cache ( $filename )");
			return false;
		}

//===============
		$refresh = (time() - strtotime("$stale_age minutes ago"));
		$nextrefresh = (strtotime("$stale_age minutes ago") - time());
                $xmlrefresh = filemtime($filename) - $nextrefresh;
                $this->cacheage = filemtime($filename);
                $this->nextrefresh = $xmlrefresh;
		//$this->log("Refresh every: ( ". (($refresh / 60) / 60 ) ." hours / Next Update: ".date("M j, Y, g:i a", $this->nextrefresh)." )");
		$this->log("Refresh every: ( ". (($refresh / 60) / 60 ) ." hours / Next Update: ". $this->nextrefresh . " )");
//===============

		$this->log("Data loaded from cache ( $filename )");
		return $cached_content;

	}

	function cache_store($func_call, $content) {
		$this->log("cache_store($func_call, content)");

		if( $this->test_cache() == false ) return false;

		$filename = $this->cachedir . '/'. md5($func_call) . '.txt';

		if (!$handle = fopen($filename, 'w')) {
			$this->log("Could no open file ( $filename ) for writing. Odd");
			return false;
		}

		if (fwrite($handle, $content) === FALSE) {
			$this->log("Write failed ( $filename ). Odd");
			return false;
		}

		fclose($handle);

		@chmod($filename, 0777);

		$this->log("Wrote cache file ( $filename ) ");
		return true;

	}


	/*
	 * URL Fetching Routine
	 * (This could use to be expanded to detect and utilize CURL since CURL roXors)
	 */

	function fgc($url) {
		// wrapper to replease file_get_contents()

		$dest = parse_url($url);
		if(!$dest['port'] && $dest['scheme'] == 'http' )
			$dest['port'] = 80;

		$fp = @fsockopen($dest['host'], $dest['port'], $errno, $errstr, 5);
		if (!$fp) {
			$this->log("FGC Error: $errstr ($errno)");
			return false;
		} else {
			stream_set_timeout($fp, 10);
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
				if( time() - $start > 10 ) break; // too many seconds passed -- the hard way, damnit.
			}
			fclose($fp);

			$this->lastFullDownload = trim(str_replace("\r", '', $res));
			$res = trim(strstr($this->lastFullDownload,"\n\n"));

		}

		return $res;
	}

	function parseXML($xml_string, &$vals, &$index) {

		$p = xml_parser_create();
		xml_parser_set_option($p, XML_OPTION_SKIP_WHITE, 1);

		$r = xml_parse_into_struct($p, $xml_string, $vals, $index);

		if( $r == 1) {
			xml_parser_free($p);
			return true;
		} else {
			$this->log("parseXML() failed: (" . xml_error_string(xml_get_error_code($p)) .') :' . $xml_string);
			xml_parser_free($p);
			return false;
		}

	}



	/*
	 * The Meat (Grabs stats from a CSV list of player IDs)
	 */

	function get($pids) {
		$this->log("get($pids)");

		if( $pids == '' ) {
			$this->log("Please pass this function a string using PID numbers separated by commas.",1);
			return false;
		}

		// get the xml from a CSV string of PIDs and pass the data back as a nice array
		$find = array(
						"\t\t\t",
						"\t\t",
						"\t",
						'   ',
						'  ',
						' '
					);

		$pids = str_replace($find, '', $pids);

		$pids = explode(',',$pids);

		if( !is_array($pids) ) {
			$this->log("I couldn't make an array out of this: \"$pids\". Please make a string using only PID numbers, separated by commas.",1);
			return false;
		}

		foreach( $pids as $pid ) if( !is_numeric($pid) ) $non_numeric = true;

		if( $non_numeric ) {
			$this->log("You may only pass PID numbers to this function.",1);
			return false;
		}

		if( !function_exists('fsockopen') ) {
			$this->log("You must have the fsockopen() function enabled to use this package. Please contact your host and have them enable it.",1);
			return false;
		}

		if( !$this->test_cache() ) {
			$this->log("Cache testing failed. You must keep a local cache of the XML data around.",1);
			return false;
		}


		$pidstring = implode(',',$pids);

		if( !$xmlitems = $this->fetchXML($pidstring) )
			return false;

		list($xml, $vals, $keys) = $xmlitems;

		foreach( $keys['PLAYER'] as $player_id ) {

			// generate inferred data
			$vals[$player_id]['attributes']['SPM'] = $vals[$player_id]['attributes']['SCORE'] / ($vals[$player_id]['attributes']['TIME'] / 60);

			if( $vals[$player_id]['attributes']['DEATHS'] != 0 )
				$vals[$player_id]['attributes']['KDR'] = $vals[$player_id]['attributes']['KILLS'] / $vals[$player_id]['attributes']['DEATHS'];
			else
				$vals[$player_id]['attributes']['KDR'] = 'na';

			if( $vals[$player_id]['attributes']['LOSSES'] != 0 )
				$vals[$player_id]['attributes']['WLR'] = $vals[$player_id]['attributes']['WINS'] / $vals[$player_id]['attributes']['LOSSES'];
			else
				$vals[$player_id]['attributes']['WLR'] = 'na';

			$data_set[] = $vals[$player_id]['attributes'];
		}

		$this->myleaderboard = $data_set;

		return true;

	}



	/*
	 * Get's the feed from BF2S utlizing local cache and doing validity tests on everything. Quite extensive.
	 */

	function fetchXML($pidstring) {

		// load from cache if it's 2 hours new
		// if not, try to pull from the server.
		// if the server chokes, try to load the cache, regardless of age.

		// at each stage check the validity of the XML (parse attempt)
		// and if it fails, move to the next step


		// get 2 hour cache
		if( $xml = $this->cache_recall($pidstring, 120) ) {
			$this->log("Fresh cache ( < 2 hour ) XML, validating");

			// validate XML
			if( $this->parseXML($xml, $vals, $keys) ) {
				$this->log("The cached XML file seems is valid");
				return array($xml, $vals, $keys);
			} else {
				$this->log("The cached XML file is INVALID");
			}

		} else {
			$this->log("No cached XML file exists (or it's too old.)");
		}


		// Still here? Get a fresh copy
		if( $raw_file = $this->fgc("http://bf2s.com/xml.php?pids=" . urlencode($pidstring)) ) {

			$this->log("Fresh XML, validating");

			$xml = trim($raw_file);

			// validate XML
			if( $this->parseXML($xml, $vals, $keys) ) {
				$this->log("The freshened XML file is valid");

				// save this new XML file
				$this->cache_store($pidstring, $xml);

				return array($xml, $vals, $keys);
			} else {
				$this->log("The freshened XML file is INVALID: $xml");
			}

		} else {
			$this->log("Could not retrieve the freshened XML. Raw file download: $this->lastFullDownload",1);
		}


		// Can't get rid of you! Use any super old cache we might have laying around
		if( $xml = $this->cache_recall($pidstring, 999999999) ) { // <-- 31 year cache? lordy...
			$this->log("Old cache ( > 2 hour ) XML, validating");

			// validate XML
			if( $this->parseXML($xml, $vals, $keys) ) {
				$this->log("The super old XML file is valid");
				return array($xml, $vals, $keys);
			} else {
				$this->log("The super old XML file is INVALID");
			}

		} else {
			$this->log("No super old XML file exists");
		}

		$this->log("Couldn't use a 2 hour cache, couldn't get a new copy, and couldn't use a cache file (regardless of age). You are S.O.L.",1);
		return false;

	}



	/*
	 * Raw function for getting a list of players sorted by value
	 */

//===============
	//function getList($by = 'score') {
	function getList($by = 'score', $way = 'desc') {
//===============
		$this->log("getList($by)");

		if( !$this->myleaderboard ) {
			$this->log("Can't make a list till you've queried for it.",1);
			return false;
		}

		if( $by == '' )
			return $this->myleaderboard;

		$by = strtoupper($by);

		$temp = $this->myleaderboard;
		$temp[0] = $temp[0];

		foreach($temp as $res)
			 $sortAux[] = $res[$by];

//===============
		$sortAux = array_map('strtolower', $sortAux);
		$way = ($way == "asc") ? SORT_ASC : SORT_DESC;
		//array_multisort($sortAux, SORT_DESC, $temp);
		array_multisort($sortAux, $way, $temp);
//===============

		return $temp;

	}



	/*
	 * "Pretty" function for outputting a sorted, replace-value list.
	 */

	function showList($sortby = 'score', $format = '<tr><td><a href="{LINK}">{NICK}</a></td><td>{SCORE}</td><td>{SPM}</td><td>{TIME}</td></tr>') {
		$this->log("showList($sortby, $format )");

		if( !$list = $this->getList($sortby) )
			return false;

		foreach( $list as $player ) {
			$forecho = $format;
			foreach( array_keys($player) as $key ) {

				$value = $player[$key];

				switch ($key) {
					case 'SPM':
					case 'KDR':
					case 'WLR':
						$value = number_format($value, 2);
						break;
					case 'SCORE':
					case 'KILLS':
					case 'DEATHS':
					case 'WINS':
					case 'LOSSES':
						$value = number_format($value);
						break;
					case 'NICK':
						$value = htmlspecialchars($value);
						break;
					case 'TIME':
						$value = $this->dateFormat($value);
						break;
				}

				$forecho = str_replace('{' . $key . '}', $value, $forecho );

			}
			$out .= $forecho;
		}

		if( $format == '<tr><td><a href="{LINK}">{NICK}</a></td><td>{SCORE}</td><td>{SPM}</td><td>{TIME}</td></tr>' ) {
			// default, add the table tags to be nice.
			$out = "<table><th>Player</th><th>Score</th><th>SPM</th><th>Time</th></tr>" . $out . "</table>";
		}

		echo $out;

	}


	/*
	 * Moved date formatting to a separate function so it can easily be overwritten by another class.
	 */

	function dateFormat($seconds) {
		return number_format($seconds/3600, 2);
	}

}

$mlb = new BF2S_MLB();

?>
