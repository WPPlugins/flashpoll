<?php

class Polls
{
    public $methodTable;
    
    function __construct()
    {
        $this->methodTable = array(
            "getPolls" => array(
                "returntype" => "recordSet"
            ),
            "getPollChoices" => array(
                "returntype" => "recordSet"
            ),
            "getPollResults" => array(
                "returntype" => "recordSet"
            ),
            "getSettings" => array(
                "returntype" => "recordSet"
            ),
            "saveVote" => array(
            ),
        );
    }
    
    
    function getPolls()
    {
        global $wpdb;
        
        $sql = "
        SELECT 
            p.id, 
            p.question, 
            p.active
        FROM " . $wpdb->prefix  . "fppp_polls p
        
        GROUP BY p.id
        ORDER BY p.id ASC
        ";
        $res = mysql_query($sql);
        
        return $res;
    }
    
    
    function getPollChoices($polls_id)
    {
        global $wpdb;
        
        if ((int)$polls_id < 1)
            return false;
            
        $sql = "SELECT id, polls_id, answer FROM " . $wpdb->prefix . "fppp_poll_answers WHERE polls_id = " . (int)$polls_id . " ORDER BY id ASC";
        $res = mysql_query($sql);
        
        return $res;
    }
    
    
    function getPollResults($polls_id)
    {
        global $wpdb;
        
        $polls_id = (int)$polls_id;
        if ($polls_id < 1)
            return false;
            
        // get total results for this poll
        $sql = "SELECT COUNT(poll_answers_id) AS total_votes FROM " . $wpdb->prefix . "fppp_poll_results WHERE polls_id = " . $polls_id;
        $res = mysql_query($sql);
        $total = mysql_fetch_object($res);
        $total_votes = $total->total_votes;

        // get percentage results for this poll
        $sql = "
            SELECT 
                pa.id as poll_answers_id,
                pa.answer,
                ROUND( (COUNT(pr.poll_answers_id) / " . $total_votes . ") * 100 ) AS percentage,
                COUNT(pr.id) AS total_votes
                
            FROM " . $wpdb->prefix . "fppp_poll_answers pa
            LEFT OUTER JOIN " . $wpdb->prefix . "fppp_poll_results pr ON pr.poll_answers_id = pa.id
            
            WHERE pa.polls_id = " . $polls_id . "
            GROUP BY pa.id
            ORDER BY pa.id ASC
        ";
        $res = mysql_query($sql);
        
        return $res;
    }
    
    
    function saveVote($poll_answers_id)
    {
        global $wpdb;
        
        $poll_answers_id = (int)$poll_answers_id;
        if ($poll_answers_id < 1)
            return false;
            
        // get poll id
        $sql = "SELECT polls_id FROM " . $wpdb->prefix . "fppp_poll_answers WHERE id = " . $poll_answers_id;
        $res = mysql_query($sql);
        $poll = mysql_fetch_object($res);

        $polls_id = (int)$poll->polls_id;
        if ($polls_id < 1)
            return false;
            
        
        // save vote
        $user_ip = get_ip();
        
        $sql = "INSERT INTO " . $wpdb->prefix . "fppp_poll_results(polls_id, poll_answers_id, ip) VALUES($polls_id, $poll_answers_id, '" . $wpdb->escape($user_ip) . "')";
        if (mysql_query($sql) !== false)
            return true;
        
        return false;

    }
    
    
    function getSettings()
    {
        global $wpdb;
        
        $sql = "SELECT REPLACE(option_name, 'fppp_', '') AS option_name, option_value FROM " . $wpdb->prefix . "options WHERE option_name LIKE 'fppp_%'";
        $res = mysql_query($sql);
        
        return $res;
    }
}



function get_ip()
{
    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP']))
    {
        //check ip from share internet
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    {
        //to check ip is pass from proxy
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    elseif (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR']))
    {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    else
    {
        $ip = '000.000.000.000';
    }
    
    return $ip;
}
?>