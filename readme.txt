Features :

This plugin brings you the leaderboard XML feed of BF2s.com to your website.

Installation :

1 : Unpack and upload the files into the folder : /plugins/bf2s/

!! Change the permissions of the directory /plugin/bf2s/inc/cache to 777 (writable). The plugin has to write a cache file, else it will not work !!

2 : Go into the administration panel, then tab "Plugins", click the name of the new plugin, and at bottom of the plugin properties, select "Install all".

3 : Then in the same page, check if this plugin require new tags in the skin files (.TPL).
If yes, then open the skin file(s) with a text editor, and add the tag(s).

4 : Some extended plugins have their own configuration entries, available by clicking the number near "Configuration" in the plugin properties, or go directly to the main configuration tab, section "Plugins".

Notes :

To save bandwidth, BF2s.com does not allow you to update your stats more often than every 2 hours. You don't have to worry about it, the script will do this automatically. You can see the last and the next refresh at the bottom of the stats.

The plugin is shipped with an english and a german language file, if you're using another language you have to translate it yourself.

Adjust the settings in the configuration panel :

- PIDs of the players, comma separated, max 64 players
- Max players listed on index (Default: 5)
- Dateformat of the last/next update (Default: d.m.y H-i)
- Real mode: Defines the way how the percent bar will be calculated (Default: Real mode)
- BF2TRACKER.com clan id to see the online/offline status (Default: empty)

If you don't know the PIDs, use the user search function of BF2s.com.

The BF2s.com XML feed does not include any online/offline status. If you want to show the online/offline status, you have to go to BF2TRACKER.com and register a clan. The clan has to include all players you wanna display on the leaderboard. Once you're done, fill in the ClanID in the BF2TRACKER.com field in the configuration.

If the shipped .tpl does not fit your skin or your needs, you could easy modify it.

After the installation the link for the plugin will be: http://www.yoursite.com/plug.php?e=bf2s

The tag to display it on the index is: {PLUGIN_BF2S}

Big thanks to :

- BF2S.com for their excelent work and of course for the genious XML feed
- BF2TRACKER.com for their cool online/offline status service

(please do me a personal favor and do not remove the link(s) back to them, it's just fair..)

Additional Downloads :

Alternative Icon set by MacBoom.

Changelog :

v100 (first version for Seditio)
- ported to Seditio
- change: Country code to display the country flag is now comming from the BF2s.com (previously form BF2tracker.com) *
- change: Added the new score values for the ranks
- change: Added the new ranks icons
- new : New tags to display *:

      - KDR (Kills/Deaths ratio): {BF2S_KDR}, {BF2S_ROW_KDR}
      - WLR (Wins/Losses ratio) : {BF2S_WLR}, {BF2S_ROW_WLR}
      - Kills : {BF2S_KILLS}, {BF2S_ROW_KILLS}
      - Deaths : {BF2S_DEATHS}, {BF2S_ROW_DEATHS}
      - Wins : {BF2S_WINS}, {BF2S_ROW_WINS}
      - Losses : {BF2S_LOSSES}, {BF2S_ROW_LOSSES}
      - Explanation of SPM : {BF2S_SPM_EXPLANATION}
      - Explanation of KDR : {BF2S_KDR_EXPLANATION}
      - Explanation of WLR : {BF2S_WLR_EXPLANATION}



- new : Now there's a "Show all players" below the table on the index
- new : Integrated the new compare mode -> now you can compare up to 8 players!
- fixed : Displays wrong date if xml feed is newly refreshed (cosmetic)

* now possible, because of the new values transmitted by the BF2S.com XML feed 