<?php
/*************************************************************************************/
/*                                                                                   */
/*      Thelia	                                                            		 */
/*                                                                                   */
/*      Copyright (c) Octolys Development		                                     */
/*		email : thelia@octolys.fr		        	                             	 */
/*      web : http://www.octolys.fr						   							 */
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
require_once __DIR__ . "/../../../fonctions/autoload.php";

class Agendadesc extends BaseobjdescReecriture
{
		public $id;
	  public $agenda;

    const TABLE = "agendadesc";
	  public $table = self::TABLE;

		public $bddvars = ["id","agenda","titre","description","lang"];

		public function __construct($agenda = 0, $lang = false)
    {
			   parent::__construct('agenda',$agenda, $lang);
		}

		public function charger($agenda = null, $lang = null)
    {
  		  if ($agenda != null) return $this->charger_desc($agenda, $lang);
  	}

    public function charger_titre($titre)
    {
  		  return $this->getVars("SELECT * FROM $this->table WHERE titre='".$this->escape_string($titre)."'");
  	}

  	protected function clef_url_reecrite()
    {
    		$agenda = new Agenda();
    		$agenda->charger_id($this->agenda);

    		return self::calculer_clef_url_reecrite($agenda->id);
  	}

  	protected function texte_url_reecrite()
    {
    		$agenda = new Agenda();
    		$agenda->charger_id($this->agenda);

    		return $agenda->id . "-" . $this->titre . ".html";
  	}

  	public static function calculer_clef_url_reecrite($id_agenda)
    {
  		  return "id_agenda=$id_agenda";
  	}
}
?>
