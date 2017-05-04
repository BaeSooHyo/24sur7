<?php
ob_start();
include ('bibli_24sur7.php');
fd_bd_connexion();
session_start();
fd_verifie_session();

fd_html_head(APP_NOM_APPLICATION.' | Abonnements', '../css/style.css');
fd_html_bandeau(APP_PAGE_ABONNEMENTS);
echo '<section id="bcContenu"><div class="aligncenter">';
if (isset($_POST['btnDesabonnement']))
{
  $suivi = mysqli_real_escape_string($GLOBALS['bd'], $_POST['btnDesabonnement']);
  $suiveur = mysqli_real_escape_string($GLOBALS['bd'], $_SESSION['utiID']);
  $sql = "
  DELETE
  FROM suivi
  WHERE suiIDSuivi = $suivi
  AND suiIDSuiveur = $suiveur
  ";
  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

}

$utiID = $_SESSION['utiID'];
$sql = "
SELECT utiNom, utiMail, suiIDSuivi
FROM utilisateur, suivi
WHERE utiID = suiIDSuivi
AND suiIDSuiveur = $utiID
";


$i = 0;
$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
echo '<h3>Liste de vos abonnements<hr></h3>';
echo '<ul class = "liste-resultats">';
while ($res = mysqli_fetch_assoc($req))
{
  $action = '<form method="POST" action="../php/abonnements.php">
            <button type = "submit" name = "btnDesabonnement" class="btn" value="'.$res['suiIDSuivi'].'">Se désabonner</button>
            </form>';
  echo ($i % 2 == 0 ) ? '<li style = "background: #9AC5E7">' : '<li style = "background: #E5ECF6">';
  echo '<p>';
  echo $res['utiNom'],' - ',$res['utiMail'];
  echo '</p>',$action,'</li>';
  $i++;
  ob_flush();
}

echo '</div></section>';
fd_html_pied();
ob_end_flush();

?>
