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
abstract class FichierAdminBase {

	protected $typeobjet;
	protected $idobjet;
	protected $class;
	protected $classdesc;
	protected $lang;
	protected $nompageadmin;

	protected $produit = false;
	protected $contenu = false;

	protected $nombre_champs_upload;

	public function __construct($class, $nombre_champs_upload, $typeobjet, $idobjet, $lang) {

		$this->typeobjet = $typeobjet;
		$this->lang = $lang;

		$this->class = ucfirst(strtolower($class));
		$this->classdesc = ucfirst(strtolower($class)."desc");

		$this->nompageadmin = $this->typeobjet."_modifier.php";
		$this->nombre_champs_upload = $nombre_champs_upload;

			if ($this->typeobjet == 'produit') {
			$this->produit = new Produit($idobjet);
			$this->idobjet = $this->produit->id;
		} else if ($this->typeobjet == 'contenu') {
			$this->contenu = new Contenu($idobjet);
			$this->idobjet = $idobjet;
		} else
			$this->idobjet = $idobjet;
	}

	public function url_page_admin() {
		if ($this->produit)
			$url = "$this->nompageadmin?ref=" . $this->produit->ref . "&rubrique=" . $this->produit->rubrique."&lang=".$this->lang;
		else if ($this->contenu)
			$url = "$this->nompageadmin?id=" . $this->idobjet."&dossier=".$this->contenu->dossier."&lang=".$this->lang;
		else {
			$url = "$this->nompageadmin?id=" . $this->idobjet."&lang=".$this->lang;
		}

		return $url;
	}

	public function form_hidden_fieds() {

		if ($this->produit) {
			?>
			<input type="hidden" name="ref" value="<?php echo($this->produit->ref); ?>" />
			<input type="hidden" name="rubrique" value="<?php echo($this->produit->rubrique); ?>" />
			<?php
		}
		else if ($this->contenu) {
			?>
			<input type="hidden" name="dossier" value="<?php echo($this->contenu->dossier); ?>" />
			<?php
		}
	}

	protected abstract function chemin_objet($fichier);

	protected function modclassement($id, $type){
		$obj = new $this->class();
		if ($obj->charger($id)) $obj->changer_classement($id, $type);
	}

	protected function supprimer($id){

		$obj = new $this->class();

		if ($obj->charger($id)) {
			if(file_exists($this->chemin_objet($obj->fichier))) unlink($this->chemin_objet($obj->fichier));
			$obj->delete();
		}

		redirige($this->url_page_admin());
	}

	protected function modifier($id, $titre, $chapo, $description){

		$objdesc = new $this->classdesc();

		$colonne = strtolower($this->class);
		$objdesc->$colonne = $id;

		$objdesc->lang = $this->lang;

		$objdesc->charger($id,$this->lang);

		$objdesc->titre = $titre;
		$objdesc->chapo = $chapo;
		$objdesc->description = $description;
		$objdesc->lang = $this->lang;
		$objdesc->$colonne = $id;

		if(!$objdesc->id) $objdesc->add();
		else $objdesc->maj();

		redirige($this->url_page_admin());
	}

	protected function ajouter($id, $nom_arg, $extensions_valides = array(), $point_d_entree) {

		for($i = 1; $i <= $this->nombre_champs_upload; $i++) {

			$fichier = $_FILES[$nom_arg . $i]['tmp_name'];
			$nom = $_FILES[$nom_arg . $i]['name'];

			if($fichier != "") {
				//position du dot dans le nom du fichier
				$dot = strrpos($nom, '.');
				if($dot !== false) {
					//nom fichier (avant dot)
					$fich = substr($nom, 0, $dot);
					//extension (après dot)
          			$extension = strtolower(substr($nom, $dot+1));

					if($fich != "" && $extension != "" && (empty($extensions_valides) || (in_array($extension, $extensions_valides))) ) {
						/*********************
						 * IMAGE AUTO-RESIZE *
						 *********************/
						if($extension == 'jpg' || $extension == 'jpeg') exec('convert '.$fichier.' -quality 80 -resize 1920x1600\> '.$fichier);
						/*********************/

						$obj = new $this->class();

						$colonne = $this->typeobjet;
						$obj->$colonne = $id;

						$lastid = $obj->add();

						$obj->charger($lastid);
						$obj->fichier = eregfic(sprintf("%s_%s", $fich, $lastid)) . "." . $extension;
						$obj->maj();

						copy($fichier, $this->chemin_objet($obj->fichier));

						ActionsModules::instance()->appel_module($point_d_entree, $obj);
					}
				}
			}
		}

		redirige($this->url_page_admin());
	}

}
?>
