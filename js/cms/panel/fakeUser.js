jQuery(document).ready(function () {
	var placeholder = jQuery('#u-panel-holder');
	if(placeholder.length == 0) {
		var div = document.createElement('div');
		div.id = 'u-panel-holder';
		document.body.appendChild(div);
		placeholder = jQuery('#u-panel-holder');
		
		//var str = '<div id="edit"><span class="in_ico_bg">&nbsp;</span>' + getLabel('js-panel-edit') + '</div>';
		
		var str = '	<div id="exit" title="' + getLabel('js-panel-exit') + '">&#160;</div>\
		<div id="butterfly"  style="cursor: auto;"><span class="in_ico_bg">&nbsp;</span>' + getLabel('js-panel-act-as-user') + FAKE_USER.user_name + '</div>';
		
		if (FAKE_USER.order_name) str += '<div id="note" style="cursor: auto;"><span class="in_ico_bg">&nbsp;</span>' + getLabel('js-panel-edited-order') + FAKE_USER.order_name + '</div>';
		
		str += '<div id="edit" style="font-weight: normal;"><span class="in_ico_bg">&nbsp;</span>' + getLabel('js-panel-switch') +'</div>';
		
		placeholder.html('<div id="u-show_hide_btn" /><div id="u-quickpanel">' + str + '</div>');
		
	}
	
	if (!jQuery.cookie('eip-panel-state-first')) {
        //function collapse without animation
        var quickpanel = jQuery("#u-quickpanel");
        quickpanel.css('overflow', 'hidden');
        quickpanel.css('height', '0');
        jQuery('#u-show_hide_btn').addClass('collapse');
        //function expand with delay
        var quickpanel = jQuery("#u-quickpanel");
        quickpanel.delay(500).animate({
            height: "25px"
        }, 500, function(){
            jQuery(this).css('overflow', 'visible');
            jQuery('#u-show_hide_btn').removeClass('collapse');
        });
        quickpanel.fadeTo(300, 0.3);
        quickpanel.fadeTo(300, 1);
        jQuery.cookie('eip-panel-state', '', {
            path: '/',
            expires: 0
        });
        // set first expand cookie
        var date = new Date();
        date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
        jQuery.cookie('eip-panel-state-first', 'Y', {
            path: '/',
            expires: date
        });
    }
	
	jQuery('html').addClass('u-eip');
	
	jQuery('#u-quickpanel #exit').click(function  () {
		window.location = '/users/logout/';
		return false;
	});
	
	jQuery('#u-quickpanel #edit').click(function  () {
		window.location = '/users/restoreUser/';
		return false;
	});
	
	jQuery('#u-show_hide_btn').click(function () {
		var quickpanel = jQuery("#u-quickpanel");
		var quickpanel_height = quickpanel.css("height");
	    if (quickpanel_height == "0px") {
		    quickpanel.css('overflow', 'visible');
		    quickpanel.animate({height:"25px"}, 700);
		    jQuery.cookie('eip-panel-state', '', { path: '/', expires: 0});
	    } else {
			quickpanel.css('overflow', 'hidden');
			quickpanel.animate({height:"0"}, 700);
			var date = new Date();
			date.setTime(date.getTime() + (30 * 24 * 60 * 60 * 1000));
			jQuery.cookie('eip-panel-state', 'collapsed', { path: '/', expires: date});
		}
		jQuery(this).toggleClass("collapse");
	});

});
