<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                                     */
/*                                                                                   */
/*      Copyright (c) 2005-2013 OpenStudio                                           */
/*      email : info@thelia.fr                                                       */
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
/*      along with this program.  If not, see <http://www.gnu.org/licenses/>.        */
/*                                                                                   */
/*************************************************************************************/
require_once(__DIR__."/FichierAdminBase.class.php");

class ImagesAdmin extends FichierAdminBase {

	const NOMBRE_UPLOAD = 1;

	public function __construct($typeobjet, $idobjet, $lang) {
		parent::__construct("Image", self::NOMBRE_UPLOAD, $typeobjet, $idobjet, $lang);
	}

	public function action($action) {

		switch($action){
			case 'ajouterphoto' :
				$this->ajouter($_REQUEST['id'], "photo", array("jpg", "gif", "png", "jpeg"), "uploadimage");
			break;

			case 'modifierphoto' :
				$this->modifier($_REQUEST['id_photo'], $_REQUEST['titre_photo'], $_REQUEST['chapo_photo'], $_REQUEST['description_photo']);
			break;

			case 'supprimerphoto' :
				$this->supprimer($_REQUEST['id_photo']);
			break;

			case 'modclassementphoto' :
				$this->modclassement($_REQUEST['id_photo'],$_REQUEST['type']);
			break;
		}
	}

	protected function chemin_objet($fichier) {
		return sprintf("../".FICHIER_URL."client/gfx/photos/$this->typeobjet/%s", $fichier);
	}

	public function bloc_transfert() {
		?>
		<!-- bloc transfert des images -->
		<div class="bloc_transfert">
			<div class="claire">
				<div class="designation" style="height:60px; padding-top:10px;"><?php echo trad('Transferer_images', 'admin'); ?></div>
				<div class="champs" style="padding-top:10px;">
					<form action="<?php echo $this->nompageadmin; ?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="ajouterphoto" />
						<input type="hidden" name="id" value="<?php echo $this->idobjet; ?>" />
						<input type="hidden" name="lang" value="<?php echo $this->lang; ?>" />
						<?php $this->form_hidden_fieds(); ?>

		      			<?php for($i=1; $i<=self::NOMBRE_UPLOAD; $i++) { ?>
			      			<input type="file" id="photo<?php echo($i); ?>" name="photo<?php echo($i); ?>" class="form" style="width:100%;box-sizing:border-box;" /><br/>
			  			<?php } ?>
			        	<input type="submit" value="<?php echo trad('Ajouter', 'admin'); ?>" />
		   			</form>
		   		</div>
		   	</div>
		</div>
		<?php
	}

	public function bloc_gestion() {

		$image = new Image();

		$query = "select * from $image->table where $this->typeobjet='$this->idobjet' order by classement";
		$resul = $image->query($query);

		while($resul && $row = $image->fetch_object($resul)) {
			$imagedesc = new Imagedesc();
			$imagedesc->charger($row->id,$this->lang);
	   		?>

			<form action="<?php echo $this->nompageadmin; ?>" method="post">
			<input type="hidden" name="action" value="modifierphoto" />
			<input type="hidden" name="id_photo" value="<?php echo $row->id; ?>" />
			<input type="hidden" name="id" value="<?php echo $this->idobjet; ?>" />
			<input type="hidden" name="lang" value="<?php echo $this->lang; ?>" />
			<?php $this->form_hidden_fieds(); ?>

			<ul>
				<li class="lignesimple">
					<div class="cellule_designation" style="height:208px;">&nbsp;</div>
					<div class="cellule_photos" style="height:200px; overflow:hidden;">
						<a href="<?php echo($this->chemin_objet($row->fichier)); ?>" target="_blank">
							<img src="../fonctions/redimlive.php?type=<?php echo $this->typeobjet ?>&nomorig=<?php echo($row->fichier); ?>&width=&height=200&opacite=&nb=" border="0" />
						</a>
					</div>
					<div class="cellule_supp"><a onclick="return confirm('<?php echo trad('Supprimer définitivement cette image ?', 'admin'); ?>');" href="<?php echo $this->url_page_admin() ?>&id_photo=<?php echo($row->id); ?>&action=supprimerphoto"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></div>
				</li>
				<li class="lignesimple">
					<div class="cellule_designation" style="height:30px;"><?php echo trad('Titre', 'admin'); ?></div>
					<div class="cellule">
					<input type="text" name="titre_photo" style="width:219px;" class="form" value="<?php echo  htmlspecialchars($imagedesc->titre) ?>" />
					</div>
				</li>
				<li class="lignesimple">
					<div class="cellule_designation" style="height:50px;"><?php echo trad('Chapo', 'admin'); ?></div>
					<div class="cellule"><textarea name="chapo_photo" rows="2" class="form" style="width:219px;"><?php echo $imagedesc->chapo ?></textarea></div>
				</li>
				<li class="lignesimple">
					<div class="cellule_designation" style="height:65px;"><?php echo trad('Description', 'admin'); ?></div>
					<div class="cellule"><textarea name="description_photo" class="form" rows="3"><?php echo $imagedesc->description ?></textarea></div>
				</li>

				<li class="lignesimple">
					<div class="cellule_designation" style="height:30px;"><?php echo trad('Classement', 'admin'); ?></div>
					<div class="cellule">
						<div class="classement">
							<a href="<?php echo $this->url_page_admin() . "&id_photo=".$row->id."&action=modclassementphoto&type=M"; ?>">
								<img src="gfx/up.gif" border="0" />
							</a>
						</div>
						<div class="classement">
							<a href="<?php echo $this->url_page_admin() . "&id_photo=".$row->id."&action=modclassementphoto&type=D"; ?>">
								<img src="gfx/dn.gif" border="0" />
							</a>
						</div>
					</div>

				</li>

				<li class="lignesimple">
					<div class="cellule_designation" style="height:30px;">&nbsp;</div>
					<div class="cellule" style="height:30px; border-bottom: 1px dotted #9DACB6">
						<input type="submit" value="<?php echo trad('Enregistrer', 'admin'); ?>" />
					</div>
				</li>
			</ul>
			</form>

	   		<?php
		}
	}
}
?>