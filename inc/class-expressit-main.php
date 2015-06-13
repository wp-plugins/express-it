<?php

/*
 * Security : https://codex.wordpress.org/Writing_a_Plugin
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

/*
 * 
 * 
 * Main Class of Plugin
 * 
 * 
 */
class expressit_main {
    
    
    public function __construct() {
        ;
    }
     
    function show_settings() {
        
        if(is_admin()) {
    
            add_action('admin_menu', 'express_menu');

            function express_menu(){
                add_options_page( 'Express It | Settings Page', 'Express It', 'manage_options', 'express-it', 'express_init' );
            }
            
            function express_init(){
            ?>
                <!-- Layout of Options Page -->
                <div class="wrap">
                    <h2>Express It - Settings Page</h2>
                     
                    <!-- Usage Options -->
                    <form action="#" method="post">
                        <?php
                            /*
                             * Writing the option name : __express-it-usage-option
                             * Format : [ Administrator, Editor, Author, Contributor, Subscriber] :: [ 1 = 'Yes' | 0 = 'No' ]
                             */
                            $usage_update_data = array(
                                'Admin' => 0,
                                'Editor' => 0,
                                'Author' => 0,
                                'Contributor' => 0,
                                'Subscriber' => 0
                            );
                            /*
                             * Updating Data
                             */
                            if( isset( $_POST['submit'] ) ) : 
                                if( isset( $_POST['allow-admin'] ) ) :
                                    $usage_update_data['Admin'] = 1;
                                else:
                                    $usage_update_data['Admin'] = 0;
                                endif;

                                if( isset( $_POST['allow-editor'] ) ) :
                                    $usage_update_data['Editor'] = 1;
                                else:
                                    $usage_update_data['Editor'] = 0;
                                endif;

                                if( isset( $_POST['allow-author'] ) ) :
                                    $usage_update_data['Author'] = 1;
                                else:
                                    $usage_update_data['Author'] = 0;
                                endif;

                                if( isset( $_POST['allow-contributor'] ) ) :
                                    $usage_update_data['Contributor'] = 1;
                                else:
                                    $usage_update_data['Contributor'] = 0;
                                endif;

                                if( isset( $_POST['allow-subscriber'] ) ) :
                                    $usage_update_data['Subscriber'] = 1;
                                else:
                                    $usage_update_data['Subscriber'] = 0;
                                endif;
                                /*
                                 * Imploding all the data
                                 */
                                $usage_updated_data = implode( ':', $usage_update_data);
                                update_option( '__express-it-usage-option',$usage_updated_data );
                            endif;
                        
                            /*
                             * Checking the option name : __express-it-usage-option
                             */
                            if ( $usage_option_data = get_option( '__express-it-usage-option',false ) ) :
                                $usage_data_array = explode( ':', $usage_option_data);
                            endif;
                        ?>
                        <h3>Usage: <p>(Who can use the buttons?)</p></h3>
                        
                        <table>
                            <tr>
                                <td>
                                    <input type="checkbox" name="allow-admin" value="1" id="ttt" <?php if($usage_data_array[0] == 1) echo 'checked="checked"'; ?> /><label for="ttt">Administrator</label>
                                    <p class="description">Allow Administrator to like post.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="allow-editor" value="1" id="te" <?php if($usage_data_array[1] == 1) echo 'checked="checked"'; ?> /><label for="te">Editor</label>
                                    <p class="description">Allow Editor to like post.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="allow-author" value="1" id="ta" <?php if($usage_data_array[2] == 1) echo 'checked="checked"'; ?> /><label for="ta">Author</label>
                                    <p class="description">Allow Author to like post.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="allow-contributor" value="1" id="tc" <?php if($usage_data_array[3] == 1) echo 'checked="checked"'; ?> /><label for="tc">Contributor</label>
                                    <p class="description">Allow Contributor to like post.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="checkbox" name="allow-subscriber" value="1" id="ts" <?php if($usage_data_array[4] == 1) echo 'checked="checked"'; ?> /><label for="ts">Subscriber</label>
                                    <p class="description">Allow Subscriber to like post.</p>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <?php submit_button(); ?>
                                </td>
                            </tr>
                        </table>
                    </form>
                    
                    
                    <!-- Reset Options -->
                    <form action="#" method="post">
                        <?php
                            if( isset( $_POST['reset'] ) ) :
                                global $wpdb;
                                $wpdb->query("DELETE FROM {$wpdb->prefix}expressit_likes_counter");
                            endif;
                        ?>
                        
                        <h3>Reset:</h3>
                        <p>Do you want to reset the counter? <span class="description" style="color:red;">WARNING: You can't undo it.</span>  </p>
                        <?php $other = array ( 'onclick' => 'return confirm("Are you sure? YOU CANT UNDO THIS")');
                        submit_button( 'Reset Counter', 'delete', 'reset','' ,$other ); ?>
                    </form>
                    
                    
                    
                </div>



            <?php
            }

        }
        
        
    }
	/*
	*
	* Add Options in WP_OPTIONS Table	
	*
	*/
	function add_options() {
		
		/*
		* Check if Options Exist already :: Button Type 
		*/
		if( ! get_option( '__express-it-button-type' ,false ) ) {
                    /*
                    * Option Doesn't Exist Add it.
                    */
                    add_option( '__express-it-button-type' ,'1' ,'' ,'no' );
		}
		
		/*
		* Check if Options Exist already :: Usage Option 
		*/
		if( ! get_option( '__express-it-usage-option' ,false ) ) {
                    /*
                    * Option Doesn't Exist Add it. [ Administrator, Editor, Author, Contributor, Subscriber] :: [ 1 = 'Yes' | 0 = 'No' ]
                    */
                    add_option( '__express-it-usage-option' ,'1:1:1:1:1' ,'' ,'no' );
		
		}
	}
        /*
         * 
         * Showing Like Buttons After the Post 
         * 
         * Adding Filter to Wordpress
         * 
         */
        function show_buttons() {
            $a = new expressit_main();
            //if(is_)
            add_filter( 'the_content', array( $a, 'btn' ) );
        }
        /*
         * 
         * Function That Shows the Buttons
         * 
         * Code to Control the Buttons
         * 
         */
        function btn($content) {
            /*
             * Not showing Like/Dislike Button on simple Page.
             * 
             * Comment the following IF statement to disable it.
             */
            if( is_page( ) ) :
                return $content;
            endif;
            /*
             * Not showing Like/Dislike Button to selected Roles.
             */
            $selected_roles = get_option( '__express-it-usage-option' );
            $selected_roles_array = explode( ':', $selected_roles);
            /*
             * [ Administrator, Editor, Author, Contributor, Subscriber] :: [ 1 = 'Yes' | 0 = 'No' ]
             */
            $user = wp_get_current_user();
            if( $user->has_cap( 'edit_users' ) ) : // Administrator
                if( $selected_roles_array[0] == 0) :
                    return $content;
                endif;
            elseif ( $user->has_cap( 'edit_pages' ) ) : // Editor
                if( $selected_roles_array[1] == 0) :
                    return $content;
                endif;
            elseif ( $user->has_cap( 'publish_posts' ) ) : // Author
                if( $selected_roles_array[2] == 0) :
                    return $content;
                endif;
            elseif ( $user->has_cap( 'edit_posts' ) ) : // Contributor
                if( $selected_roles_array[3] == 0) :
                    return $content;
                endif;
            elseif ( $user->has_cap( 'read' ) ) : // Subscriber
                if( $selected_roles_array[4] == 0) :
                    return $content;
                endif;
            endif;
            ?>    
            <?php
            /*
             * Getting Data from Database.
             * 
             * On second time values will change through AJAX request.
             * 
             */
            global $wpdb;
            $link_like = $wpdb ->get_row( "SELECT likes, dislikes FROM {$wpdb->prefix}expressit_likes_counter WHERE post_id = '" . get_the_ID() ." ' " , ARRAY_N );
            /*
             * Setting Link_like to zero :: FOR NEW POST
             */
            if (! isset($link_like) ) :
                $link_like[0] = 0;
                $link_like[1] = 0;
            endif;
            /*
             * Checking Cookies
             * 
             * 1: Like is clicked | 0: Dislike is clicked
             * 
             * flag : true if cookie and post exist :: false if not
             */
            $flag = false;$id_status = 0;
            if( isset( $_COOKIE['expressit-like'] ) ) :
                $xxMain = explode( ',' , $_COOKIE['expressit-like'] );
                foreach ($xxMain as $value) {
                    $striped_value = str_replace( array( '[' , ']' ) , '' , $value );
                    $id_status = explode ( ':' , $striped_value );
                    if( $id_status[0] == get_the_ID() ) :
                        $flag = true;
                        break;
                    endif;
                }
                
            endif;
            $content .= '<div id="errMsg" style="color: red;"></div>';
            $content .= '<div><span value="' . $link_like[0] . '">' . $link_like[0] . '</span> Likes . <span value="' . $link_like[1] . '">' . $link_like[1] . '</span> Dislikes <br />';
            
            if ( $link_like[0] == 0 && $link_like[1] == 0 ) :
                $content .= '<button class="aa" value="'. get_the_ID() .'">Like</button> . <button class="bb" value="'. get_the_ID() .'">Dislike</button>';
            elseif ( $flag && $id_status[1] == 1 ) :
                $content .= '<button class="aa" value="'. get_the_ID() .'">Unlike</button> . <button class="bb" value="'. get_the_ID() .'" disabled="disabled" >Dislike</button>';
            elseif ( $flag && $id_status[1] == 0 ) :
                $content .= '<button class="aa" value="'. get_the_ID() .'" disabled="disabled">Like</button> . <button class="bb" value="'. get_the_ID() .'">Undislike</button>';
            else :
                $content .= '<button class="aa" value="'. get_the_ID() .'">Like</button> . <button class="bb" value="'. get_the_ID() .'">Dislike</button>';
            endif;
            
            return $content;
        }
        function enqueue_scripts() {
            wp_enqueue_script( 'expressit-jquery-cookie' , plugins_url( '../js/jquery.cookie.js ', __FILE__ )  , array('jquery') , '1.0.0' , true );
            wp_enqueue_script( 'expressit-ajax-it' , plugins_url( '../js/expressit-ajax.js', __FILE__ ) , array('jquery') , '1.0.0' , true );
            /*
             * Data for jQuery Variables
             */
            $site_parameter = array (
                'plugin_dir' => WP_PLUGIN_URL
            );
			$ajaxurl = array ( 
				'ajaxurl' => admin_url( 'admin-ajax.php'),
				'expressit_nonce' => wp_create_nonce('expressit-nonce')
			);
            /*
             * Localizing Variables for jQuery
             */
            wp_register_script( 'expressit-localize-script' , plugins_url( '../js/expressit-ajax.js', __FILE__) );
            
            //wp_localize_script( 'expressit-localize-script' , 'SiteParameter' , $site_parameter );
            wp_localize_script( 'expressit-localize-script' , 'ajaxurl' , $ajaxurl );
			
            wp_enqueue_script( 'expressit-localize-script' );
            
        }
}
?>