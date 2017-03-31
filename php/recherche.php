<?php
ob_start();
include('bibli_24sur7.php');

if (! isset($_POST['btnRechercher']))
{
	// On n'est dans un premier affichage de la page.
	// => On intialise les zones de saisie.
  $motsCles = '';

} else {
	// On est dans la phase de soumission du formulaire :
	// => exécution de la recherche

}





if (isset($GLOBALS['bd'])){
    // Déconnexion de la base de données
    mysqli_close($GLOBALS['bd']);
}

 ?>
