<?php
ob_start();
include ('bibli_24sur7.php');
session_start();
fd_bd_connexion();


fd_html_head('24sur7 | Agenda', '../css/style.css');
fd_html_bandeau(APP_PAGE_AGENDA);
echo '<section id="bcContenu"><div class="aligncenter">';
echo '<aside id="bcGauche">';
fd_html_calendrier();

//TODO Bloc "Vos agendas"
//Liste catégories avec aperçu couleurs
//Liste agendas suivis

echo '<section id="categories">
  <h3>Vos agendas</h3>
  <p>
    <a href="#">Agenda de ',$_SESSION['utiNom'],'</a>
  </p>
  <ul id="mine">';

$sql = "
SELECT catID, catNom, catCouleurFond, catCouleurBordure, catPublic
FROM categorie
WHERE catIDUtilisateur = ".$_SESSION['utiID'];

$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);

while ($res = mysqli_fetch_assoc($req))
{
  echo  '<li>
          <div class = "categorie" style = "border: solid 2px #',$res['catCouleurBordure'],';	background-color: #',$res['catCouleurFond'],';" ></div>',
          $res['catNom'],
        '</li>';
}
echo '</ul>';

echo '<h2>Agendas suivis : </h2>';

$sql ='
SELECT utiNom, suiIDSuivi, catNom, catCouleurFond, catCouleurBordure
FROM suivi, categorie, utilisateur
WHERE suiIDSuivi = catIDUtilisateur
AND suiIDSuivi = utiID
AND suiIDSuiveur = '. $_SESSION['utiID'] .'
ORDER BY suiIDSuivi, catNom
';

$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);


$nom = 0;
echo '<ul>
';
while ($res = mysqli_fetch_assoc($req))
{
  if ($res['utiNom'] !== $nom)
  {
    if ($nom !== 0)
    {echo'
</ul>
</li>
';}
  $nom = $res['utiNom'];
    echo'
  <li>',$nom,'
  <ul>';
  }
  echo '
  <li>
    <div class = "categorie" style = "border: solid 2px #',$res['catCouleurBordure'],';	background-color: #',$res['catCouleurFond'],';" ></div>',
    $res['catNom'],'
  </li>';
}

//TODO CSS : categorie dans categorie -> marge gauche

if ($nom !== 0)
{echo '
  </ul>
  </li>';}
echo '
</ul>';

echo '</section>';

echo '</aside>';

$utiIDagenda = (isset($_POST['utiID'])) ? $_POST['utiID'] : $_SESSION['utiID'];

//TODO Sélection période
$periodeDebut = 0;
$periodeFin = 9999999;

$sql = "
SELECT
FROM rendezvous
WHERE utiID = $utiIDagenda
AND rdvDate BETWEEN $periodeDebut AND $periodeFin
";

echo
'<section id="bcCentre">
<p id="titreAgenda">
<a href="#" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>
<strong>Semaine du 9  au 15 F&eacute;vrier</strong> pour <strong>les L2</strong>
<a href="#" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>
</p>
<section id="agenda">
<div id="intersection"></div>
<div class="case-jour border-TRB border-L">Lundi 9</div>
<div class="case-jour border-TRB">Mardi 10</div>
<div class="case-jour border-TRB">Mercredi 11</div>
<div class="case-jour border-TRB">Jeudi 12</div>
<div class="case-jour border-TRB">Vendredi 13</div>
<div class="case-jour border-TRB">Samedi 14</div>
<div id="col-heures">
  <div>7h</div>
  <div>8h</div>
  <div>9h</div>
  <div>10h</div>
  <div>11h</div>
  <div>12h</div>
  <div>13h</div>
  <div>14h</div>
  <div>15h</div>
  <div>16h</div>
  <div>17h</div>
  <div>18h</div>
</div>
<div class="col-jour border-TRB border-L">
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#" class="case-heure-bas"></a>
  <a style="background-color: #00FF00;
          border: solid 2px #00DD00;
        color: #000000;
        top: 131px;
            height: 114px;" class="rendezvous" href="#">TP LW</a>
  <a style="color: #FFFFFF;
        background-color: #FF0000;
        border: solid 2px #DD0000;
        top: 357px;
        height: 114px;" class="rendezvous" href="#">TP LW</a>
</div>
<div class="col-jour border-TRB">
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#" class="case-heure-bas"></a>
  <a style="color: #FFFFFF;
        background-color: #0000FF;
        border: solid 2px #0000DD;
        top: 295px;
        height: 114px;" class="rendezvous" href="#">TP LW</a>
</div>
<div class="col-jour border-TRB">
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#" class="case-heure-bas"></a>
</div>
<div class="col-jour border-TRB">
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#" class="case-heure-bas"></a>
</div>
<div class="col-jour border-TRB">
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#" class="case-heure-bas"></a>
</div>
<div class="col-jour border-TRB">
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#"></a>
  <a href="#" class="case-heure-bas"></a>
</div>
</section>
</section>';



echo '</div></section>';
fd_html_pied();
ob_end_flush();
?>
