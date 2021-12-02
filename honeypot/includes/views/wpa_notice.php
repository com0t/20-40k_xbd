<?php
if ( ! defined( 'ABSPATH' ) ) exit; 
add_action('admin_notices', 'wpa_reviews_notice');
add_action('admin_notices', 'wpa_extended_notice');


if (isset($_GET['wpa_reviews_notice_hide']) == 1){
    update_option('wpa_reviews_notice_hide','yes');
}

if (isset($_GET['wpa_extended_notice_hide']) == 1){
    update_option('wpa_extended_notice_hide','yes');
}

function wpa_reviews_notice(){
    if (get_option('wpa_reviews_notice_hide') != 'yes'){
        $installedDate = strtotime(get_option('wpa_installed_date'));
        $todayDate     = time();        
        $installedDays = round(($todayDate - $installedDate)  / (60 * 60 * 24));
        $wpa_stats     = json_decode(get_option('wpa_stats'),true);
        $all_spam_blocked   = $wpa_stats['total']['all_time'];

        
        if ($installedDays > 30 && $all_spam_blocked > 30){
            echo '<div class="updated success" style="padding:10px; font-size:16px; line-height:1.6;color:#205225;">
                    Hey, WP Armour Anti Spam has blocked <strong>'.$all_spam_blocked.'</strong> spam submissions till date - that’s awesome! Could you please do us a BIG favor and give it a 5-star rating on WordPress ? Just to help us spread the word and boost our motivation.<br/><br/>

                    <ul style="padding-left:50px;list-style-type: square;">
                        <li><a href="https://wordpress.org/support/plugin/honeypot/reviews/?filter=5" target="_blank">Ok, you deserve it</a></li>
                        <li><a href="https://dineshkarki.com.np/contact" target="_blank">I still have problem !!</a></li>
                        <li><a href="?wpa_reviews_notice_hide=1">I already did</a></li>
                        <li><a href="?wpa_reviews_notice_hide=1">Hide this message</a></li>
                    </ul>

             </div>';
        }
    }
}

function wpa_extended_notice(){
    if (get_option('wpa_extended_notice_hide') != 'yes' && !is_plugin_active('wp-armour-extended/wp-armour-extended.php') ){
        $installedDate = strtotime(get_option('wpa_installed_date'));
        $todayDate     = time();        
        $installedDays = round(($todayDate - $installedDate)  / (60 * 60 * 24));
        $wpa_stats     = json_decode(get_option('wpa_stats'),true);
        $all_spam_blocked   = $wpa_stats['total']['all_time'];

        
        if ($installedDays > 90 && $all_spam_blocked > 1000){
            echo '<div class="updated success" style="padding:10px; font-size:16px; line-height:1.6;color:#205225;">
                    Hey, WP Armour has blocked <strong>'.$all_spam_blocked.'</strong> spam submissions till date - that’s awesome!<br/><br/>

                    Can you help us by purchasing our Extended Version ? This will helps up maintain and support the plugin in upcoming days and make it even better. Our Extended version starts from 19.99 USD and comes with lifetime license (No monhtly or yearly recurring) and No API calls. <br/><br/>

                    Also, it works with WooCommerce, Ajax and Multi page Gravity Forms, Easy Digital Downloads, QuForm, MC4WP: Mailchimp for WordPress and have Spammer blocking based on IP, Record Spam Submission and so on.

                    <ul style="padding-left:50px;list-style-type: square;">
                        <li><a href="https://dineshkarki.com.np/buy-wp-armour-extended" target="_blank">I will help</a></li>
                        <li><a href="?wpa_extended_notice_hide=1">Hide this message</a></li>
                    </ul>

             </div>';
        }
    }
}