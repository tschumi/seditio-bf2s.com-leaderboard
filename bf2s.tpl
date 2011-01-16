<!-- BEGIN: MAIN -->

<div id="title">

	{BF2S_TITLE}

</div>

<div id="subtitle">

	{BF2S_SUBTITLE}

</div>

<div id="main">

<script type="text/javascript">
/* Limit Visitors' Choices
 * http://www.netmechanic.com/news/vol7/javascript_no9.htm
 * by Larisa Thomason,
 * NetMechanic, Inc.
 * Modified by NM156, Oct 2005
 *  added url redirect
 *  enable/disable redirect button when maxItems == true/false
 *  added checkbox disable when maxItems == true
 * Modified by riptide, June 2006
 *  to fit the new compare mode of bf2s.com i made the following changes:
 *   - removed all url redirect things
 *   - modified the script to accept up to 8 checkboxes
 */

  function setItems_init() {
      document.compare.button.disabled = true;
      var freeItems = 0;
  }

  var freeItems = 0;
  var maxItems = 8;

  function setItems(item) {
      if(item.checked) {
          freeItems += 1;
          if (freeItems == maxItems) {
              chkbox = document.getElementsByTagName("input");
              for (c=0;c<chkbox.length;c++) {
                  thisChkbox = chkbox[c];
                  if(thisChkbox.type == 'checkbox') {
                      if ( thisChkbox.checked == false ) {
                        thisChkbox.disabled = true;
                      }
                  }
              }
              document.compare.button.disabled = false;
          } else if (freeItems <= 1) {
              document.compare.button.disabled = true;
          } else if (freeItems >= 2) {
              document.compare.button.disabled = false;
           }
      } else {
          chkbox = document.getElementsByTagName("input");
          for (c=0;c<chkbox.length;c++) {
              thisChkbox = chkbox[c];
              if(thisChkbox.type == 'checkbox') {
                  if ( thisChkbox.checked != true ) {
                    thisChkbox.disabled = false;
                  }
              }
          }
          freeItems -= 1;
          if (freeItems < 2) {
          	document.compare.button.disabled = true;
          }
      }
      if (freeItems > maxItems) {
          item.checked = false;
          freeItems -= 1;
          alert('You\'re only allowed to compare '+maxItems+' players at a time!');
      }
  }
</script>

<!-- BEGIN: BF2S_CONGRATS -->
<div class="block">
<p style="text-align:left;">{BF2S_CONGRATS}:</p>
<p style="text-align:left;">{BF2S_CONGRATSTO}</p>
</div>
<!-- END: BF2S_CONGRATS -->

<form action="http://bf2s.com/compare.php" name="compare" method="get">

<table class="cells">

	<tr>
		<td colspan="11" style="text-align:left;background:#FFFFFF;">{BF2S_COMPAREBUTTON}&nbsp;&nbsp;{BF2S_COMPARETEXT}</td>
	</tr>

	<div style="display:inline;">
	<tr>
		<th class="coltop"><img src="plugins/bf2s/img/compare.gif" alt=""/></td>
		<th class="coltop">{BF2S_COUNTRY}</td>
		<th class="coltop">{BF2S_RANK}</td>
		<th class="coltop" style="text-align:left;">{BF2S_NICK}</td>
		<th class="coltop" style="text-align:left;">{BF2S_SCORE}</td>
		<th class="coltop" style="text-align:left;">{BF2S_SPM}</td>
		<th class="coltop" style="text-align:left;">{BF2S_KDR}</td>
		<th class="coltop" style="text-align:left;">{BF2S_WLR}</td>
		<th class="coltop" style="text-align:left;">{BF2S_TIME}</td>
		<!-- BEGIN: BF2S_STATUS -->
		<th class="coltop">{BF2S_STATUS}</td>
		<!-- END: BF2S_STATUS -->
		<th class="coltop" style="text-align:left;">{BF2S_NEXTRANK}</td>
	</tr>
	</div>

	<!-- BEGIN: BF2S_ROW -->
	<tr>
		<td>{BF2S_ROW_CHECKBOX}</td>
		<td style="text-align:center;"><img src="system/img/flags/{BF2S_ROW_COUNTRY}" align="middle" alt="" /></td>
		<td style="text-align:center;"><img src="plugins/bf2s/img/{BF2S_ROW_RANK}.gif" align="middle" alt="{BF2S_ROW_RANKDESC}"></td>
		<td><a href="{BF2S_ROW_LINK}">{BF2S_ROW_NICK}</a></td>
		<td>{BF2S_ROW_SCORE}</td>
		<td>{BF2S_ROW_SPM}</td>
		<td>{BF2S_ROW_KDR}</td>
		<td>{BF2S_ROW_WLR}</td>
		<td>{BF2S_ROW_TIME}</td>
		<!-- BEGIN: STATUS -->
		<td style="text-align:center;">{BF2S_ROW_STATUS}</td>
		<!-- END: STATUS -->
		<td><div style="width:200px;"><div class="bar_back"><div class="bar_front" style="width:{BF2S_ROW_PERCENTBAR}%;font-size:85%;">&nbsp;&nbsp;{BF2S_ROW_PERCENT}%</div></div></div></td>
	</tr>
	<!-- END: BF2S_ROW -->

</table>

</form>

<div style="font-style:italic;font-size:90%;float:left;padding:10px 0 0 0;">

	{BF2S_SPM_EXPLANATION}<br />{BF2S_KDR_EXPLANATION}<br />{BF2S_WLR_EXPLANATION}
	
</div>

<div style="text-align:right;font-size:90%;float:right;padding:10px 3px 0 0;">

	{BF2S_LASTUPDATE}: {BF2S_CACHEAGE}<br />
	{BF2S_NEXTUPDATE}: {BF2S_NEXTREFRESH}<br />
	<br />
	Stats powered by <a href="http://www.bf2s.com/">BF2s.com</a>, watch their <a href="{BF2S_BF2SLEADERBOARD}">official leaderboard</a>
    <!-- BEGIN: BF2S_STATUSPOWEREDBY -->
    <br />{BF2S_STATUSPOWEREDBY}
    <!-- END: BF2S_STATUSPOWEREDBY -->
	
</div>

<div style="clear:both;">&nbsp;</div>

</div>

<!-- END: MAIN -->
