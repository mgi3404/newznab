<?php 
/** 
 * Smarty plugin 
 * @package Smarty 
 * @subpackage plugins 
 */ 


/** 
 * Smarty date modifier plugin 
 * Purpose:  converts unix timestamps or datetime strings to words 
 * Type:     modifier<br> 
 * Name:     timeAgo<br> 
 * @author   Stephan Otto 
 * @param string 
 * @return string 
 */ 
function smarty_modifier_timeAgo( $date) 
{ 
      $timeStrings = array(   'now',      // 0
                        'Sec', 'Secs',    // 1,1 
                        'Min','Mins',     // 3,3 
                        'Hour', 'Hrs',  // 5,5 
                        'Day', 'Days');
      $debug = false; 
      $sec = time() - (( strtotime($date)) ? strtotime($date) : $date); 
       
      if ( $sec <= 0) return $timeStrings[0]; 
       
      if ( $sec < 2) return $sec." ".$timeStrings[1]; 
      if ( $sec < 60) return $sec." ".$timeStrings[2]; 
       
      $min = $sec / 60; 
      if ( floor($min+0.5) < 2) return floor($min+0.5)." ".$timeStrings[3]; 
      if ( $min < 60) return floor($min+0.5)." ".$timeStrings[4]; 
       
      $hrs = $min / 60; 
      echo ($debug == true) ? "hours: ".floor($hrs+0.5)."<br />" : ''; 
      if ( floor($hrs+0.5) < 2) return floor($hrs+0.5)." ".$timeStrings[5]; 
      if ( $hrs < 24) return floor($hrs+0.5)." ".$timeStrings[6]; 
       
      return date('d M, Y',strtotime($date)); ;
} 

?> 