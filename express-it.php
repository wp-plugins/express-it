<?php
/**
 * Plugin Name: Express It.
 * Plugin URI: http://ahsansajjad.com/
 * Description: Add like & dislike button to your post, let users express what they feel about your post. Likes/Dislikes will be saved with the help of cookies.
 * Version:  1.0.0
 * Author: Ahsan Sajjad
 * Author URI: http://ahsansajjad.com
 * Text Domain: express-it
 * Domain Path: 
 *
 */ 
 /*  Copyright 2015 Ahsan Sajjad (email: ahsan9991 at gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

 */
/*
 * Security : https://codex.wordpress.org/Writing_a_Plugin
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

//Required Files
require_once( plugin_dir_path( __FILE__ ) . '/inc/class-expressit-main.php' );
require_once( plugin_dir_path( __FILE__ ) . '/inc/class-expressit-data-setup.php' );


$a = new expressit_data_setup();
register_activation_hook( __FILE__, array( $a, 'data_setup' ) );

$express_it = new expressit_main();

add_action( 'wp_enqueue_scripts', array( $express_it, 'enqueue_scripts' ) );


$express_it ->show_settings();
$express_it ->add_options();
$express_it ->show_buttons();

function expressit_likes_process() {
	if( ! isset( $_POST['expressit_nonce'] ) ) :
		return;
	endif;
	
	global $wpdb;

if( ! empty( $_POST['id'] ) ) :
    $id = intval( $_POST['id'] );
    $val = intval( $_POST['value'] );
    $like_link = $wpdb ->get_row( "SELECT likes FROM {$wpdb->prefix}expressit_likes_counter WHERE post_id = '$id'" , ARRAY_N );
    /*
     * Security Check : Likes must not be smaller than 0
     */
    if ( $like_link[0] < 0 ) $like_link[0] = 0;
    
    if( $like_link == null ) :
        /*
         * NO Record Found! 
         * 
         * Create a new row and insert the data.
         * 
         */
        $wpdb ->insert (
                    'wp_expressit_likes_counter' ,
                    array(
                        'post_id' => $id ,
                        'likes' => 1
                    ),
                    array(
                        '%d' ,
                        '%d'
                    )
                );
    else :
        /*
         * 
         * Record Found!
         * 
         * Increment the Likes in the Row
         * 
         * If value == 1 then Increment Else Decrement
         */
        if( $val ==  1 ) : 
            $wpdb ->update (
                        'wp_expressit_likes_counter' ,
                        array(
                            'likes' => $like_link[0] + 1
                        ),
                        array ( 'post_id' => $id ),
                        array ( '%d' ),
                        array ( '%d' )
                    );
        else :
            $wpdb ->update (
                        'wp_expressit_likes_counter' ,
                        array(
                            'likes' => $like_link[0] - 1
                        ),
                        array ( 'post_id' => $id ),
                        array ( '%d' ),
                        array ( '%d' )
                    );
        endif;
    endif;
        
endif;
	die('1');
}
add_action('wp_ajax_expressit_likes', 'expressit_likes_process');

function expressit_dislikes_process() {
	if( ! isset( $_POST['expressit_nonce'] ) ) 
		return;
/*
 * 
 * Saving Dislikes data in Database
 * 
 */

global $wpdb;

if( ! empty( $_POST['id'] ) ) :
    $id = intval( $_POST['id'] );
    $val = intval( $_POST['value'] );
    $dislike_link = $wpdb ->get_row( "SELECT dislikes FROM {$wpdb->prefix}expressit_likes_counter WHERE post_id = '$id'" , ARRAY_N );
    
    /*
     * Security Check : Likes must not be smaller than 0
     */
    if ( $dislike_link[0] < 0 ) $dislike_link[0] = 0;
    
    if( $dislike_link == null ) :
        /*
         * NO Record Found! 
         * 
         * Create a new row and insert the data.
         * 
         */
        $wpdb ->insert (
                    'wp_expressit_likes_counter' ,
                    array(
                        'post_id' => $id ,
                        'dislikes' => 1
                    ),
                    array(
                        '%d' ,
                        '%d'
                    )
                );
    else :
        /*
         * 
         * Record Found!
         * 
         * Increment the dislikes in the Row
         * 
         * If val == 1 Increment Else Decrement
         */
        if( $val == 1 ) :
            $wpdb ->update (
                        'wp_expressit_likes_counter' ,
                        array(
                            'dislikes' => $dislike_link[0] + 1
                        ),
                        array ( 'post_id' => $id ),
                        array ( '%d' ),
                        array ( '%d' )
                    );
        else : 
            $wpdb ->update (
                        'wp_expressit_likes_counter' ,
                        array(
                            'dislikes' => $dislike_link[0] - 1
                        ),
                        array ( 'post_id' => $id ),
                        array ( '%d' ),
                        array ( '%d' )
                    );
        endif;
    endif;
        
endif;
die('1');
}
add_action('wp_ajax_expressit_dislikes', 'expressit_dislikes_process');
?>