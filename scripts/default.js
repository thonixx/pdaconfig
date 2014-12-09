$(document).ready(function(){
	// Slidedown effect by possible warning
	$("#warning div").hide();
	$("#warning").hide().slideDown({duration: 1000, easing: "easeOutElastic"});
	window.setTimeout(function(){
		$("#warning div").fadeIn(500);
			}, 1);
	
	// Slideup after a defined time
	window.setTimeout(function(){
		$("#warning").slideUp({duration: 500, easing: "easeInExpo"});
			}, 6000);
	window.setTimeout(function(){
		$("#warning div").fadeOut(600);
			}, 5800);
	
	// rest of the effects are following here
		});
