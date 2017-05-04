<?php
/** @file
 * Bibliothèque générale de fonctions
 *
 * @author : Frederic Dadeau - frederic.dadeau@univ-fcomte.fr
 */

//____________________________________________________________________________
//
// Défintion des constantes de l'application
//____________________________________________________________________________

define('APP_TEST', TRUE);

// Gestion des infos base de données
// define('APP_BD_URL', 'localhost');
// define('APP_BD_USER', 'Paola');
// define('APP_BD_PASS', 'plop');
// define('APP_BD_NOM', '24sur7');

define('APP_BD_URL', 'localhost');
define('APP_BD_USER', 'u_24sur7_jeannin_thibaud');
define('APP_BD_PASS', 'p_24sur7_jeannin_thibaud');
define('APP_BD_NOM', '24sur7_jeannin_thibaud');


define('APP_NOM_APPLICATION','24sur7');

// Gestion des pages de l'application
define('APP_PAGE_AGENDA', 'agenda.php');
define('APP_PAGE_RECHERCHE', 'recherche.php');
define('APP_PAGE_ABONNEMENTS', 'abonnements.php');
define('APP_PAGE_PARAMETRES', 'parametres.php');

//---------------------------------------------------------------
// Définition des types de zones de saisies
//---------------------------------------------------------------
define('APP_Z_TEXT', 'text');
define('APP_Z_PASS', 'password');
define('APP_Z_SUBMIT', 'submit');
define('APP_Z_RESET', 'reset');
define('APP_Z_CHECKBOX', 'checkbox');

define('JOURS_SEMAINE', array('lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'));



//_______________________________________________________________
/**
* Génére le code HTML d'une ligne de tableau d'un formulaire.
*
* Les formulaires sont mis en page avec un tableau : 1 ligne par
* zone de saisie, avec dans la collone de gauche le lable et dans
* la colonne de droite la zone de saisie.
*
* @param string		$gauche		Contenu de la colonne de gauche
* @param string		$droite		Contenu de la colonne de droite
*
* @return string	Le code HTML de la ligne du tableau
*/
function fd_form_ligne($gauche, $droite) {
	return "<tr><td>{$gauche}</td><td  class='alignleft'>{$droite}</td></tr>";
}

//_______________________________________________________________
/**
* Génére le code d'une zone input de formulaire (type text, password ou button)
*
* @param string		$type	le type de l'input (constante FD_Z_xxx)
* @param string		$name	Le nom de l'input
* @param String		$value	La valeur par défaut
* @param integer	$size	La taille de l'input
*
* @return string	Le code HTML de la zone de formulaire
*/
function fd_form_input($type, $name, $value, $size=20, $length=20) {
	$value = htmlentities($value, ENT_QUOTES, 'UTF-8');
	$size = ($size == 0) ? '' : "size='{$size}'";
	$length = ($length == 0) ? '' : "maxlength='{$length}'";
	if ( $type == APP_Z_SUBMIT || ($type == APP_Z_RESET) )
	{
		return "<input type='{$type}' id='{$name}' name='{$name}' {$size} value=\"{$value}\" class='btn'>";
	}
	elseif ( $type == APP_Z_CHECKBOX)
	{
		$checked = ($size) ? 'checked' : '';
		return "<input type='{$type}' id='{$name}' name='{$name}' $checked value=\"{$value}\" class='chkbox'>";
	}
	else
	{
		return "<input type='{$type}' id='{$name}' name='{$name}' {$size} {$length} value=\"{$value}\">";
	}
}



//_______________________________________________________________
/**
* Génére le code pour un ensemble de trois zones de sélection
* représentant uen date : jours, mois et années
*
* @param string		$nom	Préfixe pour les noms des zones
* @param integer	$jour 	Le jour sélectionné par défaut
* @param integer	$mois 	Le mois sélectionné par défaut
* @param integer	$annee	l'année sélectionnée par défaut
*
* @return string 	Le code HTML des 3 zones de liste
*/
function fd_form_date($name, $jsel=0, $msel=0, $asel=0){
	$jsel=(int)$jsel;
	$msel=(int)$msel;
	$asel=(int)$asel;
	$d = date('Y-m-d');
	list($aa, $mm, $jj) = explode('-', $d);
	if ($jsel==0) $jsel = $jj;
	if ($msel==0) $msel = $mm;
	if ($asel==0) $asel = $aa;

	$res = "<select id='{$name}_j' name='{$name}_j'>";
	for ($i=1; $i <= 31 ; $i++){
		if ($i == $jsel)
			$res .= "<option value='$i' selected>$i</option>";
		else
			$res .= "<option value='$i'>$i</option>";
	}
	$res .= "</select> <select id='{$name}_m' name='{$name}_m'>"; //l'espace entre les balises  </select> et <select> est utile
	for ($i=1; $i <= 12 ; $i++){
		if ($i == $msel)
			$res .= "<option value='$i' selected>".fd_get_mois($i).'</option>';
		else
			$res .= "<option value='$i'>".fd_get_mois($i).'</option>';
	}
	$res .= "</select> <select id='{$name}_a' name='{$name}_a'>"; //l'espace entre les balises  </select> et <select> est utile
	for ($i=$aa; $i <= $aa + 99 ; $i++){
		if ($i == $asel)
			$res .= "<option value='$i' selected>$i</option>";
		else
			$res .= "<option value='$i'>$i</option>";
	}
	$res .= '</select>';
	return $res;
}

//_______________________________________________________________
/**
* Fonction afichant deux éléments de sélecion avec les pré-sélectionnés
*
*
* @param string		$nom	Préfixe pour les noms des zones
* @param integer	$hsel 	l'heure sélectionnée par défaut
* @param integer	$msel 	les minutes sélectionnées par défaut
*
* @return string 	Le code HTML des 2 zones de liste
*/
function pb_form_heure($name, $hsel=0, $msel=0){
	$hsel=(int)$hsel;
	$msel=(int)$msel;

	$res = "<select id='{$name}_H' name='{$name}_H'>";
	for ($i=0; $i <= 23 ; $i++){
		if ($i == $hsel)
			$res .= "<option value='$i' selected>$i</option>";
		else
			$res .= "<option value='$i'>$i</option>";
	}
	$res .= "</select> <select id='{$name}_M' name='{$name}_M'>"; //l'espace entre les balises  </select> et <select> est utile
	for ($i=0; $i <= 59 ; $i++){
		if ($i == $msel)
			$res .= "<option value='$i' selected>$i</option>";
		else
			$res .= "<option value='$i'>$i</option>";
	}
	$res .= '</select> ';

	return $res;
}

//_______________________________________________________________
/**
* Vérifie la présence des variables de session indiquant qu'un utilisateur est connecté.
* Cette fonction est à appeler au début des scripts des pages nécessitant une authentification
* de l'utilisateur
*
* Si l'utilisateur n'est pas authentifié, la fonction fd_exit_session() est invoquée
*/
function fd_verifie_session(){
	if (! isset($_SESSION['utiID'])) {
		fd_redirige('deconnexion.php');
	}
}


//_______________________________________________________________
/**
* Arrête une session et effectue une redirection vers la page 'inscription.php'
* Elle utilise :
*   -   la fonction session_destroy() qui détruit la session existante
*   -   la fonction session_unset() qui efface toutes les variables de session
* Puis, le cookie de session est supprimé
* Enfin, elle effectue la redirection vers la page 'inscription.php'
*/
function fd_exit_session() {
	session_destroy();
	session_unset();
	$cookieParams = session_get_cookie_params();
	setcookie(session_name(),
			'',
			time() - 86400,
         	$cookieParams['path'],
         	$cookieParams['domain'],
         	$cookieParams['secure'],
         	$cookieParams['httponly']
    	);

	header('location: inscription.php');
	exit();
}
//____________________________________________________________________________

/**
 * Connexion à la base de données.
 * Le connecteur obtenu par la connexion est stocké dans une
 * variable global : $GLOBALS['bd']
 * Le connecteur sera ainsi accessible partout.
 */
function fd_bd_connexion() {
  $bd = mysqli_connect(APP_BD_URL, APP_BD_USER, APP_BD_PASS, APP_BD_NOM);

  if ($bd !== FALSE) {
    mysqli_set_charset($bd, 'utf8') or fd_bd_erreurExit('<h4>Erreur lors du chargement du jeu de caractères utf8</h4>');
    $GLOBALS['bd'] = $bd;
    return;			// Sortie connexion OK
  }

  // Erreur de connexion
  // Collecte des informations facilitant le debugage
  $msg = '<h4>Erreur de connexion base MySQL</h4>'
          .'<div style="margin: 20px auto; width: 350px;">'
              .'APP_BD_URL : '.APP_BD_URL
              .'<br>APP_BD_USER : '.APP_BD_USER
              .'<br>APP_BD_PASS : '.APP_BD_PASS
              .'<br>APP_BD_NOM : '.APP_BD_NOM
              .'<p>Erreur MySQL num&eacute;ro : '.mysqli_connect_errno($bd)
              .'<br>'.mysqli_connect_error($bd)
          .'</div>';

  fd_bd_erreurExit($msg);
}

//____________________________________________________________________________

/**
 * Traitement erreur mysql, affichage et exit.
 *
 * @param string		$sql	Requête SQL ou message
 */
function fd_bd_erreur($sql) {
	$errNum = mysqli_errno($GLOBALS['bd']);
	$errTxt = mysqli_error($GLOBALS['bd']);

	// Collecte des informations facilitant le debugage
	$msg = '<h4>Erreur de requ&ecirc;te</h4>'
			."<pre><b>Erreur mysql :</b> $errNum"
			."<br> $errTxt"
			."<br><br><b>Requ&ecirc;te :</b><br> $sql"
			.'<br><br><b>Pile des appels de fonction</b>';

	// Récupération de la pile des appels de fonction
	$msg .= '<table border="1" cellspacing="0" cellpadding="2">'
			.'<tr><td>Fonction</td><td>Appel&eacute;e ligne</td>'
			.'<td>Fichier</td></tr>';

	// http://www.php.net/manual/fr/function.debug-backtrace.php
	$appels = debug_backtrace();
	for ($i = 0, $iMax = count($appels); $i < $iMax; $i++) {
		$msg .= '<tr align="center"><td>'
				.$appels[$i]['function'].'</td><td>'
				.$appels[$i]['line'].'</td><td>'
				.$appels[$i]['file'].'</td></tr>';
	}

	$msg .= '</table></pre>';

	fd_bd_erreurExit($msg);
}

//___________________________________________________________________
/**
 * Arrêt du script si erreur base de données.
 * Affichage d'un message d'erreur si on est en phase de
 * développement, sinon stockage dans un fichier log.
 *
 * @param string	$msg	Message affiché ou stocké.
 */
function fd_bd_erreurExit($msg) {
	ob_end_clean();		// Supression de tout ce qui a pu être déja généré

	// Si on est en phase de développement, on affiche le message
	if (APP_TEST) {
		echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>',
				'Erreur base de données</title></head><body>',
				$msg,
				'</body></html>';
		exit();
	}

	// Si on est en phase de production on stocke les
	// informations de débuggage dans un fichier d'erreurs
	// et on affiche un message sibyllin.
	$buffer = date('d/m/Y H:i:s')."\n$msg\n";
	error_log($buffer, 3, 'erreurs_bd.txt');

	// Génération d'une page spéciale erreur
	fd_html_head('24sur7');

	echo '<h1>24sur7 est overbook&eacute;</h1>',
			'<div id="bcDescription">',
				'<h3 class="gauche">Merci de r&eacute;essayez dans un moment</h3>',
			'</div>';

	fd_html_pied();

	exit();
}
//____________________________________________________________________________

/**
 * Génère le code HTML du début des pages.
 *
 * @param string	$titre		Titre de la page
 * @param string	$css		url de la feuille de styles liée
 */
function fd_html_head($titre, $css = '../css/style.css') {
	if ($css == '-') {
		$css = '';
	} else {
		$css = "<link rel='stylesheet' href='$css'>";
	}

	echo '<!DOCTYPE HTML>',
		'<html lang="fr">',
			'<head>',
				'<meta charset="UTF-8">',
				'<title>', $titre, '</title>',
				$css,
				'<link rel="shortcut icon" href="../images/favicon.ico" type="image/x-icon">',
			'</head>',
			'<body>',
				'<main id="bcPage">';
}

//____________________________________________________________________________

/**
 * Génère le code HTML du bandeau des pages.
 *
 * @param string	$page		Constante APP_PAGE_xxx
 */
 function fd_html_bandeau($page) {
 	//On affiche le bandeau sans les onglets  si $option = '-'
 	if ($page == '-')
 	{
 		echo '<header id="bcEntete">',
 			'<div id="bcLogo"></div>',
 			'<a href="deconnexion.php" id="btnDeconnexion" title="Se d&eacute;connecter"></a>',
 		 '</header>';
 	}
 	//Sinon on l'affiche avec les onglets
 	else
 	{
 		echo '<header id="bcEntete">',
 				'<nav id="bcOnglets">',
 					($page == APP_PAGE_AGENDA) ? '<h2>Agenda</h2>' : '<a href="'.APP_PAGE_AGENDA.'">Agenda</a>',
 					($page == APP_PAGE_RECHERCHE) ? '<h2>Recherche</h2>' : '<a href="'.APP_PAGE_RECHERCHE.'">Recherche</a>',
 					($page == APP_PAGE_ABONNEMENTS) ? '<h2>Abonnements</h2>' : '<a href="'.APP_PAGE_ABONNEMENTS.'">Abonnements</a>',
 					($page == APP_PAGE_PARAMETRES) ? '<h2>Paramètres</h2>' : '<a href="'.APP_PAGE_PARAMETRES.'">Paramètres</a>',
 				'</nav>',
 				'<div id="bcLogo"></div>',
 				'<a href="deconnexion.php" id="btnDeconnexion" title="Se d&eacute;connecter"></a>',
 			 '</header>';
 	}
 }

//____________________________________________________________________________

/**
 * Génère le code HTML du pied des pages.
 */
function fd_html_pied() {
	echo '<footer id="bcPied">',
			'<a id="apropos" href="../html/presentation.html#quefaire">A propos</a>',
			'<a id="confident" href="../html/presentation.html#confidentialite">Confidentialité</a>',
			'<a id="conditions" href="../html/presentation.html#pied">Conditions</a>',
			'<p id="copyright">24sur7 &amp; Partners &copy; 2017</p>',
		'</footer>';

	echo '</main>',	// fin du bloc bcPage
		'</body></html>';
}

//____________________________________________________________________________

/**
 * Génère le code HTML d'un calendrier.
 *
 * @param integer	$jour		Numéro du jour à afficher
 * @param integer	$mois		Numéro du mois à afficher
 * @param integer	$annee		Année à afficher
 */
function fd_html_calendrier($jour = 0, $mois = 0, $annee = 0) {
	list($JJ, $MM, $AA) = explode('-', date('j-n-Y'));

	// Vérification des paramètres
	$jour = (int) $jour;
	$mois = (int) $mois;
	$annee = (int) $annee;
	($jour == 0) && $jour = $JJ;
	($mois == 0) && $mois = $MM;
	($annee < 2012) && $annee = $AA;

	if (!checkdate($mois, $jour, $annee)) {
		$jour = $JJ;
		$mois = $MM;
		$annee = $AA;
	}

	// Initialisations diverses
	$timeAujourdHui = mktime(0, 0, 0, $MM, $JJ, $AA);
	$timePremierJourMoisCourant = mktime(0, 0, 0, $mois, 1, $annee);
	$timeJourCourant = mktime(0, 0, 0, $mois, $jour, $annee);
	$timeDernierJourMoisCourant = mktime(0, 0, 0, ($mois + 1), 0, $annee);

	$nbJoursMoisCourant = date('j', $timeDernierJourMoisCourant);	// nombre de jours dans le mois

	$semaineDebut = date('W', $timePremierJourMoisCourant);
	$semaineFin = date('W', $timeDernierJourMoisCourant);
	$semaineCourante = date('W', $timeJourCourant);
	if ($semaineDebut >= 52){
        $semaineDebut = 0;
        if ($semaineCourante >= 52) $semaineCourante = 0;
    }

	$jourSemaineJourDebut = date ('w', $timePremierJourMoisCourant);
	($jourSemaineJourDebut == 0) && $jourSemaineJourDebut = 7;

  /*
  Les variables $jourAff, $moisAff, $dernierJourMoisAff, $jourCourant, $jourAujourdhui sont utilisées dans
  dans les boucles :
  for ($sem = $semaineDebut ; $sem <= $semaineFin; $sem++){
		for($i = 1; $i <= 7 ; $i++){
		}
  }
  - $moisAff représente le mois en cours d'affichage : peut prendre successivement les valeurs $mois -1, $mois,
    $mois + 1 pour représenter respectivement le mois précédent le mois courant, le mois courant et le mois suivant
    le mois courant
  - $jourAff : sa valeur initiale représente le 1er numéro de jour à afficher de $moisAff
  - $dernierJourMoisAff : dernier numéro de jour à afficher de $moisAff
  - $jourCourant : utilisé pour repérer le jour courant (sélectionné) quand $moisAff == $mois
  - $jourAujourdhui : utilisé pour repérer le jour d'aujourd'hui dans le mois précédent, le mois courant, ou le mois
    courant, ou le mois suivant le mois courant

  */

	if ($jourSemaineJourDebut == 1){
		$jourAff = 1;
		$moisAff = $mois;
		$dernierJourMoisAff = $nbJoursMoisCourant;
		$jourCourant = $jour;
		$jourAujourdhui = ($timeAujourdHui < $timePremierJourMoisCourant ||
							$timeAujourdHui > $timeDernierJourMoisCourant) ? 0 : $JJ;
	}
	else{
        $timeDernierJourMoisPrecedent = mktime(0, 0, 0, $mois, 0, $annee);
        $nbJoursMoisPrecedent = date('j', $timeDernierJourMoisPrecedent);
		$jourAff = $nbJoursMoisPrecedent - $jourSemaineJourDebut + 2;
		$moisAff = $mois - 1;
		$dernierJourMoisAff = $nbJoursMoisPrecedent;
		$jourCourant = 0;
		$timePremierJourAffMoisPrecedent = mktime(0, 0, 0, $moisAff, $jourAff, $annee);

		$jourAujourdhui = ($timeAujourdHui < $timePremierJourAffMoisPrecedent ||
				$timeAujourdHui > $timeDernierJourMoisPrecedent) ? 0 : $JJ;
	}

	// Affichage du titre du calendrier
	echo '<section id="calendrier">',
	'<p>',
	'<a href="agenda.php?jourCourantCalendrier=',($annee * 10000) + ($mois * 100) + $jour,'&mois=-1" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>',
	fd_get_mois($mois), ' ', $annee,
	'<a href="agenda.php?jourCourantCalendrier=',($annee * 10000) + ($mois * 100) + $jour,'&mois=1" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>',
	'</p>';

	// Affichage des jours du calendrier
	echo '<table>',
	'<tr>',
	'<th>Lu</th><th>Ma</th><th>Me</th><th>Je</th><th>Ve</th><th>Sa</th><th>Di</th>',
	'</tr>';


	for ($sem = $semaineDebut ; $sem <= $semaineFin; $sem++){
		if ($sem == $semaineCourante){
			echo '<tr class="semaineCourante">';
		}
		else{
			echo '<tr>';
		}
		for($i = 1; $i <= 7 ; $i++){

			$cibleLien = 'agenda.php?jourCourantAgenda='.$annee.$mois.$jourAff;

			if ($jourAff == $jourAujourdhui) {
				echo '<td class="aujourdHui">';
			} elseif ($jourAff == $jourCourant) {
				echo '<td class="jourCourant">';
			} else {
				echo '<td>';
			}
			if ($moisAff == $mois){
              echo '<a href="',$cibleLien,'">', $jourAff, '</a></td>';
            }
            else{
              echo '<a class="lienJourHorsMois" href="',$cibleLien,'">', $jourAff, '</a></td>';
            }
			$jourAff++;
			if ($jourAff > $dernierJourMoisAff){
				$moisAff++;
				$jourAff = 1;
				if ($moisAff == $mois){
					$dernierJourMoisAff = $nbJoursMoisCourant;
					$jourCourant = $jour;
					$jourAujourdhui = ($timeAujourdHui < $timePremierJourMoisCourant ||
							$timeAujourdHui > $timeDernierJourMoisCourant) ? 0 : $JJ;
				}
				else{
                    if ($i == 7) break;
					$dernierJourMoisAff = 7 - $i;
					$timePremierJourMoisSuivant = mktime(0, 0, 0, ($mois + 1), 1, $annee);
					$timeDernierJourMoisSuivant = mktime(0, 0, 0, ($mois + 1), $dernierJourMoisAff, $annee);
					$jourCourant = 0;
					$jourAujourdhui = ($timeAujourdHui < $timePremierJourMoisSuivant ||
							$timeAujourdHui > $timeDernierJourMoisSuivant) ? 0 : $JJ;
				}
			}
		}
		echo '</tr>';
	}
	echo '</table></section>';
}

//_______________________________________________________________

/**
 * Renvoie le nom d'un mois.
 *
 * @param integer	$numero		Numéro du mois (entre 1 et 12)
 *
 * @return string 	Nom du mois correspondant
 */
function fd_get_mois($numero) {
	$numero = (int) $numero;
	($numero < 1 || $numero > 12) && $numero = 0;

	$mois = array('Erreur', 'Janvier', 'F&eacute;vrier', 'Mars',
				'Avril', 'Mai', 'Juin', 'Juillet', 'Ao&ucirc;t',
				'Septembre', 'Octobre', 'Novembre', 'D&eacute;cembre');

	return $mois[$numero];
}

//____________________________________________________________________________

/**
 * Formatte une date AAAAMMJJ en format lisible
 *
 * @param integer	$amj		Date au format AAAAMMJJ
 *
 * @return string	Date formattée JJ nomMois AAAA
 */
function fd_date_claire($amj) {
	$a = (int) substr($amj, 0, 4);
	$m = (int) substr($amj, 4, 2);
	$m = fd_get_mois($m);
	$j = (int) substr($amj, -2);

	return "$j $m $a";
}

//____________________________________________________________________________

/**
* Formatte une heure HHMM en format lisible
*
* @param integer	$h		Heure au format HHMM
*
* @return string	Heure formattée HH h SS
*/
function fd_heure_claire($h) {
	$m = (int) substr($h, -2);
	($m == 0) && $m = '';
	$h = (int) ($h / 100);

	return "{$h}h{$m}";
}

//____________________________________________________________________________

/**
 * Redirige l'utilisateur sur une page
 *
 * @param string	$page		Page où rediriger
 */
function fd_redirige($page) {
	header("Location: $page");
	exit();
}

function tj_setSessionUserInfo($ID)
{
	$sql ="
		SELECT utiNom, utiMail, utiJours, utiHeureMin, utiHeureMax
		FROM utilisateur
		WHERE utiID = $ID
	";
	$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
	$res = mysqli_fetch_assoc($req);
	foreach ($res as $key => $value)
	{
		$_SESSION[$key] = htmlentities($value, ENT_QUOTES, 'UTF-8');
	}
}

?>
