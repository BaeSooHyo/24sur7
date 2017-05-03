<?php
ob_start();
include ('bibli_24sur7.php');
session_start();
fd_bd_connexion();
fd_verifie_session();


fd_html_head(APP_NOM_APPLICATION.' | Parametres', '../css/style.css');
fd_html_bandeau(APP_PAGE_PARAMETRES);

//TODO Gérer erreurs (champs vide)

echo '<section id="bcContenu"><div class="aligncenter">';

echo '<h3>Informations sur votre compte<hr></h3>';
$size = 25;

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

  if (isset($erreurs))
  {
    echo '<strong>Les erreurs suivantes ont été détectées</strong>';
    foreach ($erreurs as $key => $value)
    {
      echo '<br>',htmlentities($value, ENT_QUOTES, 'UTF-8');
    }
  }
}





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
ob_flush();


echo '<h3>Options d\'affichage du calendrier<hr></h3>';
if (isset($_POST['btnValiderCalendrier']))
{
  $joursSelectionnes = 0;
  if (isset($_POST['chkDimanche']))
  {
    $joursSelectionnes += 1;
  }
  $joursSelectionnes = $joursSelectionnes << 1;
  if (isset($_POST['chkSamedi']))
  {
    $joursSelectionnes += 1;
  }
  $joursSelectionnes = $joursSelectionnes << 1;
  if (isset($_POST['chkVendredi']))
  {
    $joursSelectionnes += 1;
  }
  $joursSelectionnes = $joursSelectionnes << 1;
  if (isset($_POST['chkJeudi']))
  {
    $joursSelectionnes += 1;
  }
  $joursSelectionnes = $joursSelectionnes << 1;
  if (isset($_POST['chkMercredi']))
  {
    $joursSelectionnes += 1;
  }
  $joursSelectionnes = $joursSelectionnes << 1;
  if (isset($_POST['chkMardi']))
  {
    $joursSelectionnes += 1;
  }
  $joursSelectionnes = $joursSelectionnes << 1;
  if (isset($_POST['chkLundi']))
  {
    $joursSelectionnes += 1;
  }

  $joursSelectionnes = mysqli_real_escape_string($GLOBALS['bd'], $joursSelectionnes);
  $heureMin = mysqli_real_escape_string($GLOBALS['bd'], $_POST['heureMin']);
  $heureMax = mysqli_real_escape_string($GLOBALS['bd'], $_POST['heureMax']);


  if (!(0 < $joursSelectionnes && $joursSelectionnes < 128))
  {
    $erreurs[] = 'Sélection invalide';
  }
  if (0 > $heureMin || $heureMin > 23)
  {
    $erreurs[] = 'Heure minimale invalide';
  }
  if (0 > $heureMax || $heureMax > 23)
  {
    $erreurs[] = 'Heure maximale invalide';
  }
  if ($heureMin > $heureMax)
  {
    $erreurs[] = 'L\'heure minimale doit être avant l\'heure maximale';
  }


  if (!isset($erreurs))
  {
    $sql = "
    UPDATE utilisateur
    SET utiJours = '$joursSelectionnes', utiHeureMin = '$heureMin', utiHeureMax = '$heureMax'
    WHERE utiID = ".$_SESSION['utiID'];

    $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
    tj_setSessionUserInfo($_SESSION['utiID']);
  }
  else
  {
    echo '<strong>Les erreurs suivantes ont été détectées</strong>';
    foreach ($erreurs as $key => $value)
    {
      echo '<br>',htmlentities($value, ENT_QUOTES, 'UTF-8');
    }
  }


}

$jours = $_SESSION['utiJours'];
$lundiChecked =     ($jours % 2 == 1) ? 'checked' : '';
$mardiChecked =     (($jours>>1) % 2 == 1) ? 'checked' : '';
$mercrediChecked =  (($jours>>2) % 2 == 1) ? 'checked' : '';
$jeudiChecked =     (($jours>>3) % 2 == 1) ? 'checked' : '';
$vendrediChecked =  (($jours>>4) % 2 == 1) ? 'checked' : '';
$samediChecked =    (($jours>>5) % 2 == 1) ? 'checked' : '';
$dimancheChecked =  (($jours>>6) % 2 == 1) ? 'checked' : '';

$formJours = '<table>
</tr><tr> <td><input type ="'.APP_Z_CHECKBOX.'" name="chkLundi" value = "lundi"'.       $lundiChecked .'><label for="chkLundi">Lundi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkMardi" value = "mardi"'.       $mardiChecked .'><label for="chklMardi">Mardi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkMercredi" value = "mercredi"'. $mercrediChecked .'><label for="chkMercredi">Mercredi</label></td>
</tr><tr> <td><input type ="'.APP_Z_CHECKBOX.'" name="chkJeudi" value = "jeudi"'.       $jeudiChecked .'><label for="chkJeudi">Jeudi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkVendredi" value = "vendredi"'. $vendrediChecked .'><label for="chkVendredi">Vendredi</label></td>
          <td><input type ="'.APP_Z_CHECKBOX.'" name="chkSamedi" value = "samedi"'.     $samediChecked .'><label for="chkSamedi">Samedi</label></td>
</tr><tr> <td><input type ="'.APP_Z_CHECKBOX.'" name="chkDimanche" value = "dimanche"'. $dimancheChecked .'><label for="chkDimanche">Dimanche</label></td>
</tr></table>';

$selectHeureMin = '<select id="heureMin" name="heureMin">';
$selectHeureMax = '<select id="heureMax" name="heureMax">';
for ($i=0; $i < 24; $i++)
{
  $selectHeureMin .= '<option value = '.$i;
  $selectHeureMax .= '<option value = '.$i;

  if ($i == $_SESSION['utiHeureMin']){$selectHeureMin .= ' selected';}
  if ($i == $_SESSION['utiHeureMax']){$selectHeureMax .= ' selected';}

  $selectHeureMin .= ">$i:00</option>";
  $selectHeureMax .= ">$i:00</option>";
}
$selectHeureMin .= '</select>';
$selectHeureMax .= '</select>';



echo'<div class="formulaire">',
  '<form method="POST" action="../php/parametres.php">',
  '<table border="1" cellpadding="4" cellspacing="0">',
      fd_form_ligne('Jours affichés', $formJours),
      fd_form_ligne('Heure minimale', $selectHeureMin),
      fd_form_ligne('Heure minimale', $selectHeureMax),
      fd_form_ligne(fd_form_input(APP_Z_SUBMIT,'btnValiderCalendrier', 'Mettre à jour'),
                    fd_form_input(APP_Z_RESET,'btnAnnulerCalendrier', 'Annuler')),
      '</table></form></div>';

ob_flush();

echo '<h3>Vos cat&eacutegories<hr></h3>';

if (isset($_POST['updateCategorie']))
{
  $catNomLen = mb_strlen($_POST['catNom'], 'UTF-8');
  if ($catNomLen < 4 || $catNomLen > 20)
  {
    $erreurs[] = 'Le nom de la catégorie doit avoir une longueur comprise entre 4 et 20 caractères';
  }
  if(mb_strlen($_POST['catCouleurFond'], 'UTF-8') != 6)
  {
    $erreurs[] = 'La couleur de fond doit être un code HEXA composé de 6 digits';
  }
  if(mb_strlen($_POST['catCouleurBordure'], 'UTF-8') != 6)
  {
    $erreurs[] = 'La couleur de bordure doit être un code HEXA composé de 6 digits';
  }

  if (!isset($erreurs))
  {
    $catNom = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catNom']);
    $catCouleurFond = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catCouleurFond']);
    $catCouleurBordure = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catCouleurBordure']);
    $catPublic = (isset($_POST['catPublic'])) ? '1' : '0';
    $sql = "
    UPDATE categorie
    SET catNom = '$catNom', catCouleurFond = '$catCouleurFond', catCouleurBordure = '$catCouleurBordure', catPublic = '$catPublic'
    WHERE catID = ".$_POST['updateCategorie'];

    $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
  }
  else
  {
    $erreurs[] = 'Couleurs invalides';
  }
}
if (isset($_POST['deleteCategorie']))
{
  $sql = "
  DELETE
  FROM categorie
  WHERE catID =".$_POST['deleteCategorie'];

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
}

if (isset($_POST['addCategorie']))
{
  $catNomLen = mb_strlen($_POST['catNom'], 'UTF-8');
  if ($catNomLen < 4 || $catNomLen > 20)
  {
    $erreurs[] = 'Le nom de la catégorie doit avoir une longueur comprise entre 4 et 20 caractères';
  }
  if(mb_strlen($_POST['catCouleurFond'], 'UTF-8') != 6)
  {
    $erreurs[] = 'La couleur de fond doit être un code HEXA composé de 6 digits';
  }
  if(mb_strlen($_POST['catCouleurBordure'], 'UTF-8') != 6)
  {
    $erreurs[] = 'La couleur de bordure doit être un code HEXA composé de 6 digits';
  }

  if (!isset($erreurs))
  {
    $catNom = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catNom']);
    $catCouleurFond = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catCouleurFond']);
    $catCouleurBordure = mysqli_real_escape_string($GLOBALS['bd'], $_POST['catCouleurBordure']);
    $catPublic = (isset($_POST['catPublic'])) ? '1' : '0';
    $sql = "
    INSERT INTO categorie(catNom, catCouleurFond, catCouleurBordure, catIDUtilisateur, catPublic)
    VALUES ('$catNom', '$catCouleurFond', '$catCouleurBordure',".$_SESSION['utiID'].", $catPublic)
    ";

    $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
  }
}

if (isset($erreurs) && (isset($_POST['addCategorie']) || isset($_POST['deleteCategorie']) || isset($_POST['updateCategorie']) ) )
{
  echo '<strong>Les erreurs suivantes ont été détectées</strong>';
  foreach ($erreurs as $key => $value)
  {
    echo '<br>',htmlentities($value, ENT_QUOTES, 'UTF-8');
  }
}

$sql = "
SELECT catID, catNom, catCouleurFond, catCouleurBordure, catPublic
FROM categorie
WHERE catIDUtilisateur = ".$_SESSION['utiID'];

$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

while ($res = mysqli_fetch_assoc($req))
{
  echo
    '<div class="formulaire"><form method="POST" action="parametres.php">
    <table border="1" cellpadding="4" cellspacing="0">';
  echo 'Nom :',fd_form_input(APP_Z_TEXT, "catNom", $res['catNom'], 10,20);
  echo 'Fond : ',fd_form_input(APP_Z_TEXT, "catCouleurFond", $res['catCouleurFond'], 6,6);
  echo 'Bordure : ',fd_form_input(APP_Z_TEXT, "catCouleurBordure", $res['catCouleurBordure'], 6,6);
  echo fd_form_input(APP_Z_CHECKBOX, "catPublic", 1),"<label for 'public>Public</label>";
  echo '<div class = "apercu" style = "border-color #',$res['catCouleurBordure'],';	background-color: #',$res['catCouleurFond'],';" >Apercu</div>';
  echo '<input class = "btnEnregistrer" type = "submit" name = "updateCategorie" value = ',$res['catID'],'>';
  echo '<input class = "btnSupprimer" type = "submit" name = "deleteCategorie" value = ',$res['catID'],'>';

  //TODO Mise en page

  echo '</table></form></div>';
};

echo '<h3> Nouvelle catégorie :</h3>
<div class="formulaire"><form method="POST" action="parametres.php">';
echo 'Nom :',fd_form_input(APP_Z_TEXT, "catNom", '' , 10,20);
echo 'Fond : ',fd_form_input(APP_Z_TEXT, "catCouleurFond", '', 6,6);
echo 'Bordure : ',fd_form_input(APP_Z_TEXT, "catCouleurBordure", '', 6,6);
echo fd_form_input(APP_Z_CHECKBOX, "catPublic", 1),"<label for 'public>Public</label>";
echo '<input class = "btnEnregistrer" type = "submit" name = "addCategorie" value = "new">';
echo '</form></div>';
echo '<br>';

echo '</div></section>';
fd_html_pied();
ob_end_flush();

?>
