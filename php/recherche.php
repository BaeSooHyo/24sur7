<?php
ob_start();
include('bibli_24sur7.php');
fd_bd_connexion();

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
  $motsCles = $_POST['motsCles'];
}

fd_html_head('24sur7 | Agenda', '../css/style.css');
fd_html_bandeau(APP_PAGE_AGENDA);
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
  SELECT utiNom, utiMail
  FROM utilisateur
  WHERE utiNom LIKE '%$motsCles%' or utiMail LIKE '%$motsCles%'
  ";

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

  while ($res = mysqli_fetch_assoc($req))
  {
    echo $res['utiNom'],' ',$res['utiMail'],'<br>';
    //TODO Protéger et présenter
  }
}

echo '</div></section>';
fd_html_pied();

if (isset($GLOBALS['bd'])){
    // Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);
}

?>
