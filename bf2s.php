<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/bf2s/bf2s.php
Version=100
Updated=2006-jun-06
Type=Plugin
Author=riptide
Description=Brings the leaderboard XML feed of BF2s.com to your website
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=bf2s
Part=plugin
File=bf2s
Hooks=standalone
Order=10
Tags=
[END_SED_EXTPLUGIN]

==================== */

if ( !defined('SED_CODE') || !defined('SED_PLUG') ) { die("Wrong URL."); }

require('inc/bf2s-mlb.php');

$sort = sed_import('sort','G','TXT');
$way = sed_import('way','G','TXT');

$sort = ($sort == "") ? "score" : $sort;
$way = ($way == "") ? "desc" : $way;

function bf2s_sec2log($seconds)
	{
    $h = floor($seconds / 3600);
    $m = floor(($seconds % 3600) / 60);
    $s = floor(($seconds % 3600) % 60);
    $h=sprintf("%02d", $h);
    $m=sprintf("%02d", $m);
    $s=sprintf("%02d", $s);
    return "{$h}:{$m}:{$s}";
    }

function bf2s_rankdesc($rank)
	{
    $ranks = array(
         "0" => "Private",
         "1" => "Private First Class",
         "2" => "Lance Corporal",
         "3" => "Corporal",
         "4" => "Sergeant",
         "5" => "Staff Sergeant",
         "6" => "Gunnery Sergeant",
         "7" => "Master Sergeant",
         "8" => "First Sergeant",
         "9" => "Master Gunnery Sergeant",
        "10" => "Sergeant Major",
        "11" => "Sergeant Major of the Corps",
        "12" => "2nd Lieutenant",
        "13" => "1st Lieutenant",
        "14" => "Captain",
        "15" => "Major",
        "16" => "Lieutenant Colonel",
        "17" => "Colonel",
        "18" => "Brigadier General",
        "19" => "Major General",
        "20" => "Lieutenant General",
        "21" => "General",
        );
    return $ranks[$rank];
    }

function bf2s_ranks($rank, $which = '')
	{
	$rank = ($which == "next") ? $rank + 1 : $rank;

    $ranks = array(
         "0" => "0",
         "1" => "150",
         "2" => "500",
         "3" => "800",
         "4" => "2500",
         "5" => "5000",
         "6" => "8000",
         "7" => "20000",
         "8" => "20000",
         "9" => "50000",
        "10" => "50000",
        "11" => "50000",
        "12" => "60000",
        "13" => "75000",
        "14" => "90000",
        "15" => "115000",
        "16" => "125000",
        "17" => "150000",
        "18" => "180000",
        "19" => "180000",
        "20" => "200000",
        "21" => "200000",
        "22" => "200000",
        );
    return $ranks[$rank];
    }

function bf2s_arrow($sort)
	{
	global $usr;

	$arrow  = "<a href=\"plug.php?e=bf2s&sort=".$sort."&way=asc\"><img src=\"skins/".$usr['skin']."/img/system/arrow-down.gif\" /></a>";
	$arrow .= "<a href=\"plug.php?e=bf2s&sort=".$sort."&way=desc\"><img src=\"skins/".$usr['skin']."/img/system/arrow-up.gif\" /></a>";

	return $arrow;
	}

$mlb->get($cfg['plugin']['bf2s']['players']);

$row = $mlb->getList($sort, $way);

//refresh page again if it would display a wrong date due fresh import of the feed (cosmetic only)
if ($mlb->cacheage == '' OR $mlb->nextrefresh == '')
	{
	header("Location: plug.php?e=bf2s&sort=".$sort."&way=".$way."");
	exit;
	}

if ($cfg['plugin']['bf2s']['bf2trackerclanid'] != "")
	{
	require('inc/bf2tracker.php');
	$playerstats = get_bf2tracker_data($cfg['plugin']['bf2s']['bf2trackerclanid']);
	}

for ($i = 0; $i < count($row); $i++)
    {
    if ($cfg['plugin']['bf2s']['percentmode'] == "BF2S.com mode")
    	{
    	$percentcalc = @round(100*($row[$i][SCORE])/(bf2s_ranks($row[$i][RANK],"next")),2);
    	}
    else
    	{
    	$percentcalc = @round(100*($row[$i][SCORE]-bf2s_ranks($row[$i][RANK]))/(bf2s_ranks($row[$i][RANK],"next")-bf2s_ranks($row[$i][RANK])),2);
    	}

	if ($percentcalc < 100)
   		{
   		$percent = $percentcalc;
   		$percentbar = @floor($percentcalc);
   		}
	else
		{
		$percent = 100;
		$percentbar = 100;
		}

	if ($percentcalc < 10)
		{
		$congrats[htmlspecialchars($row[$i][NICK])] = bf2s_rankdesc($row[$i][RANK]);
		}
		
	if ($cfg['plugin']['bf2s']['bf2trackerclanid'] != "")
		{
		$playerstatus = ($playerstats[$row[$i][PID]][PLAYERSTATUS] != "") ? "<img src=\"plugins/bf2s/img/".$playerstats[$row[$i][PID]][PLAYERSTATUS].".png\" />" : "n/a";
		
		$t-> assign(array(
            "BF2S_ROW_STATUS" => $playerstatus,
           	));
    	$t->parse("MAIN.BF2S_ROW.STATUS");
    	}

	$playercountry = ($row[$i][COUNTRY] != "") ? "f-".strtolower($row[$i][COUNTRY]).".gif" : "f-00.gif";

    $t-> assign(array(
        "BF2S_ROW_NICK" => htmlspecialchars($row[$i][NICK]),
        "BF2S_ROW_PID" => $row[$i][PID],
        "BF2S_ROW_COUNTRY" => $playercountry,
        "BF2S_ROW_RANK" => $row[$i][RANK],
        "BF2S_ROW_RANKDESC" => bf2s_rankdesc($row[$i][RANK]),
        "BF2S_ROW_SCORE" => number_format($row[$i][SCORE]),
        "BF2S_ROW_SPM" => number_format($row[$i][SPM], 2),
        "BF2S_ROW_KILLS" => number_format($row[$i][KILLS]),
        "BF2S_ROW_DEATHS" => number_format($row[$i][DEATHS]),
        "BF2S_ROW_KDR" => number_format($row[$i][KDR], 2),
        "BF2S_ROW_WINS" => number_format($row[$i][WINS]),
        "BF2S_ROW_LOSSES" => number_format($row[$i][LOSSES]),
        "BF2S_ROW_WLR" => number_format($row[$i][WLR], 2),
        "BF2S_ROW_TIME" => bf2s_sec2log($row[$i][TIME]),
        "BF2S_ROW_LINK" => $row[$i][LINK],
        "BF2S_ROW_PERCENTBAR" => $percentbar,
        "BF2S_ROW_PERCENT" => number_format($percent,2),
        "BF2S_ROW_CHECKBOX" => "<input type=\"checkbox\" name=\"pids[]\" value=\"".$row[$i][PID]."\" onclick=\"setItems(this);\">",
    	));
    $t->parse("MAIN.BF2S_ROW");
    }

if (is_array($congrats))
	{
	$congratsto = "<ul>";
    foreach ($congrats as $key => $value)
        {
        $congratsto .= "<li>".$key." => ".$value."</li>";
        }
    $congratsto .= "</ul>";

    $t-> assign(array(
        	"BF2S_CONGRATS" => $L['plu_congrats'],
            "BF2S_CONGRATSTO" => $congratsto,
            ));
    $t->parse("MAIN.BF2S_CONGRATS");
    }

if ($cfg['plugin']['bf2s']['bf2trackerclanid'] != "")
    {
    $playerstatus = ($playerstats[$row[$i][PID]][PLAYERSTATUS] != "") ? "<img src=\"plugins/bf2s/img/".$playerstats[$row[$i][PID]][PLAYERSTATUS].".png\" />" : "n/a";
    $playercountry = ($playerstats[$row[$i][PID]][PLAYERCOUNTRY] != "") ? "f-".strtolower($playerstats[$row[$i][PID]][PLAYERCOUNTRY]).".gif" : "f-00.gif";
    
    $t-> assign(array(
        "BF2S_STATUS" => $L['plu_status'],
        "BF2S_COUNTRY" => $L['plu_country'],
        "BF2S_STATUSPOWEREDBY" => $L['plu_statuspoweredby'],
        ));
    $t->parse("MAIN.BF2S_STATUS");
    $t->parse("MAIN.BF2S_COUNTRY");
    $t->parse("MAIN.BF2S_STATUSPOWEREDBY");
    }

$t->assign(array(
    "BF2S_TITLE" => "<a href=\"plug.php?e=bf2s\">".$L['plu_title']."</a>",
    "BF2S_SUBTITLE" => $L['plu_subtitle'],
    "BF2S_CONGRATS" => $L['plu_congrats'],
    "BF2S_NICK" => $L['plu_nick']."&nbsp;".bf2s_arrow("nick"),   
    "BF2S_RANK" => $L['plu_rank'],
    "BF2S_SCORE" => $L['plu_score']."&nbsp;".bf2s_arrow("score"),
    "BF2S_SPM" => $L['plu_spm']."&nbsp;".bf2s_arrow("spm"),
    "BF2S_KILLS" => $L['plu_kills']."&nbsp;".bf2s_arrow("kills"),
    "BF2S_DEATHS" => $L['plu_deaths']."&nbsp;".bf2s_arrow("deaths"),
    "BF2S_KDR" => $L['plu_kdr']."&nbsp;".bf2s_arrow("kdr"),
    "BF2S_WINS" => $L['plu_wins']."&nbsp;".bf2s_arrow("wins"),
    "BF2S_LOSSES" => $L['plu_losses']."&nbsp;".bf2s_arrow("losses"),
    "BF2S_WLR" => $L['plu_wlr']."&nbsp;".bf2s_arrow("wlr"),
    "BF2S_TIME" => $L['plu_time']."&nbsp;".bf2s_arrow("time"),
    "BF2S_LINK" => $L['plu_link'],   
    "BF2S_NEXTRANK" => $L['plu_nextrank'],
    "BF2S_LASTUPDATE" => $L['plu_lastupdate'],
    "BF2S_NEXTUPDATE" => $L['plu_nextupdate'],
    "BF2S_CACHEAGE" => date($cfg['plugin']['bf2s']['dateformat'], $mlb->cacheage),
    "BF2S_NEXTREFRESH" => date($cfg['plugin']['bf2s']['dateformat'], $mlb->nextrefresh),
    "BF2S_BF2SLEADERBOARD" => "http://bf2s.com/my-leaderboard.php?pids=".$cfg['plugin']['bf2s']['players'],
    "BF2S_COMPAREBUTTON" => "<input type=\"submit\" name=\"button\" value=\"".$L['plu_comparebutton']."\" disabled>",
    "BF2S_COMPARETEXT" => $L['plu_comparetext'],
    "BF2S_SPM_EXPLANATION" => $L['plu_spm_explanation'],
    "BF2S_KDR_EXPLANATION" => $L['plu_kdr_explanation'],
    "BF2S_WLR_EXPLANATION" => $L['plu_wlr_explanation'],
    ));

$t->parse("MAIN.BF2S");

?>
