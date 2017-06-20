<?php
/*
 * generation_tous_scripts_sur_disque.php
 *
 * Generation sur disque dans <root_site>/include/script/ de:
 *  tous les scripts des tables de la base  (xxxxx.inc)
 *  toutes les requetes de la table entete_combo (xxxxxx.sql)
 *
 * @(#) $Id$
 *
 */
// mettre la constante DEBUG_SCRIPT à 21  pour regenerer tous les scripts même s'ils exsitent déjà.
define("DEBUG_SCRIPT",21);
include_once '../../include/base.inc';
include_once 'date/date.inc';
include_once 'global.inc';
include_once 'database.inc';
include_once 'layout.inc';
include_once 'script.inc';
if ($SITE["fonctions_specifiques"] > "" &&
    include_exists($SITE["fonctions_specifiques"]))
include_once $SITE["fonctions_specifiques"];

// variables permettant la genration des scripts et requete SQL GDS sur disque en .inc ou .sql
// Contruire requette SQL
$requette_sql = array();
$requette_sql["entete_combo"]["cle"]  = array("cle_liste");
$requette_sql["entete_combo"]["list"] = array("requete_sql");

// Construire la liste de tous les scripts de GDS
$champs_scripts = array();

/** les "scripts" sur les champs sont identique à ceux de la table "libelle" donc leur génération n'est pas forcement utile.
$champs_scripts["champ"]["cle"]         = array("code_ecran","nom_table","nom_colonne");
$champs_scripts["champ"]["list"]        = array("controle","script_affichage","script_visibilite","script_jquery",
                                                "script_affichage_colonne","script_visibilite_colonne","script_jquery_colonne");
**/

$champs_scripts["champ_reseau"]["cle"]  = array("nom_table","nom_champ","champ_session");
$champs_scripts["champ_reseau"]["list"] = array("script_initalisation");

$champs_scripts["ecran"]["cle"]         = array("code_ecran","nom_ecran","table_principale");
$champs_scripts["ecran"]["list"]        = array("script1","script2","script3","script4","script5","script6",
                                                "script7","script8","script9","script10","script11","script12",
                                                "script13", "script14", "script15", "script16", "script17", "script18");

$champs_scripts["entete_combo"]["cle"]  = array("cle_liste");
$champs_scripts["entete_combo"]["list"] = array("script_combo");

$champs_scripts["fonction_spe"]["cle"]  = array("nom_fonction");
$champs_scripts["fonction_spe"]["list"] = array("script_fonction");

$champs_scripts["groupe_ecran"]["cle"]  = array("code_groupe_ecran","libelle_groupe_ecran","nom_table");
$champs_scripts["groupe_ecran"]["list"] = array("script_validation","script_enregistrer","script1","script2","script3",
                                                "script4","script5","script6","script7","script8","script9","script10",
                                                "script11","script12", "script13", "script14", "script15", "script16", "script17", "script18",
                                                "aiguillage","script_recherche","script_affichage");

$champs_scripts["include_spe"]["cle"]   = array("nom_include");
$champs_scripts["include_spe"]["list"]  = array("script_include");

$champs_scripts["libelle"]["cle"]       = array("code_spe","libelle_groupe_ecran","nom_table","nom_champ");
$champs_scripts["libelle"]["list"]      = array("script_affichage","script_affichage_colonne","script_recherche","script_visibilite",
                                                "script_visibilite_colonne","script_visibilite_recherche","script_jquery",
                                                "script_jquery_colonne","script_jquery_recherche","controle","controle_colonne");

$champs_scripts["lien_champ"]["cle"]    = array("nom_association","table1","table2","cle_lien_table");
$champs_scripts["lien_champ"]["list"]   = array("script_liaison");

$champs_scripts["menu"]["cle"]          = array("libelle_menu","cle_menu");
$champs_scripts["menu"]["list"]         = array("script_menu");

$champs_scripts["messages"]["cle"]      = array("no_message","libelle_message");
$champs_scripts["messages"]["list"]     = array("script_message");

$champs_scripts["objet"]["cle"]         = array("cle_objet");
$champs_scripts["objet"]["list"]        = array("script");

$champs_scripts["onglet"]["cle"]        = array("code_groupe_ecran","nom_table","numero","nom");
$champs_scripts["onglet"]["list"]       = array("script","script_visibilite","script_vldt");

$champs_scripts["service_web"]["cle"]   = array("code_service_web");
$champs_scripts["service_web"]["list"]  = array("script_acces");

$champs_scripts["url"]["cle"]           = array("code_url","nom_table","nom_champ","cle_primaire");
$champs_scripts["url"]["list"]          = array("script_fusion","script_genere","script_gestion_page");

$champs_scripts["workflow_prerequis"]["cle"]  = array("cle_workflow_prerequis");
$champs_scripts["workflow_prerequis"]["list"] = array("prerequis_code");

$champs_scripts["workflow_script"]["cle"]  = array("cle_workflow_script");
$champs_scripts["workflow_script"]["list"] = array("script_code");

$champs_scripts["workflow_var"]["cle"]     = array("code_workflow","var_name");
$champs_scripts["workflow_var"]["list"]    = array("var_script");

// construire les scripts du PROJET
$champs_scripts["produit"]["cle"]       = array("code_produit");
$champs_scripts["produit"]["list"]      = array("script1","script2","script3","script4","script5","script6","script7","script8",
                                                "script9","script10");
// pour chaque script creer un script
if (!isset($ECRAN["table_principale"])) $ECRAN["table_principale"] = "information";
$erreur = array();
echo '<table border="1"><tr><td colspan="3" align="center">Creation des scripts sur les tables</td></tr>
             <tr><td width="150"><b>Nom table</b></td>
                 <td width="100" align="center"><b>nb enregistrements</b></td>
                 <td width="100" align="center"><b>nb scripts traités</b></td></tr>';
$liste_table = db_read_tables();
foreach ($champs_scripts as $nom_table => $proprietes) {
    // Recherche des tables de la base
    if (!in_array($nom_table,$liste_table)) {
        echo '<tr><td colspan="3">Table '.$nom_table.' inconnu dans la base '.$SITE["db_name"].'!</td></tr>';
        continue;
    }
    switch($nom_table) {
    case "champ":
        $dbh = db_query("select $nom_table.*,ecran.nom_ecran
                           from ecran,$nom_table
                          where ecran.code_ecran=champ.code_ecran");
        break;
    case "libelle":
        $dbh = db_query("select $nom_table.*,groupe_ecran.libelle_groupe_ecran
                           from groupe_ecran,$nom_table
                          where groupe_ecran.code_groupe_ecran=libelle.code_spe");
        break;
    case "onglet":
        $dbh = db_query("select $nom_table.*,groupe_ecran.nom_table
                           from groupe_ecran,$nom_table
                          where groupe_ecran.code_groupe_ecran=onglet.code_groupe_ecran");
        break;
    case "entete_combo":
    case "champ_reseau":
    case "ecran":
    case "fonction_spe":
    case "groupe_ecran":
    case "include_spe":
    case "lien_champ":
    case "menu":
    case "messages":
    case "objet":
    case "service_web":
    case "url":
    case "workflow_prerequis":
    case "workflow_script":
    case "workflow_var":
    case "produit":
        $dbh = db_query("select * from $nom_table");
        break;
    default:
        $dbh = 0;
        echo '<tr><td colspan="3">Table '.$nom_table.' non trouvé pour le champ '.$nom_champ.' !</td></tr>';
    }
    if (!$dbh) continue;
    $nb_lignes = db_numrows($dbh);
    $count = 0;
    while ($table = db_fetch_array($dbh)) {
        switch($nom_table) {
            case "ecran":
                // si la table n'est pas renseigner alors on met "information"
                if ($table["table_principale"] == '') {
                    if (in_array($table["type_fiche"],array("contact","ecran","XLS","popup_transfert"))) {
                        $table["table_principale"] = 'information';
                    }
                }
            break;
        }
        // recuperation de(s) clef(s)
        $com_ecran = "#";
        $aff_err = false;
        foreach ($proprietes["cle"] as $cle) {
            if ($com_ecran != "#") $com_ecran .= ";";
            if (!array_key_exists($cle, $table) || $table[$cle]===NULL) {
                $script = "";
                foreach ($champs_scripts[$nom_table]["list"] as $champ_script) {
                    $script .= trim($table[$champ_script]);
                }
                if ($script > "") $aff_err = true;
                $table[$cle]="";
            }
            $com_ecran .= ($cle."=".trim(str_replace(array("\r\n","\n",","),array(" "," ","-"),$table[$cle])));
        }
        $com_ecran .= "#";
        $com_ecran = trim(str_replace(array('"','\\','/',':','*','?','<','>','|'),"-",$com_ecran));
        if ($aff_err) {
            if (!in_array($nom_table."_".$com_ecran,$erreur)) {
                array_push($erreur,$nom_table."_".$com_ecran);
                echo '<tr><td colspan="3">Certains champs clefs NULL dans la table '.$nom_table.' : $com_ecran !</td></tr>';
            }
        }
        // creation d'un fichier par script
        foreach ($proprietes["list"] as $nom_champ) {
            if (!array_key_exists($nom_champ, $table)) {
                if (!in_array($nom_table."_".$nom_champ,$erreur)) {
                    array_push($erreur,$nom_table."_".$nom_champ);
                    echo '<tr><td colspan="3">Champ '.$nom_champ.' non trouvé dans la table '.$nom_table.'  : $com_ecran !</td></tr>';
                }
                continue;
            }
            $script = trim($table[$nom_champ]);
            if ($script > '') {
                if (preg_match("/jquery/",$nom_champ)) {
                    $script = transformer_jquery_to_php($script);
                }
                $script = remplacer_variable_script($script);
                $count++;
                rebuild_script("/*#$nom_table#$nom_champ$com_ecran*/\n".$script,false,"script".DIRECTORY_SEPARATOR."referentiel_php");
            }
        }
        if ($nom_table == "entete_combo") {
            // creation des requetes SQL pour les entete_combo
            build_requete_sql($table["nature"],$table["cle_liste"],"script".DIRECTORY_SEPARATOR."referentiel_sql");
        }
    }
    db_free_result($dbh);
    echo "<tr><td>$nom_table</td><td align='center'>$nb_lignes</td><td align='center'>$count</td></tr>";
}
echo "</table>";
