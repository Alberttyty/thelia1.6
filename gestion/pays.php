<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia - Gestion des pays depuis le back office                              */
/*                                                                                   */
/*      Copyright (c) 2009-2001, Franck Allimant, CQFDev                             */
/*		email : contact@cqfdev.fr		        	                             	 */
/*      web : http://www.cqfdev.fr                                                	 */
/*                                                                                   */
/*      This program is free software; you can redistribute it and/or modify         */
/*      it under the terms of the GNU General Public License as published by         */
/*      the Free Software Foundation; either version 2 of the License, or            */
/*      (at your option) any later version.                                          */
/*                                                                                   */
/*      This program is distributed in the hope that it will be useful,              */
/*      but WITHOUT ANY WARRANTY; without even the implied warranty of               */
/*      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the                */
/*      GNU General Public License for more details.                                 */
/*                                                                                   */
/*      You should have received a copy of the GNU General Public License            */
/*      along with this program; if not, write to the Free Software                  */
/*      Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA    */
/*                                                                                   */
/*************************************************************************************/

require_once("pre.php");
require_once("auth.php");

if(! est_autorise("acces_configuration")) exit;

?>

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

		class AdmGestionpays
		{
			function __construct()
			{
			}

			function ajouter()
			{
			}

			function supprimer()
			{
				$id = intval(lireParam('pays', 'int'));

				mysql_query("delete from pays where id=$id");
				mysql_query("delete from paysdesc where pays=$id");
			}

			function edition()
			{
				$id = intval(lireParam('pays', 'int'));

				$maj = ($id > 0);

			  	$pays = new Pays();

			  	if ($maj)
			  		$pays->charger($id);
			  	else
			  	{
			  		$pays->lang = 0;
			  		$pays->defaut = 0;
			  	}

			  	$pays->tva = intval($_REQUEST['tva']) != 0 ? 1 : 0;
			  	$pays->zone = intval($_REQUEST['zone']);

			  	$pays->isocode = intval($_REQUEST['isocode']);
			  	$pays->isoalpha2 = $_REQUEST['isoalpha2'];
			  	$pays->isoalpha3 = $_REQUEST['isoalpha3'];

			  	if ($maj)
			  		$pays->maj();
			  	else
			  		$id = $pays->add();

				if ($id > 0)
				{
					foreach($_REQUEST['langue'] as $langue)
					{
					  	$paysdesc = new Paysdesc();

					  	if ($maj) $paysdesc->charger($id, $langue);

					  	$paysdesc->pays = $id;
					  	$paysdesc->lang = $langue;
					  	$paysdesc->titre = $_REQUEST['titre'][$langue];
					  	$paysdesc->chapo = $_REQUEST['chapo'][$langue];
					  	$paysdesc->description = $_REQUEST['description'][$langue];

					  	if ($maj)
					  		$paysdesc->maj();
					  	else
					  		$paysdesc->add();
					}
				}
			}

			function prepare_page()
			{
			}
		}

		$adm = new AdmGestionpays();

		$commande = lireParam('commande', 'string');

		switch($commande)
		{
		        case 'edition' :
		            $adm->edition();
		        break;

		        case 'supprimer' :
		            $adm->supprimer();
		        break;
		}

		$adm->prepare_page();
		?>

<script type="text/javascript">

	var ajaxurl = 'ajax/pays.php';

	function supprimer_pays(id)
	{
		if (confirm("<?php echo trad('Supprimer définitivement ce pays ?', 'admin'); ?>"))
		{
			$('#commande').val('supprimer');
			$('#pays').val(id);
			$('#offset').val($('#scroller').scrollTop());

			$('#pays_form').submit();
		}

		return false;
	}

	function change_tva(id)
	{
		jQuery.ajax({
			url: ajaxurl,
			type:'POST',
			data: { action: 'tva', pays: id, tva: ($('input[name=tva-'+id+']').attr('checked') ? '1' : '0') },
			async: false
		})
	}

	function change_defaut(id)
	{
		jQuery.ajax({
			url: ajaxurl,
			type:'POST',
			data: { action: 'defaut', pays: id },
			async: false
		})
	}

    function change_boutique(id)
    {
        jQuery.ajax({
            url: ajaxurl,
            type:'POST',
            data: { action: 'boutique', pays: id },
            async: false
        })
    }

	function nouveau()
	{
		$('.bloc_modif_pays').hide();
		$('.bloc_load_pays').show();

		$('.bloc_modif_pays').load(ajaxurl, {}, function() {
			$('.bloc_load_pays').hide();
			$('.bloc_modif_pays').show();
		});
	}

	function modifier(id, link)
	{
		$('.bloc_modif_pays').hide();
		$('.bloc_load_pays').show();

		$('.bloc_modif_pays').load(ajaxurl, {pays: id, offset: $('#scroller').scrollTop()}, function() {
			$('.bloc_load_pays').hide();
			$('.bloc_modif_pays').show();
		});
	}

	$(document).ready(function() {

		// Scroller vers le dernier element modifié/supprime
		<?php if (isset($_REQUEST['offset'])) { ?>
		$('#scroller').scrollTop(<?php echo $_REQUEST['offset'] ?>);
		<?php } ?>
	});

</script>

<style type="text/css">
	#contenu_int table th {
		height: 30px;
		font-weight: bold;
	}
</style>

<div id="contenu_int">
 <p align="left"><a href="index.php" class="lien04"><?php echo trad('Accueil', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="configuration.php" class="lien04"> <?php echo trad('Configuration', 'admin'); ?></a> <img src="gfx/suivant.gif" width="12" height="9" border="0" /><a href="pays.php" class="lien04"><?php echo trad('Gestion des pays', 'admin'); ?></a></p>

	<div id="bloc_description">

		<div class="entete_liste_config">
			<div class="titre"><?php echo trad('GESTION DES PAYS', 'admin'); ?></div>
			<div class="fonction_ajout"><a href="#" onclick="nouveau(); return false;"><?php echo trad('NOUVEAU', 'admin'); ?></a></div>
		</div>

		<div id="scroller" style="border: 1px solid #9DACB6; border-top: none; height: 571px; overflow-y: scroll; overflox-x: hidden">
	        <form method="post" action="pays.php" id="pays_form">

		        <input type="hidden" name="commande" id="commande" value="" />
		        <input type="hidden" name="pays" id="pays" value="" />
		        <input type="hidden" name="offset" id="offset" value="0" />

				<table width="100%">
					<tr>
						<th><?php echo trad('ID', 'admin'); ?></th>
						<th><?php echo trad('Nom', 'admin'); ?></th>
						<th style="text-align: center;"><?php echo trad('TVA', 'admin'); ?></th>
						<th style="text-align: center;"><?php echo trad('Défaut', 'admin'); ?></th>
                        <th style="text-align: center;"><?php echo trad('boutique', 'admin') ?></th>
						<th style="text-align: center;"><?php echo trad('N° ISO', 'admin'); ?></th>
						<th style="text-align: center;"><?php echo trad('Codes ISO', 'admin'); ?></th>

						<th>&nbsp;</th>
						<th>&nbsp;</th>
					</tr>
					<?php
					$query = "SELECT *,p.id as id_pays FROM pays p left join paysdesc pd on pd.pays = p.id where pd.lang=".ActionsLang::instance()->get_id_langue_courante()." order by pd.titre";

					$result = mysql_query($query);

					$style = 'fonce';

					while ($result && $row = mysql_fetch_object($result))
					{
						?>
						<tr class="<?php echo $style ?>">
							<td style="padding-left: 5px;"><?php echo $row->id_pays ?></td>
							<td><?php echo $row->titre != '' ? $row->titre : '<i>Non spécifié</i>' ?></td>
							<td style="text-align: center;"><input type="checkbox" name="tva-<?php echo($row->id_pays); ?>" value="1" <?php echo $row->tva ? 'checked="checked"' : '' ?> onclick="change_tva(<?php echo($row->id_pays); ?>);" /></td>
							<td style="text-align: center;"><input type="radio" name="defaut" value="<?php echo($row->id_pays); ?>" <?php echo $row->defaut ? 'checked="checked"' : '' ?> onclick="change_defaut(<?php echo($row->id_pays); ?>);" /></td>
                            <td style="text-align: center;"><input type="radio" name="boutique" value="<?php echo($row->id_pays); ?>" <?php echo $row->boutique ? 'checked="checked"' : '' ?> onclick="change_boutique(<?php echo($row->id_pays); ?>);" /></td>
							<td style="text-align: center;"><?php echo $row->isocode; ?></td>
							<td style="text-align: center;"><?php echo $row->isoalpha2; ?> / <?php echo $row->isoalpha3; ?></td>

							<td><a href="#" onclick="modifier(<?php echo($row->id_pays); ?>, $(this)); return false;"><?php echo trad('édition', 'admin'); ?></a></td>
							<td style="text-align: center;padding-right: 5px; padding-left: 5px;"><a href="#" onclick="return supprimer_pays(<?php echo($row->id_pays); ?>)" title="<?php echo trad('Supprimer ce pays', 'admin'); ?>"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></td>
						</tr>
						<?php

						$style = $style == 'fonce' ? 'claire' : 'fonce';
					}
					?>
				</table>
			</form>
		</div>
	</div>
<div id="bloc_colonne_droite" class="bloc_load_pays" style="display: none;">

		<div class="entete_config">
		  <div class="titre"><?php echo trad('Chargement en cours...', 'admin'); ?></div>
		</div>
</div>

<div id="bloc_colonne_droite" class="bloc_modif_pays"></div>


<!-- fin du bloc de description / colonne de gauche -->
</div>
<?php require_once("pied.php");?>
</div>
</div>
</body>
</html>