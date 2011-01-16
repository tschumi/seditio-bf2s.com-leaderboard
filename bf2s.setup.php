<?PHP

/* ====================
Seditio - Website engine
Copyright Neocrome
http://www.neocrome.net

[BEGIN_SED]
File=plugins/bf2s/bf2s.setup.php
Version=100
Updated=2006-jun-06
Type=Plugin
Author=riptide
Description=Brings the leaderboard XML feed of BF2s.com to your website
[END_SED]

[BEGIN_SED_EXTPLUGIN]
Code=bf2s
Name=BF2S.com XML feed
Description=Brings the leaderboard XML feed of BF2s.com to your website
Version=100
Date=2006/06/06
Author=riptide
Copyright=This plugin can be used for free. The main XML feed and the API is made by Jeff Minard from BF2S.com. The XML feed for the online status and the country flags is made by BF2TRACKER.com. The last/next update and the compare thing comes from NM156, watch his leaderboard here: www.mscwar.com/bf2lb/. The idea with the percent bar comes form aTi|Sanders, watch his leaderboard here: www.ati-gaming.com/bf2stats/index.php.
Notes=Please do me a personal favour and do not remove the 'Stats powered by BF2S.com' - as a tribute to the hard work of Jeff Minard (same for BF2TRACKER.com if you use the online status and the flags).
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_SED_EXTPLUGIN]
[END_SED_EXTPLUGIN]

[BEGIN_SED_EXTPLUGIN_CONFIG]
players=01:text::46209227,43783305:Enter the PIDs of the players, comma separated, max 64 players
maxindex=02:string::5:Max players listed on index (Only the top 5 players by default)
dateformat=03:string::d.m.y H-i:Dateformat of the last/next update
percentmode=04:select:Real mode,BF2S.com mode:Real mode:Defines the way how the percent bar will be calculated ('Real mode' suggested)
bf2trackerclanid=05:string:::BF2TRACKER.com clan id to see the online/offline status (leave empty if you don't have a clanid there)
[END_SED_EXTPLUGIN_CONFIG]

==================== */

if ( !defined('SED_CODE') ) { die("Wrong URL."); }

?>
