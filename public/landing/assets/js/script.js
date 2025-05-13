/*
Author       : Dreamguys
Template Name: Dreams Rent - Bootstrap Template
Version      : 1.0
*/

(function($) {
    "use strict";
	
	// Close Ad
	$(".close-ad").click(function(){
		$(".top-header").fadeOut();
	  });

	  // Sticky Header
	
	$(window).scroll(function () {
		var sticky = $('.header'),
			scroll = $(window).scrollTop();
		if (scroll >= 50) sticky.addClass('fixed');
		else sticky.removeClass('fixed');
	});
	
	// Mobile menu sidebar overlay
	
	$('.header-fixed').append('<div class="sidebar-overlay"></div>');
	$(document).on('click', '#mobile_btn', function() {
		$('main-wrapper').toggleClass('slide-nav');
		$('.sidebar-overlay').toggleClass('opened');
		$('html').addClass('menu-opened');
		return false;
	});
	
	$(document).on('click', '.sidebar-overlay', function() {
		$('html').removeClass('menu-opened');
		$(this).removeClass('opened');
		$('main-wrapper').removeClass('slide-nav');
	});
	
	$(document).on('click', '#menu_close', function() {
		$('html').removeClass('menu-opened');
		$('.sidebar-overlay').removeClass('opened');
		$('main-wrapper').removeClass('slide-nav');
	});

	// tooltip
	
	$(document).ready(function(){
		$('[data-bs-toggle="tooltip"]').tooltip();   
	});		
	
	// Fade in scroll

	if($('.main-wrapper .aos').length > 0) {
	    AOS.init({
		  duration: 1200,
		  once: true,
		});
	}

	// Smooth Scrool

	$(".main-nav li a[href^='#']").on('click', function(e) {	
		e.preventDefault();
		var hash = this.hash;
		var header_height = $('header').outerHeight();
		 $('html, body').animate( { scrollTop: $(hash).offset().top - header_height}, {duration: 500 } ); 
	 });

	// Top Scroll 
	
	$(document).ready(function(){ 
		$(window).scroll(function(){ 
			if ($(this).scrollTop() > 100) { 
				$('#scroll').fadeIn(); 
			} else { 
				$('#scroll').fadeOut(); 
			} 
		}); 
		$('#scroll').click(function(){ 
			$("html, body").animate({ scrollTop: 0 }, 600); 
			return false; 
		}); 
	});
	
	
})(jQuery);