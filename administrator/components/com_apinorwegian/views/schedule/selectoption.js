jQuery.noConflict();
jQuery(document).ready(function($){
    jQuery("select[name=fromdest]").change(function(){
        var from = jQuery(this).val();
        if(from=='ARN'||from=='CPH'||from=='OSL'){
            jQuery("select[name=todest]")[0].selectedIndex=0;
            jQuery("select[name=todest]").children().each(function(){
               var to = jQuery(this).val(); 
               if(to=='ARN'||to=='CPH'||to=='OSL'){
                   jQuery(this).attr("disabled", true);
               } else if(to=='RAK'||to=='AGA'){
                   jQuery(this).removeAttr("disabled");
               }
            });
        } else if(from=='RAK'||from=='AGA'){
            jQuery("select[name=todest]")[0].selectedIndex=0;
            jQuery("select[name=todest]").children().each(function(){
               var to = jQuery(this).val(); 
               if(to=='RAK'||to=='AGA'){
                   jQuery(this).attr("disabled", true);
               } else if(to=='ARN'||to=='CPH'||to=='OSL'){
                   jQuery(this).removeAttr("disabled");
               }
            });
        } else {
            jQuery("select[name=todest]").children().each(function(){
               jQuery("select[name=todest]")[0].selectedIndex=0;
               jQuery(this).removeAttr("disabled");
            });
        }    
    });
});