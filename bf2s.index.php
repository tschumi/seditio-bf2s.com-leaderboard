<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/bf2s/bf2s.index.php
Version=100
Updated=2006-jun-06
Type=Plugin
Author=riptide
Description=Brings the leaderboard XML feed of BF2s.com to your website
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=bf2s
Part=index
File=bf2s.index
Hooks=index.tags
Order=10
Tags=index.tpl:{PLUGIN_BF2S}
[END_SED_EXTPLUGIN]

==================== */

if ( !defined('SED_CODE') ) { die("Wrong URL."); }

require('inc/bf2s-mlb.php');
require("lang/bf2s.".$usr['lang'].".lang.php");

$mlb->get($cfg['plugin']['bf2s']['players']);

$row = $mlb->getList('');

$bf2s_index  = "<table class=\"cells\">";
$bf2s_index .=	"<tr>";
$bf2s_index .=	"<td class=\"coltop\" style=\"width:15px;\">".$L['plu_rank']."</td>";
$bf2s_index .=	"<td class=\"coltop\" style=\"text-align:left;width:60%;\">".$L['plu_nick']."</td>";
$bf2s_index .=	"<td class=\"coltop\" style=\"text-align:left;width:40%;\">".$L['plu_score']."</td>";
$bf2s_index .=	"</tr>";

$maxindex = (count($row) < $cfg['plugin']['bf2s']['maxindex']) ? count($row) : $cfg['plugin']['bf2s']['maxindex'];

for ($i = 0; $i < $maxindex; $i++)
    {
    $bf2s_index .=	"<tr>";
    $bf2s_index .=	"<td style=\"text-align:center;\"><img src=\"plugins/bf2s/img/".$row[$i][RANK].".gif\"></td>";
    $bf2s_index .=	"<td><a href=\"".$row[$i][LINK]."\">".htmlspecialchars($row[$i][NICK])."</a></td>";
    $bf2s_index .=	"<td>".number_format($row[$i][SCORE])."</td>";
    $bf2s_index .=	"</tr>";
    }

$bf2s_index .= "</table>";
$bf2s_index .= "<p style=\"text-align:right;font-size:80%;\">Stats powered by <a href=\"http://www.bf2s.com/\">BF2S.com</a>&nbsp;&nbsp;</p>";
$bf2s_index .= "<p style=\"text-align:center;font-size:90%;\"><a href=\"plug.php?e=bf2s\">".$L['plu_showallplayers']."</a></p>";

$t-> assign(array(
"PLUGIN_BF2S" => $bf2s_index,
));

?>
