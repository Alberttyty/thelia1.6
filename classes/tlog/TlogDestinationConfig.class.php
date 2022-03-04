<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) OpenStudio		                                             */
/*		email : thelia@openstudio.fr		        	                          	 */
/*      web : http://www.openstudio.fr						   						 */
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
/*	    along with this program.  If not, see <http://www.gnu.org/licenses/>.		 */
/*                                                                                   */
/*************************************************************************************/

require_once __DIR__ . "/../../fonctions/autoload.php";

class TlogDestinationConfig extends Variable
{
		const TYPE_TEXTAREA = 1;
		const TYPE_TEXTFIELD = 2;

    public $titre;
    public $label;
    public $defaut;
    public $type;
    public $valeur;

    public function __construct($nom, $titre, $label, $defaut, $type)
		{
				parent::__construct();

        $this->nom = $nom;
        $this->titre = $titre;
        $this->label = $label;
        $this->defaut = $defaut;
        $this->type = $type;

				$this->charger();
    }

    public function charger($var1 = null, $var2 = null)
		{
        // La variable n'existe pas ? La créer en y affectant la valeur par defaut
        if (! parent::charger($this->nom)) {
	        	$this->valeur = $this->defaut;
	        	$this->protege = 1;
						$this->cache = 1;

						$this->add();
        }
    }
}
?>
