<?php                                                                    
  include_once(realpath(dirname(__FILE__)) . "/Loginviaurl.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Administrateur.class.php");
  include_once(realpath(dirname(__FILE__)) . "/../../../classes/Variable.class.php");
	autorisation("loginviaurl");   
?>

<div id="contenu_int"> 
  <p align="left">
    <a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0" />Login via URL            
  </p>
  <div class="entete_liste_client">
  	<div class="titre">Login via URL</div>
  </div>

  <ul class="Nav_bloc_description">
		<li style="height:25px; width:158px;"><?php echo trad('Nom', 'admin'); ?> et <?php echo trad('Prenom', 'admin'); ?></li>
		<li style="height:25px; width:157px; border-left:1px solid #96A8B5;"><?php echo trad('Identifiant', 'admin'); ?></li>
		<li style="height:25px; width:137px; border-left:1px solid #96A8B5;">Destination</li>
		<li style="height:25px; width:117px; border-left:1px solid #96A8B5;">Lien de login direct</li>
		<li style="height:25px; width:30px;">&nbsp;</li>
  </ul>

  <?php             
              
	$administrateur = new Administrateur();
  $loginviaurl = new Loginviaurl();
  $variable = new Variable();
  
  $variable->charger('urlsite');

 	$query = "select nom,prenom,identifiant,id from $administrateur->table where 1";
	$resul = mysql_query($query, $administrateur->link);
	$i=0;
	while($row = mysql_fetch_object($resul)){
		if(!($i%2)) $fond="ligne_claire_rub";
			else $fond="ligne_fonce_rub";
			$i++;
      
      $query_loginviaurl = "select login_key,redirect from $loginviaurl->table where id_admin=$row->id";
	    $resul_loginviaurl = mysql_query($query_loginviaurl, $loginviaurl->link);
      $row_loginviaurl = mysql_fetch_object($resul_loginviaurl)
        
 	 ?>
   <form action="module.php" id="formadmin<?php echo($row->id); ?>" method="post">
		<ul class="<?php echo $fond; ?>">
			<li style="width:150px;"><?php echo $row->nom; ?> <?php echo $row->prenom; ?></li>
			<li style="width:150px; border-left:1px solid #96A8B5;"><?php echo  $row->identifiant; ?></li>
			<li style="width:130px; border-left:1px solid #96A8B5;"><input name="destination" id="destination<?php echo($row->id); ?>" type="text" value="<?php echo $row_loginviaurl->redirect; ?>" class="form" style="width:130px;" /></li>
			<li style="width:150px; border-left:1px solid #96A8B5;"><a href="<?php echo $variable->valeur; ?>/client/plugins/loginviaurl/login.php?key=<?php echo $row_loginviaurl->login_key; ?>" target="_blank"><?php echo  $row_loginviaurl->login_key; ?></a></li>
      <li style="width:50px; border-left:1px solid #96A8B5;"><a href="#" class="modifier"><?php echo trad('modifier', 'admin'); ?></a></li>
			<li style="width:20px; border-left:1px solid #96A8B5; text-align:right;"><a href="module.php?nom=loginviaurl&amp;action=supprimer&amp;id=<?php echo($row->id); ?>" class="supprimer"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></li>
		</ul>
 	  <input type="hidden" name="action" value="modifier" />
    <input type="hidden" name="nom" value="loginviaurl" />
   	<input type="hidden" name="id" value="<?php echo($row->id); ?>" />
  </form>
	<?php } ?>
          
</div>
