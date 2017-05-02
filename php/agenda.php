<?php
ob_start();
include ('bibli_24sur7.php');
session_start();
fd_bd_connexion();

// if(isset($_GET['sem']))
// {
//   //TODO Avancer/Reculer date +/- 1 semaine
// }


fd_html_head('24sur7 | Agenda', '../css/style.css');
fd_html_bandeau(APP_PAGE_AGENDA);
echo '<section id="bcContenu"><div class="aligncenter">';
echo '<aside id="bcGauche">';
fd_html_calendrier();

echo '<section id="categories">
  <h3>Vos agendas</h3>
  <p>
    <a href="agenda.php?utiIDagenda=',$_SESSION['utiID'],'">Agenda de ',$_SESSION['utiNom'],'</a>
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
    {
echo'
</ul>
</li>
';}
  $nom = $res['utiNom'];
  $id = $res['suiIDSuivi'];

  echo"
  <li><a href = agenda.php?utiIDagenda=$id>$nom</a>
  <ul>
  ";
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
$utiIDagenda = (isset($_GET['utiIDagenda'])) ? $_GET['utiIDagenda'] : $_SESSION['utiID'];

$sql ="
SELECT utiNom
FROM utilisateur
WHERE utiID = $utiIDagenda";
$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
$res = mysqli_fetch_assoc($req);
$utiNomAgenda = $res['utiNom'];


//TODO Sélection période
$periodeDebut = 20150209;
$periodeFin = 20150215;

for ($i=0; $i < 7; $i++) {
  $jours[JOURS_SEMAINE[$i]] = ($_SESSION['utiJours'] >> $i) % 2;
}

$nbJours = 0;
foreach ($jours as $key => $value) {
  $nbJours += $value;
}

$colWidth = (int) (678 / $nbJours);
$colHeight = 40;
$topOffset = 29;


echo
'<section id="bcCentre">
<p id="titreAgenda">
<a href="agenda.php?sem=-1" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>
<strong>',
'Semaine du 9  au 15 F&eacute;vrier',
'</strong> pour <strong>',
$utiNomAgenda,
'</strong>
<a href="agenda.php?sem=1" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>
</p>
<section id="agenda">
<div id="intersection"></div>';

$style = 'style = "width : '.$colWidth.'px;"';
$firstDay = 1;

$height = $colHeight-4;
$width = $colWidth-4;
$height .='px';
$width .= 'px';

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




echo '<div class = "semainier">';
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















  $dateJourAgenda = 20150302;
  $public = ($_SESSION['utiID'] == $utiIDagenda) ? '' : 'AND catPublic = 1';
  $heureMin = $_SESSION['utiHeureMin']/100;
  $heureMax = $_SESSION['utiHeureMax']/100;

  $sql = "
  SELECT rdvID, catCouleurFond, catCouleurBordure, rdvLibelle, rdvHeureDebut, rdvHeureFin
  FROM utilisateur, rendezvous, categorie
  WHERE rdvIDUtilisateur = utiID
  AND utiID = $utiIDagenda
  AND rdvIDCategorie = catID
  AND rdvDate = $dateJourAgenda

  $public
  ORDER BY rdvHeureDebut, rdvHeureFin
  ";

  // AND rdvHeureDebut BETWEEN $heureMin AND $heureMax
  // AND rdvHeureFin BETWEEN $heureMin AND $heureMax

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);


  while ($rdv = mysqli_fetch_assoc($req))
  {
    $top = (($colHeight * ($rdv['rdvHeureDebut'] / 100) - $_SESSION['utiHeureMin']) + $topOffset).'px';
    $catCouleurFond = htmlentities($rdv['catCouleurFond'], ENT_QUOTES, 'UTF-8');
    $catCouleurBordure = htmlentities($rdv['catCouleurBordure'], ENT_QUOTES, 'UTF-8');
    $rdvLibelle = $rdv['rdvLibelle'];
    $rdvID = $rdv['rdvID'];
    $rdvStyle = "style = \"
    background-color: #$catCouleurFond;
    border: 2px solid #$catCouleurBordure;
    margin: 1px;
    position: absolute;
    top: $top;
    height : $height;
    width: $width;\"
    ";

    echo "<a $rdvStyle class=\"rendezvous\" href = \"rendezvous.php?rdvID=1\">$rdvLibelle</a>";
    //$rdvID
  }












  for ($i = $_SESSION['utiHeureMin'] ; $i < $_SESSION['utiHeureMax']; $i++)
  {

    // Affichage test
    if ($j == 2 && $i == 12)
    {
      $top = ($colHeight * ($i - $_SESSION['utiHeureMin'])) + $topOffset;
      $top .= 'px';

      $rdvStyle = "style = \"color: #FFFFFF;
      background-color: #0000FF;
      border: solid 3px #000000;
      position: absolute;
      margin: 1px 1px 1px 1px;
      top: $top;
      height: $height;
      width: $width;\"";
      echo '<a ',$rdvStyle,' class="rendezvous" href="#">TP LW</a>';

    }

    echo '<a href="#"></a>';
  }
  echo '<a href="#" class="case-heure-bas"></a>';



  echo '</div>';
}

echo '</div>';

echo '</section>
</section>';

echo '</div></section>';
fd_html_pied();
ob_end_flush();
?>
