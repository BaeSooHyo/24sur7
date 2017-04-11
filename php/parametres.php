<?php
ob_start();
include ('bibli_24sur7.php');
session_start();
fd_bd_connexion();


fd_html_head('24sur7 | Agenda', '../css/style.css');
fd_html_bandeau(APP_PAGE_AGENDA);

if (isset($_POST['btnValiderInfo']))
{
  if ($_POST['txtNom'] !== $_SESSION['utiNom'])
  {
    // Vérification du nom
  	$txtNom = trim($_POST['txtNom']);
  	$long = mb_strlen($txtNom, 'UTF-8');
  	if ($long < 4 || $long > 30)
  	{
  		$erreurs[] = 'Le nom doit avoir de 4 à 30 caractères';
  	}
    else
    {
      $txtNom = mysqli_real_escape_string($GLOBALS['bd'],$txtNom);
      $sql = "
      UPDATE utilisateur
      SET utiNom = '$txtNom'
      WHERE utiID = '".$_SESSION['utiID'].'\'';

      mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
      tj_setSessionUserInfo($_SESSION['utiID']);  //Actualisation des informations
    }
  }
  if ($_POST['txtMail'] !== $_SESSION['utiMail'])
  {
    // Vérification du mail
  	$txtMail = trim($_POST['txtMail']);
  	if ($txtMail == '')
    {
  		$erreurs[] = 'L\'adresse mail est obligatoire';
  	}
    elseif (mb_strpos($txtMail, '@', 0, 'UTF-8') === FALSE || mb_strpos($txtMail, '.', 0, 'UTF-8') === FALSE)
  	{
  		$erreurs[] = 'L\'adresse mail n\'est pas valide';
  	}
    else
    {
  		// Vérification que le mail n'existe pas dans la BD
  		$txtmail = mysqli_real_escape_string($GLOBALS['bd'], $txtMail);

  		$S = "SELECT	count(*)
  				FROM	utilisateur
  				WHERE	utiMail = '$txtmail'";

  		$R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);
  		$D = mysqli_fetch_row($R);

  		if ($D[0] > 0) {
  			$erreurs[] = 'Cette adresse mail est déjà inscrite.';
  		}
      else
      {
        $sql = "
        UPDATE utilisateur
        SET utiMail = '$txtMail'
        WHERE utiID = '".$_SESSION['utiID'].'\'';
        $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
        tj_setSessionUserInfo($_SESSION['utiID']);  //Actualisation des informations

      }
  		mysqli_free_result($R);// Libère la mémoire associée au résultat $R
  	}
  }
  if ($_POST['txtPasse'] !== '')
  {
    if ($_POST['txtPasse'] !== $_POST['txtVerif'])
    {
      $erreurs[] = 'Le mot de passe est différent dans les 2 zones';
    }
    else
    {
      $txtPasse = $_POST['txtPasse'];
    	$long = mb_strlen($txtPasse, 'UTF-8');
    	if ($long < 4 || $long > 20)
      {
        $erreurs[] = 'Le mot de passe doit avoir de 4 à 20 caractères';
      }
      else
      {
        $passe = md5($txtPasse);
        $sql = "
        UPDATE utilisateur
        SET utiPasse = '$passe'
        WHERE utiID = '".$_SESSION['utiID'].'\'';

        mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

      }
    }
  }

}


//TODO Affichage des erreurs
//TODO Afficher message succès


echo '<section id="bcContenu"><div class="aligncenter">';

echo '<h3>Informations sur votre compte<hr></h3>';
$size = 25;
echo '<form method="POST" action="../php/parametres.php">',
    '<div class="formulaire">',
		'<table border="1" cellpadding="4" cellspacing="0">',
		fd_form_ligne('Nom',
            fd_form_input(APP_Z_TEXT,'txtNom', $_SESSION['utiNom'], $size	 ,100)),
		fd_form_ligne('Email',
            fd_form_input(APP_Z_TEXT,'txtMail', $_SESSION['utiMail'], $size ,150)),
		fd_form_ligne('Mot de passe',
            fd_form_input(APP_Z_PASS,'txtPasse', '', $size ,50)),
        fd_form_ligne('Retapez votre mot de passe',
            fd_form_input(APP_Z_PASS,'txtVerif', '', $size ,50)),
        fd_form_ligne(fd_form_input(APP_Z_SUBMIT,'btnValiderInfo', 'Mettre à jour'),
                      fd_form_input(APP_Z_RESET,'btnAnnulerInfo', 'Annuler')),
    '</table></div></form>';

echo '<h3>Options d\'affichage du calendrier<hr></h3>',
  '<form method="POST" action="../php/parametres.php">',
  		'<table border="1" cellpadding="4" cellspacing="0">',
      fd_form_ligne('Jours affichés', fd_form_checkboxes(JOURS_SEMAINE, 3)),
      fd_form_ligne('Heure minimale', pb_form_heure(6,0)),
      fd_form_ligne('Heure minimale', pb_form_heure(22,0)),
      '</table></form>';
//TODO utiliser valeurs utilisateur

echo '<h3>Vos cat&eacutegories<hr></h3>';
echo '<form method="POST" action="parametres.php">
  <table border="1" cellpadding="4" cellspacing="0">';
echo 'Cat&eacute;gorie :',fd_form_input(APP_Z_TEXT, "nom", 'Categorie', 10,20);
echo 'Fond : ',fd_form_input(APP_Z_TEXT, "fond", "FFAA33", 6,6);
echo 'Bordure : ',fd_form_input(APP_Z_TEXT, "bordure", "A3A3A3", 6,6);
echo fd_form_input(APP_Z_CHECKBOX, "public", "Public"),"<label for 'public>Public</label>";
//TODO foreach catagorie
echo '</form>';





echo '</div></section>';
fd_html_pied();

?>
