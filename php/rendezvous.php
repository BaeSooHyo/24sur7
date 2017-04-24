<?php

//TODO gérer la connexion (mail présent dans la base de données)
/**
*
*Page d'identification
*/
// Bufferisation des sorties
ob_start();

// Inclusion de la bibliothéque
include('bibli_24sur7.php');

	//_______________________________________________________________
	/**
	* Effectue les v&eacute;rifications de saisie et de connexion
	*
	* @return array 	tableau des erreurs d&eacute;tect&eacute;es
	*/
	function pbl_verif_rdv()
	{
		//On fait les v&eacute;rifications des donn&eacute;es
		$er = array();
		$Libelle = $_POST['txtLibelle'];
		$dateD = $_POST['selDate_d'];
		$dateM = $_POST['selDate_m'];
		$dateY = $_POST['selDate_y'];
		$Categorie = $_POST['selCat'];
		$HDebutH = $_POST['selHDebut_H'];
		$HDebutM = $_POST['selHDebut_M'];
		$HFinH = $_POST['selHFin_H'];
		$HFinM = $_POST['selHFin_M'];
		$EvJournee = $_POST['chkJournee'];

		//Date
		if ( date("j", mktime(0, 0, 0, $dateM, -1, $dateY)) < $dateD )
		{
			array_push( $er, "La date n'est pas valide <br>");
		}

		//Libelle
		if ( (strlen($Libelle)) == 0)
		{
			array_push($er, "Le libell&eacute; est obligatoire<br>");
		}

		//On v&eacute;rifie la dur&eacute;e du rendez-vous
		if ( ($HDebutH > $HFinM) || ( ($HDebutH == $HFinM) && ($HFinM < $HDebutM) ) )
		{
			array_push($er, "le rendez-vous doit durer au moins 15 minutes<br>");
		}
		if ( ($HDebutH == $HFinM) && (($HFinM - $HDebutM) < 15) )
		{
			array_push($er, "le rendez-vous doit durer au moins 15 minutes<br>");
		}

		/*$bd = fd_db_connexion();

		//On v&eacute;rifie si le rendez vous n'en chevauche pas un d&eacute;j&agrave; pr&eacute;sent
		$sql = 'SELECT  *
			FROM rendezvous
			WHERE rdvIDUtilisateur = ' . '"' . (mysqli_real_escape_string($GLOBALS['bd'], $_SESSION['utiID'])) . '"';

		$res = mysqli_query($GLOBALS['bd'], $sql) OR fd_bd_erreur($sql);


		$rdvHeureFinH = (int) substr( fd_heure_claire($test['rdvHeureFin']), 0, 1);
		$rdvHeureFinM = (int) substr( fd_heure_claire($test['rdvHeureFin']), 3, 4);
		$rdvHeureDebutH = (int) substr( fd_heure_claire($test['rdvHeureDebut']), 0, 1);
		$rdvHeureDebutM = (int) substr( fd_heure_claire($test['rdvHeureDebut']), 3, 4);

		while ($test = mysqli_fetch_assoc($res) )
		{
			if( ( ($HDebutH > $rdvHeureDebutH) && ($HDebutH < $rdvHeureFinH) ) || ( ($HFinH >= $rdvHeureDebutH) && ($HFinH <= $rdvHeureFinH) ) )
			{
				array_push($er, "le rendez-vous ne doit pas chevaucher un autre rendez-vous<br>");
			}
			if ( ($HDebutH = $rdvHeureDebutH) && ($HDebutM > $rdvHeureDebutM) )
			{
				array_push($er, "le rendez-vous ne doit pas chevaucher un autre rendez-vous<br>");
			}
			if ( ($HFinH = $rdvHeureFintH) )
			{
				array_push($er, "le rendez-vous ne doit pas chevaucher un autre rendez-vous<br>");
			}

		}	*/

		return $er;
	}

	$erreurs = array();


	if($_POST['btnIdentifier'])
	{
		$Libelle = $_POST['txtLibelle'];
		$dateD = $_POST['selDate_d'];
		$dateM = $_POST['selDate_m'];
		$dateY = $_POST['selDate_y'];
		$Categorie = $_POST['selCat'];
		$HDebutH = $_POST['selHDebut_H'];
		$HDebutM = $_POST['selHDebut_M'];
		$HFinH = $_POST['selHFin_H'];
		$HFinM = $_POST['selHFin_M'];
		$EvJournee = $_POST['chkJournee'];
		$erreurs = pbl_verif_rdv();

		//L'utilisateur est connect&eacute;
		if ( $erreurs == NULL)
		{
			//ajout d'un rendez-vous &agrave; la base de donn&eacute;es
			echo 'Ajout possible<br>';
		}


	}
	else
	{
		$Libelle ='';
		$dateD = '21';
		$dateM = '10';
		$dateY = '2017';
		$Categorie = '';
		$HDebutH = '0';
		$HDebutM ='0';
		$HFinH = '0';
		$HFinM = '0';
		$EvJournee = '';
	}

// Si on est encore là, c'est que l'utilisateur est bien authentifié.
fd_html_head(APP_NOM_APPLICATION' | RendezVous');

//On affiche le bandeau avec les onglets non s&eacute;lectionn&eacute;s
fd_html_bandeau('x');


//On affiche la phrase avant le formulaire
echo '<main id="bcContenu">',
		'<h1>Nouvelle saisie</h1>',
		'<div class="aligncenter">';

	//On affiche le formulaire
	echo		'<form method="POST" action="rendezvous.php">',
				'<div class="formulaire">',
				'<table border="0" cellpadding="4" cellspacing="0">',
					fd_form_ligne('Libell&eacute; :', fd_form_input(APP_Z_TEXT, 'txtLibelle', (htmlentities(($Libelle), ENT_COMPAT, 'ISO-8859-1')) , '40')),
					fd_form_ligne('Date :',fd_form_date('selDate',$dateD ,$dateM,$dateY )),
					fd_form_ligne('Cat&eacute;gorie :', 'a faire' ),
					fd_form_ligne('Horaire d&eacute;but :',pb_form_heure('selHeure',$HDebutH,$HDebutM)),
					fd_form_ligne('Horaire fin :',pb_form_heure('selHeure',$HFinH,$HFinM)),
					fd_form_ligne('Ou', fd_form_input(APP_Z_CHECKBOX, 'btnajouter', 'chkJournee').'Ev&eacute;nement sur une journ&eacute;e'),
					fd_form_ligne( fd_form_input(APP_Z_SUBMIT, 'btnajouter', 'Ajouter'), fd_form_input(APP_Z_RESET, 'btnAnnuler', 'Annuler')),
				'</table>',
				'</div>',
			'</form>';

	//Si il y a des erreurs, on les affiches apr�s le formulaire
	if ( $erreurs != NULL)
	{
		//Affichage du d&eacute;but de la page html
		$tailleA = count($erreurs);
		echo '<b>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</b><br>';
		for ($i=0;$i < $tailleA;$i++)
		{
			echo "<p>$erreurs[$i]\n</p>";
		}
	}


//On affiche les phrases apr�s le formulaire
	echo 	 '<br><p><a href="agenda.php">', "Retour &agrave; l'agenda</a></p>",
		'</div>',
	'</main>';

//On affiche le pied de page
fd_html_pied();

?>
