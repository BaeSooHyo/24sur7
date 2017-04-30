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






//SEMAINIER

$utiIDagenda = (isset($_POST['utiID'])) ? $_POST['utiID'] : $_SESSION['utiID'];
$sql ="
SELECT utiNom
FROM utilisateur
WHERE utiID = $utiIDagenda";
$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
$res = mysqli_fetch_assoc($req);
$utiNomAgenda = $res['utiNom'];



//TODO Sélection période
$periodeDebut = 0;
$periodeFin = 9999999;

$sql = "
SELECT utiNom, catID, catCouleurFond, catCouleurBordure, catPublic, rdvDate, rdvHeureDebut, rdvHeureFin, rdvLibelle
FROM utilisateur, rendezvous, categorie
WHERE $utiIDagenda = utiID
AND utiID = rdvIDUtilisateur
AND rdvIDCategorie = catID
";
//AND rdvDate BETWEEN $periodeDebut AND $periodeFin

$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);


for ($i=0; $i < 7; $i++) {
  $jours[JOURS_SEMAINE[$i]] = ($_SESSION['utiJours'] >> $i) % 2;
}

$nbJours = 0;
foreach ($jours as $key => $value) {
  $nbJours += $value;
}

$colWidth = (int) (678 / $nbJours);


echo
'<section id="bcCentre">
<p id="titreAgenda">
<a href="#" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>
<strong>',
'Semaine du 9  au 15 F&eacute;vrier',
'</strong> pour <strong>',
$utiNomAgenda,
'</strong>
<a href="#" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>
</p>
<section id="agenda">
<div id="intersection"></div>';

$style = 'style = "width : '.$colWidth.'px;"';
$firstDay = 1;
for ($j = 0; $j < 7; $j++)
{
  if ($jours[JOURS_SEMAINE[$j]] == 0){continue;}
  if ($firstDay)
  {
    $firstDay = 0;
    $classe = 'class = "case-jour border-TRB border-L"';
  }
  else
  {
    $classe = 'class = "case-jour border-TRB"';
  }
  echo "<div $style $classe>".JOURS_SEMAINE[$j].'</div>';
}

echo '<div id="col-heures">
';

for ($i = $_SESSION['utiHeureMin']; $i <= $_SESSION['utiHeureMax']; $i++)
{
  echo '<div>',$i,'h</div>';
}
echo '</div>';
for ($j = 0; $j < 7; $j++)
{
  if ($jours[JOURS_SEMAINE[$j]] == 0){continue;}
  if ($j == 0)
  {
    echo '<div ',$style,' class="col-jour border-TRB border-L">';
  }
  else
  {
    echo '<div ',$style,' class="col-jour border-TRB">';
  }

  for ($i = $_SESSION['utiHeureMin'] ; $i < $_SESSION['utiHeureMax']; $i++)
  {
    echo '<a href="#"></a>';
  }
  echo '<a href="#" class="case-heure-bas"></a>';
  echo '</div>';
}

echo '</section>
</section>';


echo '</div></section>';
fd_html_pied();
ob_end_flush();
?>
