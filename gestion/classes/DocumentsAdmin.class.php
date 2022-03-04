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

class DocumentsAdmin extends FichierAdminBase {

	const NOMBRE_UPLOAD = 1;

	public function __construct($typeobjet, $idobjet, $lang) {
		parent::__construct("Document",  self::NOMBRE_UPLOAD, $typeobjet, $idobjet, $lang);
	}

	public function action($action) {

		switch($action){
			case 'ajouterdoc' :
				$this->ajouter($_REQUEST['id'], "doc", array(), "uploaddocument");
				break;

			case 'modifierdoc' :
				$this->modifier($_REQUEST['id_document'], $_REQUEST['titredoc'], $_REQUEST['chapodoc'], $_REQUEST['descriptiondoc']);
				break;

			case 'supprimerdoc' :
				$this->supprimer($_REQUEST['id_document']);
				break;

			case 'modclassementdoc' :
				$this->modclassement($_REQUEST['id_document'],$_REQUEST['type']);
				break;
		}
	}

	protected function chemin_objet($fichier) {
		return sprintf("../".FICHIER_URL."client/document/%s", $fichier);
	}

	public function bloc_transfert() {
		?>
		<div class="bloc_transfert">
			<div class="claire">
				<div class="designation" style="height:70px; padding-top:10px;"><?php echo trad('Transferer_documents', 'admin'); ?></div>
				<div class="champs" style="padding-top:10px;">
					<form action="<?php echo $this->nompageadmin; ?>" method="post" enctype="multipart/form-data">
						<input type="hidden" name="action" value="ajouterdoc" />

						<input type="hidden" name="id" value="<?php echo $this->idobjet; ?>" />
						<input type="hidden" name="lang" value="<?php echo $this->lang; ?>" />

						<?php $this->form_hidden_fieds(); ?>

		      			<?php for($i=1; $i<=self::NOMBRE_UPLOAD; $i++) { ?>
			      			<input type="file" id="doc<?php echo($i); ?>" name="doc<?php echo($i); ?>" class="form" style="width:100%;box-sizing:border-box;"/><br/>
			  			<?php } ?>

		      			<input type="submit" value="<?php echo trad('Ajouter', 'admin'); ?>" />
		    		</form>
				</div>
			</div>
		</div>
		<?php
	}

	public function bloc_gestion() {

		$document = new Document();
		$documentdesc = new Documentdesc();

		$query = "select * from $document->table where $this->typeobjet='$this->idobjet' order by classement";
		$resul =$document->query($query);

		while($resul && $row = $document->fetch_object($resul)) {

			$document = new Document();
			$documentdesc = new Documentdesc();

			$documentdesc->charger($row->id, $this->lang);
			?>
			<form action="<?php echo $this->nompageadmin; ?>" method="post">
				<input type="hidden" name="action" value="modifierdoc" />
				<input type="hidden" name="id_document" value="<?php echo $row->id; ?>" />

				<input type="hidden" name="id" value="<?php echo $this->idobjet; ?>" />
				<input type="hidden" name="lang" value="<?php echo $this->lang; ?>" />

				<?php $this->form_hidden_fieds(); ?>

				<ul>
	   				<li class="lignesimple">
						<div class="cellule_designation"><?php echo trad('Fichier', 'admin'); ?></div>
						<div class="cellule_document"><a href="<?php echo $this->chemin_objet($row->fichier); ?>" target="_blank"><?php if(strlen($row->fichier) > 26) echo(substr($row->fichier,0,26)." ... ".substr($row->fichier,strlen($row->fichier)-3,strlen($row->fichier)));
						else echo $row->fichier; ?></a></div>
						<div class="cellule_supp_fichier">
						<a onclick="return confirm('<?php echo trad('Supprimer définitivement ce document ?', 'admin'); ?>');" href="<?php echo $this->url_page_admin() ?>&id_document=<?php echo($row->id); ?>&action=supprimerdoc"><img src="gfx/supprimer.gif" width="9" height="9" border="0" /></a></div>
					</li>

					<li class="lignesimple">
						<div class="cellule_designation" style="height:30px;"><?php echo trad('Titre', 'admin'); ?></div>
						<div class="cellule">
						<input type="text" name="titredoc" style="width:219px;" class="form" value="<?php echo htmlspecialchars($documentdesc->titre); ?>" />
						</div>
					</li>

					<li class="lignesimple">
						<div class="cellule_designation" style="height:50px;"><?php echo trad('Chapo', 'admin'); ?></div>
						<div class="cellule"><textarea name="chapodoc" rows="2" class="form" style="width:219px;"><?php echo $documentdesc->chapo ?></textarea>
						</div>
					</li>

					<li class="lignesimple">
						<div class="cellule_designation" style="height:65px;"><?php echo trad('Description', 'admin'); ?></div>
						<div class="cellule"><textarea name="descriptiondoc" class="form" rows="3" style="width:219px;"><?php echo $documentdesc->description ?></textarea></div>
					</li>

					<li class="lignesimple">
						<div class="cellule_designation" style="height:30px;"><?php echo trad('Classement', 'admin'); ?></div>
						<div class="cellule">
							<div class="classement">
								<a href="<?php echo $this->url_page_admin() . "&id_document=".$row->id."&action=modclassementdoc&type=M"; ?>"><img src="gfx/up.gif" border="0" /></a></div>
							<div class="classement">
								<a href="<?php echo $this->url_page_admin() . "&id_document=".$row->id."&action=modclassementdoc&type=D"; ?>"><img src="gfx/dn.gif" border="0" /></a></div>
						</div>

					</li>
					<li class="lignesimple">
						<div class="cellule_designation" style="height:30px;">&nbsp;</div>
						<div class="cellule" style="height:30px; border-bottom: 1px dotted #9DACB6"><input type="submit" value="<?php echo trad('Enregistrer', 'admin'); ?>" /></div>
					</li>
				</ul>
			</form>
	   		<?php
		}
	}
}
?>