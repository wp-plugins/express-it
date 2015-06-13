<?php
/*
 * Security : LINK
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
 * 
 * Setup Database Tables
 * 
 */
class expressit_data_setup {
    /*
     * 
     * Creating Tables
     * 
     */
    function data_setup() {
        global $wpdb;
        /*
         * First Table :: expressit_likes_counter
         */
        $table_name = $wpdb -> prefix . 'expressit_likes_counter';
        $charset_collate = $wpdb->get_charset_collate();
        
        if($wpdb->get_var("show tables like ' $wpdb->dbname '") != $wpdb->dbname) 
	{
            $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                post_id mediumint(9) NOT NULL,
                likes int(11) NOT NULL,
                dislikes int(11) NOT NULL,
                UNIQUE KEY id (id)
              ) $charset_collate;";
            
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
    }
}
?>