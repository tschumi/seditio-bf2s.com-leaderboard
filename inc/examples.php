<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>BF2S.com MyLeaderBoard Demo</title>
</head>

<body>

<h1>BF2S.com MyLeaderBoard Demo</h1>
<p>This page provides a basic demo of how the BF2S_MLB class can be used to pull a custom leaderboard from BF2S.com and display it on your own pages.</p>
<p>Check out <a href="examples.phps">the source code of this page</a> to see how rediculously easy this is. <a href="readme.html">The documentation</a> is available as well. </p>

<?php 
require('./bf2s-mlb.php'); 
$mlb->debug = true; // By default this is off, 
					// but I thought I'd keep it 
					// on for demonstration purposes.
$mlb->get('43917103,45507687,45138378,43485335,44260977,39709471'); 
?>

<h2>Default, no parameters</h2>
<?php $mlb->showList(); ?>

<h2>SPM</h2>
<?php $mlb->showList('SPM'); ?>

<h2>Time</h2>
<?php $mlb->showList('time'); ?>

<h2>As a list, by rank</h2>
<ul><?php $mlb->showList('rank','<li>{NICK}, {RANK}</li>'); ?></ul>

<h2>Plain, all fields</h2>
<p><?php $mlb->showList('score', '{NICK} - {PID} - {RANK} - {SCORE} - {SPM} - {KDR} - {WLR} - {COUNTRY} - {TIME} - {WINS} - {LOSSES} - {KILLS} - {DEATHS} - {LINK}<br />'); ?></p>

<h2>Raw Data, Baby!</h2>
<pre><?php print_r($mlb->getList('')); ?></pre>

<h2>Raw data can be pre-sorted, too -- in this case, on time</h2>
<pre><?php print_r($mlb->getList('time')); ?></pre>

<h2>Debug Info, when activated -- errors alawys show</h2>
<?php $mlb->printLog(); ?>

</body>
</html>