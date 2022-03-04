<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("titlemeta");

include_once(realpath(dirname(__FILE__)) . "/../../../classes/Cnx.class.php");
require_once(realpath(dirname(__FILE__)) . "/Titlemeta.class.php");

$objet = new Titlemeta();
$objetdesc = new Titlemetadesc();
$cnx = new Cnx();

if (!isset($_GET['lang']))
    $_GET['lang'] = "1";

$objet->rubrique = $_GET['id'];
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
<div>
    <input type="hidden" name="titlemeta_action" value="modifier"/>
    <input type="hidden" name="titlemeta_lang" value="<?php echo $_GET['lang']; ?>"/>
    <input type="hidden" name="titlemeta_rubrique" value="<?php echo $_GET['id']; ?>"/>
    <input type="hidden" name="titlemeta_produit" value="0"/>
    <input type="hidden" name="titlemeta_dossier" value="0"/>
    <input type="hidden" name="titlemeta_contenu" value="0"/>
</div>
<div class="entete">
    <div class="titre">SEO</div>
    <div class="fonction_valider"><a href="#" onclick="document.getElementById('formulaire').submit()">VALIDER LES
            MODIFICATIONS</a></div>
</div>
<table width="100%" cellpadding="5" cellspacing="0">
    <tr class="fonce">
        <td class="designation">Titre <span id="titlemeta_title_nbr" class="note"></span></td>
        <td><input name="titlemeta_title" id="titlemeta_title" type="text" class="form_long"
                   value="<?php echo($objetdesc->title); ?>"/></td>
    </tr>
    <tr class="claire">
        <td class="designation">Description<br/> <span id="titlemeta_metadesc_nbr" class="note"></span></td>
        <td>
            <textarea name="titlemeta_metadesc" id="titlemeta_metadesc" cols="40" rows="2"
                      class="form_long"><?php echo($objetdesc->metadesc); ?></textarea></td>
    </tr>
    <tr class="fonce">
        <td class="designation">Mots-cl&eacute;s<br/> <span id="titlemeta_metakeyword_nbr" class="note"></span></td>
        <td>
            <textarea name="titlemeta_metakeyword" id="titlemeta_metakeyword" cols="40" rows="2"
                      class="form_long"><?php echo($objetdesc->metakeyword); ?></textarea></td>
    </tr>
</table>