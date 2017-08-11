<?php
/*
    Plugin Name: FlashPoll++
    Plugin URI: http://www.flash-poll.com
    Description: A Flash based poll builder and voting system.
    Version: 1.2
    Author: AtteroMedia
    Author URI: http://www.atteromedia.com
*/


/*  
    Copyright 2010 AtteroMedia  (email : contacts@atteromedia.com)

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


define('FPPP_VERSION', '1.2');
define('FPPP_PLUGIN_DIR', '/flashpoll');

register_activation_hook(__FILE__, 'fppp_activate');
register_deactivation_hook(__FILE__, 'fppp_deactivate');


add_action('admin_init', 'fppp_admin_init');
add_action('admin_menu', 'fppp_admin_menu');
add_action('init', 'fppp_init');



// widget
add_action('widgets_init', 'fppp_load_widgets');


// shortcode
add_shortcode('fppp_display_poll', 'fppp_shortcode');


// [bartag foo="foo-value"]
function fppp_shortcode($atts) 
{
	extract(shortcode_atts(array(
		'polls_id' => 1
	), $atts));

	fppp_display_poll();
}




function fppp_init()
{
    wp_enqueue_script('swfobject');
}

function fppp_admin_init()
{
    wp_register_style('fppp_admin_css', WP_PLUGIN_URL . FPPP_PLUGIN_DIR . '/admin.css');
    wp_enqueue_style('fppp_admin_css');
    
    wp_register_script('fppp_admin_js', WP_PLUGIN_URL . FPPP_PLUGIN_DIR . '/admin.js');
    wp_enqueue_script('fppp_admin_js');
}    


function fppp_admin_menu() 
{
  add_menu_page('FlashPoll++', 'FlashPoll++', 'administrator', 'fppp', 'fppp_menu_polls');
  add_submenu_page('fppp', 'FlashPoll++ Settings', 'Settings', 'administrator', 'fppp_menu_settings', "fppp_menu_settings");
  add_submenu_page('fppp', 'FlashPoll++ Voting Logs', 'Voting Logs', 'administrator', 'fppp_logs', "fppp_menu_logs");
}

function fppp_menu_settings() 
{
    if ($_POST['sbm'])
    {
        $hide_signature = $_POST['hide_signature'] == "on" ? 1 : 0;
        update_option('fppp_hide_signature', $hide_signature);
        
        $allow_multiple_votes = $_POST['allow_multiple_votes'] == "on" ? 1 : 0;
        update_option('fppp_allow_multiple_votes', $allow_multiple_votes);
        
        $show_total_votes = $_POST['show_total_votes'] == "on" ? 1 : 0;
        update_option('fppp_show_total_votes', $show_total_votes);
        
        $show_button_next = $_POST['show_button_next'] == "on" ? 1 : 0;
        update_option('fppp_show_button_next', $show_button_next);
        
        
        update_option('fppp_color_progress_bars', $_POST['color_progress_bars']);
        update_option('fppp_bgcolor', $_POST['bgcolor']);
        update_option('fppp_height', $_POST['height']);
        update_option('fppp_width', $_POST['width']);
        
        // fonts
        update_option('fppp_font_title_family', $_POST['font_title_family']);
        update_option('fppp_font_title_size', $_POST['font_title_size']);
        update_option('fppp_font_title_color', $_POST['font_title_color']);
        update_option('fppp_font_title_bold', $_POST['font_title_bold'] == "on" ? 1 : 0);
        
        update_option('fppp_font_choice_family', $_POST['font_choice_family']);
        update_option('fppp_font_choice_size', $_POST['font_choice_size']);
        update_option('fppp_font_choice_color', $_POST['font_choice_color']);
        update_option('fppp_font_choice_bold', $_POST['font_choice_bold'] == "on" ? 1 : 0);
        
        update_option('fppp_font_perc_family', $_POST['font_perc_family']);
        update_option('fppp_font_perc_size', $_POST['font_perc_size']);
        update_option('fppp_font_perc_color', $_POST['font_perc_color']);
        update_option('fppp_font_perc_bold', $_POST['font_perc_bold'] == "on" ? 1 : 0);
        
        
        // texts
        update_option('fppp_str_next_poll', $_POST['str_next_poll']);
        update_option('fppp_str_total_votes', $_POST['str_total_votes']);
    }
    
    
    $hide_signature = get_option('fppp_hide_signature');
    $allow_multiple_votes = get_option('fppp_allow_multiple_votes');
    $show_total_votes = get_option('fppp_show_total_votes');
    $show_button_next = get_option('fppp_show_button_next');
    $color_progress_bars = get_option('fppp_color_progress_bars');
    $bgcolor = get_option('fppp_bgcolor');
    $height = get_option('fppp_height');
    $width = get_option('fppp_width');

    $font_title_family = get_option('fppp_font_title_family');
    $font_title_size = get_option('fppp_font_title_size');
    $font_title_color = get_option('fppp_font_title_color');
    $font_title_bold = get_option('fppp_font_title_bold');
    
    $font_choice_family = get_option('fppp_font_choice_family');
    $font_choice_size = get_option('fppp_font_choice_size');
    $font_choice_color = get_option('fppp_font_choice_color');
    $font_choice_bold = get_option('fppp_font_choice_bold');
    
    $font_perc_family = get_option('fppp_font_perc_family');
    $font_perc_size = get_option('fppp_font_perc_size');
    $font_perc_color = get_option('fppp_font_perc_color');
    $font_perc_bold = get_option('fppp_font_perc_bold');
    
    
    // texts
    $str_next_poll = get_option('fppp_str_next_poll');
    $str_total_votes = get_option('fppp_str_total_votes');
    
    ?>
    <div class="wrap">
        <h2>FlashPoll++ Settings</h2>
        
        <form method="post" action="">
        
                        
            <p>
                <input type="checkbox" name="allow_multiple_votes" <?php echo $allow_multiple_votes == "1" ? " checked='checked' " : "" ?> id="allow_multiple_votes" />
                <label for="allow_multiple_votes">Allow multiple votes</label>
            </p>
            
            <p>
                <input type="checkbox" name="show_total_votes" <?php echo $show_total_votes == "1" ? " checked='checked' " : "" ?> id="show_total_votes" />
                <label for="show_total_votes">Show total votes</label>
            </p>
            
            <p>
                <input type="checkbox" name="show_button_next" <?php echo $show_button_next == "1" ? " checked='checked' " : "" ?> id="show_button_next" />
                <label for="show_button_next">Show button 'Next'</label>
            </p>
            
            <p>
                <input type="checkbox" name="hide_signature" <?php echo $hide_signature == "1" ? " checked='checked' " : "" ?> id="hide_signature" />
                <label for="hide_signature">Hide signature</label>
            </p>
            
            
            <p>
                <label for="color_progress_bars">Progress bars color:</label> <br />
                <input type="text" name="color_progress_bars" id="color_progress_bars" value="<?php echo $color_progress_bars ?>" />
            </p>
            
            <p>
                <label for="bgcolor">Background color:</label> <br />
                <input type="text" name="bgcolor" id="bgcolor" value="<?php echo $bgcolor ?>" />
            </p>
            
            <p>
                <label for="width">Width:</label> <br />
                <input type="text" name="width" id="width" value="<?php echo $width ?>" />
            </p>
            
            <p>
                <label for="height">Height:</label> <br />
                <input type="text" name="height" id="height" value="<?php echo $height ?>" />
            </p>
            
            
            
            <br /><br />
            
            <h3>Fonts</h3>
            <div class="fppp_admin_section">
                
                <div id="fppp_admin_font_title">
                
                    <h4>Poll Title</h4>
                    <p>
                        <label for="font_title_family">Font family:</label> <br />
                        <input type="text" name="font_title_family" id="font_title_family" value="<?php echo $font_title_family ?>" />
                    </p>
                    
                    <p>
                        <label for="font_title_size">Font size:</label> <br />
                        <input type="text" size="2" name="font_title_size" id="font_title_size" value="<?php echo $font_title_size ?>" />
                        
                        <input type="checkbox" name="font_title_bold" id="font_title_bold" <?php echo $font_title_bold == "1" ? " checked='checked' " : "" ?> />
                        <label for="font_title_bold">Bold</label>
                    </p>
                    
                    <p>
                        <label for="font_title_color">Color:</label> <br />
                        <input type="text" name="font_title_color" id="font_title_color" value="<?php echo $font_title_color ?>" />
                    </p>
                    
                </div>
                
                
                <div id="fppp_admin_font_choice">
                
                    <h4>Poll Choices</h4>
                    <p>
                        <label for="font_choice_family">Font family:</label> <br />
                        <input type="text" name="font_choice_family" id="font_choice_family" value="<?php echo $font_choice_family ?>" />
                    </p>
                    
                    <p>
                        <label for="font_choice_size">Font size:</label> <br />
                        <input type="text" size="2" name="font_choice_size" id="font_choice_size" value="<?php echo $font_choice_size ?>" />
                        
                        <input type="checkbox" name="font_choice_bold" id="font_choice_bold" <?php echo $font_choice_bold == "1" ? " checked='checked' " : "" ?> />
                        <label for="font_choice_bold">Bold</label>
                    </p>
                    
                    <p>
                        <label for="font_choice_color">Color:</label> <br />
                        <input type="text" name="font_choice_color" id="font_choice_color" value="<?php echo $font_choice_color ?>" />
                    </p>
                    
                </div>
                
                
                <div id="fppp_admin_font_perc">
                
                    <h4>Result Percentages</h4>
                    <p>
                        <label for="font_perc_family">Font family:</label> <br />
                        <input type="text" name="font_perc_family" id="font_perc_family" value="<?php echo $font_perc_family ?>" />
                    </p>
                    
                    <p>
                        <label for="font_perc_size">Font size:</label> <br />
                        <input type="text" size="2" name="font_perc_size" id="font_perc_size" value="<?php echo $font_perc_size ?>" />
                        
                        <input type="checkbox" name="font_perc_bold" id="font_perc_bold" <?php echo $font_perc_bold == "1" ? " checked='checked' " : "" ?> />
                        <label for="font_perc_bold">Bold</label>
                    </p>
                    
                    <p>
                        <label for="font_perc_color">Color:</label> <br />
                        <input type="text" name="font_perc_color" id="font_perc_color" value="<?php echo $font_perc_color ?>" />
                    </p>
                    
                </div>
            </div>
            
            <div style="clear: both;"></div>
            
            <br /><br />
            
            <h3>Labels</h3>
            <div class="fppp_admin_section">
                <p>
                    <label for="str_next_poll">'Next poll' text:</label> <br />
                    <input type="text" name="str_next_poll" id="str_next_poll" value="<?php echo $str_next_poll ?>" />
                </p>
                
                <p>
                    <label for="str_total_votes">'Total votes' text:</label> <br />
                    <input type="text" name="str_total_votes" id="str_total_votes" value="<?php echo $str_total_votes ?>" />
                </p>
            </div>
            
            
            <br /><br />
            <input type="submit" value="Save changes" name="sbm" />
        </form>
    </div>
    <?php


}

function fppp_menu_polls()
{
    global $wpdb;
    
    if ($_POST['fppp_action'] == 'delete_poll' && (int)$_POST['fppp_arg1'] > 0)
    {
        // delete a poll
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "fppp_poll_results WHERE polls_id = " . (int)$_POST['fppp_arg1']);
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "fppp_poll_answers WHERE polls_id = " . (int)$_POST['fppp_arg1']);
        $wpdb->query("DELETE FROM " . $wpdb->prefix . "fppp_polls WHERE id = " . (int)$_POST['fppp_arg1']);
    }
    
    if ($_POST['fppp_new_poll'])
    {
        $sql = "INSERT INTO " . $wpdb->prefix . "fppp_polls (question) VALUES ('" . $wpdb->escape($_POST['fppp_new_poll']) . "')";
        $wpdb->query($sql);
        
    }
    
    
    if ($_POST['fppp_action'] == 'edit_poll' && (int)$_POST['fppp_arg1'] > 0)
    {
        // edit a poll
        $polls_id = (int)$_POST['fppp_arg1'];
        
        
        // delete a choice
        if ($_POST['fppp_arg2'] == 'delete_choice' && (int)$_POST['fppp_arg3'] > 0)
        {
            $wpdb->query("DELETE FROM " . $wpdb->prefix . "fppp_poll_results WHERE poll_answers_id = " . (int)$_POST['fppp_arg3']);
            $wpdb->query("DELETE FROM " . $wpdb->prefix . "fppp_poll_answers WHERE id = " . (int)$_POST['fppp_arg3']);
        }    

            
        // add new choices
        if (is_array($_POST['fppp_new_choices']))
        foreach ($_POST['fppp_new_choices'] as $new_choice)
            if (!empty($new_choice))
                $wpdb->query("INSERT INTO " . $wpdb->prefix . "fppp_poll_answers (polls_id, answer) VALUES ($polls_id, '" . $wpdb->escape($new_choice) . "')");

        
        
        // update choices
        foreach ($_POST as $key => $value)
        {
            $key_arr = explode("_", $key);
            
            if ($key_arr[0] == 'fppp' && $key_arr[1] == 'choice' && (int)$key_arr[2] > 0)
            {
                $poll_answers_id = (int)$key_arr[2];
                $wpdb->query('UPDATE ' . $wpdb->prefix . 'fppp_poll_answers SET answer = "' . $wpdb->escape($value) . '" WHERE id = ' . $poll_answers_id);
            }
        }
        
        
        // update the poll question
        if (!empty($_POST['fppp_poll_question']))
            $wpdb->query('UPDATE ' . $wpdb->prefix . 'fppp_polls SET question = "' . $wpdb->escape($_POST['fppp_poll_question']) . '" WHERE id = ' . $polls_id);
        
        
        // set active
        if (isset($_POST['fppp_active']))
        {
            if ($_POST['fppp_active'] == "on")
            {
                $wpdb->query("UPDATE " . $wpdb->prefix . "fppp_polls SET active = 'N'");
                $wpdb->query("UPDATE " . $wpdb->prefix . "fppp_polls SET active = 'Y' WHERE id = " . $polls_id);
            }
            else
            {
                $wpdb->query("UPDATE " . $wpdb->prefix . "fppp_polls SET active = 'N' WHERE id = " . $polls_id);
            }
        }
        
        
        
        $poll = $wpdb->get_row('SELECT * FROM ' . $wpdb->prefix . 'fppp_polls WHERE id = ' . $polls_id);
        $poll_choices = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'fppp_poll_answers WHERE polls_id = ' . $polls_id . " ORDER BY id ASC");
        
        ?>
        <div class="wrap">
        <h2>Edit a Poll</h3>
        
        <form method="post" action="" name="action">
            <input type="hidden" name="fppp_action" value="edit_poll" />
            <input type="hidden" name="fppp_arg1" value="<?php echo $polls_id ?>" />
            <input type="hidden" name="fppp_arg2" value="" />
            <input type="hidden" name="fppp_arg3" value="" />
            
            <h3>Question:</h3>
            <input type="text" size="40" name="fppp_poll_question" id="fppp_poll_question" value="<?php echo $poll->question ?>" />
            
            <br />
            <input type="checkbox" name="fppp_active" id="fppp_active" <?php echo $poll->active == "Y" ? " checked='checked' " : "" ?> />
            <label for="fppp_active">Active</label>
            
            <h3>Choices:</h3>
            <div id="fppp_choices">
                <?php if (is_array($poll_choices)): ?>
                <ul>
                    <?php foreach($poll_choices as $choice): ?>
                    <li>
                        <input type="text" name="fppp_choice_<?php echo $choice->id ?>" value="<?php echo $choice->answer ?>" /> <a class="delete" href="javascript: void(0);" onclick="javascript: fppp_deleteChoice(<?php echo $choice->id ?>);">x</a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
            
            <a href="javascript: void(0);" onclick="javascript: fppp_newChoice();">Add a new choice</a>
            
            <br /><br />
            <input type="submit" name="sbm" value="Save" /> 
            
            <br /><br />
            <a href="?page=fppp">&laquo; Back</a>
        </form>
        
        </div>
        <?php
    }
    
    else
    {
        // not editing a poll - display all polls
            
        $polls = $wpdb->get_results("SELECT * FROM " . $wpdb->prefix . "fppp_polls ORDER BY id ASC");
        
        ?>
        
        <form name="action" method="post">
            <input type="hidden" name="fppp_action" value="" />
            <input type="hidden" name="fppp_arg1" value="" />
        </form>
        
        <div class="wrap">
        <h2>Polls</h2>
        
        <br />
        
        <?php if (is_array($polls)): ?>
        <ul>
            <?php foreach ($polls as $poll): ?>
            <li>
                <h3 style="display: inline;"><?php echo $poll->question ?></h3>
                
                <a href="javascript: void(0);" onclick="javascript: fppp_editPoll(<?php echo $poll->id ?>)">edit</a>
                <a href="javascript: void(0);" onclick="javascript: fppp_deletePoll(<?php echo $poll->id ?>)">delete</a>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        
        
        <br /><br />
        
        <form method="post" action="">
            Add a new poll: <br />
            <input type="text" name="fppp_new_poll" value="" /> <input type="submit" value="Add" name="sbm" />
        </form>
        
        </div>
        <?php
    }
}

function fppp_menu_logs()
{
    global $wpdb;
    
    $rows_per_page = 50;
    
    // get total rows & pages
    $sql = "
        SELECT 
            COUNT(pr.id) as total_votes
            
        FROM " . $wpdb->prefix . "fppp_poll_results pr
        LEFT JOIN " . $wpdb->prefix . "fppp_poll_answers pa ON pa.id = pr.poll_answers_id
        LEFT JOIN " . $wpdb->prefix . "fppp_polls p ON p.id = pr.polls_id
    ";
    $row = $wpdb->get_row($sql);
    $total_rows = $row->total_votes;
    
    $total_pages = ceil($total_rows / $rows_per_page);
    
    $current_page = (int)$_POST['current_page'];
    
    if ($current_page < 1 || $current_page > $total_pages)
        $current_page = 1;
    
    $sql = "
        SELECT 
            pa.answer,
            p.question,
            pr.ip,
            pr.vote_date
            
        FROM " . $wpdb->prefix . "fppp_poll_results pr
        LEFT JOIN " . $wpdb->prefix . "fppp_poll_answers pa ON pa.id = pr.poll_answers_id
        LEFT JOIN " . $wpdb->prefix . "fppp_polls p ON p.id = pr.polls_id
        
        ORDER BY pr.vote_date DESC
        
        LIMIT " . (($current_page - 1)  * $rows_per_page) . ", " . $rows_per_page . "
    ";
    $logs = $wpdb->get_results($sql);
    
    
    ?>
    <div class="wrap">
    <h2>Voting Logs</h2>
    
    <br /><br />
    <table border="0" class="logs">
        <tr>
            <th>Vote Date</th>
            <th>Poll</th>
            <th>Answer</th>
            <th>Voter IP</th>
        </tr>
        
        <? foreach ($logs as $log): ?>
        <tr>
            <td><?= $log->vote_date ?></td>
            <td><?= $log->question ?></td>
            <td><?= $log->answer ?></td>
            <td><?= $log->ip ?></td>
        </tr>
        <? endforeach; ?>
    </table>
    
    <br /><br />
    
    <form method="post" name="pagination" action="">
    
        Go to page:
        <select name="current_page" onchange="javascript: document.forms['pagination'].submit();">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <option value="<?php echo $i ?>" <?php echo ($current_page == $i ? " selected='selected' " : "") ?> ><?= $i ?></option>
            <?php endfor; ?>
        </select>
        
    </form>
    
    <br /><br />
    </div>
    <?php
}



function fppp_activate()
{
    global $wpdb;


    // add options
    add_option("fppp_version", FPPP_VERSION);
    add_option("fppp_hide_signature", "0");
    
    add_option("fppp_show_total_votes", "1");
    add_option("fppp_show_button_next", "1");
    add_option("fppp_allow_multiple_votes", "0");
    add_option("fppp_color_progress_bars", "0xC6DD5B");
    add_option("fppp_str_next_poll", "Next poll");
    add_option("fppp_str_total_votes", "Total votes:");
    add_option("fppp_width", "250");
    add_option("fppp_height", "400");
    add_option("fppp_bgcolor", "#ffffff");
    
    add_option("fppp_font_title_family", "Trebuchet MS");
    add_option("fppp_font_title_size", "14");
    add_option("fppp_font_title_color", "0x000000");
    add_option("fppp_font_title_bold", "1");
    
    add_option("fppp_font_choice_family", "Verdana");
    add_option("fppp_font_choice_size", "11");
    add_option("fppp_font_choice_color", "0x000000");
    add_option("fppp_font_choice_bold", "0");
    
    add_option("fppp_font_perc_family", "Verdana");
    add_option("fppp_font_perc_size", "10");
    add_option("fppp_font_perc_color", "0xAEC350");
    add_option("fppp_font_perc_bold", "0");
    
    
    
    
    
    // create tables
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    
    // polls
    $table_name = $wpdb->prefix . "fppp_polls";

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
    {
        $sql = "
            CREATE TABLE `" . $table_name . "` (
              `id` int(11) NOT NULL auto_increment,
              `question` text NOT NULL,
              `active` enum('Y','N') NOT NULL default 'N',
              PRIMARY KEY  (`id`)
            )
        ";
        dbDelta($sql);
   }
   
   
    // poll_answers
    $table_name = $wpdb->prefix . "fppp_poll_answers";

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
    {
        $sql = "
            CREATE TABLE `" . $table_name . "` (
              `id` int(11) NOT NULL auto_increment,
              `polls_id` int(11) NOT NULL,
              `answer` text NOT NULL,
              PRIMARY KEY  (`id`),
              KEY `polls_id` (`polls_id`)
            )
        ";
        dbDelta($sql);
   }
   
   
   
   // poll_results
    $table_name = $wpdb->prefix . "fppp_poll_results";

    if($wpdb->get_var("show tables like '$table_name'") != $table_name) 
    {
        $sql = "
            CREATE TABLE `" . $table_name . "` (
              `id` int(11) NOT NULL auto_increment,
              `polls_id` int(11) default NULL,
              `poll_answers_id` int(11) NOT NULL,
              `ip` varchar(20) NOT NULL,
              `vote_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
              PRIMARY KEY  (`id`),
              KEY `polls_id` (`polls_id`),
              KEY `poll_answers_id` (`poll_answers_id`)
            )
        ";
        dbDelta($sql);
   }

}


function fppp_deactivate()
{
    global $wpdb;
    
    // remove options
    delete_option("fppp_version");
    
    delete_option("fppp_hide_signature");
    delete_option("fppp_show_total_votes");
    delete_option("fppp_show_button_next");
    delete_option("fppp_allow_multiple_votes");
    delete_option("fppp_color_progress_bars");
    delete_option("fppp_str_next_poll");
    delete_option("fppp_str_total_votes");
    delete_option("fppp_width");
    delete_option("fppp_height");
    delete_option("fppp_bgcolor");
    
    delete_option("fppp_font_title_family");
    delete_option("fppp_font_title_size");
    delete_option("fppp_font_title_color");
    delete_option("fppp_font_title_bold");
    
    delete_option("fppp_font_choice_family");
    delete_option("fppp_font_choice_size");
    delete_option("fppp_font_choice_color");
    delete_option("fppp_font_choice_bold");
    
    delete_option("fppp_font_perc_family");
    delete_option("fppp_font_perc_size");
    delete_option("fppp_font_perc_color");
    delete_option("fppp_font_perc_bold");
    
    
    
    // drop db tables
    $wpdb->query("DROP TABLE `" . $wpdb->prefix . "fppp_polls`");
    $wpdb->query("DROP TABLE `" . $wpdb->prefix . "fppp_poll_answers`");
    $wpdb->query("DROP TABLE `" . $wpdb->prefix . "fppp_poll_results`");
}




function fppp_display_poll()
{
    
    $height = get_option('fppp_height');
    $width = get_option('fppp_width');
    $bgcolor = get_option('fppp_bgcolor');
    
    ?>
    <script type="text/javascript">
        var flashvars = false;
        var params = {
          menu: "false",
          flashvars: "base_url=<?php echo WP_PLUGIN_URL . FPPP_PLUGIN_DIR ?>/",
          scale: "noscale",
          salign: "lt",
          wmode: "opaque",
          quality: "best",
          bgcolor: "<?php echo $bgcolor ?>"
        };
        var attributes = {
          id: "fppp_swf",
          name: "fppp_swf"
        };

        // smart IE can't properly read stage width, so swfobject can't be used with it
        if (!(/msie/i.test(navigator.userAgent) && !/opera/i.test(navigator.userAgent)))
        {
            swfobject.embedSWF("<?php echo WP_PLUGIN_URL . FPPP_PLUGIN_DIR ?>/FlashPollPP.swf", "fppp_swf_div", "<?php echo $width ?>", "<?php echo $height ?>", "9.0.0","expressInstall.swf", flashvars, params, attributes);
            swfobject.createCSS("object", "outline: none");
        }
    </script>
    
    <div id="fppp_swf_div">
        
        <object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="<?php echo $width ?>" height="<?php echo $height ?>" id="fppp_swf" align="left">
            <param name="allowFullScreen" value="false" />
            <param name="movie" value="<?php echo WP_PLUGIN_URL . FPPP_PLUGIN_DIR ?>/FlashPollPP.swf" />
            <param name="quality" value="best" />
            <param name="scale" value="noscale" />
            <param name="bgcolor" value="<?php echo $bgcolor ?>" />
            <param name="salign" value="lt" />
            <param name="FlashVars" value="base_url=<?php echo WP_PLUGIN_URL . FPPP_PLUGIN_DIR ?>/" />

            <embed src="<?php echo WP_PLUGIN_URL . FPPP_PLUGIN_DIR ?>/FlashPollPP.swf" FlashVars="base_url=<?php echo WP_PLUGIN_URL . FPPP_PLUGIN_DIR ?>/" quality="best" scale="noscale" bgcolor="<?php echo $bgcolor ?>" width="<?php echo $width ?>" height="<?php echo $height ?>" name="fppp_swf" salign="lt" align="left" allowFullScreen="false" type="application/x-shockwave-flash" pluginspage="http://www.adobe.com/go/getflashplayer" />
        </object>

    </div>
    
    <?php
}






// widgets

function fppp_load_widgets()
{
    register_widget('FPPP_Widget');
}

class FPPP_Widget extends WP_Widget 
{
    function FPPP_Widget()
    {
        /* Widget settings. */
		$widget_ops = array('classname' => 'fppp', 'description' => 'This widget displays FlashPoll++ on your page.');

		/* Widget control settings. */
		$control_ops = array('width' => get_option('fppp_width'), 'height' => get_option('fppp_height'), 'id_base' => 'fppp-widget' );

		/* Create the widget. */
		$this->WP_Widget('fppp-widget', 'FlashPoll++ Widget', $widget_ops, $control_ops);
	}
    
    
    function widget($args, $instance) 
    {
		extract($args);
        //wp_enqueue_script('swfobject');

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );

		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

        fppp_display_poll();
        
		/* After widget (defined by themes). */
		echo $after_widget;
	}
}

?>
