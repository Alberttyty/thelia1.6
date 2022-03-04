<?php

	include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");

	$cle = "352edd116ce090947daa11ae681af2c0";
	$secret = "5d29a81888e1a624249e77b704dcf77a";
	
	$serveur_smtp = "in.mailjet.com";
  
  	// serveur
  	$port_smtp = 443;
  	$auth_smtp = true;
  	$secur_smtp = 'ssl';
  
  	// local
  	/*$port_smtp = 587;
  	$auth_smtp = true;
  	$secur_smtp = '';*/
	
	$mailpenv = 4;
  
  	$resize_width=630;
  	$resize_height=630;
	
	$urlsite = new Variable();
	$urlsite->charger("urlsite");
	
	$nom_site = new Variable();
	$nom_site->charger("nomsite");
  
?>
