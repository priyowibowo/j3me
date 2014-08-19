jQuery(document).ready(function(){   
    if(jQuery("#system-message dd").length > 0){
        var h = jQuery("#system-message").height()+60;
        jQuery('#system-message').dialog({
            width: 600,
            height: h,
            draggable : false,
            resizable : false,
            modal: true
        });
    }
});