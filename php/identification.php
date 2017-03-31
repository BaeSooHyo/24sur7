<?php
/**
* 
*Page d'identification
*/
// Bufferisation des sorties
ob_start();

// Inclusion de la bibliothéque
include('bibli_24sur7.php');

// Si on est encore là, c'est que l'utilisateur est bien authentifié.
fd_html_head('Identification | 24sur7');

//On affiche le bandeau sans les onglets 
fd_html_bandeau(0, '-');

//On affiche la phrase avant le formulaire
echo '<main id="bcContenu">',
		'<div class="aligncenter">',
			'<p>Pour vous connecter, veuillez vous identifier.</p>';

//On affiche le formulaire
	echo		'<form method="POST" action="inscription.php">',
				'<div id="formulaire">',
				'<table border="0" cellpadding="4" cellspacing="0">',
					fd_form_ligne('Email', fd_form_input(APP_Z_TEXT, 'txtMail', '' , '40')),
					fd_form_ligne('Mot de passe', fd_form_input(APP_Z_PASS, 'txtPasse', '' , '40')),
					fd_form_ligne( fd_form_input(APP_Z_SUBMIT, 'btnIdentifier', "S'identifier"), fd_form_input(APP_Z_RESET, 'btnAnnuler', 'Annuler')),
				'</table>',
				'</div>',
			'</form>';
	
	
//On affiche les phrases apr�s le formulaire
	echo 	 '<p>Pas encore de compte ? <a href="../php/inscription.php">Inscrivez-vous</a> sans plus tarder !</p>',
			'<p>Vous h&eacute;sitez &agrave; vous inscrire ? Laissez-vous s&eacute;duire par <a href="../php/inscription.php">une pr&eacute;sentation</a> des possibilit&eacute;s de 24sur7</p>',
		'</div>',
	'</main>';		
//On affiche le pied de page
fd_html_pied();

?>
