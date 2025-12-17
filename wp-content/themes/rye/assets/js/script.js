$(document).ready(function() {
	
	// on resize
	var resizeTimer;
	$(window).resize(function() {
	    clearTimeout(resizeTimer);
	    resizeTimer = setTimeout(toggleMobileMenu, 200);
	});
	
	
	// clicking on a question on the FAQs page
	$('.question >a').click (function(e) {
		e.preventDefault();
		var answer = $(this).parent('div.question').siblings('.answer');
		
		if (!$(this).hasClass('active')) {
			$(this).addClass ('active');
			answer.slideDown();
		}
		else {
			$(this).removeClass ('active');
			answer.slideUp();
		}
	});
	
	// a link in one of the answers is an anchor to another answer
	//  so we are expanding that answer upon click of that link (this is a very exceptional case)
	$('.answer a#link_hints').click (function (e) {
		$('#question-general_hints').children ('a').addClass ('active');
		$('div#answer-general_hints').slideDown();
	});
	
	// click on Expand All
	$('#expand-all').click ( function (e) {
		e.preventDefault();
		$('.question').each ( function() {
			var link = $(this).children('a');
			var answer = $(this).siblings('.answer');
			link.addClass ('active');
			answer.slideDown();
		});
	});
	// click on Collalse All
	$('#collapse-all').click ( function (e) {
		e.preventDefault();
		$('.question').each ( function() {
			var link = $(this).children('a');
			var answer = $(this).siblings('.answer');
			link.removeClass ('active');
			answer.slideUp();
		});
	});
	
	
	// clear out any previous PDF links in the Previous Years button
	$('#previous_years_button').attr ('href', '#');
	$('select.previous_years').val ('');
	$(document).unbind('click.fb-start');
	
	$('select.previous_years').on ('change', function (e) {
		var link = $(this).val();
		if (link == '') {
			$('#previous_years_button').attr ('href', '#');
			$(document).unbind('click.fb-start');
		}
		else $('#previous_years_button').attr ('href', link);
	});
	
	
	$('#previous_years_button').click ( function (e) {
		
		if ($(this).attr ('href') == '#') {
			e.preventDefault();
			alert ('Please select year');
		}
		else {
			if (strpos ($(this).attr ('href'), '?filter') === false) {
				e.preventDefault();
				
				/* Previous Year PDF fancybox trigger */
				$(".fancypdf").fancybox({
					type: 'iframe',		
					iframe : {
				        preload: false
				    },
				    helpers: {
			        	overlay: {
				          locked: false
				        }
				    },
				    beforeShow: function(){
				        $("body").css({'overflow-y':'hidden'});
				    },
				    afterClose: function(){
				        $("body").css({'overflow-y':'visible'});
				    }
				});
			}
			else {
				window.location ('http://folkseminarplovdiv.net' + $(this).attr ('href'));
			}
		}
	});
	
	
	
	// Audio for Program page
	audiojs.events.ready(function() {
		var as = audiojs.createAll();
	});
	
	
	
	$('.sound-bites-item.fancybox').fancybox({
		width: '70%',
        height: '70%',
        autoSize: false,
        closeClick: false,
        openEffect: 'none',
        closeEffect: 'none',
        afterLoad: function (current, previous) {
        	$('.audiojs.playing').removeClass ('playing').find('.play-pause').trigger ('click');
        },
        helpers: {
        	overlay: {
	          locked: false
	        }
	    },
	    beforeShow: function(){
	        $("body").css({'overflow-y':'hidden'});
	    },
	    afterClose: function(){
	        $("body").css({'overflow-y':'visible'});
	    }
	});
		
	$('.person-wrapper.fancybox').fancybox({
		width: '70%',
        height: '70%',
        autoSize: false,
        closeClick: false,
        openEffect: 'none',
        closeEffect: 'none',
        helpers: {
        	overlay: {
	          locked: false
	        }
	    },
	    beforeShow: function(){
	        $("body").css({'overflow-y':'hidden'});
	    },
	    afterClose: function(){
	        $("body").css({'overflow-y':'visible'});
	    }
	});
	
	
	$('.share-wrapper a').click ( function (e) {
		e.preventDefault();
		
		var $this = $(this);
		$this.siblings('.addthis_sharing_toolbox').find('.at-share-btn.at-svc-compact .at4-icon').trigger('click');
	});
	
	
	var $homepage_slider = $('.slider-wrapper');

	/* initiate slider */

	$homepage_slider.cycle({ 
		speed: 1300,
		timeout: 7000,
	    fx: 'scrollHorz',
	    slides: '.slide',
	    swipe: true,
	    loop : 0,
	    pager : '.cycle-pager',
	    prev : '.arrow-prev',
	    next : '.arrow-next'
	});
	
	$('.frm_submit.clear .submit-button, .frm_submit input[type="reset"]').click ( function (e) {
		e.preventDefault();
		
		clearForm($(this).parents ('form').attr ('id'));
	});
	
	
	$('#register-form').on ('submit', function (e) {

		var $form = $(this);
		var errors = validateForm('register-form');
		
		// clear the password mismatch notice
		$('.error-password-mismatch').hide();
		$('.error-math-incorrect').hide();
		
		if (errors.length) {
			
			e.preventDefault();
			
			for (var index in errors) {
				var element_name = errors [index];
				
				if (element_name != 'ckb_class[]') {
					$form.find ('.required').each ( function () {
						if (element_name == $(this).attr ('name')) {
							$(this).parents ('.input-row').addClass ('error');
							return false;
						}
					});
					if ($('#' + element_name).length) {
						$('#' + element_name).parent ('.radioboxes').addClass ('error');
					}
				}
				else {
					// no class selected
					$('.classes-wrapper').addClass ('error');
				}
				
			}
						
			// check for mismatching email
			if (errors.indexOf('email_mismatch') != -1) {
				$('.error-password-mismatch').show();
			}
			
			// check for math solution
			if (errors.indexOf('math_incorrect') != -1) {
				$('.error-math-incorrect').show();
			}
			
			$('.feedback').fadeIn(200);
		} else {
			$('#go_submit_button').addClass('disable-button');
		}
	});
	
	$('#add-form input[type="submit"]').click ( function (e) {
		$(this).addClass ('clicked')
	});
	
	$('#add-form').on ('submit', function (e) {
		
		var val = $(this).find("input.clicked").val();
		
		$(this).find ('.clicked').removeClass ('clicked');
		
		// clear the password mismatch notice
		$('.error-password-mismatch').hide();
		
		if (val == 'Continue') {
			
			var $form = $(this);
			var errors = validateForm('add-form');
			
			if (errors.length) {
				
				e.preventDefault();
				
				for(var index in errors) {
					var element_name = errors [index];
					
					if (element_name != 'ckb_class[]') {
						$form.find ('.required').each ( function () {
							if (element_name == $(this).attr ('name')) {
								$(this).parents ('.input-row').addClass ('error');
								return false;
							}
						});
						if ($('#' + element_name).length) {
							$('#' + element_name).parent ('.radioboxes').addClass ('error');
						}
						
						
					}
					else {
						// no class selected
						$('.classes-wrapper').addClass ('error');
					}
				}

				// check for mismatching email
				if (errors.indexOf('email_mismatch') != -1) {
					$('.error-password-mismatch').show();
				}
				$('.feedback').fadeIn(200);
			} else {
				$('#go_clear_button').addClass('disable-button');
			}
		}
		
	});

	$('#continue-form').on('submit', function (e) {
		$('#go_add_button').addClass('disable-button');
		$('#go_confirm_button').addClass('disable-button');
		$('#go_cancel_button').addClass('disable-button');
	});
	
	// click on Expand All
	$('#expand-all').click ( function (e) {
		e.preventDefault();
		$('.question').each ( function() {
			var link = $(this).children('a');
			var answer = $(this).siblings('.answer');
			link.addClass ('active');
			answer.slideDown();
		});
	});
	// click on Collalse All
	$('#collapse-all').click ( function (e) {
		e.preventDefault();
		$('.question').each ( function() {
			var link = $(this).children('a');
			var answer = $(this).siblings('.answer');
			link.removeClass ('active');
			answer.slideUp();
		});
	});
	
	// click on class title checkbox
	// activates the radio boxes for each class (level and bring/rent)
	$('.class-title input[type="checkbox"]').on ('change', function () {
		if ($(this).prop('checked')) {
			// we checked the checkbox
			
			$(this).parents('.class-row').find('input[type="radio"]').prop('checked', false).prop('disabled', false);
			var $level = $(this).parents('.class-row').find('.class-level');
			var $bring = $(this).parents('.class-row').find('.class-bring-rent');
			
			if ($level.find('.radioboxes').length == 1) {
				$level.find('input[type="radio"]').prop('checked', true);
			}
			if ($bring.find('.radioboxes').length == 1) {
				$bring.find('input[type="radio"]').prop('checked', true);
			}
			
		}
		else {
			// we unchecked a class name so disable the radio buttons
			$(this).parents('.class-row').find('input[type="radio"]').prop('checked', false).prop('disabled', true);
			$(this).parents ('.class-row').removeClass ('checked');
			$(this).parents ('.class-row').find('.error').removeClass ('error');
		}
	});
	
	$('.class-title input[type="radio"]').on ('change', function () {
		if ($(this).prop('checked') == true) {
			$(this).parents('.radioboxes').addClass ('checked');
		}
	});
	
	if ($('.input-row.math').length) {
		
		var ops = {
				'by' : '*',
				'to' : '+'
		}
		
		var val1 = parseInt ($('.input-row.math').find('.value1').text());
		var val2 = parseInt ($('.input-row.math').find('.value2').text());
		var op = $('.input-row.math').find('.op1').text();
		
		var solution = eval("val1 " + ops [op] + " val2");
		$('.input-row.math').attr ('data-reshenie', solution);
	}
	
	$('input[name="radio-dvd"]').on ('change', function () {
		if ($(this).val() == 'yes') {
			$('.dvd-format-wrapper').addClass ('show-me');
			$('.dvd-format-info-wrapper').addClass ('show-me');
			$('.dvd-format-wrapper').find ('input:first-child').addClass ('required');
		}
		else {
			$('.dvd-format-wrapper').removeClass ('show-me');
			$('.dvd-format-info-wrapper').removeClass ('show-me');
			$('.dvd-format-wrapper').find ('input:first-child').removeClass ('required');
			$('input[name="radio-dvd-format"]').val('');
			$('input[name="radio-dvd-format"]').prop('checked', false).attr('checked', false);
		}
		
	});
	
	$('input[name="radio-gala"]').on ('click', function () {
		var days = $('#select-broi-dni').val();
		$('.gala-vkluchena').removeClass ('selected');
		if ($('input[name="radio-gala"]:checked').val() == 'Yes') {
			$('input[name="radio-gala-option"]:first-child').addClass('required');
			$('.gala-option').addClass ('show-me');
			// if (days == 6) {
			// 	$('.gala-vkluchena.waive').addClass ('selected');
			// }
			// else
			if (days){
				$('.gala-vkluchena.add').addClass ('selected');
			}
		}
		else {
			$('.gala-option').removeClass ('show-me');
			$('input[name="radio-gala-option"]').prop('checked', false).removeClass('required');;
		}
	});
	
	$('#select-broi-dni').on ( 'change', function () {
		var selection = $(this).val();
		var gala = $('input[name="radio-gala"]:checked').val();
		$('.gala-vkluchena').removeClass ('selected');
		if (!selection) {
			$('input[name="radio-gala"]').prop('checked', false);
			$('input[name="radio-gala-option"]').prop('checked', false);
			$('.gala-option').removeClass ('show-me');
			return;
		}
		if (gala == 'Yes') {
			// if (selection == 6) {
			// 	$('.gala-vkluchena.waive').addClass ('selected');
			// }
			// else
			if (selection) {
				$('.gala-vkluchena.add').addClass ('selected');
			}
		}
	});
	
	var counter = $('.charCount');
	$('textarea[name="txt-speshnost"]').on('keyup', function () {
		var textEntered = $(this).val();
		counter.html(500 - textEntered.length);
		if (500 - textEntered.length <= 50 && !counter.hasClass ('red')) {
			counter.addClass ('red');
		}
		else if (500 - textEntered.length > 50 && counter.hasClass ('red')) {
			counter.removeClass ('red');
		}
	});
	
	/*$('#select-broi-dni').on ( 'change', function () {
		alert ($(this).val());
	});*/
	
	/*$('body').on ('click', '.fancybox-nav', function () {alert ('in here');
		$('.audiojs.playing').removeClass ('playing').find('.play-pause').trigger ('click');
	}) ;
	
	$('.fancybox-nav').click ( function () {
		alert ('fancybox');
	})*/
	
	/*if ($('.row.register').length) {
		// check to make sure cookies are enabled
		if (navigator.cookieEnabled) return;
		else {
			// disabled cookies
			// show notice and hide buttons
			$('.cookies-required-wrapper').show();
			$('.row.register .form-buttons').hide();
		}
	}*/
	
});

function toggleMobileMenu () {
	if ($(window).width() > 991) {
		$('.navbar-toggle').addClass ('collapsed');
		$('.navbar-collapse').addClass ('collapse').removeClass ('in').attr ('style', '')
	}
}

function clearForm (form_id) {
	var $form = $('#' + form_id);
	
	$form.find ('input[type="text"], input[type="tel"], input[type="email"]').each ( function () {
		$(this).val ('');		
	});
	
	$form.find ('select').each ( function () {
		var value_first = $(this).find('option:first-child').val();
		$(this).val (value_first);
	});
	
	$form.find ('input[type="radio"]').each ( function () {
		$(this).attr ('checked', false);	
		$(this).prop ('checked', false);
	});
	
	$form.find ('input[type="checkbox"]').each ( function () {
		$(this).attr ('checked', false);
		$(this).prop ('checked', false);
	});
	
	$form.find ('.error').removeClass('error');
	$('.feedback').hide(200);
	$form.find ('.checked').removeClass('checked');
	$('.error-password-mismatch').hide();
	$('.error-math-incorrect').hide();
	$form.find ('.gala-vkluchena').removeClass ('selected');
	$form.find ('.gala-option').removeClass ('show-me');
}

function validateForm (form_id) {
	
	var error_array = new Array ();
	
	var $form = $('#' + form_id);
	
	$form.find ('.error').removeClass('error');
	
	var $required = $form.find ('.required');
	
	var form_elements_required = new Array ();
	$required.each (function () {
		
		var element_name = $(this).attr ('name');
		form_elements_required.push (element_name);
		
	});
	
	for (var j in form_elements_required) {
		
		var element_name = form_elements_required [j];
		//var $element = $('input[name="' + element_name + '"]');
		
		if (element_name.indexOf ('select-') !== -1) {
			
			if ($('select[name="' + element_name + '"]').val() == '') {
				error_array.push (element_name);
			}
			
		}
		else if (element_name.indexOf ('radio-') !== -1) {
						
			var count = 0;
			$('input:radio[name=' + element_name + ']').each ( function() {
				if ($(this).is(':checked')) count++;
			});
			if (!count) {
				if (error_array.indexOf (element_name) == -1)
					error_array.push (element_name);
				
			}
		}
		else if ($('input[name="' + element_name + '"]').val() == '' ||
				$('input[name="' + element_name + '"]').val() == null) {
			
			if (error_array.indexOf (element_name) == -1)
				error_array.push (element_name);

		}
		
		
	}
	
	var $classes = $('.class-row');
	var checked = 0;
	var checked_ids = new Array ();
	$classes.each ( function () {
		if ($(this).find ('input[type="checkbox"]').prop ('checked') == true) {
			checked++;
			checked_ids.push ($(this).attr('id'))
		}
	});
	
	if (checked == 0) error_array.push ('ckb_class[]');
	else {
		// make sure rent/bring and level are indicated
		for (var i in checked_ids) {
			var checked_id = checked_ids [i];
			
			var $class_row = $('#' + checked_id);
			
			var $bring_checked = $class_row.find ('.class-bring-rent .radioboxes input').filter ( function () {
				return ($(this).prop('checked') == true);
			});
			if (!$bring_checked.length) {
				$class_row.find ('.class-bring-rent .radioboxes input').each ( function () {
					error_array.push ($(this).attr('id'));
				});
				
			}
			
			var $level_checked = $class_row.find ('.class-level .radioboxes input').filter ( function () {
				return ($(this).prop('checked') == true);
			});
			if (!$level_checked.length) {
				$class_row.find ('.class-level .radioboxes input').each ( function () {
					error_array.push ($(this).attr('id'));
				});
				
			}
			
		}
	}
	
	// check that email entered matches in both fields
	var email1 = $('input#txt-poshta').val();
	var email2 = $('input#txt-poshta2').val();
	if (email1 != email2) {
		error_array.push ('email_mismatch');
	}
	
	// check that math problem is solved correctly
	if ($('#txt-math').length) {
		var entered = $('#txt-math').val();
		
		if (parseInt (entered) != parseInt ($('.input-row.math').attr('data-reshenie'))) {
			error_array.push ('math_incorrect');
		}
	}
	
	return error_array;
}

$.fn.getType = function() { console.log (this);
	return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); 
}