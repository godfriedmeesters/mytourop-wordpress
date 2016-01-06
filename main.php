<?php
/*
Plugin Name: MyTourOp Client
Plugin URI: http://www.mytourop.com
Description: Intended for Tour Operators, the MyTourOp Client connects with MyTourOp.com to manage enquiries from tourists.
Version: 1.0.1
Author: Godfried Meesters	
Author URI: http://www.itgm.be
*/


// Clean up wp_head
// Remove Really simple discovery link
remove_action('wp_head', 'rsd_link');
// Remove Windows Live Writer link
remove_action('wp_head', 'wlwmanifest_link');
// Remove the version number
remove_action('wp_head', 'wp_generator');

// Remove curly quotes
remove_filter('the_content', 'wptexturize');
remove_filter('comment_text', 'wptexturize');

// Allow HTML in user profiles
//remove_filter('pre_user_description', 'wp_filter_kses');

add_shortcode('mto_bookable', 'bookable_shortcode');
add_shortcode('mto_contactform', 'contactform_shortcode');

add_shortcode('mto_testimonials', 'testimonials_shortcode');

add_shortcode('mto_paymentform', 'paymentform_shortcode');

add_shortcode('mto_customtour', 'customtour_shortcode');

add_shortcode('mto_testimonialform', 'testimonialform_shortcode');


function pre_process_shortcode($content)
{
    global $shortcode_tags;

    // Backup current registered shortcodes and clear them all out
    $orig_shortcode_tags = $shortcode_tags;
    $shortcode_tags = array();

    add_shortcode('bookable', 'bookable_shortcode');

    // Do the shortcode (only the one above is registered)
    $content = do_shortcode($content);

    // Put the original shortcodes back
    $shortcode_tags = $orig_shortcode_tags;

    return $content;
}

//add_filter('the_content', 'pre_process_shortcode', 6);

function mytourop_register_settings()
{
    add_option('mytourop_admin_url', 'https://admin.mytourop.com');
    add_option('mytourop_organisation_id', '1');
    register_setting('default', 'mytourop_admin_url');
    register_setting('default', 'mytourop_organisation_id');
}

add_action('admin_init', 'mytourop_register_settings');

function mytourop_register_options_page()
{
    add_options_page('Page title', 'MyTourOp', 'manage_options', 'mytourop-options', 'mytourop_options_page');
}

add_action('admin_menu', 'mytourop_register_options_page');

function mytourop_options_page()
{
    ?>

    <div class="wrap">
        <?php screen_icon(); ?>
        <h2>MyTourOp Client Settings</h2>

        <form method="post" action="options.php">
            <?php settings_fields('default'); ?>

            <p>Change your settings here to connect your Wordpress website with the MyTourOp backend.</p>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><label for="mytourop_admin_url">Admin Url</label></th>
                    <td><input type="text" id="mytourop_admin_url" size="35" name="mytourop_admin_url"
                               value="<?php echo get_option('mytourop_admin_url'); ?>"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row"><label for="mytourop_organisation_id">Organisation Id</label></th>
                    <td><input size="5" type="text" id="mytourop_organisation_id" name="mytourop_organisation_id"
                               value="<?php echo get_option('mytourop_organisation_id'); ?>"/></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function decode($input)
{
    $input = str_replace('&nbsp;', ' ', $input);
    return htmlspecialchars_decode($input);
}

////scripts

add_action('template_redirect', 'them_js_head_load');

function them_js_head_load()
{
    wp_enqueue_script('tabber', plugins_url('/mytourop/js/wizard/jquery.history.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker'), '1.0');

    wp_enqueue_script('tabber2', plugins_url('/mytourop/js/wizard/jquery.form.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker'), '1.0');

    wp_enqueue_script('tabber3', plugins_url('/mytourop/js/wizard/jquery.validate.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker'), '1.0');

    wp_enqueue_script('tabber4', plugins_url('/mytourop/js/wizard/bbq.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker'), '1.0');

    wp_enqueue_script('tabber5', plugins_url('/mytourop/js/wizard/jquery.form.wizard-3.0.0.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'jquery-ui-position'), '1.0');
    wp_enqueue_script('tabber5', plugins_url('/mytourop/js/jquery-form.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'jquery-ui-position'), '1.0');

    wp_enqueue_script('tabber5', plugins_url('/mytourop/js/jquery-validate.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'jquery-ui-position'), '1.0');


    wp_enqueue_script('tabber7', plugins_url('/mytourop/js/jAlert-v2-min.js'), array('jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-ui-datepicker', 'jquery-ui-position'), '1.0');


    wp_enqueue_style('wp-jquery-ui-timepicker');
    //wp_enqueue_style( 'jquery.ui.theme', plugins_url( '/css/jquery-ui-1.8.4.custom.css', __FILE__ ) );

    wp_enqueue_style('tourpluginstyle', plugins_url('/css/tour-plugin.css', __FILE__));


    wp_enqueue_script('blockui', plugins_url('/mytourop/js/jquery.blockUI.js'), array('jquery'), '1.0');

    wp_enqueue_script('rating', plugins_url('/mytourop/jquery.rating.js'), array('jquery'), '1.0');
    wp_enqueue_style('ratingstyle', plugins_url('/jquery.rating.css', __FILE__));

    wp_enqueue_style('jquery.modal', plugins_url('/css/jAlert-v2-min.css', __FILE__));

}

function testimonials_shortcode()
{
    // $data = file_get_contents("https://admin.mytourop.com/testimonials/getTestimonials/" . get_option('mytourop_organisation_id'));


    $data = file_get_contents("http://localhost/testimonials/getTestimonials/10");


    $json = json_decode($data, true);


    echo "<dl>";

    foreach ($json as $key => $value) {
        echo "<dt>" . $key . " said: </dt>" . "<dd>" . $value . "</dd>";
        echo "<hr/>";
    }

    echo "</dl>";
}

function bookable_shortcode($atts, $content = null)
{
    ob_start();

    ?>

    <div id="SimienDialog" title="Tour Enquireing" style="margin-bottom: 2em">
        <form id="EnquiryForm" method="post" action="<?php echo get_option('mytourop_admin_url'); ?>/enquiries/enquire">

            <input type="hidden" name="utma" value="<?php echo $_COOKIE['__utma'] ?>"/>
            <input type="hidden" name="utmb" value="<?php echo $_COOKIE['__utmb'] ?>"/>
            <input type="hidden" name="utmc" value="<?php echo $_COOKIE['__utmc'] ?>"/>
            <input type="hidden" name="utmz" value="<?php echo $_COOKIE['__utmz'] ?>"/>


            <fieldset id="EnquiryFirstStep" class="step">
                <legend> Step 1: Your Tour Details</legend>

                <ol style="list-style:none">
                    <li>
                        <label for="SimienDepartureDate">I wish to start my tour on:</label>
                        <input type="text" name="data[Enquiry][start_date]" id="SimienDepartureDate" class="required"/>
                    </li>
                    <li>
                        <label for="SimienPersons">Number of people in group: </label>
                        <select name="data[Enquiry][nr_persons]" id="SimienPersons">
                            <option> 1 person</option>
                            <option selected="selected"> 2 persons</option>
                            <option> 3 persons</option>
                            <option> 4 persons</option>
                            <option> 5 persons</option>

                            <option> 6 persons</option>
                            <option> 7 persons</option>
                            <option> 8 persons</option>
                        </select>
                    </li>
                </ol>
            </fieldset>
            <fieldset id="EnquirySecondStep" class="step">
                <legend>Step 2: Your Special Requests</legend>
                <ol style="list-style:none">
                    <li>
                        <label for="SimienSpecialRequests">Additional requests:</label>
                        <textarea name="data[Enquiry][remarks]" cols="50" rows="8"
                                  id="SimienSpecialRequests"> </textarea>
                    </li>
                </ol>
            </fieldset>
            <fieldset id="EnquiryThirdStep" class="step submit_step">
                <legend>Step 3: Your Contact Details</legend>
                <ol style="list-style:none">
                    <li></li>
                    <li>
                        <label for="SimienFirstName">First Name:</label>
                        <input type="text" name="data[Enquiry][first_name]" id="SimienFirstName"
                               class="textbox, required"/>
                        <label for="SimienFirstName" class="error" style="float:none; color:#F00;padding-left:1em;">Required.</label>
                    </li>
                    <li>
                        <label for="SimienLastName">Last Name:</label>
                        <input type="text" name="data[Enquiry][last_name]" id="SimienLastName"
                               class="textbox, required"/>
                        <label for="SimienLastName" class="error" style="float:none; color:#F00;padding-left:1em;">Required.</label>
                    </li>
                    <li>
                        <label for="SimienEmail">E-Mail Adress:</label>
                        <input name="data[Enquiry][email]" type="text" class="textbox, required email" id="SimienEmail"
                        />
                        <label for="SimienEmail" class="error" style="float:none; color:#F00;padding-left:1em;">
                            Required. </label>
                    </li>


                </ol>
            </fieldset>

            <input type="hidden" name="data[Enquiry][ip]" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>"/>

            <input type="hidden" name="data[Enquiry][organisation_id]"
                   value="<?php echo get_option('mytourop_organisation_id'); ?>"/>

            <input type="reset" value="Reset" style="font-size:x-large; font-weight:bold"/>
            <input type="submit" value="Submit" style="font-size:x-large; font-weight:bold"/>
        </form>
    </div>

    <div id="content1"><?php echo $content ?></div>

    <script type="text/javascript">
        jQuery(document).ready(function () {

            jQuery("#EnquiryForm").formwizard({
                    formPluginEnabled: true,
                    validationEnabled: true,
                    historyEnabled: true,
                    focusFirstInput: true,
                    formOptions: {
                        beforeSerialize: function ($form, options) {
                            jQuery("#SimienSpecialRequests").val(jQuery("#SimienSpecialRequests").val() + "---- Enquiring for:  " + jQuery("#content1").text());
                        },

                        error: handleError,

                        success: function () {
                            alert('Your enquiry has been registered succesfully! We will contact you within the next 48 hours.');


                            //window.location = "/";

                            jQuery("#SimienDialog").dialog('close');

                        },
                        resetForm: true
                    }
                }
            );

//date picker 
            jQuery("#SimienDepartureDate").datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0,
                showOn: 'button',
                buttonImage: "<?php echo plugins_url('/mytourop/img/calendar.bmp') ?>",
                buttonImageOnly: true
            });


// opener

            jQuery('#opener7').click(function () {
                jQuery('#SimienDialog').dialog('option', 'title', "");
                jQuery('#EnquiryForm').formwizard('show', 'EnquiryFirstStep');
                jQuery('#SimienDialog').data('SimienPersons', '<option>1 Person </option><option>2 Persons </option><option>3 Persons </option><option>4 Persons </option><option>5 Persons </option><option>6 Persons </option><option>7 Persons </option><option>8 Persons </option><option>9 Persons </option>').dialog('open');
                return false;
            });


        });


        function handleError() {
            jQuery("#captchaError").show();
        }

    </script>


    <?php

    $variable = ob_get_clean();

    return $variable;

}


/**************************
 *
 * CONTACT FORM SHORTCODE
 ***************************/

function contactform_shortcode($atts, $content = null)
{

    ?>

    <div id="contactform">
        <form id="form1" action="<?php echo get_option('mytourop_admin_url'); ?>/enquiries/enquire" method="post"
        >

            <input type="hidden" name="utma" value="<?php echo $_COOKIE['__utma'] ?>"/>

            <input type="hidden" name="utmb" value="<?php echo $_COOKIE['__utmb'] ?>"/>
            <input type="hidden" name="utmc" value="<?php echo $_COOKIE['__utmc'] ?>"/>

            <input type="hidden" name="utmz" value="<?php echo $_COOKIE['__utmz'] ?>"/>


            <p>Your First Name (required)<br>
                <span><input type="text" name="data[Enquiry][first_name]"
                             value="" class="required" size="40"></span>
            </p>

            <p>Your Last Name (required)<br>
                <span><input type="text" name="data[Enquiry][last_name]"
                             value="" class="required" size="40"></span>
            </p>

            <div style="display:none">
                <p>Your Email (required)<br>
                    <input type="text" name="email" value=""/>

                </p>
            </div>

            <p>Your Email (required)<br>
                <span><input type="text" name="data[Enquiry][email]" value=""
                             class="required email" size="40"></span>
            </p>




            <p>Your Message<br>
                <span><textarea name="data[Enquiry][remarks]"
                                class="required" cols="40"
                                rows="10"></textarea></span></p>


            <p class="antispam">Website: <input type="text" name="url"/></p>


            <p><input id="submitbutton" type="submit" value="Submit" style="font-weight:bold; font-size:14px;">
            </p>


            <input type="hidden" name="data[Enquiry][ip]" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>"/>

            <input type="hidden" name="data[Enquiry][organisation_id]"
                   value="<?php echo get_option('mytourop_organisation_id'); ?>"/>

        </form>
    </div>


    <script type="text/javascript">


        jQuery(document).ready(function () {

            jQuery('#submitbutton').attr('disabled', false);
            jQuery(this).attr("title", "Contact Us");


            var options = {
                error: handleError, beforeSubmit: validate, resetForm: true, success: function () {

                    jQuery.unblockUI();

                    jQuery('#submitbutton').attr('disabled', false);


                }
            };

            jQuery('#form1').ajaxForm(options);

            jQuery("#form1").validate();

            jQuery("input:text:visible:first").focus();

            jQuery("#accordion").accordion({

                navigation: true
            });

        });


        function handleError() {
            jQuery("#captchaError").show();
        }


        function validate(formData, jqForm, options) {
            if (jQuery("#form1").valid()) {
                jQuery('#submitbutton').attr('disabled', true);

                jQuery.blockUI({message: 'Sending Enquiry...'});

            }


            return jQuery("#form1").valid();
        }


    </script>


    <?php
}

/***************************************
 * TESTIMONIAL FORM
 *****************************************/

function testimonialform_shortcode($atts, $content = null)
{


    if (!(isset($_REQUEST['n']) && isset($_REQUEST['tid']))) {
        echo "<h3>INVALID REQUEST. PLEASE CONTACT CUSTOMER SERVICE.</h3>";
        return;
    }
    ?>


    <div class="wpcf7" id="wpcf7-f14-p15-o1">
        <form id="form1" action="<?php echo get_option('mytourop_admin_url'); ?>/testimonials/testify" method="post"
              class="wpcf7-form">


            <input type="hidden" name="data[Testimonial][id]" value="<?php echo base64_decode($_REQUEST['tid']); ?>"/>

            <?php echo base64_decode($_REQUEST['n']) ?>, please give a general rating of your tour: <br/>

            <input name="data[Testimonial][rating]" type="radio" class="auto-submit-star" value="1"/>
            <input name="data[Testimonial][rating]" type="radio" class="auto-submit-star" value="2"/>
            <input name="data[Testimonial][rating]" type="radio" class="auto-submit-star" value="3"/>
            <input name="data[Testimonial][rating]" type="radio" class="auto-submit-star" value="4"/>
            <input name="data[Testimonial][rating]" type="radio" class="auto-submit-star" value="5"/>


            <div style="margin-top:40px;margin-left:0px;">

                Please enter your Testimonial:<br/>
                <span class="wpcf7-form-control-wrap your-message"><textarea name="data[Testimonial][text]"
                                                                             class="required" cols="40"
                                                                             rows="10"></textarea></span></div>
            <p><input id="testimonialSubmit" type="submit" value="Send" class="wpcf7-form-control  wpcf7-submit"></p>


        </form>
    </div>


    <script type="text/javascript">


        jQuery(document).ready(function () {

            jQuery('.auto-submit-star').rating();


            jQuery(this).attr("title", "Enter your testimonial");


            var options = {
                beforeSubmit: validate, resetForm: true, success: function () {

                    alert("Your testimonial has been sent successfully. Thanks very much and we hope to see you again soon!");


                    jQuery('.auto-submit-star').rating('drain');

                    jQuery('#testimonialSubmit').removeAttr('disabled');

                    //window.location = "/";

                }
            };

            jQuery('#form1').ajaxForm(options);

            jQuery("#form1").validate();

            jQuery("input:text:visible:first").focus();


        });


        function validate(formData, jqForm, options) {
            if (jQuery("#form1").valid()) {
                jQuery('#testimonialSubmit').attr('disabled', 'disabled');

            }
            else {
                jQuery('#testimonialSubmit').removeAttr('disabled');
            }


            return jQuery("#form1").valid();
        }

        function handleError() {
            jQuery("#captchaError").show();
        }


    </script>


    <?php


}


/************************************
 * CUSTOM TOURS
 ********************************************/

function customtour_shortcode($atts, $content = null)
{


    if (is_array($atts) && array_key_exists('places', $atts)) {
        $places = explode(", ", $atts['places']);


    }

    ?>


    <div id="tour-ethiopia">


        <form id="CustomEnquiryForm" method="post"
              action="<?php echo get_option('mytourop_admin_url'); ?>/enquiries/enquire">
            <input type="hidden" name="utma" value="<?php echo $_COOKIE['__utma'] ?>"/>
            <input type="hidden" name="utmb" value="<?php echo $_COOKIE['__utmb'] ?>"/>
            <input type="hidden" name="utmc" value="<?php echo $_COOKIE['__utmc'] ?>"/>
            <input type="hidden" name="utmz" value="<?php echo $_COOKIE['__utmz'] ?>"/>

            <?php $step = 1; ?>
            <fieldset id="CustomEnquiryFirstStep" class="step">

                <legend>Step <?php echo $step++ ?>: Your Tour Settings</legend>

                <ol>
                    <li>
                        <label for="EthiopiaDepartureDate">I wish to start my tour on:</label>
                        <input type="text" name="data[Enquiry][start_date]" id="EthiopiaDepartureDate"
                               class="required"/>
                    </li>

                    <li>
                        <label for="EthiopiaPersons">I wish to last my tour for: </label>
                        <select name="data[Enquiry][nr_days]" id="Days">

                            <option> 1 day</option>
                            <option> 2 days</option>
                            <option> 3 days</option>
                            <option> 4 days</option>
                            <option selected="selected"> 5 days</option>

                            <option> 6 days</option>
                            <option> 7 days</option>
                            <option> 8 days</option>
                            <option> 9 days</option>
                            <option> 10 days</option>
                            <option> 11 days</option>
                            <option> 12 days</option>

                            <option> 13 days</option>
                            <option> 14 days</option>
                            <option> 15 days</option>


                            <option> 16 days</option>
                            <option> 17 days</option>
                            <option> 18 days</option>


                            <option> 19 days</option>
                            <option> 20 days</option>
                            <option> 21 days</option>


                        </select>
                    </li>
                    <li>
                        <label for="EthiopiaPersons">Number of people:</label>
                        <select name="data[Enquiry][nr_persons]" id="EthiopiaPersons">

                            <option> 1 person</option>
                            <option selected="selected"> 2 persons</option>
                            <option> 3 persons</option>
                            <option> 4 persons</option>
                            <option> 5 persons</option>

                            <option> 6 persons</option>
                            <option> 7 persons</option>
                            <option> 8 persons</option>
                        </select>
                    </li>
                </ol>

            </fieldset>

            <?php if (isset($places)): ?>
                <fieldset id="CustomEnquirySecondStep" class="step">
                    <legend>Step <?php echo $step++ ?>: Places to see</legend>
                    <div class="divcolumnlist">

                        <ol>
                            <?php foreach ($places as $place): ?>

                                <li style="margin-bottom:1em"><input type="checkbox" name="data[Enquiry][places][]"
                                                                     value="<?php echo $place; ?>"/> <?php echo $place; ?>
                                </li>

                            <?php endforeach; ?>
                        </ol>
                    </div>


                </fieldset>

            <?php endif; ?>


            <fieldset id="CustomEnquiryThirdStep" class="step">

                <legend>Step <?php echo $step++ ?>: Your Special Requests</legend>
                <ol>
                    <li>
                        <label for="EthiopiaSpecialRequests">Additional requests:</label>
                        <textarea name="data[Enquiry][remarks]" cols="50" rows="8"
                                  id="EthiopiaSpecialRequests"> </textarea>
                    </li>
                </ol>

            </fieldset>
            <fieldset id="CustomEnquiryFourthStep" class="step submit_step">
                <legend>Step <?php echo $step++ ?>: Your Contact Details</legend>
                <ol>

                    <li>
                        <label for="EthiopiaFirstName">First Name:</label>

                        <input type="text" name="data[Enquiry][first_name]" id="EthiopiaFirstName"
                               class="textbox, required"/>
                        <label for="EthiopiaFirstName" class="error" style="float:none; color:#F00;padding-left:1em;">Required.</label>
                    </li>
                    <li>
                        <label for="EthiopiaLastName">Last Name:</label>
                        <input type="text" name="data[Enquiry][last_name]" id="EthiopiaLastName"
                               class="textbox, required"/>
                        <label for="EthiopiaLastName" class="error" style="float:none; color:#F00;padding-left:1em;">Required.</label>

                    </li>
                    <li>
                        <label for="EthiopiaEmail">E-Mail Adress:</label>
                        <input name="data[Enquiry][email]" type="text" class="textbox, required email"
                               id="EthiopiaEmail"/>
                        <label for="EthiopiaEmail" class="error" style="float:none; color:#F00;padding-left:1em;">
                            Required. </label>
                    </li>


                    <li>


                    </li>
                </ol>

            </fieldset>
            <input type="reset" value="Reset" style="font-size:x-large; font-weight:bold"/>
            <input type="submit" value="Submit" style="font-size:x-large; font-weight:bold"/>

            <input type="hidden" name="data[Enquiry][ip]" value="<?php echo $_SERVER["REMOTE_ADDR"]; ?>"/>

            <input type="hidden" name="data[Enquiry][organisation_id]"
                   value="<?php echo get_option('mytourop_organisation_id'); ?>"/>

        </form>

    </div>


    <script type="text/javascript">
        jQuery(document).ready(function () {


            jQuery("#EthiopiaDepartureDate").datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 0,
                showOn: 'button',
                buttonImage: "<?php echo plugins_url('/mytourop/img/calendar.bmp') ?>",
                buttonImageOnly: true
            });


            jQuery("#CustomEnquiryForm").formwizard({
                    formPluginEnabled: true,
                    validationEnabled: true,
                    historyEnabled: true,
                    focusFirstInput: true,
                    formOptions: {
                        error: handleError,
                        success: function () {
                            alert('Your enquiry has been registered succesfully! We will contact you within the next 48 hours.');
                            window.location = "/";

                        },
                        resetForm: true
                    }
                }
            );


        });

        function handleError() {
            jQuery("#captchaError").show();
        }

    </script>


    <?php


}

//////////////////////// PAYMENT SHORTCODE
function paymentform_shortcode($atts, $content = null)
{


    ///////////////// request variables


    if (isset($_REQUEST['Confirmed'])) {
        if (!isset($_REQUEST['n'])) {
            echo("<center><h1>INVALID REQUEST; PLEASE CONTACT CUSTOMER SERVICE</h1></center>");
            exit(0);
        } else {
            ?>
            <fieldset>
                <center style="line-height: 300px;"><h3 style="line-height: 200px;">
                        Thanks <?php echo base64_decode($_REQUEST['n']) ?> for your payment. A confirmation e-mail has
                        been sent to you. </h3>
                </center>
            </fieldset>
            <?php

            return;
        }
    }

    if (!isset($_REQUEST['amt'])) {
        echo("<center><h1>INVALID REQUEST; PLEASE CONTACT CUSTOMER SERVICE</h1></center>");

        exit(0);
    }

    if (!isset($_REQUEST['pid']) || !isset($_REQUEST['n'])) {
        echo("<center><h1>INVALID REQUEST; PLEASE CONTACT CUSTOMER SERVICE</h1></center>");

        exit(0);
    }


    $amount = base64_decode($_REQUEST['amt']);

    $name = base64_decode($_REQUEST['n']);

    /////////////////////////////////////


    ?>


    <form action="https://admin.mytourop.com/payments/pay" method="post">
        <input type="hidden" name="paymentType" value='<?php echo $paymentType ?>'/>
        <fieldset>

            <ol style="list-style:none">
                <!-- Identify your business so that you can collect the payments. -->


                <input type="text" style="display:none" name="pid" value="<?php echo $_REQUEST['pid'] ?>"/>
                <input type="text" style="display:none" name="Name" value="<?php echo $name ?>"/>


                <li>
                    <label style="width:auto" for="knop"><?php echo $name ?>, kindly pay
                        <b>USD <?php echo $amount ?></b> to confirm your tour: </label>
                    <input name="submit" type="image" id="knop"
                           src="https://www.paypal.com/en_US/BE/i/btn/btn_paynowCC_LG.gif"
                           alt="PayPal - The safer, easier way to pay online" align="middle" border="0"/>
                    <img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" border="0" height="1"/>
                </li>
            </ol>
        </fieldset>
    </form>
    <?php
}

?>