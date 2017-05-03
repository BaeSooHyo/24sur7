<?php
ob_start();
include('bibli_24sur7.php');
fd_bd_connexion();
session_start();
fd_verifie_session();


if(isset($_POST['btnAbonnement']))
{
  $suivi = htmlentities($_POST['btnAbonnement'], ENT_QUOTES, 'UTF-8');
  $suiveur = $_SESSION['utiID'];

  $sql ="
  INSERT INTO suivi (suiIDSuiveur, suiIDSuivi)
  VALUES ($suiveur, $suivi)";

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
}
elseif (isset($_POST['btnDesabonnement']))
{
  $suivi = htmlentities($_POST['btnDesabonnement'], ENT_QUOTES, 'UTF-8');
  $suiveur = $_SESSION['utiID'];
  $sql = "
  DELETE
  FROM suivi
  WHERE suiIDSuivi = $suivi
  AND suiIDSuiveur = $suiveur
  ";
  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

}

if (!isset($_POST['btnRechercher']))
{
	// On est dans un premier affichage de la page.
	// => On intialise les zones de saisie.
  $motsCles = '';
}
else
{
	// On est dans la phase de soumission du formulaire :
	// => exécution de la recherche
  $motsCles = htmlentities($_POST['motsCles'], ENT_QUOTES, 'UTF-8');
}


fd_html_head(APP_NOM_APPLICATION.' | Recherche', '../css/style.css');
fd_html_bandeau(APP_PAGE_RECHERCHE);
echo '<section id="bcContenu"><div class="aligncenter">',
    '<form method="POST" action="../php/recherche.php">',
    '<div class="formulaire">',
    '<table border="1" cellpadding="4" cellspacing="0">',
    fd_form_ligne('Entrez le critère de recherche : ',
    fd_form_input(APP_Z_TEXT, 'motsCles', $motsCles, 40,40)
    .fd_form_input(APP_Z_SUBMIT, 'btnRechercher', 'Rechercher')),
    '</table></div></form>';

if ($motsCles !== '')
{
  $motsCles = mysqli_real_escape_string($GLOBALS['bd'], $motsCles);

  $sql = "
  SELECT utiNom, utiMail, utiID
  FROM utilisateur
  WHERE utiNom LIKE '%$motsCles%' or utiMail LIKE '%$motsCles%'
  ";

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

  $i = 0;
  echo '<ul class = "liste-resultats">';
  while ($res = mysqli_fetch_assoc($req))
  {
    if ($res['utiID'] === $_SESSION['utiID'])
    {
      $abo = 0;
    }
    else
    {
      $idSuivi = $res['utiID'];
      $idSuiveur = $_SESSION['utiID'];
      $sql = "
      SELECT suiIDSuivi
      FROM suivi
      WHERE suiIDSuivi = $idSuivi
      AND suiIDSuiveur = $idSuiveur
      ";

      $req2 = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
      $res2 = mysqli_fetch_assoc($req2);

      if (!$res2){$abo = -1;}
      else {$abo = 1;}
    }

    switch ($abo)
    {
      case -1:
        $action = '<form method="POST" action="../php/recherche.php">
                  <button type = "submit" name = "btnAbonnement" class="btn" value="'.$res['utiID'].'">S\'abonner</button>
                  </form>';
        break;
      case 0:
        $action = '';
        break;
      case 1:
      $action = '<form method="POST" action="../php/recherche.php">
                <button type = "submit" name = "btnDesabonnement" class="btn" value="'.$res['utiID'].'">Se désabonner</button>
                </form>';
        break;
    }


    echo ($i % 2 == 0 ) ? '<li style = "background: #9AC5E7">' : '<li>';
    echo '<p>';
    echo $res['utiNom'],' - ',$res['utiMail'];
    echo '</p>',$action,'</li>';
    $i++;
    ob_flush();
  }
  echo '</ul>';

}

echo '</div></section>';
fd_html_pied();

if (isset($GLOBALS['bd'])){
    // Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);
}

ob_end_flush();

?>
