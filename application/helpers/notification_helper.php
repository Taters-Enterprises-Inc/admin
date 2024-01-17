<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


if ( ! function_exists('notify'))
{
    function notify($subscribe_to, $channel, $data){
        $CI = get_instance();
        $pusher = $CI->pusher->get_pusher();
        $pusher->trigger($subscribe_to, $channel, $data);
    }   
}