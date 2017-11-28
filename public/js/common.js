/*
 * common.js
 *
 *  version --- 3.6
 *  updated --- 2011/09/06
 */


/* !stack ------------------------------------------------------------------- */
jQuery(document).ready(function($) {
	addCss();	
});

/* !Addition Fitst & Last --------------------------------------------------- */
var addCss = (function(){
	$('.section:first-child:not(:last-child)').addClass('first');
	$('.section:last-child').addClass('last');
	$('li:first-child:not(:last-child)').addClass('first');
	$('li:last-child').addClass('last');
});

//Set value from placeholder
function setValueRand(elem) {
	if ($('#'+elem).val() == '') {
		$('#'+elem).val($('#'+elem).attr('placeholder'));
	}
}