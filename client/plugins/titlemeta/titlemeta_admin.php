<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("titlemeta");

include_once(realpath(dirname(__FILE__)) . "/../../../classes/Lang.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Cnx.class.php");
require_once(realpath(dirname(__FILE__)) . "/Titlemeta.class.php");

$objet = new Titlemeta();
$objetdesc = new Titlemetadesc();
$cnx = new Cnx();

if (!isset($_GET['lang']))
{
    $_GET['lang'] = "1";
    $lang = "1";
}

$objet->rubrique = 1;
$objet->produit = 1;
$objet->contenu = 1;
$objet->dossier = 1;
$objet->charger_objet();
$objetdesc->charger_desc($objet->id, $_GET['lang']);

?>

<script type="text/javascript">
    function countchar(cible, destination) {
        $('#' + destination).html("");
        $('#' + destination).append("(" + $('#' + cible).val().length + ")");
    }
    $(document).ready(function () {
        countchar("titlemeta_title", "titlemeta_title_nbr");
        $("#titlemeta_title").keyup(function (e) {
            countchar("titlemeta_title", "titlemeta_title_nbr");
        });

        countchar("titlemeta_metadesc", "titlemeta_metadesc_nbr");
        $("#titlemeta_metadesc").keyup(function (e) {
            countchar("titlemeta_metadesc", "titlemeta_metadesc_nbr");
        });

        countchar("titlemeta_metakeyword", "titlemeta_metakeyword_nbr");
        $("#titlemeta_metakeyword").keyup(function (e) {
            countchar("titlemeta_metakeyword", "titlemeta_metakeyword_nbr");
        });
    });
</script>
<div id="contenu_int">
    <p align="left">
        <a href="accueil.php" class="lien04">Accueil </a><img src="gfx/suivant.gif" width="12" height="9" border="0"/><a
            href="module_liste.php" class="lien04">Liste des modules </a><img src="gfx/suivant.gif" width="12"
                                                                              height="9" border="0"/><a
            href="./module.php?nom=titlemeta" class="lien04">Titlemeta</a>
    </p>

    <div id="bloc_description">
        <form
            action="<?php echo($_SERVER['PHP_SELF']); ?>?nom=<?php echo($_GET['nom']); ?>&amp;lang=<?php echo($_GET['lang']); ?>"
            method="post" id="formulaire" enctype="multipart/form-data">
            <div>
                <input type="hidden" name="titlemeta_action" value="modifier"/>
                <input type="hidden" name="titlemeta_lang" value="<?php echo $_GET['lang']; ?>"/>
                <input type="hidden" name="titlemeta_rubrique" value="1"/>
                <input type="hidden" name="titlemeta_produit" value="1"/>
                <input type="hidden" name="titlemeta_dossier" value="1"/>
                <input type="hidden" name="titlemeta_contenu" value="1"/>
            </div>
            <div class="entete">
                <div class="titre">Title M&eacute;ta</div>
                <div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()">VALIDER
                        LES MODIFICATIONS</a></div>
            </div>

            <table width="100%" cellpadding="5" cellspacing="0">
                <tr class="fonce">
                    <td class="designation">Changer la langue</td>
                    <td>
                        <?php
                        $langl = new Lang();
                        $query = "SELECT * FROM " . $langl->table;
                        $resul = $langl->query($query);
                        while ($row = mysql_fetch_object($resul))
                        {
                            $langl->charger($row->id);
                            if ($_GET['lang'] == "")
                                $lang = 1;
                            ?>

                            <div class="flag<?php if ($lang == $langl->id)
                            { ?>Selected<?php } ?>"><a
                                    href="<?php echo($_SERVER['PHP_SELF']); ?>?nom=<?php echo($_GET['nom']); ?>&amp;lang=<?php echo($langl->id); ?>"><img
                                        src="gfx/lang<?php echo($langl->id); ?>.gif" alt="-"/></a></div>

                        <?php } ?>
                    </td>
                </tr>
                <tr class="claire">
                    <td class="designation">Title <span id="titlemeta_title_nbr" class="note"></span></td>
                    <td><input name="titlemeta_title" id="titlemeta_title" type="text" class="form_long"
                               value="<?php echo($objetdesc->title); ?>"/></td>
                </tr>
                <tr class="fonce">
                    <td class="designation">M&eacute;ta Description<br/> <span id="titlemeta_metadesc_nbr"
                                                                               class="note"></span></td>
                    <td><textarea name="titlemeta_metadesc" id="titlemeta_metadesc" cols="40" rows="2"
                                  class="form_long"><?php echo($objetdesc->metadesc); ?></textarea></td>
                </tr>
                <tr class="claire">
                    <td class="designation">Keyword Description<br/> <span id="titlemeta_metakeyword_nbr"
                                                                           class="note"></span></td>
                    <td><textarea name="titlemeta_metakeyword" id="titlemeta_metakeyword" cols="40" rows="2"
                                  class="form_long"><?php echo($objetdesc->metakeyword); ?></textarea></td>
                </tr>
            </table>
        </form>
    </div>
</div>