<?php
//Configure the page title and loaded scripts
	$essentials->setTitle("Audio Archive");
	$essentials->includePluginClass("Display_Manager");
	$display = new FFI\AAM\Display_Manager();
	
//Welcome splash section
	$sermons = $display->total == 1 ? "1 sermon" : $display->total . " sermons";
	$age = $display->first === 0 ? "" : " <em>This archive dates back to " . $display->first . ".</em>"; //Only works with ===, == is a tautology
	
	echo "<section id=\"splash\">
<h2>Audio Archive</h2>

<div class=\"ad-container\" data-background=\"http://localhost/wordpress/wp-content/plugins/audio-archive/app/images/splash/" . $display->background() . "\">
<div class=\"ad-contents\">
<h2>Audio Archive</h2>
</div>
</div>

<div class=\"info\">
<p><strong>" . $sermons . " on record.</strong>" . $age . "</p>
</div>
</section>

";

//Welcome splash section	
	echo "<section class=\"center content\">
<h2>2013 Sermon Recordings</h2>

<ul class=\"sermons\">
<li>
<h3>A</h3>
<p class=\"preacher\">Oliver Spryn</p>
<time datetime=\"2013-03-02\">Sunday, March 2nd, 2013</time>
<p class=\"length\">0:06</p>
<p class=\"size\">0.1 MB</p>
<a class=\"download link\" href=\"#\">Download</a>
<a class=\"link stream\" href=\"#\">Stream</a>
</li>

<li>
<h3>Made in the Image of God - Part 2</h3>
<p class=\"preacher\">Paul LaFontaine</p>
<time datetime=\"2013-02-16\">Sunday, February 16th, 2013</time>
<p class=\"length\">1:22:49</p>
<p class=\"size\">19.9 MB</p>
<a class=\"download link\" href=\"#\">Download</a>
<a class=\"link stream\" href=\"#\">Stream</a>
</li>

<li>
<a href=\"#\">
<h3>Church Order</h3>
<p class=\"preacher\">Paul LaFontaine</p>
<time datetime=\"2013-02-16\">Sunday, February 16th, 2013</time>
<p class=\"length\">2:20:48</p>
<p class=\"size\">33.8 MB</p>
</a>
<a class=\"download link\" href=\"#\">Download</a>
<a class=\"link stream\" href=\"#areno\">Stream</a>
</li>

<li>
<h3>Called, Created for a Purpose</h3>
<p class=\"preacher\">Richard Hyatt</p>
<time datetime=\"2013-02-16\">Sunday, February 16th, 2013</time>
<p class=\"length\">1:10:14</p>
<p class=\"size\">33.7 MB</p>
<a class=\"download link\" href=\"#\">Download</a>
<a class=\"link stream\" href=\"#\">Stream</a>
</li>
</ul>
</section>

";
?>