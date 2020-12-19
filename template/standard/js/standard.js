/*===============================================*\
|| ############################################# ||
|| # JAKWEB.CH / Version 3.5.1                 # ||
|| # ----------------------------------------- # ||
|| # Copyright 2020 JAKWEB All Rights Reserved # ||
|| ############################################# ||
\*===============================================*/

// Navigation Animation and scroll to top
$(function(){
    var num = 100; //number of pixels before modifying styles
    if ($(window).scrollTop() > num) {
        $('.scrollToTop').fadeIn();
    }

    $(window).bind('scroll', function () {
        if ($(window).scrollTop() > num) {
            $('.scrollToTop').fadeIn();
        } else {
            $('.scrollToTop').fadeOut();
        }
    });
});

$(document).ready( function () {

	// Click event to scroll to top
	$('.scrollToTop').click(function(){
	    $('html, body').animate({scrollTop : 0}, 500);
	    return false;
	});

});