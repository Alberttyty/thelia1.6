<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) OpenStudio                                                     */
/*		email : info@thelia.net                                                      */
/*      web : http://www.thelia.net                                                  */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 3 of the License                */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*	    along with this program. If not, see <http://www.gnu.org/licenses/>.         */
/*                                                                                   */
/*************************************************************************************/
	require_once("pre.php");
	require_once("auth.php");
?>
<?php if(! est_autorise("acces_configuration")) exit; ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php require_once("title.php");?>
</head>

<body>
<div id="wrapper">
<div id="subwrapper">

<?php
	$menu="configuration";
	require_once("entete.php");
?>
<?php
class AdmParseur extends Parseur
{
	function __construct()
	{
		parent::__construct();
	}

	public function get_cache_dir()
	{
		return realpath($this->cache_dir);
	}

	function update_config()
	{
        foreach($_REQUEST as $var => $value)
        {
            if (! preg_match('/^'.Parseur::PREFIXE.'/', $var)) continue;

            Variable::ecrire($var, $value);
        }

        // Bug 1.4.3.1
        if (class_exists('CacheBase')) CacheBase::getCache()->reset_cache();
	}

	function prepare_page()
	{
	 	$date = intval(Variable::lire(Parseur::PREFIXE.'_cache_check_time'));

	 	$this->last_date = $date > 0 ? date("d/m/Y H:i:s", $date) : 'Jamais';
	 	$this->next_date = date("d/m/Y H:i:s", $date + 3600 * intval(Variable::lire(Parseur::PREFIXE.'_cache_check_period')));

	 	if (is_dir($this->cache_dir)) $files = scandir ($this->cache_dir);

	 	$this->cache_count = count($files) - 2; // -2 pour '.' et '..'

	}

    public function make_yes_no_radio($var_name)
    {
        $val = Variable::lire($var_name);

        echo '<input type="radio" name="'.$var_name.'" value="1"'.($val == 1 ? ' checked="checked"':'').'>' . trad('Oui', 'admin') . '
              <input type="radio" name="'.$var_name.'" value="0"'.($val == 0 ? ' checked="checked"':'').'>' . trad('Non', 'admin');
    }

    public function clear_cache()
    {
    	if ($dh = opendir($this->cache_dir))
    	{
    		while ($file = readdir($dh))
    		{
    			if ($file == '.' || $file == '..') continue;

    			unlink($this->cache_dir . '/' . $file);
    		}
    	}
        
        ActionsModules::instance()->appel_module("clear_cache");
    }

    public function check_cache()
    {
    	Analyse::cleanup_cache($this->cache_dir, 1);
    }

    public function check_cache_dir()
    {
    	if (! is_dir($this->cache_dir))
    	{
        	mkdir($this->cache_dir, 0777, true);

        	@clearstatcache();
    	}
    }
}

$adm = new AdmParseur();

$commande = lireParam('commande', 'string');

switch($commande)
{
        case 'maj_config' :
            $adm->update_config();
        break;

        case 'clear_cache' :
            $adm->clear_cache();
        break;

        case 'check_cache' :
            $adm->check_cache();
        break;

        case 'patch' :
            $adm->patch();
        break;

        case 'check_cache_dir' :
        	$adm->check_cache_dir();
        break;
}

$adm->prepare_page();
?>

<div id="contenu_int">
   <p align="left"><span class="lien04"><a href="accueil.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a></span> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"> <?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="cache.php" class="lien04"> <?php echo trad('Gestion_cache', 'admin'); ?></a></p>

	</p>

	<div id="bloc_description">


		<div class="entete">
			<div class="titre"><?php echo trad('CONFIGURATION', 'admin'); ?></div>
			<div class="fonction_valider"><a href="#" onclick="document.getElementById('<?php echo Parseur::PREFIXE ?>_form').submit(); return false;"><?php echo trad('VALIDER_LES_MODIFICATIONS', 'admin'); ?></a></div>
		</div>

        <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>" id="<?php echo Parseur::PREFIXE ?>_form">
        <input type="hidden" name="commande" id="commande" value="maj_config" />

		 	<table width="100%" cellpadding="5" cellspacing="0">
			 	<tr class="claire">
					<td width="70%" class="designation"><?php echo trad('Utiliser_cache', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('ameliore_parseur', 'admin'); ?> <?php echo $adm->get_cache_dir() ?></div></td>
					<td><?php $adm->make_yes_no_radio(Parseur::PREFIXE.'_use_cache') ?></td>
				</tr>

			 	<tr class="fonce">
					<td width="70%" class="designation"><?php echo trad('duree_vie', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('detail_duree_vie', 'admin'); ?></div></td>
					<td><input type="text" size="5" name="<?php echo Parseur::PREFIXE.'_cache_file_lifetime' ?>" value="<?php echo intval(Variable::lire(Parseur::PREFIXE.'_cache_file_lifetime')); ?>" /> <?php echo trad('heures', 'admin'); ?></td>
				</tr>

			 	<tr class="claire">
					<td width="70%" class="designation"><?php echo trad('Periode_examen', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('detail_periode_examen', 'admin'); ?></div></td>
					<td><input type="text" size="5" name="<?php echo Parseur::PREFIXE.'_cache_check_period' ?>" value="<?php echo intval(Variable::lire(Parseur::PREFIXE.'_cache_check_period')); ?>" /> <?php echo trad('heures', 'admin'); ?></td>
				</tr>

			 	<tr class="fonce">
					<td width="70%" class="designation">
					<table style="margin: 0; padding: 0; background-color: #9EB0BE" cellspacing="0" cellpadding="2">
						<tr><td><?php echo trad('Fichier_actuellement', 'admin'); ?>:</td><td><?php echo $adm->cache_count; ?></td></tr>
						<tr><td><?php echo trad('Dernier_examen', 'admin'); ?>:</td><td><?php echo $adm->last_date;  ?></td></tr>
						<tr><td><?php echo trad('Prochain_examen', 'admin'); ?>:</td><td><?php echo $adm->next_date;  ?></td></tr>
					</table>
					</td>
					<td><button onclick="document.getElementById('commande').value ='check_cache'; this.form.submit();"><?php echo trad('examiner_cache', 'admin'); ?></button></td>
				</tr>

			 	<tr class="claire">
					<td class="designation"><?php echo trad('Vider_cache_parseur', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('Avant_mise_production', 'admin'); ?></div></td>
					<td><button onclick="document.getElementById('commande').value ='clear_cache'; this.form.submit();"><?php echo trad('Vider_cache', 'admin'); ?></button></td>
				</tr>

			 	<tr class="fonce">
					<td class="designation"><?php echo trad('Ajouter_temps', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('Commentaire_avant_alt', 'admin'); ?></div></td>
					<td><?php $adm->make_yes_no_radio(Parseur::PREFIXE.'_show_time') ?></td>
				</tr>

			 	<tr class="clairebottom">
					<td class="designation"><?php echo trad('Permettre_affichage', 'admin'); ?><div style="font-size: 0.8em"><?php echo trad('detail_info_debog', 'admin'); ?></div></td>
					<td><?php $adm->make_yes_no_radio(Parseur::PREFIXE.'_allow_debug') ?></td>
				</tr>
			</table>
		</form>

	</div>

<!-- fin du bloc de description / colonne de gauche -->
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>
