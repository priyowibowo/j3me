jQuery.noConflict();
jQuery(document).ready(function($){
    // create the loading window and set autoOpen to false
//    jQuery("#loadingScreen").dialog({
//            autoOpen: false,	// set this to false so we can manually open it
//            dialogClass: "loadingScreenWindow",
//            closeOnEscape: false,
//            draggable: false,
//            width: 60,
//            height: 110,
//            modal: true,
//            buttons: {},
//            resizable: false,
//            open: function() {
//                    // scrollbar fix for IE
//                    jQuery('body').css('overflow','hidden');
//            },
//            close: function() {
//                    // reset overflow
//                    jQuery('body').css('overflow','auto');
//            }
//    }); // end of dialog
    
    function waitingDialog(waiting) { // I choose to allow my loading screen dialog to be customizable, you don't have to
	jQuery("#loadingScreen").html(waiting.message && '' != waiting.message ? waiting.message : '');
	jQuery("#loadingScreen").dialog('option', 'title', waiting.title && '' != waiting.title ? waiting.title : 'Loading');
	jQuery("#loadingScreen").dialog('open');
    }
    
    function closeWaitingDialog() {
        jQuery("#loadingScreen").dialog('close');
    }
        
    // validate
    jQuery("#search-button").click(function(){
        var holidayType = '';
        jQuery('input[name="holidayType"]').each(function(){
            var checked = jQuery(this).attr('checked');
            if(checked){
                holidayType = jQuery(this).val();
            }
        });
        
        var fromd = jQuery("#fromDate").val();
        var tod = jQuery("#toDate").val();
        
        if(holidayType=='A'||holidayType=='D'){
            if(fromd==''||tod==''){
                alert("Udfyld datoer.");
                return false;
            } 
        } else if(holidayType=='R'){
            if(fromd==''){
                alert("Udfyld datoer.");
                return false;
            } 
        } else if(holidayType=='I'){
            
        }
        
        waitingDialog({title: "Loading..", message: ""});
    });
    
    // Tabs
    //    jQuery('#tabs').tabs();
 
    // if adult option room is changed
    jQuery(".adult-room").live('change', function(){
        var postfixid = this.id.substr((this.id.length-1), this.id.length);
        var adult = parseInt(jQuery(this).val());
        var child = parseInt(jQuery("#child-room-"+postfixid).val());
        var baby = parseInt(jQuery("#baby-room-"+postfixid).val());
        
        if(baby==0&child==0){
            jQuery("#child-room-"+postfixid+", #baby-room-"+postfixid).each(function(){
                jQuery(this).removeAttr("disabled").children().each(function(){
                    jQuery(this).removeAttr("disabled");
                });
            });
        }
        
        if(adult==3){
            jQuery("#child-room-"+postfixid+" option:eq(0), #baby-room-"+postfixid+" option:eq(0)").attr("selected", "selected");
            jQuery("#child-room-"+postfixid+", #baby-room-"+postfixid).attr("disabled", "disabled");
        } 
    });
    
    // if child option room is changed
    jQuery(".child-room").live('change', function(){
        var postfixid = this.id.substr((this.id.length-1), this.id.length);
        var child = parseInt(jQuery(this).val());
        var baby = parseInt(jQuery("#baby-room-"+postfixid).val());
        
        jQuery("#baby-room-"+postfixid).each(function(){
            jQuery(this).removeAttr("disabled").children().each(function(){
                jQuery(this).removeAttr("disabled");
            });
        }); 
        
        if(child==2){
            jQuery("#baby-room-"+postfixid+" option:first-child").attr("selected", "selected");  
            jQuery("#baby-room-"+postfixid).attr("disabled", "disabled");  
        } else if (child==1){
            if(baby==2){
                jQuery("#baby-room-"+postfixid+" option:eq(1)").attr("selected", "selected");  
            } 
            
            jQuery("#baby-room-"+postfixid+" option:eq(2)").attr("disabled", "disabled");  
        }         
    });
    
    // if child option room is changed
    jQuery(".baby-room").live('change', function(){
        var postfixid = this.id.substr((this.id.length-1), this.id.length);
        var baby = parseInt(jQuery(this).val());
        var child = parseInt(jQuery("#child-room-"+postfixid).val());

        jQuery("#child-room-"+postfixid).each(function(){
            jQuery(this).removeAttr("disabled").children().each(function(){
                jQuery(this).removeAttr("disabled");
            });
        }); 

        if(baby==2){
            jQuery("#child-room-"+postfixid+" option:first-child").attr("selected", "selected");  
            jQuery("#child-room-"+postfixid).attr("disabled", "disabled");  
        } else if (baby==1){
            if(child==2){
                jQuery("#child-room-"+postfixid+" option:eq(1)").attr("selected", "selected");
            } 
            
            jQuery("#child-room-"+postfixid+" option:eq(2)").attr("disabled", "disabled");  
        } 
    });

    // add room dynamically
    jQuery("#add-room-hotel").click(function(){
        var lastroom = jQuery(".rooms").length;
        var newroom = lastroom+1;
        var cloned = jQuery("#room-1").clone();
        jQuery(cloned).attr('id', 'room-'+newroom).children(':first-child').text("Værelse "+newroom);
        jQuery(cloned).children('select').each(function(){
            var prefixid = this.id.substr(0, (this.id.length-1));
            jQuery(this).attr('id', prefixid+newroom);  
            jQuery(this).removeAttr("disabled", "disabled");
            jQuery(this).find('option').removeAttr("disabled", "disabled");
            
            var arrname = this.id.split("-");
            jQuery(this).attr('name', arrname[0]+arrname[1]+newroom);
            jQuery(".delete-room").remove();
            if(arrname[0]=='baby'){
                jQuery("#baby-room-"+prefixid+" option:eq(0)").attr("selected", "selected");
                jQuery(this).after("</br><a href=# class=delete-room>Fjerne Værelse</a>");
            } 
        });
        
        jQuery(cloned).insertAfter("#room-"+lastroom);
    });
    
    // delete room dynamically
    jQuery(".delete-room").live('click', function(ev){
        var lastroom = jQuery(".rooms").length;
        
        if(lastroom>1){
            ev.preventDefault();
            ev.stopPropagation();
            
            jQuery("#room-"+lastroom).remove();
            if(lastroom>2)
                jQuery("#baby-room-"+(lastroom-1)).after("</br><a href=# class=delete-room>Fjerne Værelse</a>");
        }
    });
    
    // hide hotel category
    jQuery("#fieldset-category, #fieldset-date").hide("fast");

    // Datepicker
    jQuery.datepicker.setDefaults(jQuery.datepicker.regional["da"]);
        
    // Hide/show related fields and resetting date
    jQuery('span[class^="type"]').click(function(){
        /**
         * HIDEN
         */
        
        // reset value
        jQuery("#select-category").val("");
                
        // select from span
        var selected = jQuery(this).children('input[name="holidayType"]');
        selected.attr('checked', true);
        
        if(selected.val()=='A'||selected.val()=='I'||selected.val()=='D'){
            jQuery("#todest").html("");        
            
            if(selected.val()=='I'){
                // remove default essaouira and casablanca
                jQuery("#todest").html("<option value=''>V&aelig;lg rejsem&aring;l</option><option value=RAK>Marrakech</option><option value=AGA>Agadir</option>");
            } else if(selected.val()=='D'){
                // remove default mar and agadir     
                jQuery("#todest").html("<option value=''>V&aelig;lg rejsem&aring;l</option><option value=RAK-ESS>Essaouira</option><option value=RAK>Marrakech</option>");
            } else if(selected.val()=='A'){
                // restore all                          
                jQuery("#todest").html("<option value=''>V&aelig;lg rejsem&aring;l</option><option value=RAK>Marrakech</option><option value=AGA>Agadir</option><option value=RAK-ESS>Essaouira</option><option value=RAK-CAS>Casablanca</option>");
            } 
            
            //#fieldset-roundtrip, #fieldset-combination, #fieldset-category, 
            jQuery("#fieldset-date").hide("slow");      
//            jQuery("#div-todest, #returnDate, #todest").show("slow");
            jQuery("#fromdest").val("CPH");      
            jQuery("#todest").val("");
        } else if(selected.val()=='R'){           
            jQuery("#fieldset-roundtrip").show("slow");
            jQuery("#returnDate, #fieldset-combination, #div-todest, #todest, #fieldset-category, #fieldset-date").hide("slow");
            jQuery("#roundtrip").val("");
        } 
    });
    
    // resetting dates
    jQuery("#fromdest").change(function(){
        var from = jQuery(this).val();
        jQuery("#fieldset-date").hide();
        jQuery("#fromDate, #toDate, #select-category").val("");
        jQuery("#todest option:eq(0)").attr('selected', 'selected');
                
//        if(from=='CPH'){
//            jQuery('#todest option:eq(2)').attr('value', 'AGA');
//        } else if(from=='BLL'){
//            jQuery('#todest option:eq(2)').attr('value', 'RAK-AGA');
//        }
                
        var holidayType;
        jQuery('input[name="holidayType"]').each(function(){
            var checked = jQuery(this).attr('checked');
            if(checked){
                holidayType = jQuery(this).val();
            }
        });
        
        if(holidayType=='A'||holidayType=='D'){
            var til = jQuery("#todest").val();
            if(til!='') resetAllDateAjax(til, from, true);
        } else if(holidayType=='R'){
            var packageid = jQuery("#roundtrip").val();
            if(packageid!='') resetDateRoundtrip(packageid, from);
        } else if(holidayType=='I'){
            /**
             * buat reset disini 
             */
        }
    });
    
    // disable-enable select hotel kategori from "destination to"
    jQuery("#todest").change(function(){
        var til = jQuery(this).val();
        jQuery("#toDate, #fromDate, #select-category").val("");
        jQuery("#accomodation").val("all");
        
        if(til==''){
            jQuery("#fieldset-category, #fieldset-date").hide("slow");            
        } else if(til!=''){
            var holidayType;
            jQuery('input[name="holidayType"]').each(function(){
                var checked = jQuery(this).attr('checked');
                if(checked){
                    holidayType = jQuery(this).val();
                }
            });
            
            jQuery("#fieldset-category, #fieldset-date").show("slow");  
            var from = jQuery("#fromdest").val();
            
            if(holidayType=='A'||holidayType=='D'){            
                jQuery("#returnDate").show();                
                resetAllDateAjax(til, from, true);
            } else if(holidayType=='R'){
                var packageid = jQuery("#roundtrip").val();
                if(packageid!='') resetDateRoundtrip(packageid, from);
            } else if(holidayType=='I'){
                jQuery("#returnDate").hide();
                resetDateCombi(from, til);
            }  

//          resetHotel(til);
        }
    });
    
    // disable-enable select hotel kateogri from " hotel star/category"
    jQuery("#select-category").change(function(){
        var cat = jQuery(this).val();
        var til = jQuery("#todest").val();
        var hotels = jQuery.parseJSON(jQuery("#mod-hotels").val());
        
        jQuery("#accomodation").children().each(function(){
            if(jQuery(this).val()!='all'){
                jQuery(this).remove();
            }
        });
        
        if(cat==''){
            jQuery("#accomodation").val("all");
            jQuery.each(hotels.data, function(i, attributes){
                jQuery("#accomodation").append("<option value="+attributes.pkg+">"+attributes.hotel+"</option");
            });
        } else if(cat!=''){
            jQuery.each(hotels.data, function(i, attributes){
                if(cat==attributes.category){
                    jQuery("#accomodation").append("<option value="+attributes.pkg+">"+attributes.hotel+"</option");
                }
            });
        }
    });
    
    // date flights for roundtrip
    jQuery("#roundtrip").change(function(){
        var packageid = jQuery(this).val();
        jQuery("#returnDate").hide();
        if(packageid!=''){
            jQuery("#fromDate").val("");
            var from = jQuery("#fromdest").val();
            resetDateRoundtrip(packageid, from);
            jQuery("#fieldset-date, #fromDate").show("slow");
        } else {
            jQuery("#fieldset-date, #fromDate").hide("slow");
            jQuery("#fromDate").val("");
        }
    });
                    
    /**
     * not used autocomplete
     *jQuery("#search-hotel").autocomplete({
        source:  function( request, response ) {
                        jQuery.ajax({
                                minLength: 2,
                                url: "index.php?option=com_travelsearch&task=noView&type=hotelajax",
                                dataType: "json",
                                data: {
                                        term : request.term,
                                        todest : jQuery("#todest").val(),
                                        category : jQuery("#select-category").val()
                                },
                                success: function( data ) {
                                    if(data.status=="true"){
                                        // loop and return the response data
                                        response(jQuery.map(data.data, function(item){
                                            return{
                                                label: item.hotel + ", "+ item.city,
                                                value: item.hotel,
                                                id_pkg: item.pkg
                                            }
                                        }));
                                    } 
                                }
                        });
                },
        minLength: 2,
        select: function( event, ui ) {
            /**
             * this.value = value of selected
             * to get other variable use 'ui.item.[name_of_variable]'
            // set id_pkg selected, remove class if error (defined by validation plugin and hide the message)
            jQuery("#hotel").val(ui.item.id_pkg).removeClass().addClass("valid").next().hide();
            jQuery("#fieldset-date").show("slow");
        }
    }); */
    
    // reset datepicker for combi
    function resetDateCombi(from, til){
        
        waitingDialog({title: "Loading..", message: ""});
        
        jQuery.ajax({
            url: "index.php?option=com_travelsearch",
            data: "task=noView&type=dateajaxcombi&to="+til+"&from="+from,
            success: function(msg){                    
                var dates = new Array();
                dates = eval("("+msg+")");
                
                function availabledates(date) {
                    dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
                    if ($.inArray(dmy, dates) == -1) {
                        return [false,"","Unavailable"];
                    } else {
                        return [true, ""];
                    }
                }
                
                jQuery("#fromDate").datepicker("destroy").datepicker({
                    onSelect: function(date) {
                        jQuery("#toDate").val(date);
                    },
                    beforeShowDay: availabledates,
                    minDate: 0,
                    changeMonth: true,
                    dateFormat: "dd-mm-yy",
                    defaultDate: dates[0]
                });
            }
        });
        
        jQuery.ajax({
            url: "index.php?option=com_travelsearch",
            data: "task=noView&type=getapisrccombi&to="+til+"&from="+from,
            success: function(msg){                    
                var src = jQuery.parseJSON(msg);
                jQuery("#mod-srcapi").val(src.data);
                
                closeWaitingDialog();
            }
        });        
    }
        
    // reset datepicker based on package id and origin city
    function resetDateRoundtrip(packageid, from){
        
        jQuery.ajax({
            url: "index.php?option=com_travelsearch",
            data: "task=noView&type=dateajax&packageid="+packageid+"&from="+from,
            success: function(msg){                    
                var dates = new Array();
                dates = eval("("+msg+")");
                
                function availabledates(date) {
                    dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
                    if ($.inArray(dmy, dates) == -1) {
                        return [false,"","Unavailable"];
                    } else {
                        return [true, ""];
                    }
                }

                jQuery("#fromDate").datepicker("destroy").datepicker({
                    beforeShowDay: availabledates,
                    minDate: 0,
                    changeMonth: true,
                    dateFormat: "dd-mm-yy",
                    defaultDate: dates[0]
                });
                
                closeWaitingDialog();
            }
        });
    }
    

    // reset date from and date to based on destination and origin
    function resetAllDateAjax(til, from, all){
        waitingDialog({title: "Loading..", message: ""});
        
        jQuery.ajax({
            url: "index.php?option=com_travelsearch",
            data: "task=noView&type=dateajax&to="+til+"&from="+from,
            success: function(msg){                    
                var dates = new Array();
                dates = eval("("+msg+")");
                
                function availabledates(date) {
                    dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
                    if ($.inArray(dmy, dates) == -1) {
                        return [false,"","Unavailable"];
                    } else {
                        return [true, ""];
                    }
                }
                
                jQuery("#fromDate").datepicker("destroy").datepicker({
                    onSelect: function(date) {
                        jQuery("#toDate").val(date);
                    },
                    beforeShowDay: availabledates,
                    minDate: 0,
                    changeMonth: true,
                    dateFormat: "dd-mm-yy",
                    defaultDate: dates[0]
                });
            }
        });
          
        if(all){
            jQuery.ajax({
                url: "index.php?option=com_travelsearch",
                data: "task=noView&type=dateajax&to="+from+"&from="+til,
                success: function(msg){                    
                    var dates = new Array();
                    dates = eval("("+msg+")");
                    function availabledates(date) {
                        dmy = date.getDate() + "-" + (date.getMonth()+1) + "-" + date.getFullYear();
                        if ($.inArray(dmy, dates) == -1) {
                            return [false,"","Unavailable"];
                        } else {
                            return [true, ""];
                        }
                    }

                    jQuery("#toDate").datepicker("destroy").datepicker({
                        beforeShowDay: availabledates,
                        minDate: 0,
                        changeMonth: true,
                        dateFormat: "dd-mm-yy",
                        defaultDate: dates[0]
                    });
                }
            });
        }
        
        jQuery.ajax({
            url: "index.php?option=com_travelsearch",
            data: "task=noView&type=getapisrc&to="+til+"&from="+from,
            success: function(msg){                    
                var src = jQuery.parseJSON(msg);
                jQuery("#mod-srcapi").val(src.data);
                
                closeWaitingDialog();
            }
        });        
    }
    
    function resetHotel(to){
        jQuery("#mod-hotels").val("");
        jQuery("#accomodation").children().each(function(){
            if(jQuery(this).val()!='all'){
                jQuery(this).remove();
            }
        });
        
        jQuery.ajax({
            url: "index.php?option=com_travelsearch",
            data: "task=noView&type=hotelajax&todest="+to,
            success: function(msg){                    
                jQuery("#mod-hotels").val(msg);
                
                var hotels = jQuery.parseJSON(jQuery("#mod-hotels").val());
                jQuery.each(hotels.data, function(i, attributes){
                    jQuery("#accomodation").append("<option value="+attributes.pkg+">"+attributes.hotel+"</option");
                });
            }
        });
    }
    
    function triggercategory(){
        jQuery("#select-category").trigger('change');
    }
    
    function waitforcategory(elem){
        jQuery("#accomodation").val(elem);
    }
    
    // auto trigger the search
    var searchdata = jQuery.parseJSON(jQuery("#mod-search").val());
    
    if(searchdata!=null){        
        var type = '';
        jQuery.each(searchdata, function (i, elem) {
            if(i=="type"){
                type = elem;
                jQuery(".type-"+elem).trigger('click');
            } else if (i=="fromdest"){
                jQuery("#fromdest").val(elem).trigger('change');               
            } else if(i=="todest"){
                if(type=='A'||type=='I'||type=='D'){                    
                    jQuery("#todest").val(elem).trigger('change');
                } 
            }// else if(i=="category"){
//                if(jQuery("#mod-hotels").val()!=''){
//                    jQuery("#select-category").val(elem);
//                    jQuery("#select-category").trigger('change');
//                } else {
//                    // wait for the data to complete
//                    jQuery("#select-category").val(elem);
//                    setTimeout(triggercategory, 3000);
//                }
//            } else if(i=="accomodation"){
//                setTimeout(waitforcategory, 3000, elem);
//            } else if(i=="roundtrip"){
//                if(type==1){
//                    jQuery("#roundtrip").val(elem);
//                    jQuery("#roundtrip").trigger('change');
//                }
//            } else if(i=="combi"){
//                if(type==2){
//                    jQuery("#combination").val(elem);
//                    jQuery("#combination").trigger('change');
//                }
//            } 
            else if(i=="fromdate"){
                jQuery("#fromDate").val(elem);
            } else if(i=="todate"){
                jQuery("#toDate").val(elem);
            } else if(i=="currency"){
                jQuery("#idcurrency").val(elem);
            } else if(i=="rooms"){
                jQuery.each(elem, function(j, obj){
                    if(j>1){
                        jQuery("#add-room-hotel").trigger('click');
                    }
                    
                    jQuery("#adult-room-"+j).val(obj.adults).trigger('change');
                    jQuery("#child-room-"+j).val(obj.child).trigger('change');
                    jQuery("#baby-room-"+j).val(obj.infants).trigger('change');
                });
            } 
        });
    }
});