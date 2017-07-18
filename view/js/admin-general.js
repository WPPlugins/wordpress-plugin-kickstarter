/*
  Copyright (C) <2011>  Vasyl Martyniuk <martyniuk.vasyl@gmail.com>

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

function mpks_object(){}

mpks_object.prototype.submitForm = function(){}

jQuery(document).ready(function(){
    
    mpksObj = new mpks_object();
    
    var icons = {
        header: "ui-icon-circle-arrow-e",
        headerSelected: "ui-icon-circle-arrow-s"
    };
    jQuery('.plugin-information').accordion({
        collapsible: true,
        header: 'h4',
        autoHeight: false,
        icons: icons,
        active: -1
    });
    
    jQuery('.change-style').bind('click', function(e){
        e.preventDefault();
        jQuery('div #style-select').show();
        jQuery(this).hide();
    });
    
    jQuery('#style-ok').bind('click', function(e){
        e.preventDefault();
        jQuery('#current-style-display').html(jQuery('#style option:selected').text());
        jQuery('div #style-select').hide();
        jQuery('.change-style').show();
    });
    jQuery('#style-cancel').bind('click', function(e){
        e.preventDefault();
        jQuery('div #style-select').hide();
        jQuery('.change-style').show();
    });
    
    jQuery('.plugin-information').show();
    
    jQuery('#save').bind('click', function(event){
        event.preventDefault();
        jQuery('#saving-ind').show();
        //check if plugin already exists
        var params = {
            'action' : 'mvbapk',
            'sub_action' : 'check_name',
            //'_ajax_nonce': wpaccessLocal.nonce,
            'name' : jQuery('input[name="mp[kickstarter][name]"]').val()
        }
        jQuery.post(ajaxurl, params, function(data){  
            if (data.status == 'success'){
                if (data.result != 0){
                    switch(data.result){
                        case 1:
                            jQuery("#dialog-rewrite-confirm p").append(data.message);
                            
                            jQuery( "#dialog-rewrite-confirm" ).dialog({
                                resizable: true,
                                modal: true,
                                buttons: {
                                    "Rewrite": function() {
                                        jQuery('#mp-kickstarter').submit();
                                    },
                                    Cancel: function() {
                                        jQuery( this ).dialog( "close" );
                                    }
                                }
                            });
                            break;
                            
                        case 2:
                            alert(data.message);
                            break;
                                
                        default:
                            break;
                    }
                }else{//submit a form
                    jQuery('#mp-kickstarter').submit();
                }
            }
        }, 'json');
    });
});