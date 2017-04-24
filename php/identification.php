<?php

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
	* Effectue les vérifications de saisie et de connexion
	*
	* @return array 	tableau des erreurs détectées
	*/
	function pbl_verif_co()
	{
		$bd = fd_bd_connexion();

		//On fait les vérifications des donnes
		$er = array();
		$Mail = $_POST['txtMail'];
		$Passe = $_POST['txtPasse'];

		//Mail
		if ( (strlen($Mail)) == 0)
		{
			array_push($er, "L'adresse mail est obligatoire<br>");
		}
		elseif ( ((strpos(($Mail), '@')) == FALSE) || ((strpos(($Mail), '.')) == FALSE) )
		{
			array_push($er, "L'adresse mail n'est pas valide <br>");
		}

		$Mail = (mysqli_real_escape_string($GLOBALS['bd'], $Mail)) ;

		//On vérifie si l'adress mail est  présente dans notre base de données
		$sql = 'SELECT  *
			FROM utilisateur
			WHERE utiMail= "'.$Mail .'"';

		$res = mysqli_query($GLOBALS['bd'], $sql) OR fd_bd_erreur($sql);
		$enr = mysqli_fetch_assoc($res);

		if ($enr['utiMail'] == NULL)
		{
			array_push($er, "Cette adresse mail n'est pas inscrite sur notre site.<br>");
		}
		if ( ($enr['utiMail'] != NULL) && ($enr['utiPasse'] != (md5($Passe)) ) )
		{
			array_push($er, "Le mot de passe ne correspond pas &agrave; cette adresse mail.<br>");
		}

    session_start();
    $_SESSION['utiID'] = $enr['utiID'];
		tj_setSessionUserInfo($_SESSION['utiID']);

		mysqli_free_result($res);
		mysqli_close($GLOBALS['bd']);

		return $er;
	}

	$erreurs = array();
	$Mail = "";
	$Passe = "";

	if(isset($_POST['btnIdentifier']))
	{
		$Mail = $_POST['txtMail'];
		$Passe = $_POST['txtPasse'];
		$erreurs = pbl_verif_co();

		//L'utilisateur est connecté
		if ( $erreurs == NULL)
		{
			fd_redirige('agenda.php');
		}


	}
	else
	{

		$Mail = "";
		$Passe = "";
	}

// Si on est encore là, c'est que l'utilisateur est bien authentifié.
fd_html_head(APP_NOM_APPLICATION.' | Identification');

//On affiche le bandeau sans les onglets
fd_html_bandeau(0, '-');


//On affiche la phrase avant le formulaire
echo '<main id="bcContenu">',
		'<div class="aligncenter">',
			'<p>Pour vous connecter, veuillez vous identifier.</p>';

//On affiche le formulaire
	echo		'<form method="POST" action="identification.php">',
				'<div class="formulaire">',
				'<table border="0" cellpadding="4" cellspacing="0">',
					fd_form_ligne('Email', fd_form_input(APP_Z_TEXT, 'txtMail', (htmlentities(($Mail), ENT_COMPAT, 'ISO-8859-1')) , '40')),
					fd_form_ligne('Mot de passe', fd_form_input(APP_Z_PASS, 'txtPasse', (htmlentities(($Passe), ENT_COMPAT, 'ISO-8859-1')) , '40')),
					fd_form_ligne( fd_form_input(APP_Z_SUBMIT, 'btnIdentifier', "S'identifier"), fd_form_input(APP_Z_RESET, 'btnAnnuler', 'Annuler')),
				'</table>',
				'</div>',
			'</form>';

	//Si il y a des erreurs, on les affiches aprés le formulaire
	if ( $erreurs != NULL)
	{
		//Affichage du début de la page html
		$tailleA = count($erreurs);
		echo '<b>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</b><br>';
		for ($i=0;$i < $tailleA;$i++)
		{
			echo "<p>$erreurs[$i]\n</p>";
		}
	}


//On affiche les phrases aprés le formulaire
	echo 	 '<br><p>Pas encore de compte ? <a href="../php/inscription.php">Inscrivez-vous</a> sans plus tarder !</p>',
			'<p>Vous h&eacute;sitez &agrave; vous inscrire ? Laissez-vous s&eacute;duire par <a href="../php/inscription.php">une pr&eacute;sentation</a> des possibilit&eacute;s de ',APP_NOM_APPLICATION,'</p>',
		'</div>',
	'</main>';
//On affiche le pied de page
fd_html_pied();

?>
