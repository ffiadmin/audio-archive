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

<div class=\"ad-container\" data-background=\"" . REAL_ADDR . "app/images/splash/" . $display->background() . "\">
<div class=\"ad-contents\">
<h2>Audio Archive</h2>
</div>
</div>

<div class=\"info\">
<p><strong>" . $sermons . " on record.</strong>" . $age . "</p>
</div>
</section>

";

//Audio archive sections
	$counter = 1;
	
	while ($display->monthAvaliable()) {
		echo "<section class=\"center content" . ($counter % 2 ? "" : " even") . "\">
<h2>" . $display->getMonth() . "</h2>

<ul class=\"sermons " . $display->getColor() . "\">";
		
		while ($display->fileAvaliable()) {
			echo $display->renderItem();
		}
		
		echo "</ul>
</section>

";

		++$counter;
	}
	
//Audio archive avaliable years
	echo "<section class=\"center content" . ($counter % 2 ? "" : " even") . "\">
<h2 class=\"hidden\">Other Years</h2>

<ul class=\"years\">";

	while ($display->yearAvaliable()) {
		echo $display->renderYear();
	}

	echo "</ul>
</section>";
?>