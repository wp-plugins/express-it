jQuery(function($) {
    /*
     * Sending AJAX request upon click on Link tag
     * 
     */
    //DEBUG
    console.log($.cookie('expressit-like'));
    
    /*
     * 
     * Jquery Cookies.
     * 
     * https://github.com/carhartl/jquery-cookie
     * 
     */
    
    function setLikeCookie( xx , isLike ) {
        /*
         * Setting Up a new Cookie
         * 
         * Cookie Storage Format : [post_id : post_like_status ] 1 => Like | 0 => Dislike
         * 
         *  isLike => true ( Like Button is Clicked ) | false => ( Dislike Button is Clicked ) 
         */
        if( isLike ) 
            var xyz = '['+xx.attr('value')+':1]' ;
        else 
            var xyz = '['+xx.attr('value')+':0]' ;
        $.cookie('expressit-like',xyz,{ expires: 30 , path: '/' } );
    }
    function updateLikeCookie( xx , isLike ) {
        /*
         * Updating Cookie
         * 
         */
	
        if($.cookie('expressit-like') !== undefined || $.cookie('expressit-like') !== '') {
            var flagy = false;var arr = 0;
            var xyz = $.cookie('expressit-like');                       // ; reading from cookies
            var data = xyz.split(',');                
            var parent_data = new Array();
            jQuery.each(data, function(index, value) {                  // ; Loop to check all values in cookies
                    var child_data = value.replace(/[\[\]']+/g,'');
                    var id_status = child_data.split(':');

                    if( id_status[0] === xx.attr('value')) {                // ; If Cookie value is equal to clicked post value

                        if( xx.text() === 'Unlike' || xx.text() === 'Undislike' ) {
                            if(index !== 0) {
                                if( isLike )
                                    parent_data.push(',['+xx.attr('value')+':1]');
                                else
                                    parent_data.push(',['+xx.attr('value')+':0]');
                            }
                            else {
                                if( isLike )
                                    parent_data.push('['+xx.attr('value')+':1]');
                                else
                                    parent_data.push(',['+xx.attr('value')+':0]');
                            }
                            flagy = true;
                        }
                    }
                    else {
                        if(index !== 0) {
                            parent_data.push(','+value);
                        }
                        else parent_data.push(value);
                    }	

                    arr = index;
            });
            /*
             * If post is first time clicked, its value is not already in cookie
             */
            if(!flagy && ( xx.text() == 'Unlike' || xx.text() == 'Undislike' ) ) {
                if(arr !== "") {
                    if( isLike )
                        parent_data.push(',['+xx.attr('value')+':1]');
                    else
                        parent_data.push(',['+xx.attr('value')+':0]');
                }
                else {
                    if( isLike )
                        parent_data.push('['+xx.attr('value')+':1]');
                    else
                        parent_data.push(',['+xx.attr('value')+':0]');
                }
            }					
        }
        /*
         * Writing Data back to Cookie
         */
        if(parent_data[0] === undefined) {
            $.removeCookie('expressit-like',{ path: '/' });
            //jQuery.exit;
        }
        else {
            var final_data = "";
            jQuery.each(parent_data, function(index, value) {
                final_data += value;
            });
            if(final_data === '') {
                //Destroying cookie
                $.removeCookie('expressit-like',{ path: '/' });
            }
            else 
            $.cookie('expressit-like',final_data,{ expires: 30 , path: '/' } );
        }									
	
    }
    
    
    $(function() {
        /*
         * Solution 2 : http://www.gajotres.net/prevent-jquery-multiple-event-triggering
         * 
         * To stop click event firing twice.
         * 
         * AJAX Call for Like Button
         */
        $('.aa').off("click").on("click" , function( event ) {
        /*
         * Some Important Variables
         * xx == the button clicked on
         * ixx == the button next to the clicked one
         * xxv == checks the text of like/unlike button
         */
        var xx = $(this);var ixx = xx.next();var xxv;
        if(xx.text() === 'Like') xxv = 1;
        else xxv = 0;
        
		// Checks the permission : : WP_NONCE
		var ajax_flag = false;
        $.ajax({
            method: "POST",
			url: ajaxurl.ajaxurl,
            data: { action: 'expressit_likes' , id: $(this).attr('value') , value: xxv , expressit_nonce: ajaxurl.expressit_nonce },
            beforeSend: function() {
                //alert("before send");
                /*
                 * Disabling the button after its clicked.
                 */
                xx.attr("disabled","disabled");
                ixx.attr("disabled","disabled");
            },
            success: function(response) {
				//alert(response);
				if( response != 0) {
				
					/*
					 * Removing the disabled Attr
					 * 
					 * Changing the Like text to Unlike
					 */
					xx.removeAttr("disabled","disabled");
					if(xxv === 1) {
						xx.text("Unlike");
						ixx.attr("disabled","disabled");
					}
					else {
						xx.text("Like");
						ixx.removeAttr("disabled","disabled");
					}
					/*
					 * Setting Cookies
					 * 
					 */
					if($.cookie('expressit-like') === undefined ) {
						setLikeCookie( xx , true );
					}
					else {
						updateLikeCookie( xx , true );
					}
				}
				else {
					xx.removeAttr("disabled","disabled");
					ixx.removeAttr("disabled","disabled");
					$('#errMsg').text("Permission Denied!");
					ajax_flag = true;
				}
            },
            error: function() {
                xx.removeAttr("disabled","disabled");
                ixx.removeAttr("disabled","disabled");
                $('#errMsg').text("Error! Please Refresh the Page.");
            }
        }).done(function( msg ) {
            /*
             * Finding the first span :: Incrementing Likes Counter
             */
			if( ! ajax_flag ) {
				var xxi = xx.parent().find("span:first");
				var num = parseInt(xxi.attr("value"));
				if( xxv === 1 ) num += 1;
				else num -= 1;
				xxi.text(num);
				xxi.attr("value",num);
			}
            });
        });
        /*
         * Solution 2 : http://www.gajotres.net/prevent-jquery-multiple-event-triggering
         * 
         * To stop click event firing twice.
         * 
         * AJAX Call for Dislike Button
         */
        $('.bb').off("click").on("click" , function( event ) {
        /*
         * Some Important Variables
         * xx == the button clicked on
         * ixx == the button next to the clicked one
         * xxv == checks the text of like/unlike button
         */
        
        // Checks the permission : : WP_NONCE
		var ajax_flag_dislike = false;
        var xx = $(this);var ixx = xx.prev();var xxv;
        if(xx.text() === 'Dislike') xxv = 1;
        else xxv = 0;
        $.ajax({
            method: "POST",
            url: ajaxurl.ajaxurl,
            data: { action: 'expressit_dislikes' , id: $(this).attr('value') , value: xxv , expressit_nonce: ajaxurl.expressit_nonce },
            beforeSend: function() {
                /*
                 * Disabling the button after its clicked.
                 */
                xx.attr("disabled","disabled");
                ixx.attr("disabled","disabled");
            },
            success: function(response) {
                if( response != 0 ) {
					/*
					 * Removing the disabled Attr
					 * 
					 * Changing the Dislike text to Unlike
					 */
					xx.removeAttr("disabled","disabled");
					if(xxv === 1) {
						xx.text("Undislike");
						ixx.attr("disabled","disabled");
					}
					else {
						xx.text("Dislike");
						ixx.removeAttr("disabled","disabled");
					}
					/*
					 * Setting Cookies
					 * 
					 */
					if($.cookie('expressit-like') === undefined ) {
						setLikeCookie( xx , false );
					}
					else {
						updateLikeCookie( xx , false );
					}
				}
				else {
					xx.removeAttr("disabled","disabled");
					ixx.removeAttr("disabled","disabled");
					$('#errMsg').text("Permission Denied!");
					ajax_flag_dislike = true;
				}
            },
            error: function() {
                xx.removeAttr("disabled","disabled");
                ixx.removeAttr("disabled","disabled");
                $('#errMsg').text("Error! Please Refresh the Page.");
            }
        }).done(function( msg ) {
            if ( ! ajax_flag_dislike ) {
				/*
				 * Finding the last span :: Incrementing Likes Counter
				 */
				var xxi = xx.parent().find("span:last");
				var num = parseInt(xxi.attr("value"));
				if( xxv === 1 ) num += 1;
				else num -= 1;
				xxi.text(num);
				xxi.attr("value",num);
			}
            });
        });
    
    });
    /*
     * Jquery Cookie Plugin 1.4.1
     * 
     * https://github.com/carhartl/jquery-cookie
     * 
     */
    /*
     * Removing Cookie
     */
    function removeCookie() {
        $.removeCookie('expressit-like');
    }
});

