/**
 * Splash section controller class
 *
 * This class controls all of the essential features and logic of the
 * splash section on the public webpages of this plugin. This class 
 * simply preloads the background image and transitions it images into 
 * view.
 *
 * @author    Oliver Spryn
 * @copyright Copyright (c) 2013 and Onwards, ForwardFour Innovations
 * @license   MIT
 * @package   scripts
 * @since     v1.0 Dev
*/

function Splash() {
//Frequently references jQuery objects
	this.adContainer = $('section#splash').children('div.ad-container');
	
//Start up the splash display
	var reference = this;
	
	setTimeout(function() {
		reference.init();
	}, 1000);
}

/**
 * Preload the splash background image and transition the background
 * image into view. This method will also store the URLs of all of 
 * the background images for future usage.
 *
 * @access public
 * @return void
 * @since  v1.0 Dev
*/

Splash.prototype.init = function() {
	var reference = this;
	var img = new Image();
	img.src = this.adContainer.attr('data-background');
	
//Preload the image
	img.onload = function() {
		reference.adContainer.css('background-image', 'url(' + img.src + ')').children('div').addClass('show-background');
	};
};

$(function() {
	new Splash();
});