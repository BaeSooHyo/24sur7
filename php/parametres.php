<?php
ob_start();
include ('bibli_24sur7.php');
session_start();
fd_bd_connexion();


fd_html_head(APP_NOM_APPLICATION.' | Parametres', '../css/style.css');
fd_html_bandeau(APP_PAGE_PARAMETRES);

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
if (isset($_POST['updateCategorie']))
{
  $catNom = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catNom']);
  $catCouleurFond = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catCouleurFond']);
  $catCouleurBordure = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catCouleurBordure']);
  $catPublic = $_POST['catPublic'];
  //= mysqli_real_escape_string($GLOBALS['bd'], $_POST['catPublic']);
  $sql = "
  UPDATE categorie
  SET catNom = '$catNom', catCouleurFond = '$catCouleurFond', catCouleurBordure = '$catCouleurBordure', catPublic = '$catPublic'
  WHERE catID = ".$_POST['updateCategorie'];

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
}
if (isset($_POST['deleteCategorie']))
{
  $sql = "
  DELETE
  FROM categorie
  WHERE catID =".$_POST['deleteCategorie'];

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
}


//TODO Affichage des erreurs
//TODO Afficher message succès


echo '<section id="bcContenu"><div class="aligncenter">';

echo '<h3>Informations sur votre compte<hr></h3>';
$size = 25;
echo '<div class="formulaire">',
    '<form method="POST" action="../php/parametres.php">',
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
    '</table></form></div>';

$jours = $_SESSION['utiJours'];
$lundiChecked =     ($jours % 1 == 1) ? 'checked' : '';
$mardiChecked =     ($jours>>1 % 1 == 1) ? 'checked' : '';
$mercrediChecked =  ($jours>>2 % 1 == 1) ? 'checked' : '';
$jeudiChecked =     ($jours>>3 % 1 == 1) ? 'checked' : '';
$vendrediChecked =  ($jours>>4 % 1 == 1) ? 'checked' : '';
$samediChecked =    ($jours>>5 % 1 == 1) ? 'checked' : '';
$dimancheChecked =  ($jours>>6 % 1 == 1) ? 'checked' : '';

$formJours = '<table>
</tr><tr> <td><input type ="'.APP_Z_CHECKBOX.'" name="chkLundi" value = "lundi"'.       $lundiChecked .'><label for="chkLundi">Lundi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkMardi" value = "mardi"'.       $mardiChecked .'><label for="chklMardi">Mardi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkMercredi" value = "mercredi"'. $mercrediChecked .'><label for="chkMercredi">Mercredi</label></td>
</tr><tr> <td><input type ="'.APP_Z_CHECKBOX.'" name="chkJeudi" value = "jeudi"'.       $jeudiChecked .'><label for="chkJeudi">Jeudi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkVendredi" value = "vendredi"'. $vendrediChecked .'><label for="chkVendredi">Vendredi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkSamedi" value = "samedi"'.     $samediChecked .'><label for="chkSamedi">Samedi</label></td>
</tr><tr> <td><input type ="'.APP_Z_CHECKBOX.'" name="chkDimanche" value = "dimanche"'. $dimancheChecked .'><label for="chkDimanche">Dimanche</label></td>
</tr>';

echo '<h3>Options d\'affichage du calendrier<hr></h3>',
  '<div class="formulaire">',
  '<form method="POST" action="../php/parametres.php">',
  '<table border="1" cellpadding="4" cellspacing="0">',
      fd_form_ligne('Jours affichés', $formJours),
      fd_form_ligne('Heure minimale', pb_form_heure(6,0)),
      fd_form_ligne('Heure minimale', pb_form_heure(22,0)),
      fd_form_ligne(fd_form_input(APP_Z_SUBMIT,'btnValiderCalendrier', 'Mettre à jour'),
                    fd_form_input(APP_Z_RESET,'btnAnnulerCalendrier', 'Annuler')),
      '</table></div></form>';
//TODO utiliser valeurs utilisateur heures et déterminers jours
//TODO exécuter maj

echo '<h3>Vos cat&eacutegories<hr></h3>';

$sql = "
SELECT catID, catNom, catCouleurFond, catCouleurBordure, catPublic
FROM categorie
WHERE catIDUtilisateur = ".$_SESSION['utiID'];

$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

while ($res = mysqli_fetch_assoc($req))
{
  echo '<form method="POST" action="parametres.php">
    <table border="1" cellpadding="4" cellspacing="0">';
  echo 'Cat&eacute;gorie :',fd_form_input(APP_Z_TEXT, "catNom", $res['catNom'], 10,20);
  echo 'Fond : ',fd_form_input(APP_Z_TEXT, "catCouleurFond", $res['catCouleurFond'], 6,6);
  echo 'Bordure : ',fd_form_input(APP_Z_TEXT, "catCouleurBordure", $res['catCouleurBordure'], 6,6);
  echo fd_form_input(APP_Z_CHECKBOX, "catPublic", 1),"<label for 'public>Public</label>";
  echo '<input type = "submit" name = "updateCategorie" value = ', $res['catID'],'>';
  echo '<input type = "submit" name = "deleteCategorie" value = ', $res['catID'],'>';

  //TODO changer labels dans fd_form_input
  //TODO foreach catagorie
  //TODO Boutons aperçu enregistrer et supprimer
  echo '</form>';
};




echo '</div></section>';
fd_html_pied();

?>
