<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("titlemeta");

require_once(realpath(dirname(__FILE__)) . "/../../../fonctions/divers.php");


if (isset($_POST['titlemeta_action']) && $_POST['titlemeta_action'] == "modifier")
{
    ActionsModules::appel_module("titlemeta_action", $_POST);
}