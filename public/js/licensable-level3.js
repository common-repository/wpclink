var get_metadata_ajax_var;
function wpclink_boxes_expand(){
    
    
    jQuery(".minifest-boxes2 fieldset.level-4").addClass('collapse');
    jQuery(".minifest-boxes2 .level-4.collapse > legend").css("display","block");
    
    jQuery(".minifest-boxes2 fieldset.level-3").addClass('collapse');
    jQuery(".minifest-boxes2 .level-3.collapse > legend").css("display","block");
    
    jQuery(".minifest-boxes2 fieldset.level-5").addClass('collapse');
    jQuery(".minifest-boxes2 .level-5.collapse > legend").css("display","block");
    
    jQuery(".minifest-boxes2 fieldset.level-1 >  fieldset:last-child").addClass('last-box');
    
    jQuery(".minifest-boxes2 .last-box fieldset.level-4").removeClass('collapse');
    jQuery(".minifest-boxes2 .last-box fieldset.level-3").removeClass('collapse');
    jQuery(".minifest-boxes2 .last-box fieldset.level-5").removeClass('collapse');
    
    
    jQuery(".expand-boxes").off('click');
    jQuery(".expand-boxes").click(function(){
                        
    if(jQuery(this).data('expand') == 1){
        jQuery(this).parent().parent().parent().find(".level-1 fieldset").addClass("collapse");
        jQuery(this).parent().parent().parent().find(".level-1 fieldset legend").css("display","block");
        jQuery(this).data('expand',0);
        jQuery(this).text("Expand");
        jQuery(this).addClass("expended");
       
    }else{
        jQuery(this).parent().parent().parent().find(".level-1 fieldset").removeClass("collapse");
         jQuery(this).parent().parent().parent().find(".level-1 fieldset legend").css("display","block");
         jQuery(this).data('expand',1);
        jQuery(this).text("Collapse");
        jQuery(this).removeClass("expended");
        
        
        var cl_list_loader_id = jQuery(this).parent().parent().parent().find('.level-3 .more-loader-btn').data('loader-id');
        var cl_load_more_assertion = jQuery(this).parent().parent().parent().find('.level-3 .more-loader-btn');
        
        var cl_list_loader_id_2 = jQuery(this).parent().parent().parent().find('.level-6 .more-loader-btn').data('loader-id');
        var cl_load_more_authors_2 = jQuery(this).parent().parent().parent().find('.level-6 .more-loader-btn');
        
        jQuery('.full-metadata .'+cl_list_loader_id).slideDown();
          if(jQuery(cl_load_more_assertion).text() == 'View'){
            jQuery(cl_load_more_assertion).text('Hide');
        }else if(jQuery(cl_load_more_assertion).text() == 'See More +'){
            jQuery(cl_load_more_assertion).text('See Less -');
        }else if(jQuery(cl_load_more_assertion).text() == 'See Less -'){
            jQuery(cl_load_more_assertion).text('See More +');
        }else{
            jQuery(cl_load_more_assertion).text('View');
        }
        
        jQuery('.full-metadata .'+cl_list_loader_id_2).slideDown();
          if(jQuery(cl_load_more_authors_2).text() == 'View'){
            jQuery(cl_load_more_authors_2).text('Hide');
        }else if(jQuery(cl_load_more_authors_2).text() == 'See More +'){
            jQuery(cl_load_more_authors_2).text('See Less -');
        }else if(jQuery(cl_load_more_authors_2).text() == 'See Less -'){
            jQuery(cl_load_more_authors_2).text('See More +');
        }else{
            jQuery(cl_load_more_authors_2).text('View');
        }
        
        
    }
    }
);
}
function wpclink_myFunction(selection,button) {
  /* Get the text field */
  var copyText = selection;
  /* Select the text field */
  copyText.select();
  
   /* Copy the text inside the text field */
  navigator.clipboard.writeText(copyText.val());
  /* Alert the copied text */
  button.parent().find('.copyme').text("Copied!..");
}
function wpclink_metadata_sync_tags(tagcatch){
    var var_synctag_status = jQuery(".synctags").data("status");
    
    if(var_synctag_status == '1'){
        
                var metadata_filter_group = jQuery(tagcatch).data("group");
                var metadata_selected = jQuery(tagcatch).data("selected");
                if(metadata_selected == '1'){
                    jQuery(".compare_center .archive-side .full-metadata "+"."+metadata_filter_group+ ", .compare_center .ingredients-side .full-metadata "+"."+metadata_filter_group).fadeIn();
                    // Both
                    jQuery(".metadata-menu .metadata_filter_btn"+"."+metadata_filter_group).addClass("selected");
                }else{
                     jQuery(".compare_center .archive-side .full-metadata "+"."+metadata_filter_group+ ", .compare_center .ingredients-side .full-metadata "+"."+metadata_filter_group).fadeOut();
                    // Both
                    jQuery(".metadata-menu .metadata_filter_btn"+"."+metadata_filter_group).removeClass("selected");
                }
        
    } 
    
}
function wpclink_metadata_compare_popup_func(){
    // Ingredient
    var get_indregrient_id = jQuery(".ing.longtree").find('.selected').children('img').data('attach-id');
    
    // Box Number
    var get_box_number_ing = jQuery(".ing.longtree").find('.selected').children('img').data('box-num');
    
    // Archive
    var get_archive_id = jQuery(".archive.longtree").find('.selected').children('img').data('archive-id');
    var get_attach_id = jQuery(".archive.longtree").find('.selected').children('img').data('attach-id');
    var get_archive_img = jQuery(".archive.longtree").find('.selected').children('img').attr('src');
    var get_indregrient_img = jQuery(".ing.longtree").find('.selected').children('img').attr('src');
    
     // Box Number
    var get_archive_box_number = jQuery(".archive.longtree").find('.selected').children('img').data('box-num');
    
    jQuery(".ingredients-side").css("background-image",'url("'+get_indregrient_img+'")');
    jQuery(".archive-side").css("background-image",'url("'+get_archive_img+'")');
    //alert("A:"+get_archive_id+"I:"+get_indregrient_id);
    jQuery(".wpclink_metadata_compare_popup").show();
    jQuery(".backto_popup").show();
    jQuery(".sidebyside_btn").show();
   
    var var_get_metadata = jQuery(".metadata_btn").data('metadata');
    if(var_get_metadata == '1'){
        jQuery(".wpclink_metadata_compare_popup").addClass("metadata_expand");
        jQuery(".wpclink_metadata_popup").addClass("metadata_expand_popup");
        jQuery('.wpclink_metadata_compare_popup.metadata_expand .sidebar-left').animate({"left":"-25%"}, 1000);
        jQuery('.wpclink_metadata_compare_popup.metadata_expand .sidebar-right').animate({"right":"-25%"}, 1000);
        jQuery('.metadata_expand .compare_center .archive-side').css("width","50%");
        jQuery('.metadata_expand .compare_center .archive-side').css("height","100%");
        jQuery('.metadata_expand .compare_center .archive-side').css("right","0");
        jQuery('.metadata_expand .compare_center .ingredients-side').css("width","50%");
        jQuery('.metadata_expand .compare_center .ingredients-side').css("height","100%");
        jQuery('.metadata_expand .compare_center .ingredients-side').css("left","0");
        
        jQuery(".syncscroll").show();
        
        
var cl_active_manifiest_id_ing = jQuery('.active_manifiest_val.ing').val();
var cl_active_manifiest_id_archive = jQuery('.active_manifiest_val.archive').val();
// Box Number
var get_box_number_ing = jQuery(".ing.longtree").find('.selected').children('img').data('box-num');  
// Box Number
var get_archive_box_number = jQuery(".archive.longtree").find('.selected').children('img').data('box-num');
    
if(typeof get_box_number_ing != 'undefined' && typeof get_archive_box_number != 'undefined'){
      // Disable Sync Tags
    if(cl_active_manifiest_id_ing != get_box_number_ing || cl_active_manifiest_id_ing != get_archive_box_number ){
        jQuery('.synctags').data("selected",0);
        jQuery('.synctags').removeClass("selected");
        jQuery('.synctags').hide();
    }else{
        jQuery('.synctags').show();
    }
}else if(typeof get_box_number_ing != 'undefined'){
    if(cl_active_manifiest_id_ing != get_box_number_ing  ){
        jQuery('.synctags').data("selected",0);
        jQuery('.synctags').removeClass("selected");
        jQuery('.synctags').hide();
    }else{
        jQuery('.synctags').show();
    }
}
        
        
               
    }else{
        jQuery(".wpclink_metadata_compare_popup").removeClass("metadata_expand");
        jQuery(".wpclink_metadata_popup").removeClass("metadata_expand_popup");
        jQuery('.wpclink_metadata_compare_popup .sidebar-left').css("left","0%");
        jQuery('.wpclink_metadata_compare_popup .sidebar-right').css("right","0%");
        
        jQuery('.compare_center .archive-side').css("width","100%");
        jQuery('.compare_center .archive-side').css("height","50%");
        jQuery('.compare_center .archive-side').css("right","0");
        jQuery('.compare_center .ingredients-side').css("width","100%");
        jQuery('.compare_center .ingredients-side').css("height","50%");
        jQuery('.compare_center .ingredients-side').css("left","0");
        
        
         jQuery(".syncscroll").hide();
         jQuery(".synctags").hide();
    }  
    
 
    
  
   // jQuery( ".ingredients-side" ).draggable();
if(typeof get_indregrient_id != 'undefined'){
   
    
  
    console.log("enter-ing:"+get_archive_id);
    get_metadata_ajax_var = jQuery.ajax({
        url: wpclink_vars.url,
        data: {
            'action':'wpclink_get_box_metadata_ajax',
            'attach_id' : get_indregrient_id,
            'archive_id' : get_indregrient_id,
            'cl_box_number' : get_box_number_ing,
            'cl_metadata_action' : 'ingredients',
            'nonce' : wpclink_vars.nonce
        },
        beforeSend: function() {
            jQuery('.sidebar-left').html('<div class="loading-wrapper"><span class="cl_loader"></span></div>');
            jQuery('.sidebar-left .loading-circle-quick').fadeIn();
            
        },
        success:function(data) {
            // This outputs the result of the ajax request
            jQuery('.sidebar-left').html(data);
             // METADATA LIST
             jQuery.ajax({
                url: wpclink_vars.url,
                data: {
                    'action':'wpclink_get_metadata_list_ajax',
                    'attach_id' : get_indregrient_id,
                    'archive_id' : get_indregrient_id,
                    'cl_box_number' : get_box_number_ing,
                    'cl_metadata_action' : 'ingredients',
                    'nonce' : wpclink_vars.nonce
                },
                beforeSend: function() {
                    // jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                   
                        jQuery('.compare_center .ingredients-side').html('<div class="lds-dual-ring"></div>');
                                                       
                },
                success:function(data) {
                    // This outputs the result of the ajax request
                  
                        jQuery('.compare_center .ingredients-side').html(data);
                        jQuery('.compare_center .ingredients-side .metadata_filter_btn').click(function(){
                            var metadata_filter_group = jQuery(this).data("group");
                            var metadata_selected = jQuery(this).data("selected");
                            if(metadata_selected == '1'){
                                jQuery(".compare_center .ingredients-side .full-metadata "+"."+metadata_filter_group).fadeOut();
                                jQuery(this).data("selected",0);
                                jQuery(this).removeClass("selected");
                                
                                wpclink_metadata_sync_tags(jQuery(this));
                            }else{
                                jQuery(".compare_center .ingredients-side .full-metadata "+"."+metadata_filter_group).fadeIn();
                                jQuery(this).data("selected",1);
                                jQuery(this).addClass("selected");
                                
                                wpclink_metadata_sync_tags(jQuery(this));
                            }
                        });
                        jQuery('.compare_center .ingredients-side .metadata_action_select').click(function(){
                            
                             var synctag_status = jQuery(".synctags").data("status");
                            if(synctag_status == '1'){
                                jQuery(".compare_center .metadata-field, .compare_center .subheader").fadeIn();
                                jQuery('.compare_center .metadata_filter_btn').data("selected",1);
                                jQuery('.compare_center .metadata_filter_btn').addClass("selected");
                            }else{
                                jQuery(".compare_center .ingredients-side .metadata-field, .compare_center .ingredients-side .subheader").fadeIn();
                                jQuery('.compare_center .ingredients-side .metadata_filter_btn').data("selected",1);
                                jQuery('.compare_center .ingredients-side .metadata_filter_btn').addClass("selected");
                            }
                            
                        });
                        jQuery('.compare_center .ingredients-side .metadata_action_clear').click(function(){
                            
                            var synctag_status = jQuery(".synctags").data("status");
                            if(synctag_status == '1'){
                                
                                jQuery(".compare_center .metadata-field, .compare_center .subheader").fadeOut();
                                jQuery('.compare_center .metadata_filter_btn').data("selected",0);
                                jQuery('.compare_center .metadata_filter_btn').removeClass("selected");
                                
                            } else {
                                
                                 jQuery(".compare_center .ingredients-side .metadata-field, .compare_center .ingredients-side .subheader").fadeOut();
                                jQuery('.compare_center .ingredients-side .metadata_filter_btn').data("selected",0);
                                jQuery('.compare_center .ingredients-side .metadata_filter_btn').removeClass("selected");
                                
                            }
                           
                        });
                    
                   
                    var var_get_metadata = jQuery(".metadata_btn").data('metadata');
                    if(var_get_metadata == '1'){
                        jQuery(".compare_center .ingredients-side .action_filter_btn").show();
                        jQuery(".compare_center .ingredients-side .full-metadata-wrapper").show();
                         jQuery(".syncscroll").show();
                        
                        
var cl_active_manifiest_id_ing = jQuery('.active_manifiest_val.ing').val();
var cl_active_manifiest_id_archive = jQuery('.active_manifiest_val.archive').val();
// Box Number
var get_box_number_ing = jQuery(".ing.longtree").find('.selected').children('img').data('box-num');  
// Box Number
var get_archive_box_number = jQuery(".archive.longtree").find('.selected').children('img').data('box-num');

                        
                    }else{
                        jQuery(".compare_center .ingredients-side .action_filter_btn").hide();
                        jQuery(".compare_center .ingredients-side .full-metadata-wrapper").hide();
                         jQuery(".syncscroll").hide();
                         jQuery(".synctags").hide();
                    }      
                    
                    /* == See More == */
                    jQuery('.more-loader-btn').off('click');
                    jQuery('.more-loader-btn').on("click",function(){
                        var cl_list_loader_id = jQuery(this).data('loader-id');
                        jQuery('.full-metadata .'+cl_list_loader_id).slideToggle();
                        
                          if(jQuery(this).text() == 'View'){
                            jQuery(this).text('Hide');
                        }else if(jQuery(this).text() == 'See More +'){
                            jQuery(this).text('See Less -');
                        }else if(jQuery(this).text() == 'See Less -'){
                            jQuery(this).text('See More +');
                        }else{
                            jQuery(this).text('View');
                        }
                        
                    });
                     jQuery('.minifest-boxes2 legend').off('click');   
                    jQuery(".minifest-boxes2 legend").click(function(){
                        jQuery(this).parent().toggleClass("collapse");
                        jQuery(this).parent().find("> legend").css("display","block");
                    });
                    
                    jQuery(".expand-boxes").click(function(){
                        
                        if(jQuery(this).data('expand') == 1){
                            jQuery(this).parent().parent().parent().find(".level-1 fieldset").addClass("collapse");
                            jQuery(this).data('expand',0);
                            
                        }else{
                            jQuery(this).parent().parent().parent().find(".level-1 fieldset").removeClass("collapse");
                             jQuery(this).data('expand',1);
                        }
                        }
                    );
                    
                    jQuery(".level-6.collapse > legend").css("display","block");
                    
                    
                    /* == Binary == */
                    jQuery('.ingredients-side .load-binary').off('click');
                    jQuery('.ingredients-side .load-binary').on("click",function(){
                        var load_binary_key = jQuery(this).data('key');
                        var same_binary_btn = jQuery(this);
                        
                        jQuery.ajax({
                            url: wpclink_vars.url,
                            data: {
                                'action':'wpclink_load_binary_metadata_ajax',
                                'target_metadata' : load_binary_key,
                                'attach_id' : get_indregrient_id,
                                'archive_id' : get_indregrient_id,
                                'cl_metadata_action' : 'ingredients',
                                'nonce' : wpclink_vars.nonce
                            },
                            beforeSend: function() {
                               if(same_binary_btn.text() == 'View'){
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html("Loading...");
                                }
                            },
                            success:function(data) {
                                same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                same_binary_btn.parent().find('.view-binary').html(data);
                               same_binary_btn.parent().find('.copyme').show();
                                
                            },
                            error: function(errorThrown){
                                console.log(errorThrown);
                            }
                        });
                            if(same_binary_btn.text() == 'Hide'){
                                    same_binary_btn.parent().find('.view-binary').slideUp();
                                    same_binary_btn.text('View');
                                    same_binary_btn.parent().find('.copyme').hide();
                                }else{
                                    same_binary_btn.parent().find('.view-binary').slideDown();
                                    same_binary_btn.text('Hide');
                                    same_binary_btn.parent().find('.copyme').show();
                                }  
                        
                        same_binary_btn.parent().find('.copyme').click(function(){
                            var copy_element = same_binary_btn.parent().find('.view-binary');
                            wpclink_myFunction(copy_element,same_binary_btn);
                        });
                        
                    });
                    
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
         
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}else{
    jQuery('.sidebar-left').html('<span class="no-info"><span class="infobar"></span>No content credentials for this file</span>');
    jQuery('.compare_center .ingredients-side').html('');
    
    // Hide
    jQuery('.metadata_btn').hide();
     jQuery('.metadata_btn').removeClass("activated");
    jQuery('.metadata_btn').data('metadata',0);
}
if(typeof get_archive_id != 'undefined'){
    console.log("enter-arc:"+get_archive_id);
    get_metadata_ajax_var = jQuery.ajax({
        url: wpclink_vars.url,
        data: {
            'action':'wpclink_get_box_metadata_ajax',
            'attach_id' : get_attach_id,
            'archive_id' : get_archive_id,
            'cl_box_number' : get_archive_box_number,
            'cl_metadata_action' : 'archive',
            'nonce' : wpclink_vars.nonce
        },
        beforeSend: function() {
            jQuery('.sidebar-right').html('<div class="loading-wrapper"><span class="cl_loader"></span></div>');
            jQuery('.sidebar-right .loading-circle-quick').fadeIn();
            
        },
        success:function(data) {
            // This outputs the result of the ajax request
            jQuery('.sidebar-right').html(data);
            /* COMPARE */
                 // METADATA LIST
                 jQuery.ajax({
                    url: wpclink_vars.url,
                    data: {
                        'action':'wpclink_get_metadata_list_ajax',
                        'attach_id' : get_attach_id,
                        'archive_id' : get_archive_id,
                        'cl_box_number' : get_archive_box_number,
                        'cl_metadata_action' : 'archive',
                        'nonce' : wpclink_vars.nonce
                    },
                    beforeSend: function() {
                        // jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                       
                            jQuery('.compare_center .archive-side').html('<div class="lds-dual-ring"></div>');
                                                           
                    },
                    success:function(data) {
                        // This outputs the result of the ajax request
                      
                            jQuery('.compare_center .archive-side').html(data);
                            
                            jQuery('.compare_center .archive-side .metadata_filter_btn').click(function(){
                                var metadata_filter_group = jQuery(this).data("group");
                                var metadata_selected = jQuery(this).data("selected");
                                if(metadata_selected == '1'){
                                    jQuery(".compare_center .archive-side .full-metadata "+"."+metadata_filter_group).fadeOut();
                                    jQuery(this).data("selected",0);
                                    jQuery(this).removeClass("selected");
                                    
                                    wpclink_metadata_sync_tags(jQuery(this));
                                }else{
                                    jQuery(".compare_center .archive-side .full-metadata "+"."+metadata_filter_group).fadeIn();
                                    jQuery(this).data("selected",1);
                                    jQuery(this).addClass("selected");
                                    
                                    wpclink_metadata_sync_tags(jQuery(this));
                                }
                            });
                            jQuery('.compare_center .archive-side .metadata_action_select').click(function(){
                                
                            var synctag_status = jQuery(".synctags").data("status");
                            if(synctag_status == '1'){
                                
                                 jQuery(".compare_center .metadata-field, .compare_center .subheader").fadeIn();
                                    jQuery('.compare_center .metadata_filter_btn').data("selected",1);
                                    jQuery('.compare_center .metadata_filter_btn').addClass("selected");
                                
                            }else{
                                jQuery(".compare_center .archive-side .metadata-field, .compare_center .archive-side .subheader").fadeIn();
                                    jQuery('.compare_center .archive-side .metadata_filter_btn').data("selected",1);
                                    jQuery('.compare_center .archive-side .metadata_filter_btn').addClass("selected");
                                
                            }
                            });
                            jQuery('.compare_center .archive-side .metadata_action_clear').click(function(){
                                
                                
                            var synctag_status = jQuery(".synctags").data("status");
                            if(synctag_status == '1'){
                                
                                jQuery(".compare_center .metadata-field, .compare_center .subheader").fadeOut();
                                    jQuery('.compare_center .metadata_filter_btn').data("selected",0);
                                    jQuery('.compare_center .metadata_filter_btn').removeClass("selected");
                                
                            }else{
                                
                                jQuery(".compare_center .archive-side .metadata-field, .compare_center .archive-side .subheader").fadeOut();
                                    jQuery('.compare_center .archive-side .metadata_filter_btn').data("selected",0);
                                    jQuery('.compare_center .archive-side .metadata_filter_btn').removeClass("selected");
                                
                            }
                                
                            });
                        
                       
                        var var_get_metadata = jQuery(".metadata_btn").data('metadata');
                        if(var_get_metadata == '1'){
                            jQuery(".compare_center .archive-side .action_filter_btn").show();
                            jQuery(".compare_center .archive-side .full-metadata-wrapper").show();
                            jQuery(".syncscroll").show();
                            
var cl_active_manifiest_id_ing = jQuery('.active_manifiest_val.ing').val();
var cl_active_manifiest_id_archive = jQuery('.active_manifiest_val.archive').val();
// Box Number
var get_box_number_ing = jQuery(".ing.longtree").find('.selected').children('img').data('box-num');  
// Box Number
var get_archive_box_number = jQuery(".archive.longtree").find('.selected').children('img').data('box-num');
    
if(typeof get_box_number_ing != 'undefined' && typeof get_archive_box_number != 'undefined'){
      // Disable Sync Tags
    if(cl_active_manifiest_id_ing != get_box_number_ing || cl_active_manifiest_id_ing != get_archive_box_number ){
        jQuery('.synctags').data("selected",0);
        jQuery('.synctags').removeClass("selected");
        jQuery('.synctags').hide();
    }else{
        jQuery('.synctags').show();
    }
}else if(typeof get_box_number_ing != 'undefined'){
    if(cl_active_manifiest_id_ing != get_box_number_ing  ){
        jQuery('.synctags').data("selected",0);
        jQuery('.synctags').removeClass("selected");
        jQuery('.synctags').hide();
    }else{
        jQuery('.synctags').show();
    }
}
      
                        }else{
                            jQuery(".compare_center .archive-side .action_filter_btn").hide();
                            jQuery(".compare_center .archive-side .full-metadata-wrapper").hide();
                            jQuery(".syncscroll").hide();
                             jQuery(".synctags").hide();
                        }
                        
                         /* == See More == */
                        jQuery('.more-loader-btn').off('click');
                        jQuery('.more-loader-btn').on("click",function(){
                            var cl_list_loader_id = jQuery(this).data('loader-id');
                            jQuery('.full-metadata .'+cl_list_loader_id).slideToggle();
                            
                          if(jQuery(this).text() == 'View'){
                            jQuery(this).text('Hide');
                        }else if(jQuery(this).text() == 'See More +'){
                            jQuery(this).text('See Less -');
                        }else if(jQuery(this).text() == 'See Less -'){
                            jQuery(this).text('See More +');
                        }else{
                            jQuery(this).text('View');
                        }
                        });
                    
                     jQuery('.minifest-boxes2 legend').off('click');   
                     jQuery(".minifest-boxes2 legend").click(function(){
                        jQuery(this).parent().toggleClass("collapse");
                         jQuery(this).parent().find("> legend").css("display","block");
                    });
                        
                        
                  
                        
                    jQuery(".level-6.collapse > legend").css("display","block");
                        
                        wpclink_boxes_expand();
                        
                        
                         /* == Binary == */
                    jQuery('.archive-side .load-binary').off('click');
                    jQuery('.archive-side .load-binary').on("click",function(){
                        var load_binary_key = jQuery(this).data('key');
                        var same_binary_btn = jQuery(this);
                        
                        jQuery.ajax({
                            url: wpclink_vars.url,
                            data: {
                                'action':'wpclink_load_binary_metadata_ajax',
                                'target_metadata' : load_binary_key,
                                'attach_id' : get_attach_id,
                                'archive_id' : get_archive_id,
                                'cl_metadata_action' : 'archive',
                                'nonce' : wpclink_vars.nonce
                            },
                            beforeSend: function() {
                                if(same_binary_btn.text() == 'View'){
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html("Loading...");
                                }
                            },
                            success:function(data) {
                                same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                same_binary_btn.parent().find('.view-binary').html(data);
                                same_binary_btn.parent().find('.copyme').show();
                                
                            },
                            error: function(errorThrown){
                                console.log(errorThrown);
                            }
                        });
                        
                        
                         if(same_binary_btn.text() == 'Hide'){
                                    same_binary_btn.parent().find('.view-binary').slideUp();
                                    same_binary_btn.text('View');
                                    same_binary_btn.parent().find('.copyme').hide();
                                }else{
                                    same_binary_btn.parent().find('.view-binary').slideDown();
                                    same_binary_btn.text('Hide');
                                    same_binary_btn.parent().find('.copyme').show();
                                }
                        
                         same_binary_btn.parent().find('.copyme').click(function(){
                            var copy_element = same_binary_btn.parent().find('.view-binary');
                            wpclink_myFunction(copy_element,same_binary_btn);
                        });
                        
                        });
                        
                        
                        
                    },
                    error: function(errorThrown){
                        console.log(errorThrown);
                    }
                });
         
        },
        error: function(errorThrown){
            console.log(errorThrown);
        }
    });
}else{
    jQuery('.sidebar-right').html('<span class="no-info"><span class="infobar"></span>No content credentials for this file</span>');
    jQuery('.compare_center .archive-side').html('');
    
    // Hide
    jQuery('.metadata_btn').hide();
    jQuery('.metadata_btn').removeClass("activated");
    jQuery('.metadata_btn').data('metadata',0);
}
    
}
jQuery( window ).resize(function() {
    
  
    var outer_width = jQuery('.wpclink_metadata_popup').outerWidth();
    var outer_height = jQuery('.wpclink_metadata_popup').outerHeight();
    jQuery(".wpclink_metadata_popup").wpclink_center(outer_width,outer_height);
    
    });
    
    
    jQuery(document).ready(function() {
    jQuery(".tree-view-more").click(function(){
        
        if(jQuery(this).data("attach-id") !== "undefined") {
        
            // Load Metadata in popup
    
            var attach_id = jQuery(this).data("attach-id");
            var box_height = 0;
            
             if(jQuery(this).data("box-num") !== "undefined") {
                var box_number = jQuery(this).data("box-num");   
             }else{
                 var box_number = jQuery(this).data("box-num");  
             }
    
            jQuery(".wpclink_metadata_popup_wrapper").show();
    
            // Hide All
            jQuery('.cl-subitem').fadeOut();
            jQuery('.cl-darkmode-icon').fadeOut();	
    
             // This does the ajax request
get_metadata_ajax_var = jQuery.ajax({
                url: wpclink_vars.url,
                data: {
                    'action':'wpclink_metadata_level_3_ajax',
                    'attach_id' : attach_id,          
                    'box_number' : box_number,
                    'nonce' : wpclink_vars.nonce
                },
                beforeSend: function() {
                    jQuery('.wpclink_metadata_popup').html('<div class="loading-circle-quick" style="display: none"><div class="loading-wrapper"><span class="cl_loader"></span></div></div>');
                    jQuery('.wpclink_metadata_popup .loading-circle-quick').fadeIn();
                },
                success:function(data) {
                    // This outputs the result of the ajax request
    
                    jQuery('.wpclink_metadata_popup').html(data);
                    box_height = jQuery('.wpclink_metadata_level_3').height();
                    var outer_width = jQuery('.wpclink_metadata_popup').outerWidth();
                    var outer_height = jQuery('.wpclink_metadata_popup').outerHeight();
                    
                    jQuery(".wpclink_metadata_popup").wpclink_center(outer_width,outer_height,box_height);
                 
                    jQuery('.c2pa_image').each(function(){
                        
                        if(jQuery(this).data("attach-id") !== "undefined" && jQuery(this).data("type") !== "undefined") {
                            
                            // Load Metadata in popup
                    
                            var image_attach_id = jQuery(this).data("attach-id");
                            
                            var box_number = jQuery(this).data("box-num");
                            var image_cl_type = jQuery(this).data("type");
                            if(attach_id == image_attach_id && image_cl_type == 'ingredients'){
                                
                                // Old version
                                //jQuery(this).parent().addClass("selected");
                                jQuery('.longtree.ing > li > span.image_cover ').addClass("selected");
                              
                                
                                
                                  // METADATA LIST
                                  jQuery.ajax({
                                    url: wpclink_vars.url,
                                    data: {
                                        'action':'wpclink_get_metadata_list_ajax',
                                        'attach_id' : image_attach_id,
                                        'archive_id' : image_attach_id,
                                        
                                        'cl_box_number': box_number,
                                        'cl_metadata_action' : image_cl_type,
                                        'nonce' : wpclink_vars.nonce
                                    },
                                    beforeSend: function() {
                                        // jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                                        if(image_cl_type == 'archive'){
                                            jQuery('.wpclink_metadata_thumbnail_archive').html('<div class="lds-dual-ring"></div>');
                                        }else{
                                            jQuery('.wpclink_metadata_thumbnail').html('<div class="lds-dual-ring"></div>');
                                        }                                        
                                    },
                                    success:function(data) {
                                        // This outputs the result of the ajax request
       
                                        if(image_cl_type == 'archive'){
                                            
                                            console.log('out:archiveo');
                                            
                                            
                                            jQuery('.wpclink_metadata_thumbnail_archive').html(data);
                                            jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').click(function(){
                                                var metadata_filter_group = jQuery(this).data("group");
                                                var metadata_selected = jQuery(this).data("selected");
                                                if(metadata_selected == '1'){
                                                    jQuery(".wpclink_metadata_thumbnail_archive .full-metadata "+"."+metadata_filter_group).fadeOut();
                                                    jQuery(this).data("selected",0);
                                                    jQuery(this).removeClass("selected");
                                                    
                                                    wpclink_metadata_sync_tags(jQuery(this));
                                                }else{
                                                    jQuery(".wpclink_metadata_thumbnail_archive .full-metadata "+"."+metadata_filter_group).fadeIn();
                                                    jQuery(this).data("selected",1);
                                                    jQuery(this).addClass("selected");
                                                    
                                                    wpclink_metadata_sync_tags(jQuery(this));
                                                }
                                            });
                                            jQuery('.wpclink_metadata_thumbnail_archive .metadata_action_select').click(function(){
                                                
                                                
                                                jQuery(".wpclink_metadata_thumbnail_archive .metadata-field, .wpclink_metadata_thumbnail_archive .subheader").fadeIn();
                                                    jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').data("selected",1);
                                                    jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').addClass("selected");
                                            });
                                            jQuery('.wpclink_metadata_thumbnail_archive .metadata_action_clear').click(function(){
                                                jQuery(".wpclink_metadata_thumbnail_archive .metadata-field, .wpclink_metadata_thumbnail_archive .subheader").fadeOut();
                                                    jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').data("selected",0);
                                                    jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').removeClass("selected");
                                            });
                                        }else{
                                            jQuery('.wpclink_metadata_thumbnail').html(data);
                                            jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').click(function(){
                                                var metadata_filter_group = jQuery(this).data("group");
                                                var metadata_selected = jQuery(this).data("selected");
                                                if(metadata_selected == '1'){
                                                    jQuery(".wpclink_metadata_thumbnail .full-metadata "+"."+metadata_filter_group).fadeOut();
                                                    jQuery(this).data("selected",0);
                                                    jQuery(this).removeClass("selected");
                                                }else{
                                                    jQuery(".wpclink_metadata_thumbnail .full-metadata "+"."+metadata_filter_group).fadeIn();
                                                    jQuery(this).data("selected",1);
                                                    jQuery(this).addClass("selected");
                                                }
                                            });
                                            
                                            jQuery('.wpclink_metadata_thumbnail .metadata_action_select').click(function(){
                                                jQuery(".wpclink_metadata_thumbnail .metadata-field, .wpclink_metadata_thumbnail .subheader").fadeIn();
                                                    jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').data("selected",1);
                                                    jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').addClass("selected");
                                            });
                                            jQuery('.wpclink_metadata_thumbnail .metadata_action_clear').click(function(){
                                                jQuery(".wpclink_metadata_thumbnail .metadata-field, .wpclink_metadata_thumbnail .subheader").fadeOut();
                                                    jQuery('.wpclink_metadata_thumbnail .metadata_action_clear').data("selected",0);
                                                    jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').removeClass("selected");
                                            });
                                        }
                                       
                                        var var_get_metadata = jQuery(".metadata_btn").data('metadata');
                                        if(var_get_metadata == '1'){
                                            jQuery(".action_filter_btn").show();
                                            jQuery(".full-metadata-wrapper").show();
                                             
                                        }else{
                                            jQuery(".action_filter_btn").hide();
                                            jQuery(".full-metadata-wrapper").hide();
                                           
                                        }
                                        
                                         /* == See More == */
                                        jQuery('.more-loader-btn').off('click');
                                        jQuery('.more-loader-btn').on("click",function(){
                                            var cl_list_loader_id = jQuery(this).data('loader-id');
                                            jQuery('.full-metadata .'+cl_list_loader_id).slideToggle();
                                            
                         if(jQuery(this).text() == 'View'){
                            jQuery(this).text('Hide');
                        }else if(jQuery(this).text() == 'See More +'){
                            jQuery(this).text('See Less -');
                        }else if(jQuery(this).text() == 'See Less -'){
                            jQuery(this).text('See More +');
                        }else{
                            jQuery(this).text('View');
                        }
                                        });
                         
                    
if(image_cl_type == 'archive'){
    
    console.log('enter:archiveo');
                     jQuery('.minifest-boxes2 legend').off('click');   
                     jQuery(".minifest-boxes2 legend").click(function(){
                        jQuery(this).parent().toggleClass("collapse");
                         jQuery(this).parent().find("> legend").css("display","block");
                    });
    
                    jQuery(".level-6.collapse > legend").css("display","block");
    
                    wpclink_boxes_expand();
                                            
                    jQuery('.wpclink_metadata_thumbnail_archive .load-binary').off('click');
                    jQuery('.wpclink_metadata_thumbnail_archive .load-binary').on("click",function(){
                        var load_binary_key = jQuery(this).data('key');
                        var same_binary_btn = jQuery(this);
                        
                        jQuery.ajax({
                            url: wpclink_vars.url,
                            data: {
                                'action':'wpclink_load_binary_metadata_ajax',
                                'target_metadata' : load_binary_key,
                                'attach_id' : image_attach_id,
                                'archive_id' : image_attach_id,
                                'cl_metadata_action' : image_cl_type,
                                'nonce' : wpclink_vars.nonce
                            },
                            beforeSend: function() {
                                if(same_binary_btn.text() == 'View'){
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html("Loading...");
                                }
                            },
                            success:function(data) {
                                same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                same_binary_btn.parent().find('.view-binary').html(data);
                               same_binary_btn.parent().find('.copyme').show();
                                
                            },
                            error: function(errorThrown){
                                console.log(errorThrown);
                            }
                        }); 
                        if(same_binary_btn.text() == 'Hide'){
                                    same_binary_btn.parent().find('.view-binary').slideUp();
                                    same_binary_btn.text('View');
                                    same_binary_btn.parent().find('.copyme').hide();
                                }else{
                                    same_binary_btn.parent().find('.view-binary').slideDown();
                                    same_binary_btn.text('Hide');
                                    same_binary_btn.parent().find('.copyme').show();
                                }
                        
                         same_binary_btn.parent().find('.copyme').click(function(){
                            var copy_element = same_binary_btn.parent().find('.view-binary');
                            wpclink_myFunction(copy_element,same_binary_btn);
                        });
                    });
                                            
}else{
    
    console.log('enter:ingo');
                     jQuery('.minifest-boxes2 legend').off('click');   
                     jQuery(".minifest-boxes2 legend").click(function(){
                        jQuery(this).parent().toggleClass("collapse");
                         jQuery(this).parent().find("> legend").css("display","block");
                    });
    
                    jQuery(".level-6.collapse > legend").css("display","block");
    
                    wpclink_boxes_expand();
                                            
                                            
                        jQuery('.wpclink_metadata_thumbnail .load-binary').off('click');
                        jQuery('.wpclink_metadata_thumbnail .load-binary').on("click",function(){
                            var load_binary_key = jQuery(this).data('key');
                            var same_binary_btn = jQuery(this);
                            jQuery.ajax({
                                url: wpclink_vars.url,
                                data: {
                                    'action':'wpclink_load_binary_metadata_ajax',
                                    'target_metadata' : load_binary_key,
                                    'attach_id' : image_attach_id,
                                    'archive_id' : image_attach_id,
                                    'cl_metadata_action' : image_cl_type,
                                    'nonce' : wpclink_vars.nonce
                                },
                                beforeSend: function() {
                                    if(same_binary_btn.text() == 'View'){
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html("Loading...");
                                }
                                },
                                success:function(data) {
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html(data);
                                   same_binary_btn.parent().find('.copyme').show();
                                    
                                },
                                error: function(errorThrown){
                                    console.log(errorThrown);
                                }
                            });
                            
                             if(same_binary_btn.text() == 'Hide'){
                                    same_binary_btn.parent().find('.view-binary').slideUp();
                                    same_binary_btn.text('View');
                                    same_binary_btn.parent().find('.copyme').hide();
                                }else{
                                    same_binary_btn.parent().find('.view-binary').slideDown();
                                    same_binary_btn.text('Hide');
                                    same_binary_btn.parent().find('.copyme').show();
                                }
                            
                             same_binary_btn.parent().find('.copyme').click(function(){
                            var copy_element = same_binary_btn.parent().find('.view-binary');
                            wpclink_myFunction(copy_element,same_binary_btn);
                        });
                            
                        });
    
                            // # Hash to Go            
                            if(typeof window.location.hash != "undefined" && window.location.hash == "#JUMBF"){
                                jQuery(".metadata_btn").trigger("click");
                                jQuery(".fullscreen-btn").trigger("click");
                                
                                 jQuery('.wpclink_metadata_thumbnail').animate({ scrollTop: parseInt( jQuery('.minifest-boxes2').offset().top-250)}, 1000);
                                
                                jQuery('.minifest-boxes2 > fieldset').addClass("highlight");
                                
                                
                            }else if(typeof window.location.hash != "undefined" && window.location.hash == "#verifiable_credentials"){
                                
                                jQuery(".metadata_btn").trigger("click");
                                jQuery(".fullscreen-btn").trigger("click");
                                     
                                if (jQuery('legend:contains("adobe.credential")').length > 0) {
                                   jQuery('legend:contains("adobe.credential")').addClass("move_to");
                                jQuery('legend:contains("adobe.credential")').parent().addClass("highlight");
                                    jQuery('legend:contains("adobe.credential")').parent().removeClass("collapse");
                                    
                                    setTimeout(function(){
                                        jQuery('.wpclink_metadata_thumbnail').animate({ scrollTop: parseInt( jQuery('.move_to').offset().top-100)}, 500);
                                        
                                        },1200);
                                    
                                }
             
                                }else if(typeof window.location.hash != "undefined" && window.location.hash == "#creative_work"){
                                
                                jQuery(".metadata_btn").trigger("click");
                                jQuery(".fullscreen-btn").trigger("click");
                                     
                                if (jQuery('legend:contains("CreativeWork")').length > 0) {
                                   jQuery('legend:contains("CreativeWork")').addClass("move_to");
                                    jQuery('legend:contains("CreativeWork")').parent().addClass("highlight");
                                    jQuery('legend:contains("CreativeWork")').parent().removeClass("collapse");
                                    
                                    setTimeout(function(){
                                        jQuery('.wpclink_metadata_thumbnail').animate({ scrollTop: parseInt( jQuery('.move_to').offset().top-100)}, 500);
                                        
                                        },1200);
             
                                }
                                     
                            }else if(typeof window.location.hash != "undefined" && window.location.hash == "#archive_live_embedded_data_comparision"){
                                
                                jQuery(".metadata_btn").trigger("click");
                                jQuery(".fullscreen-btn").trigger("click");
                                jQuery(".compare_button").trigger("click");
                                jQuery(".c2pa_image").trigger("click");
                                jQuery(".syncscroll").trigger("click");
                                     
                                
                                     
                            }
                                            
}
                        }
                                      
                                  });
                                
                            }
                            
                        }
                    });
                    jQuery(".fullscreen-btn").click(function(){
                        
                       
                        var fullscreen_var = jQuery(this).data('fullscreen');
                        if(fullscreen_var == '1'){
                           jQuery(".wpclink_metadata_popup").removeClass('fullscreen');
                           jQuery(".wpclink_metadata_compare_popup").removeClass('fullscreen');
                           jQuery(this).data('fullscreen','0');
                           jQuery(".wpclink_metadata_popup").wpclink_center();
                        }else{
                            jQuery(".wpclink_metadata_popup").addClass('fullscreen');
                            jQuery(".wpclink_metadata_compare_popup").addClass('fullscreen');
                            jQuery(this).data('fullscreen','1');
                        }
                    });
                    
    
                  
                       // if(jQuery(".compare_button").hasClass("active")){
                            jQuery(".ing.longtree .image_cover").click(function(){
                                var wpclink_metadata_image_preview = jQuery( this ).children("img").attr( "src" );
                                jQuery('.wpclink_metadata_thumbnail').css("background-image", "url('" + wpclink_metadata_image_preview + "')");
                                var compare_status_check = jQuery(".compare_button").data('status');
                                if(compare_status_check == 'inactive'){
                               
                                jQuery('.wpclink_metadata_thumbnail').show();
                                jQuery('.wpclink_metadata_thumbnail_archive').hide();
                                jQuery( ".longtree .image_cover" ).removeClass("selected");
                                jQuery( this ).addClass("selected");
                                }else{
                                jQuery('.wpclink_metadata_thumbnail').hide();
                                jQuery('.wpclink_metadata_thumbnail_archive').hide();
                                jQuery( ".ing.longtree .image_cover" ).removeClass("selected");
                                jQuery( this ).addClass("selected");
                                if( jQuery(".archive.longtree .image_cover").hasClass("selected") && 
                                        jQuery(".ing.longtree .image_cover").hasClass("selected")){
                                            wpclink_metadata_compare_popup_func();
                                }
                                }
                                
                                
                            });
                            jQuery(".archive.longtree .image_cover").click(function(){
                                var wpclink_metadata_image_preview = jQuery( this ).children("img").attr( "src" );
                                jQuery('.wpclink_metadata_thumbnail_archive').css("background-image", "url('" + wpclink_metadata_image_preview + "')");
                                var compare_status_check = jQuery(".compare_button").data('status');
                                if(compare_status_check == 'inactive'){
                                    jQuery('.wpclink_metadata_thumbnail').hide();
                                    jQuery('.wpclink_metadata_thumbnail_archive').show();
                                    jQuery( ".longtree .image_cover" ).removeClass("selected");
                                    jQuery( this ).addClass("selected");
                                   
                                }else{
                                    jQuery('.wpclink_metadata_thumbnail').hide();
                                    jQuery('.wpclink_metadata_thumbnail_archive').hide();
                                    jQuery( ".archive.longtree .image_cover" ).removeClass("selected");
                                    jQuery( this ).addClass("selected");
                                    if( jQuery(".archive.longtree .image_cover").hasClass("selected") && 
                                        jQuery(".ing.longtree .image_cover").hasClass("selected")){
                                            wpclink_metadata_compare_popup_func();
                                     }
                                    
                                }
                                
            
                                
                                
                            });
                       
                        jQuery(".compare_button").click(function(){
                            var compare_status = jQuery(this).data('status');
                            if(compare_status == 'active'){
                                // Inactive Now
                                jQuery(this).data('status','inactive');
                                jQuery(this).removeClass('active');
                                jQuery(".wpclink_metadata_level_3_box").removeClass("compare");
                                jQuery( ".archive.longtree .image_cover" ).removeClass("selected");
                                jQuery('.wpclink_metadata_thumbnail').show();
                                jQuery('.wpclink_metadata_thumbnail_archive').hide();
                                jQuery(this).text('Choose comparision');
                            }else{
                                // Active Now
                                jQuery(this).data('status','active');
                                jQuery(this).addClass('active');
                                //jQuery(".wpclink_metadata_level_3_box").addClass("compare");
                                //jQuery('.wpclink_metadata_thumbnail').hide();
                                //jQuery('.wpclink_metadata_thumbnail_archive').hide();
                                jQuery(this).text('Choose comparision');
                            }
                        });
                    
                    jQuery(".longtree > li").each(function(){
                        var selected_li_c2pa = jQuery(this);
                        jQuery(this).children('.arrow_down').click(function(){
                           jQuery(this).toggleClass( "open" );
                               selected_li_c2pa.parent().children('.image_childs').slideToggle( "fast", function() {
                                   
                              });
                        });
                    });
                    
                    
                    jQuery(".longtree > ul > li").each(function(){
                        
                        jQuery(this).children('.arrow_down').click(function(){
                            
                            var selected_li_c2pa_mini = jQuery(this);
                           jQuery(this).toggleClass( "open" );
                               selected_li_c2pa_mini.parent().children('.image_childs').slideToggle( "fast", function() {
                                   
                              });
                        });
                    });
                    
                    jQuery(document).ready(function() {
                        jQuery(".c2pa_no_metadata").click(function(){
                            jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                            jQuery('.wpclink_metadata_level_3').html('<span class="no-info"><span class="infobar"></span>No content credentials for this file</span>');
                            jQuery('.wpclink_metadata_thumbnail').html('');
                            jQuery('.wpclink_metadata_thumbnail_archive').html('');
                            
                                // Hide
                                jQuery('.metadata_btn').hide();
                                 jQuery('.metadata_btn').removeClass("activated");
                                jQuery('.metadata_btn').data('metadata',0);
                            
                        if(typeof get_metadata_ajax_var.abort() !== 'undefined'){
                           get_metadata_ajax_var.abort();
                        }
                            
                        });
                        jQuery(".c2pa_image").click(function(){
                            
                            if(jQuery(this).data("attach-id") !== "undefined" && jQuery(this).data("type") !== "undefined") {
                            
                                // Load Metadata in popup
                        
                                var attach_id = jQuery(this).data("attach-id");
                                
                                
                                var cl_type = jQuery(this).data("type");
                                
                                if(jQuery(this).data("archive-id") !== "undefined"){
                                    var archive_id = jQuery(this).data("archive-id");
                                }else{
                                    var archive_id = '';
                                }
                                
                                  // Show
                                jQuery('.metadata_btn').show();
                                jQuery('.metadata_btn').removeClass("activated");
                                jQuery('.metadata_btn').data('metadata',0);
                                
                                
                                
                                 if(jQuery(this).data("box-num") !== "undefined"){
                                    var cl_box_number = jQuery(this).data("box-num");
                                }else{
                                    var cl_box_number = '';
                                }
                                var cl_type_val;
                                if(cl_type == 'archive'){
                                    cl_type_val = 'archive';
                                }else{
                                    cl_type_val = 'ingredients';
                                }
                        
                                              // This does the ajax request
                                get_metadata_ajax_var = jQuery.ajax({
                                    url: wpclink_vars.url,
                                    data: {
                                        'action':'wpclink_get_box_metadata_ajax',
                                        'attach_id' : attach_id,
                                        'archive_id' : archive_id,
                                        
                                        'cl_box_number' : cl_box_number,
                                        'cl_metadata_action' : cl_type_val,
                                        'nonce' : wpclink_vars.nonce
                                    },
                                    beforeSend: function() {
                                        jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                                        jQuery('.wpclink_metadata_level_3').html('<div class="loading-wrapper"><span class="cl_loader"></span></div>');
                                        jQuery('.wpclink_metadata_level_3 .loading-circle-quick').fadeIn();
                                        
                                    },
                                    success:function(data) {
                                        // This outputs the result of the ajax request
                        
                                        jQuery('.wpclink_metadata_level_3').html(data);
                                        jQuery(".c2pa_no_metadata").click(function(){
                                            jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                                            jQuery('.wpclink_metadata_level_3').html('<span class="no-info"><span class="infobar"></span>No content credentials for this file</span>');
                                            
                                                // Hide
                                                jQuery('.metadata_btn').hide();
                                                jQuery('.metadata_btn').removeClass("activated");
                                                jQuery('.metadata_btn').data('metadata',0);
                                            
                                            if(typeof get_metadata_ajax_var.abort() !== 'undefined'){
                                                get_metadata_ajax_var.abort();
                                            }
                                            
                                        });
                                        
                                          
                                     
                                    },
                                    error: function(errorThrown){
                                        console.log(errorThrown);
                                    }
                                });
                                               // METADATA LIST
                                               jQuery.ajax({
                                                url: wpclink_vars.url,
                                                data: {
                                                    'action':'wpclink_get_metadata_list_ajax',
                                                    'attach_id' : attach_id,
                                                    'archive_id' : archive_id,
                                                    
                                                    'cl_box_number': cl_box_number,
                                                    'cl_metadata_action' : cl_type_val,
                                                    'nonce' : wpclink_vars.nonce
                                                },
                                                beforeSend: function() {
                                                    // jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                                                    if(cl_type == 'archive'){
                                                        jQuery('.wpclink_metadata_thumbnail_archive').html('<div class="lds-dual-ring"></div>');
                                                        
                                                    }else{
                                                        jQuery('.wpclink_metadata_thumbnail').html('<div class="lds-dual-ring"></div>');
                                               
                                                    }
            
                                                    
                                                },
                                                success:function(data) {
                                                    // This outputs the result of the ajax request
                                                    
                                    
                                                    if(cl_type == 'archive'){
                                                        jQuery('.wpclink_metadata_thumbnail_archive').html(data);
                         jQuery('.minifest-boxes2 legend').off('click');                                  
                     jQuery(".minifest-boxes2 legend").click(function(){
                        jQuery(this).parent().toggleClass("collapse");
                         jQuery(this).parent().find("> legend").css("display","block");
                    });
                                                        
                    jQuery(".level-6.collapse > legend").css("display","block");
                                                        
                    wpclink_boxes_expand();
                                                        
                jQuery('.wpclink_metadata_thumbnail_archive .load-binary').off('click');
                    jQuery('.wpclink_metadata_thumbnail_archive .load-binary').on("click",function(){
                        var load_binary_key = jQuery(this).data('key');
                        var same_binary_btn = jQuery(this);
                        jQuery.ajax({
                            url: wpclink_vars.url,
                            data: {
                                'action':'wpclink_load_binary_metadata_ajax',
                                'target_metadata' : load_binary_key,
                                'attach_id' : attach_id,
                                'archive_id' : archive_id,
                                'cl_metadata_action' : cl_type_val,
                                'nonce' : wpclink_vars.nonce
                            },
                            beforeSend: function() {
                                if(same_binary_btn.text() == 'View'){
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html("Loading...");
                                }
                            },
                            success:function(data) {
                                same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                same_binary_btn.parent().find('.view-binary').html(data);
                                same_binary_btn.parent().find('.copyme').show();
                                
                            },
                            error: function(errorThrown){
                                console.log(errorThrown);
                            }
                            
                            
                        });
                        
                        if(same_binary_btn.text() == 'Hide'){
                                    same_binary_btn.parent().find('.view-binary').slideUp();
                                    same_binary_btn.text('View');
                                    same_binary_btn.parent().find('.copyme').hide();
                                }else{
                                    same_binary_btn.parent().find('.view-binary').slideDown();
                                    same_binary_btn.text('Hide');
                                    same_binary_btn.parent().find('.copyme').show();
                                }
                        
                         same_binary_btn.parent().find('.copyme').click(function(){
                            var copy_element = same_binary_btn.parent().find('.view-binary');
                            wpclink_myFunction(copy_element,same_binary_btn);
                        });
                        
                        
                    });
                                                        jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').click(function(){
                                                            var metadata_filter_group = jQuery(this).data("group");
                                                            var metadata_selected = jQuery(this).data("selected");
                                                            if(metadata_selected == '1'){
                                                                jQuery(".wpclink_metadata_thumbnail_archive .full-metadata "+"."+metadata_filter_group).fadeOut();
                                                                jQuery(this).data("selected",0);
                                                                jQuery(this).removeClass("selected");
                                                                
                                                                wpclink_metadata_sync_tags(jQuery(this));
                                                            }else{
                                                                jQuery(".wpclink_metadata_thumbnail_archive .full-metadata "+"."+metadata_filter_group).fadeIn();
                                                                jQuery(this).data("selected",1);
                                                                jQuery(this).addClass("selected");
                                                                
                                                                wpclink_metadata_sync_tags(jQuery(this));
                                                            }
                                                        });
                                                        jQuery('.wpclink_metadata_thumbnail_archive .metadata_action_select').click(function(){
                                                            jQuery(".wpclink_metadata_thumbnail_archive .metadata-field, .wpclink_metadata_thumbnail_archive .subheader").fadeIn();
                                                                jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').data("selected",1);
                                                                jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').addClass("selected");
                                                        });
                                                        jQuery('.wpclink_metadata_thumbnail_archive .metadata_action_clear').click(function(){
                                                            jQuery(".wpclink_metadata_thumbnail_archive .metadata-field, .wpclink_metadata_thumbnail_archive .subheader").fadeOut();
                                                                jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').data("selected",0);
                                                                jQuery('.wpclink_metadata_thumbnail_archive .metadata_filter_btn').removeClass("selected");
                                                        });
                                                    
                                                    }else{
                                                        jQuery('.wpclink_metadata_thumbnail').html(data);
                          jQuery('.minifest-boxes2 legend').off('click');                                  
                      jQuery(".minifest-boxes2 legend").click(function(){
                        jQuery(this).parent().toggleClass("collapse");
                          jQuery(this).parent().find("> legend").css("display","block");
                    });              
                                                        
                    jQuery(".level-6.collapse > legend").css("display","block");
                                                        
                    wpclink_boxes_expand();
                                                                                          
                    jQuery('.wpclink_metadata_thumbnail .load-binary').off('click');
                    jQuery('.wpclink_metadata_thumbnail .load-binary').on("click",function(){
                        var load_binary_key = jQuery(this).data('key');
                        var same_binary_btn = jQuery(this);
                        jQuery.ajax({
                            url: wpclink_vars.url,
                            data: {
                                'action':'wpclink_load_binary_metadata_ajax',
                                'target_metadata' : load_binary_key,
                                'attach_id' : attach_id,
                                'archive_id' : archive_id,
                                'cl_metadata_action' : cl_type_val,
                                'nonce' : wpclink_vars.nonce
                            },
                            beforeSend: function() {
                                if(same_binary_btn.text() == 'View'){
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html("Loading...");
                                }
                            },
                            success:function(data) {
                                same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                same_binary_btn.parent().find('.view-binary').html(data);
                                same_binary_btn.parent().find('.copyme').show();
                                
                            },
                            error: function(errorThrown){
                                console.log(errorThrown);
                            }
                        });
                        
                            if(same_binary_btn.text() == 'Hide'){
                                    same_binary_btn.parent().find('.view-binary').slideUp();
                                    same_binary_btn.text('View');
                                    same_binary_btn.parent().find('.copyme').hide();
                                }else{
                                    same_binary_btn.parent().find('.view-binary').slideDown();
                                    same_binary_btn.text('Hide');
                                    same_binary_btn.parent().find('.copyme').show();
                                }
                        
                         same_binary_btn.parent().find('.copyme').click(function(){
                            var copy_element = same_binary_btn.parent().find('.view-binary');
                            wpclink_myFunction(copy_element,same_binary_btn);
                        });
                    });
                               
                                                        jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').click(function(){
                                                            var metadata_filter_group = jQuery(this).data("group");
                                                            var metadata_selected = jQuery(this).data("selected");
                                                            if(metadata_selected == '1'){
                                                                jQuery(".wpclink_metadata_thumbnail .full-metadata "+"."+metadata_filter_group).fadeOut();
                                                                jQuery(this).data("selected",0);
                                                                jQuery(this).removeClass("selected");
                                                                
                                                                wpclink_metadata_sync_tags(jQuery(this));
                                                            }else{
                                                                jQuery(".wpclink_metadata_thumbnail .full-metadata "+"."+metadata_filter_group).fadeIn();
                                                                jQuery(this).data("selected",1);
                                                                jQuery(this).addClass("selected");
                                                                
                                                                wpclink_metadata_sync_tags(jQuery(this));
                                                            }
                                                        });
                                                        jQuery('.wpclink_metadata_thumbnail .metadata_action_select').click(function(){
                                                            jQuery(".wpclink_metadata_thumbnail .metadata-field, .wpclink_metadata_thumbnail .subheader").fadeIn();
                                                                jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').data("selected",1);
                                                                jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').addClass("selected");
                                                        });
                                                        jQuery('.wpclink_metadata_thumbnail .metadata_action_clear').click(function(){
                                                            jQuery(".wpclink_metadata_thumbnail .metadata-field, .wpclink_metadata_thumbnail .subheader").fadeOut();
                                                                jQuery('.wpclink_metadata_thumbnail .metadata_action_clear').data("selected",0);
                                                                jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').removeClass("selected");
                                                        });
                                                      
                                                    }
                                                   
                                                    var var_get_metadata = jQuery(".metadata_btn").data('metadata');
                                                    if(var_get_metadata == '1'){
                                                        jQuery(".action_filter_btn").show();
                                                        jQuery(".full-metadata-wrapper").show();
                                                       
                                                    }else{
                                                        jQuery(".action_filter_btn").hide();
                                                        jQuery(".full-metadata-wrapper").hide();
                                                        
                                                    }
                                                    
                                                     /* == See More == */
                                                    jQuery('.more-loader-btn').off('click');
                                                    jQuery('.more-loader-btn').on("click",function(){
                                                        var cl_list_loader_id = jQuery(this).data('loader-id');
                                                        jQuery('.full-metadata .'+cl_list_loader_id).slideToggle();
                                                        
                        if(jQuery(this).text() == 'View'){
                            jQuery(this).text('Hide');
                        }else if(jQuery(this).text() == 'See More +'){
                            jQuery(this).text('See Less -');
                        }else if(jQuery(this).text() == 'See Less -'){
                            jQuery(this).text('See More +');
                        }else{
                            jQuery(this).text('View');
                        }
                                                    });
                                                    
                                       
                                                      
                                                 
                                                },
                                                error: function(errorThrown){
                                                    console.log(errorThrown);
                                                }
                                            });
                        
                        
                            }
                        });
                        });
    
                    jQuery( ".wpclink-close-popup .close-icon, .close_btn").click(function(){
                        jQuery(".wpclink_metadata_popup_wrapper").hide();
                        jQuery(".wpclink_metadata_compare_popup").hide();
                        jQuery(".backto_popup").hide();
                        jQuery(".sidebyside_btn").hide();
                        
                        jQuery(".synctags").hide();
                        jQuery(".synctags").data('status',0);
                        jQuery(".synctags").removeClass("activated");
                        
                        jQuery(".syncscroll").hide();
                        jQuery(".ingredients-side, .archive-side").off("scroll");
                        jQuery(".syncscroll").data('status',0);
                        jQuery(".syncscroll").removeClass("activated");
                        jQuery(".ingredients-side, .archive-side").removeClass("slock");
                    });
                    jQuery( ".backto_popup").click(function(){
                        jQuery(".wpclink_metadata_compare_popup").fadeOut();
                        jQuery(".backto_popup").hide();
                        jQuery(".sidebyside_btn").hide();
                        
                        // Show
                        jQuery('.metadata_btn').show();
                        jQuery('.metadata_btn').removeClass("activated");
                        jQuery('.metadata_btn').data('metadata',0);
                        
                        jQuery(".synctags").hide();
                        jQuery(".synctags").data('status',0);
                        jQuery(".synctags").removeClass("activated");
                        
                        jQuery(".syncscroll").hide();
                        jQuery(".ingredients-side, .archive-side").off("scroll");
                        jQuery(".syncscroll").data('status',0);
                        jQuery(".syncscroll").removeClass("activated");
                        jQuery(".ingredients-side, .archive-side").removeClass("slock");
                         // Inactive Now
                         jQuery(".compare_button").data('status','inactive');
                         jQuery(".compare_button").removeClass('active');
                         jQuery(".wpclink_metadata_level_3_box").removeClass("compare");
                         jQuery( ".archive.longtree .image_cover" ).removeClass("selected");
                         jQuery( ".ing.longtree .image_cover" ).removeClass("selected");
                         jQuery('.wpclink_metadata_thumbnail').show();
                         jQuery('.wpclink_metadata_thumbnail_archive').hide();
                         jQuery(".compare_button").text('Choose comparision');
                         if(jQuery('.sidebyside_btn').data('side') == '1'){
                            jQuery( ".ingredients-side" ).resizable( "destroy" );
                            jQuery('.sidebyside_btn').data('side',0);
                         }
                         jQuery('.compare_center').removeClass("side_action");
                        
                        
                        
         
         
                         jQuery( ".ingredients-side" ).css("background-size","contain");
                         jQuery( ".archive-side" ).css("background-size","contain");
         
                         jQuery( ".ingredients-side" ).css("width","100%");
                         jQuery( ".archive-side" ).css("width","100%");
         
                         jQuery( ".ingredients-side" ).css("height","50%");
                         jQuery( ".archive-side" ).css("height","50%");
         
                         jQuery('.sidebyside_btn').removeClass("activated");
                         jQuery(".syncscroll").removeClass("activated");
                        
                         jQuery(".synctags").removeClass("activated");
                         jQuery(".wpclink_metadata_popup").removeClass("metadata_expand_popup");
                         jQuery('.c2pa_image').each(function(){
                            if(jQuery(this).data("attach-id") !== "undefined" && jQuery(this).data("type") !== "undefined") {
                                
                                // Load Metadata in popup
                                var image_attach_id = jQuery(this).data("attach-id");
                                var image_cl_type = jQuery(this).data("type");
                                
                                var cl_box_number = jQuery(this).data("box-num");
                               // alert(attach_id);
    
                                if(attach_id == image_attach_id && image_cl_type == 'ingredients'){
                                    
                                    jQuery('.longtree.ing > li > span.image_cover ').addClass("selected");
                                    //jQuery(this).parent().addClass("selected");
    
    
                                      // METADATA LIST
                                      jQuery.ajax({
                                        url: wpclink_vars.url,
                                        data: {
                                            'action':'wpclink_get_metadata_list_ajax',
                                            'attach_id' : image_attach_id,
                                            'archive_id' : image_attach_id,
                                            'cl_metadata_action' : image_cl_type,
                                            
                                            'cl_box_number': cl_box_number,
                                            'nonce' : wpclink_vars.nonce
                                        },
                                        beforeSend: function() {
                                            // jQuery('.wpclink_metadata_level_3').css('height',box_height+25+'px');
                                            
                                                jQuery('.wpclink_metadata_thumbnail').html('<div class="lds-dual-ring"></div>');
                                                                                    
                                        },
                                        success:function(data) {
                                            // This outputs the result of the ajax request
           
                                           
                                                jQuery('.wpclink_metadata_thumbnail').html(data);
                                            
                     jQuery('.minifest-boxes2 legend').off('click');                 
                     jQuery(".minifest-boxes2 legend").click(function(){
                        jQuery(this).parent().toggleClass("collapse");
                         jQuery(this).parent().find("> legend").css("display","block");
                    });
                                            
                    jQuery(".level-6.collapse > legend").css("display","block");
                                            
                    wpclink_boxes_expand();
                                            
                                            
                    jQuery('.wpclink_metadata_thumbnail .load-binary').off('click');
                    jQuery('.wpclink_metadata_thumbnail .load-binary').on("click",function(){
                        var load_binary_key = jQuery(this).data('key');
                        var same_binary_btn = jQuery(this);
                        jQuery.ajax({
                            url: wpclink_vars.url,
                            data: {
                                'action':'wpclink_load_binary_metadata_ajax',
                                'target_metadata' : load_binary_key,
                                'attach_id' : image_attach_id,
                                'archive_id' : image_attach_id,
                                'cl_metadata_action' : image_cl_type,
                                'nonce' : wpclink_vars.nonce
                            },
                            beforeSend: function() {
                                if(same_binary_btn.text() == 'View'){
                                    same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                    same_binary_btn.parent().find('.view-binary').html("Loading...");
                                }
                            },
                            success:function(data) {
                                same_binary_btn.parent().find('.view-binary').addClass('binary-loded');
                                same_binary_btn.parent().find('.view-binary').html(data);
                                same_binary_btn.parent().find('.copyme').show();
                                
                            },
                            error: function(errorThrown){
                                console.log(errorThrown);
                            }
                        });
                        
                        if(same_binary_btn.text() == 'Hide'){
                                    same_binary_btn.parent().find('.view-binary').slideUp();
                                    same_binary_btn.text('View');
                                    same_binary_btn.parent().find('.copyme').hide();
                                }else{
                                    same_binary_btn.parent().find('.view-binary').slideDown();
                                    same_binary_btn.text('Hide');
                                    same_binary_btn.parent().find('.copyme').show();
                                }
                        
                         same_binary_btn.parent().find('.copyme').click(function(){
                            var copy_element = same_binary_btn.parent().find('.view-binary');
                            wpclink_myFunction(copy_element,same_binary_btn);
                        });
                    });
                                                jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').click(function(){
                                                    var metadata_filter_group = jQuery(this).data("group");
                                                    var metadata_selected = jQuery(this).data("selected");
                                                    if(metadata_selected == '1'){
                                                        jQuery(".wpclink_metadata_thumbnail .full-metadata "+"."+metadata_filter_group).fadeOut();
                                                        jQuery(this).data("selected",0);
                                                        jQuery(this).removeClass("selected");
                                                        
                                                        wpclink_metadata_sync_tags(jQuery(this));
                                                    }else{
                                                        jQuery(".wpclink_metadata_thumbnail .full-metadata "+"."+metadata_filter_group).fadeIn();
                                                        jQuery(this).data("selected",1);
                                                        jQuery(this).addClass("selected");
                                                        
                                                        wpclink_metadata_sync_tags(jQuery(this));
                                                    }
                                                });
                                                
                                                jQuery('.wpclink_metadata_thumbnail .metadata_action_select').click(function(){
    
                                                    jQuery(".wpclink_metadata_thumbnail .metadata-field, .wpclink_metadata_thumbnail .subheader").fadeIn();
                                                        jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').data("selected",1);
                                                        jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').addClass("selected");
    
    
                                                });
    
    
                                                jQuery('.wpclink_metadata_thumbnail .metadata_action_clear').click(function(){
    
                                                    jQuery(".wpclink_metadata_thumbnail .metadata-field, .wpclink_metadata_thumbnail .subheader").fadeOut();
                                                        jQuery('.wpclink_metadata_thumbnail .metadata_action_clear').data("selected",0);
                                                        jQuery('.wpclink_metadata_thumbnail .metadata_filter_btn').removeClass("selected");
    
    
                                                });
    
                                            
                                           
    
                                            var var_get_metadata = jQuery(".metadata_btn").data('metadata');
                                            if(var_get_metadata == '1'){
                                                jQuery(".action_filter_btn").show();
                                                jQuery(".full-metadata-wrapper").show();
                                                
                                            }else{
                                                jQuery(".action_filter_btn").hide();
                                                jQuery(".full-metadata-wrapper").hide();
                                               
                                            }                                     
                                        },
                                        error: function(errorThrown){
                                            console.log(errorThrown);
                                        }
                                    });
                                    // GET SIDEBAR
                                    get_metadata_ajax_var = jQuery.ajax({
                                        url: wpclink_vars.url,
                                        data: {
                                            'action':'wpclink_get_box_metadata_ajax',
                                            'attach_id' : attach_id,
                                            'archive_id' : attach_id,
                                            'cl_box_number' : cl_box_number,
                                            'cl_metadata_action' : image_cl_type,
                                            'nonce' : wpclink_vars.nonce
                                        },
                                        beforeSend: function() {
                                           
    
                                            jQuery('.wpclink_metadata_level_3').html('<div class="loading-wrapper"><span class="cl_loader"></span></div>');
                                            jQuery('.wpclink_metadata_level_3 .loading-circle-quick').fadeIn();
    
                                            
                                        },
                                        success:function(data) {
                                            // This outputs the result of the ajax request
                            
                                            jQuery('.wpclink_metadata_level_3').html(data);
    
    
                                            jQuery(".c2pa_no_metadata").click(function(){
                                                
                                                jQuery('.wpclink_metadata_level_3').html('<span class="no-info"><span class="infobar"></span>No content credentials for this file</span>');
                                                
                                                // Hide
                                                jQuery('.metadata_btn').hide();
                                                 jQuery('.metadata_btn').removeClass("activated");
                                                jQuery('.metadata_btn').data('metadata',0);
                                                
                                                
                                               if(typeof get_metadata_ajax_var.abort() !== 'undefined'){
                                                   get_metadata_ajax_var.abort();
                                                }
                                             
                                                
                                            });
                                            
                                              
                                         
                                        },
                                        error: function(errorThrown){
                                            console.log(errorThrown);
                                        }
                                    });
                                    var wpclink_metadata_image_preview = jQuery( this ).attr( "src" );
                                    jQuery('.wpclink_metadata_thumbnail').css("background-image", "url('" + wpclink_metadata_image_preview + "')");
                                    return false;
    
                                }
    
                            }
    
                        });
    
                    });
                    jQuery('.sidebyside_btn').click(function(){
                        var var_sidebyside = jQuery(this).data('side');
                    
                        //alert(var_sidebyside);
                        
                            if(var_sidebyside == '1'){
                              
                    
                                jQuery( ".ingredients-side" ).resizable( "destroy" );
                                jQuery(this).data('side',0);
                                jQuery('.compare_center').removeClass("side_action");
                    
                    
                                jQuery( ".ingredients-side" ).css("background-size","contain");
                                jQuery( ".archive-side" ).css("background-size","contain");
                    
                                jQuery( ".ingredients-side" ).css("width","100%");
                                jQuery( ".archive-side" ).css("width","100%");
                    
                                jQuery( ".ingredients-side" ).css("height","50%");
                                jQuery( ".archive-side" ).css("height","50%");
                    
                                jQuery(this).removeClass("activated");
                                
                                 jQuery(".syncscroll").show();
                                
                                 jQuery(".synctags").show();
                    
                    
                            }else{
                                  /* Remove Metadata Action ============ */
                                  jQuery(".action_filter_btn").hide();
                                  jQuery(".full-metadata-wrapper").hide();
                                  jQuery(".metadata_btn").removeClass("activated");
                                  jQuery(".metadata_btn").data('metadata',0);
  
                                  jQuery(".wpclink_metadata_popup").removeClass("metadata_expand_popup");
                                  jQuery(".wpclink_metadata_compare_popup").removeClass("metadata_expand");
                                  jQuery('.wpclink_metadata_compare_popup .sidebar-left').animate({"left":"0%"}, 1000);
                                  jQuery('.wpclink_metadata_compare_popup .sidebar-right').animate({"right":"0%"}, 1000);
  
  
                                  /* ============== Remove Metadata Action */
                                // Resizable
                                jQuery( ".ingredients-side" ).resizable({
                                    containment: ".compare_center",
                                    minWidth: 1,
                                });
                    
                               var compare_box_width =  jQuery('.compare_center').width();
                               var compare_box_height = jQuery('.compare_center').height();
                                // if(jQuery(".metadata_btn").data("metadata") == '1'){
                                //     setTimeout(function(){
                                //         var compare_box_width_new =  jQuery('.compare_center').width();
                                //         jQuery( ".ingredients-side" ).css("background-size",compare_box_width_new+"px auto");
                                //         jQuery( ".archive-side" ).css("background-size",compare_box_width_new+"px auto");
                                //         jQuery( ".ingredients-side" ).css("width","50%");
                                //         jQuery( ".archive-side" ).css("width","100%");
                                //     },1000);
                                    
                                // }else{
                                    jQuery( ".ingredients-side" ).css("background-size",compare_box_width+"px auto");
                                    jQuery( ".archive-side" ).css("background-size",compare_box_width+"px auto");
                                    jQuery( ".ingredients-side" ).css("width","50%");
                                    jQuery( ".archive-side" ).css("width","100%");
                               // }
                    
                    
                                jQuery(this).data('side',1);
                                jQuery('.compare_center').addClass("side_action");
                                jQuery(this).addClass("activated");
                                
                                jQuery(".syncscroll").hide();
                                jQuery(".ingredients-side, .archive-side").off("scroll");
                                jQuery(".syncscroll").data('status',0);
                                jQuery(".syncscroll").removeClass("activated");
                                jQuery(".ingredients-side, .archive-side").removeClass("slock");
                                
                                jQuery(".synctags").data('status',0);
                                jQuery(".synctags").removeClass("activated");
                                jQuery(".synctags").hide();
                                
                                
                    
                    
                    
                            }
                    
                       
                        
                    
                    
                    });
                    jQuery('.syncscroll').click(function(){
                        
                        var var_scroll_status = jQuery(this).data('status');
                        
                        if(var_scroll_status == '1'){
                            
                            jQuery(".ingredients-side, .archive-side").off("scroll");
                            jQuery(this).data('status',0);
                            jQuery(this).removeClass("activated");
                            jQuery(".ingredients-side, .archive-side").removeClass("slock");
                        }else{
                            
                            var $divs = jQuery('.ingredients-side, .archive-side');
                            var sync = function(e){
                                var $other = $divs.not(this).off('scroll'), other = $other.get(0);
                                var percentage = this.scrollTop / (this.scrollHeight - this.offsetHeight);
                                other.scrollTop = percentage * (other.scrollHeight - other.offsetHeight);
                                setTimeout( function(){ $other.on('scroll', sync ); },10);
                            }
                            $divs.on( 'scroll', sync);
                            jQuery(this).data('status',1);
                            jQuery(this).addClass("activated");
                            jQuery(".ingredients-side, .archive-side").addClass("slock");
                            
                            
                             
                                                       
                        }
                    });
                    
                    
                    jQuery('.synctags').click(function(){
                        
                        var var_synctags_status = jQuery(this).data('status');
                        
                        if(var_synctags_status == '1'){                            
                            jQuery(this).data('status',0);
                            jQuery(this).removeClass("activated");
                        }else{
                            jQuery(this).data('status',1);
                            jQuery(this).addClass("activated");
                        }
                    });
                    
                    jQuery('.metadata_btn').click(function(){
                        var var_metadata = jQuery(this).data('metadata');
                    
                        //alert(var_sidebyside);
                        
                            if(var_metadata == '1'){
                                jQuery(".action_filter_btn").hide();
                                jQuery(".full-metadata-wrapper").hide();
                                jQuery(this).removeClass("activated");
                                jQuery(this).data('metadata',0);
                                jQuery(".wpclink_metadata_popup").removeClass("metadata_expand_popup");
                                jQuery(".wpclink_metadata_compare_popup").removeClass("metadata_expand");
                                jQuery('.wpclink_metadata_compare_popup .sidebar-left').animate({"left":"0%"}, 1000);
                                jQuery('.wpclink_metadata_compare_popup .sidebar-right').animate({"right":"0%"}, 1000);
                                jQuery('.compare_center .archive-side').animate({"width":"100%"},1000,function(){
                                    // Complete
                                    //jQuery(this).html("<h2>Welcome</h2>");
                                });
                                jQuery('.compare_center .archive-side').animate({"height":"50%"},1000);
                                jQuery('.compare_center .archive-side').animate({"right":"0"},1000);
                        
                                jQuery('.compare_center .ingredients-side').animate({"width":"100%"},1000,function(){
                                    // Complete
                                    //jQuery(this).html("<h2>Welcome</h2>");
                                });
                                jQuery('.compare_center .ingredients-side').animate({"height":"50%"},1000);
                                jQuery('.compare_center .ingredients-side').animate({"left":"0"},1000);
                               
                                jQuery(".syncscroll").hide();
                                jQuery(".synctags").hide();
                                
                            }else{
                                jQuery(".action_filter_btn").show();
                                jQuery(".full-metadata-wrapper").show();
                                jQuery(this).addClass("activated");
                                jQuery(this).data('metadata',1);
                                if(jQuery('.wpclink_metadata_compare_popup ').css('display') == 'none'){
                                    
                                    
                                  
                                }else{
                                    jQuery(".wpclink_metadata_popup").addClass("metadata_expand_popup");
                                    /* Remove Slide by Slide ========== */
                                    var var_sidebyside = jQuery('.sidebyside_btn').data('side');
                                    if(var_sidebyside == '1'){
                                    jQuery( ".ingredients-side" ).resizable( "destroy" );
                                    jQuery(".sidebyside_btn").data('side',0);
                                    jQuery('.compare_center').removeClass("side_action");
                        
                        
                                    jQuery( ".ingredients-side" ).css("background-size","contain");
                                    jQuery( ".archive-side" ).css("background-size","contain");
                        
                                    jQuery( ".ingredients-side" ).css("width","100%");
                                    jQuery( ".archive-side" ).css("width","100%");
                        
                                    jQuery( ".ingredients-side" ).css("height","50%");
                                    jQuery( ".archive-side" ).css("height","50%");
                        
                                    jQuery(".sidebyside_btn").removeClass("activated");
                                    /* =========== Remove Slide by Slide */
                                    }
                                    
                                     jQuery(".syncscroll").show();
                                     jQuery(".synctags").show();
                                }
                                jQuery(".wpclink_metadata_compare_popup").addClass("metadata_expand");
                                jQuery('.wpclink_metadata_compare_popup.metadata_expand .sidebar-left').animate({"left":"-25%"}, 1000);
                                jQuery('.wpclink_metadata_compare_popup.metadata_expand .sidebar-right').animate({"right":"-25%"}, 1000);
                                jQuery('.metadata_expand .compare_center .archive-side').animate({"width":"50%"},1000, function(){
                                    // Complete
                                    //jQuery(this).html("<h2>Welcome</h2>");
                                });
                                jQuery('.metadata_expand .compare_center .archive-side').animate({"height":"100%"},1000);
                                jQuery('.metadata_expand .compare_center .archive-side').animate({"right":"0"},1000);
                        
                                jQuery('.metadata_expand .compare_center .ingredients-side').animate({"width":"50%"},1000,function(){
                                    // Complete
                                    //jQuery(this).html("<h2>Welcome</h2>");
                                });
                                jQuery('.metadata_expand .compare_center .ingredients-side').animate({"height":"100%"},1000);
                                jQuery('.metadata_expand .compare_center .ingredients-side').animate({"left":"0"},1000);
                                
                                
                                
                                
var cl_active_manifiest_id_ing = jQuery('.active_manifiest_val.ing').val();
var cl_active_manifiest_id_archive = jQuery('.active_manifiest_val.archive').val();
// Box Number
var get_box_number_ing = jQuery(".ing.longtree").find('.selected').children('img').data('box-num');  
// Box Number
var get_archive_box_number = jQuery(".archive.longtree").find('.selected').children('img').data('box-num');
    
if(typeof get_box_number_ing != 'undefined' && typeof get_archive_box_number != 'undefined'){
      // Disable Sync Tags
    if(cl_active_manifiest_id_ing != get_box_number_ing || cl_active_manifiest_id_ing != get_archive_box_number ){
        jQuery('.synctags').data("selected",0);
        jQuery('.synctags').removeClass("selected");
        jQuery('.synctags').hide();
    }else{
        if(jQuery('.wpclink_metadata_compare_popup ').css('display') == 'none'){
        }else{  
            jQuery('.synctags').show();
        }
    }
}else if(typeof get_box_number_ing != 'undefined'){
    if(cl_active_manifiest_id_ing != get_box_number_ing  ){
        jQuery('.synctags').data("selected",0);
        jQuery('.synctags').removeClass("selected");
        jQuery('.synctags').hide();
    }else{
        if(jQuery('.wpclink_metadata_compare_popup ').css('display') == 'none'){
        }else{  
            jQuery('.synctags').show();
        }
        
    }
}
                    
                            }
                          
                    
                    });
                 
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });
    
    
        }
        
    });
    });
   
        
    
    
    jQuery.fn.wpclink_center = function () {
    this.css("position","absolute");
    this.css("top", ( jQuery(window).height() - this.height() ) / 2+jQuery(window).scrollTop() - jQuery(window).scrollTop() + "px");
    this.css("left", ( jQuery(window).width() - this.width() ) / 2+jQuery(window).scrollLeft() + "px");
    jQuery(".wpclink_metadata_compare_popup").css("position","absolute");
    jQuery(".wpclink_metadata_compare_popup").css("top", ( jQuery(window).height() - this.height() ) / 2+jQuery(window).scrollTop() - jQuery(window).scrollTop() + "px");
    jQuery(".wpclink_metadata_compare_popup").css("left", ( jQuery(window).width() - this.width() ) / 2+jQuery(window).scrollLeft() + "px");
    jQuery(".wpclink_metadata_compare_popup").css("width",this.width() + "px");
    jQuery(".wpclink_metadata_compare_popup").css("height",this.height() + "px");

    return this;
    }