<?php
/*
Plugin Name: eZanga Intext
Plugin URI: http://wordpress.org/extend/plugins/ezanga/ezanga-intext/
Description: This is a plugin will automatically add eZanga Intext scripts to websites.
Author: eZanga.com, Inc.
Version: 1.0
Author URI: http://www.eZanga.com/
*/

### DEFINE CONTANTS ###
//set debuging to false when you go live
define( "WP_DEBUG", false );
define( "EZANGA_INTEXT_VERSION", "1.0" );
define( "PATH", plugins_url( '',__FILE__ ) );

//install/uninstall function calls
register_activation_hook( __FILE__, "ta_install" );
register_uninstall_hook( __FILE__, "ta_uninstall" );

//tell the functions where to place the output on site and page
add_action( "admin_head", "intext_css" );
add_action( "admin_menu", "intext_page" );
add_action( "wp_footer", "intext_page_script_insert" );

function ta_install() {
    //add intext options
   	add_option( "intext_limit", 3 );
    add_option( "intext_content", "entry-content" );
}

function ta_uninstall() {
    //delete intext options
    delete_option( "intext_token" );
   	delete_option( "intext_limit" );
   	delete_option( "intext_content" );
   	delete_option( "intext_stopclass" );
   	delete_option( "intext_stopwords" );
    delete_option( "intext_rocket" );
}

function intext_page() {
    add_options_page( "eZanga InText Advertisments", "eZanga InText", "manage_options", "intext_admin", "intext_options_page" );
}

//display form so user can get/set/update InText options in the wp_admin
function intext_options_page(){
    //save settigns
    if ( isset( $_POST["intext_save"] ) ){
        //check nonce for security
        check_admin_referer( "ezanga_intext_save", "ezanga_intext_save_field" );
        if( isset( $_POST["intext_token"] )  ){
            $intext_token  = $_POST["intext_token"];  
            $invalid_token = validateIntextToken( $intext_token ) ? false : true;
        }
        $intext_limit = ( isset( $_POST["intext_limit"] ) ) ? $_POST["intext_limit"] : 3;
        //validate the content classes if they are set and remove invalid entries
        if ( isset( $_POST["intext_content"] ) ){
            $classNames                   = validateClassNames( $_POST["intext_content"] );
            $intext_content_class         = $classNames["valid"];
            $intext_content_class_removed = $classNames["removed"];
        } else {
            $intext_content_class = "";
        }
        //validate the stop classes if they are set and remove invalid entries
        if ( isset( $_POST["intext_stopclass"] ) ){
            $classNames               = validateClassNames( $_POST["intext_stopclass"] );
            $intext_stopclass         = $classNames["valid"];
            $intext_stopclass_removed = $classNames["removed"];
        } else {
            $intext_stopclass = "";
        }
        //validate the stop words if they are set and remove invalid entries
        if ( isset( $_POST["intext_stopwords"] ) ){
            $stopWords                = validateStopWords( $_POST["intext_stopwords"] );
            $intext_stopwords         = $stopWords["valid"];
            $intext_stopwords_removed = $stopWords["removed"];
        } else {
            $intext_stopwords = "";
        }
        //validate the stop words if they are set and remove invalid entries
        if ( isset( $_POST["intext_rocket"] ) ){
            error_log("iiii: ". $_POST["intext_rocket"] );
            $intext_rocket = $_POST["intext_rocket"];
        }
        
        update_option( "intext_token"    , strip_tags( $intext_token ) );
    	update_option( "intext_limit"    , absint( $intext_limit ) );
    	update_option( "intext_content"  , strip_tags( $intext_content_class ) );
    	update_option( "intext_stopclass", strip_tags( $intext_stopclass ) );
    	update_option( "intext_stopwords", strip_tags( $intext_stopwords ) );
        update_option( "intext_rocket"   , $intext_rocket );
        
        echo "<div id=\"message\" class=\"updated\">Your eZanga InText advertisement options have been saved</div>";
    }
    //load InText option values
    $intext_token         = get_option( "intext_token" );
    $intext_limit         = get_option( "intext_limit" );
    $intext_content_class = get_option( "intext_content" );
    $intext_stopclass     = get_option( "intext_stopclass" );
    $intext_stopwords     = get_option( "intext_stopwords" );
    $intext_rocket        = get_option( "intext_rocket" );
?>
<form method="POST" name="intext_form" id="intext_form">
    <fieldset>
        <img src="<?php echo PATH; ?>/logo-ezanga-main-400x200.png" width="350" height="175">
        <h2>eZanga InText Plug-in</h2>
        <p>This plugin will automatically connect to eZanga's network and place InText advertisements to your website.</p>
    </fieldset>
    <fieldset>
    <!-- == -->
        <label for="intext_token">Token: 
            <div class="help-btn">
                <p class="help-box">The token is used to identify your account.
                <br />
                If you do not know what your token is call your eZanga sales rep at <strong>888-439-2642</strong> or login to <a target="_blank" href="https://trafficadvisors.ezanga.com/">Traffic Advisors</a> to get it.</p>
            </div>
        </label>                                                
        <input type="text" name="intext_token" maxlength="20" value="<?php echo $intext_token; ?>" id="intext_token"/>
        <?php echo $invalid_token ? "<p class=\"token-msg\">Not a valid token.</p>" : ""; ?>
    <!-- == -->               
        <label for="intext_limit">
            Maximum Advertisements Per Page: 
            <div class="help-btn">
                <p class="help-box">Limit the <strong>maximum number of InText advertisements</strong> that will be returned on your web page.</p>
            </div>
        </label>
        <select name="intext_limit" id="intext_limit">
            <option value="1"<?php echo $intext_limit == 1 ? " selected=\"selected\"" : ""; ?>>1</option>
            <option value="2"<?php echo $intext_limit == 2 ? " selected=\"selected\"" : ""; ?>>2</option>
            <option value="3"<?php echo $intext_limit == 3 ? " selected=\"selected\"" : ""; ?>>3</option>
            <option value="4"<?php echo $intext_limit == 4 ? " selected=\"selected\"" : ""; ?>>4</option>
            <option value="5"<?php echo $intext_limit == 5 ? " selected=\"selected\"" : ""; ?>>5</option>
            <option value="6"<?php echo $intext_limit == 6 ? " selected=\"selected\"" : ""; ?>>6</option>
            <option value="7"<?php echo $intext_limit == 7 ? " selected=\"selected\"" : ""; ?>>7</option>
            <option value="8"<?php echo $intext_limit == 8 ? " selected=\"selected\"" : ""; ?>>8</option>
            <option value="9"<?php echo $intext_limit == 9 ? " selected=\"selected\"" : ""; ?>>9</option>
            <option value="10"<?php echo $intext_limit == 10 ? " selected=\"selected\"" : ""; ?>>10</option>
        </select>
    <!-- == -->
        <label for="intext_content">
            Content Classes: 
            <div class="help-btn">
                <p class="help-box">Content classes tell our keyword parser where to look for content on your web page. Enter the CSS class name of the element(s), or parent element(s), where you want to place InText advertisements. The class name: <strong>"entry-content" is the default class</strong> that Wordpress uses to define an article</strong>.<br /><br />Comma separate your classes. Only valid class names are allowed.</p>
            </div>
        </label>
        <input type="text" name="intext_content" value="<?php echo $intext_content_class; ?>" id="intext_content"/>
        <?php echo $intext_content_class_removed  ? "<p class=\"blood-red sml-txt\">The invalid class name".( strpos( $intext_content_class_removed, "," ) ? "s" : "" ).": ".$intext_content_class_removed." ha".( strpos( $intext_content_class_removed, "," ) ? "ve": "s" )." been removed.</p>" : ""; ?>
    <!-- == -->    
        <label for="intext_stopclass">
            Stop Classes: 
            <div class="help-btn">
                <p class="help-box">If InText advertisements are being displayed in an undesired area, such as a comments section, <strong>stop classes will tell our keyword parser where you do not want advertisements</strong> to be rendered on your page. Enter CSS class name of the element(s), or parent element(s), you would like to ignore.<br /><br />Comma separate your classes. Only valid class names are allowed.</p>
            </div>
        </label>
        <input type="text"  name="intext_stopclass" value="<?php echo $intext_stopclass; ?>" id="intext_stopclass"/>
        <?php echo $intext_stopclass_removed  ? "<p class=\"blood-red sml-txt\">The invalid class name".( strpos( $intext_stopclass_removed, "," ) ? "s" : "" ).": ".$intext_stopclass_removed." ha".( strpos( $intext_stopclass_removed, "," ) ? "ve": "s" )." been removed.</p>" : ""; ?>
    <!-- == -->   
        <label for="intext_stopwords">
            Stop Words:
            <div class="help-btn">
                <p class="help-box">If our keyword parser is returning words or phrases that you do not want in your results, <strong>add stop words to demote keywords from the results.</strong><br />Comma separate your words.</p>
            </div>
        </label>
        <input type="text" name="intext_stopwords" value="<?php echo $intext_stopwords; ?> " id="intext_stopwords"/>
        <?php echo $intext_stopwords_removed  ? "<p class=\"blood-red sml-txt\">The invalid stop name".( strpos( $intext_stopwords_removed, "," ) ? "s" : "" ).": ".$intext_stopwords_removed." ha".( strpos( $intext_stopwords_removed, "," ) ? "ve": "s" )." been removed.</p>" : ""; ?>
    <!-- == -->
        <label>
            Are you using CloudFlare's Rocket Loader on your site?
            <div class="help-btn">
                <p class="help-box">If you are running CloudFlare's Rocket Loader and your InText advertisements are not displaying properly, check this box.</p>
            </div>
        </label>
        <?php error_log("ddddd: ". $intext_rocket ); ?>
        <input type="radio" name="intext_rocket" value="yes" <?php echo $intext_rocket == "yes" ? " checked=\"checked\"" : "";  ?>/> Yes
        <br/>
        <input type="radio" name="intext_rocket" value="no" <?php echo $intext_rocket == "no" ? " checked=\"checked\"" : ""; ?>/> No
    <!-- == -->
    </fieldset>
    <fieldset>
        <input type="submit" class="button-primary" name="intext_save" value="Save InText Options" />
        <?php echo wp_nonce_field( "ezanga_intext_save","ezanga_intext_save_field" ); ?>
    </fieldset>
</form>
<script type="text/javascript">
    function intextFunction(){
        $('#intext_token').donetyping(function(){
            var thisToken = $(this).val();
            var thisMessage = $(this).siblings('.token-msg');
            /^int-[0-9]{16}$/.test(thisToken) || thisToken == '' ? thisMessage.hide() : thisMessage.show();
        }); 
    }
</script>
        
<?php     
}
//output the InText CSS in the wp_header
function intext_css(){
?>
    <style type="text/css"> 
    /* intext form */
        #intext_form{position:relative; clear:both; float:left;width: 350px; margin:10px;}
        #intext_form fieldset{padding:0 0 20px 0; border-bottom:1px dotted #ccc;}
        #intext_form fieldset:last-child{border-bottom:none;}
        #intext_form label{position:relative; display:block; width:calc(100% - 20px); margin-top:20px; font-weight:900;}
        #intext_form input{width:350px; margin:5px 0;}
        #intext_form input[type="radio"] { width:auto;}
        #intext_form input[type="submit"]{width:auto; margin-top:20px;}
        #intext_form p{margin:0;}
        #intext_form .sml-txt{font-size:12px;color:#666; font-weight:400;}
        #intext_form .blood-red{color:#bb0000;}
        #intext_form .help-btn{display:inline-block; position: absolute; top:0; right:-20px; width:28px; height:18px; background:url("<?php echo PATH ?>/help_btn.png") no-repeat 10px 0; cursor:pointer;}
        #intext_form .help-box{position:absolute; top:-10px; right:22px; z-index:1000000; display:none; width:250px; padding:10px; background-color:rgba(255,255,255,1); font-size:12px; font-weight:100; cursor:auto; -webkit-box-shadow:rgba(0,0,0,0.3) 0 1px 3px; -moz-box-shadow:rgba(0,0,0,0.3) 0 1px 3px; box-shadow:rgba(0,0,0,0.3) 0 1px 3px;}
        #intext_form .help-box strong{color:#459E00;}
        #intext_form .help-btn:hover .help-box,
        #intext_form .help-btn .help-box:hover{display:block;}
        #intext_form .token-msg{margin:0; color:#bb0000; font-size:12px;}
    </style>
<?php 
}  

//output the InText script in the wp_footer
function intext_page_script_insert(){
    //get InText options
    $intext_token     = get_option( "intext_token" );
    $intext_limit     = get_option( "intext_limit" );
    $intext_content   = get_option( "intext_content" );
    $intext_stopclass = get_option( "intext_stopclass" );
    $intext_stopwords = get_option( "intext_stopwords" );
    $intext_rocket    = get_option( "intext_rocket" );
    
    //if there is no token for either contextual or intext do not output anything on the page
    if ( $intext_token != "" ){
?>
    
        <!-- eZanga InText v.<?php echo EZANGA_INTEXT_VERSION ?> START -->
        <script <?php echo $intext_rocket == "yes" ? "data-cfasync=\"false\"" : "" ; ?> type="text/javascript">
        	var eztext_token     = "<?php echo $intext_token ?>";
        	var eztext_limit     = <?php echo $intext_limit ?>;
        	var eztext_content   = "<?php echo $intext_content ?>";
        	var eztext_stopclass = "<?php echo $intext_stopclass ?>";
        	var eztext_stopwords = "<?php echo $intext_stopwords ?>";
        </script>
        <script <?php echo $intext_rocket == "yes" ? "data-cfasync=\"false\"" : "" ; ?> type="text/javascript" src="http://cdn.ezanga.com/scripts/intext_advert.js"></script>     
        <!-- eZanga InText END --> 
          
<?php

    } else {
        echo "<!-- eZanga.com was here! -->";
    }
}

//VALIDATION FUNCTIONS
function validateIntextToken( $token ) {
    if ( $token ) {
        $token = trim( $token );
        if ( preg_match( "/^int-[0-9]{16}$/", $token ) ) {
            return true;
        } else {
            return false;
        }
    }
}

function validateClassNames( $classes ) {
    if ( $classes ) {
        $new_classes = array();
        $removed_classes = array();
        $classes = explode( ",", $classes );
        foreach ( $classes as $class ) {
            $class = trim( $class );
            if ( $class ) {
                //removes the period in front of the class name if any
                if ( substr( $class, 0, 1 ) == "." ) {
                    $class = substr( $class, 1 );
                }
                if ( preg_match( "/^[a-zA-Z]+[a-zA-Z0-9-_]*$/", $class ) && !in_array( $class, $new_classes ) ) {
                    $new_classes[] = $class;
                } else {
                    $removed_classes[] = "\"".$class."\"";
                }
            }
        }
        $return_classes = array();
        $return_classes["valid"]   = join( ", ", $new_classes );
        $return_classes["removed"] = join( ", ", $removed_classes );
        return $return_classes;
    }
}

function validateStopWords( $stopwords ) {
    if ( $stopwords ) {
        $new_stopwords = array();
        $removed_stopwords = array();
        $stopwords = explode( ",", $stopwords );
        foreach ( $stopwords as $stopword ) {
            $stopword = trim( $stopword );
            if ( $stopword ) {
                if ( preg_match( "/^[a-zA-Z0-9&:\+\- ]+$/", $stopword ) ) {
                    $new_stopwords[] = strtolower( $stopword );
                } else {
                    $removed_stopwords[] = "\"".strtolower( $stopword )."\"";
                }
            }
        };
        $words = array();
        $words["valid"]   = join( ", ", $new_stopwords );
        $words["removed"] = join( ", ", $removed_stopwords );
        return $words;
    }
}
?>