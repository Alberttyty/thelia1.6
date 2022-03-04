<?php

function convertColor($color){
  #convert hexadecimal to RGB
  if(!is_array($color) && preg_match("/^[#]([0-9a-fA-F]{6})$/",$color)){
  
  $hex_R = substr($color,1,2);
  $hex_G = substr($color,3,2);
  $hex_B = substr($color,5,2);
  $RGB = hexdec($hex_R).",".hexdec($hex_G).",".hexdec($hex_B);
  
  return $RGB;
  }
  
  #convert RGB to hexadecimal
  else{
  if(!is_array($color)){$color = explode(",",$color);}
  
  foreach($color as $value){
  $hex_value = dechex($value); 
  if(strlen($hex_value)<2){$hex_value="0".$hex_value;}
  $hex_RGB.=$hex_value;
  }
  
  return "#".$hex_RGB;
  }
}

?>