<?php
ob_start();
include ('bibli_24sur7.php');

fd_html_head('24sur7 | Agenda', '../css/style.css');
fd_html_bandeau(APP_PAGE_AGENDA);
echo '<section id="bcContenu"><div class="aligncenter">';


echo '<h3>Informations sur votre compte<hr></h3>';
$size = 25;
echo '<form method="POST" action="../php/inscription.php">',
    '<div class="formulaire">',
		'<table border="1" cellpadding="4" cellspacing="0">',
		fd_form_ligne('Nom',
            fd_form_input(APP_Z_TEXT,'txtNom', $_SESSION['txtNom'], $size	 ,100)),
		fd_form_ligne('Email',
            fd_form_input(APP_Z_TEXT,'txtMail', $_SESSION['txtMail'], $size ,150)),
		fd_form_ligne('Mot de passe',
            fd_form_input(APP_Z_PASS,'txtPasse', '', $size ,50)),
        fd_form_ligne('Retapez votre mot de passe',
            fd_form_input(APP_Z_PASS,'txtVerif', '', $size ,50)),
        fd_form_ligne(fd_form_input(APP_Z_SUBMIT,'btnValider', 'Mettre à jour'),
                      fd_form_input(APP_Z_RESET,'btnAnnuler', 'Annuler')),
    '</table></div></form>';

echo '<h3>Options d\'affichage du calendrier<hr></h3>',
  '<form method="POST" action="../php/parametres.php">',
      '<div class="formulaire">',
  		'<table border="1" cellpadding="4" cellspacing="0">',
      fd_form_ligne('Jours affichés', fd_form_checkboxes(JOURS_SEMAINE, 3)),
      '</table></div></form>';

echo '<h3>Vos cat&eacutegories<hr></h3>';


echo '</div></section>';
fd_html_pied();

?>
