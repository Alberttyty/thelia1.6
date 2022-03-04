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
@ini_set('default_socket_timeout', 5);
require_once("pre.php");
session_start();
header("Content-type: text/html; charset=utf-8");
if(isset($action)) if($action == "deconnexion") unset($_SESSION["util"]);
require_once("../lib/simplepie.inc");

function couperTexte($texte, $nbcar) {
	if(strlen($texte) < $nbcar) return $texte;

	$arr = explode("\n", wordwrap ($texte, $nbcar, "\n"));
	return $arr[0] . "…";
}

function lire_feed($url, $nb=3) {
	$feed = new SimplePie($url, '../client/cache/flux');
	$feed->init();
	$feed->handle_content_type();

	$tab = $feed->get_items();

	return (count($tab) > 0) ? array_slice($tab, 0, 3) : false;
}

function afficher_feed($url, $picto, $nb = 3) {
	$items = lire_feed($url, $nb);

	if($items !== false) {
        foreach($items as $item) {
			$link = $item->get_permalink();

			$title = strip_tags($item->get_title());
			$author = strip_tags($item->get_author());
			$description = $item->get_description();
			$date = $item->get_date('d/m/Y');

			?>
			<div class="Bloc_news_index">
				<div class="picto"><img src="gfx/<?php echo $picto ?>" /></div>
				<ul class="texte">
					<li class="date"><?php echo($date); ?></li>
					<li class="titre"><a href="<?php echo($link); ?>" target="_blank"><?php echo $title; ?></a></li>
					<li><?php echo couperTexte($description, 150); ?></li>
				</ul>
			</div>
			<?php
        }
	}
}
?>
<!DOCTYPE html>
<html>
<head><?php require_once("title.php");?></head>
<body class="login">
		<div id="entete">
				<div class="logo"><a href="accueil.php"><img src="gfx/thelia_logo.jpg" alt="THELIA solution e-commerce" /></a></div>

				<div id="menuGeneral">
						<div id="formConnex">

								<form action="accueil.php" method="post" id="formulaire">
										<input name="action" type="hidden" value="identifier" />
				     				<div>
                				<label>Nom d'utilisateur :
				           					<input name="identifiant" type="text" class="form" size="19" />
												</label>
              			</div><div>
				        				<label>Mot de passe :
				           					<input name="motdepasse" type="password" class="form" size="19" />
												</label>
                		</div><div>
				       					<input type="submit" value="Valider"/>
                		</div>
								</form>

						</div>
				</div>

				<?php require_once("pied.php"); ?>
		</div>
</body>
</html>
