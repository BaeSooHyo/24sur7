<?php
ob_start();
include ('bibli_24sur7.php');
session_start();
fd_bd_connexion();
fd_verifie_session();

fd_html_head('24sur7 | Agenda', '../css/style.css');
fd_html_bandeau(APP_PAGE_AGENDA);
echo '<section id="bcContenu"><div class="aligncenter">';
echo '<aside id="bcGauche">';


if(isset($_GET['jourCourantAgenda']))
{
  $jourCourantAgenda = $_GET['jourCourantAgenda'];
  $jourCourantCalendrier = $jourCourantAgenda;
}
else
{
  if (isset($_SESSION['jourCourantAgenda']))
  {
    $jourCourantAgenda = $_SESSION['jourCourantAgenda'];
  }
  else
  {
    list($JJ, $MM, $AA) = explode('-', date('j-n-Y'));
    $jourCourantAgenda = ($AA * 10000) + ($MM * 100) + $JJ;
  }
}
if (isset($_GET['sem']))
{
  $jour = $jourCourantAgenda % 100;
  $mois = (($jourCourantAgenda % 10000 ) / 100);
  $annee = ($jourCourantAgenda / 10000);
  $t = mktime(0,0,0,$mois, $jour+($_GET['sem']*7), $annee);
  list($JJ, $MM, $AA) = explode('-', date('j-n-Y', $t));
  $jourCourantAgenda = ($AA * 10000) + ($MM * 100) + $JJ;
}

$_SESSION['jourCourantAgenda'] = $jourCourantAgenda;

if(isset($_GET['jourCourantCalendrier']))
{
  $jourCourantCalendrier = $_GET['jourCourantCalendrier'];
}
else
{
  $jourCourantCalendrier = $_SESSION['jourCourantAgenda'];
}
if (isset($_GET['mois']))
{
  $jour = $jourCourantCalendrier % 100;
  $mois = (int)(($jourCourantCalendrier % 10000 ) / 100);
  $annee = (int)($jourCourantCalendrier / 10000);

  if ($_GET['mois'] == -1)
  {
    if ($mois > 1)
    {
      $mois--;
    }
    else
    {
      $annee--;
      $mois = 12;
    }
  }
  elseif ($_GET['mois'] == 1)
  {
    if ($mois < 12)
    {
      $mois++;
    }
    else
    {
      $annee++;
      $mois = 1;
    }
  }
  $jourCourantCalendrier = ($annee * 10000) + ($mois * 100) + $jour;
}

$_SESSION['jourCourantCalendrier'] = $jourCourantCalendrier;

$jour = $jourCourantCalendrier % 100;
$mois = (int)(($jourCourantCalendrier % 10000 ) / 100);
$annee = (int)($jourCourantCalendrier / 10000);

fd_html_calendrier($jour, $mois, $annee);

$jour = $_SESSION['jourCourantAgenda'] % 100;
$mois = (int)(($_SESSION['jourCourantAgenda'] % 10000 ) / 100);
$annee = (int)($_SESSION['jourCourantAgenda'] / 10000);

$t = mktime(0,0,0,$mois, $jour, $annee);
$wday = date('w', $t);

$t = mktime(0,0,0,$mois, $jour-$wday+1, $annee);
list($JJ, $MM, $AA) = explode('-', date('j-n-Y', $t));
$lundi = ($AA * 10000) + ($MM * 100) + $JJ;


$t = mktime(0,0,0,$mois, $jour-$wday+7, $annee);
list($JJ, $MM, $AA) = explode('-', date('j-n-Y', $t));
$dimanche = ($AA * 10000) + ($MM * 100) + $JJ;


echo '<section id="categories">
  <h3>Vos agendas</h3>
  <p>
    <a href="agenda.php?utiIDagenda=',$_SESSION['utiID'],'"><strong>Agenda de ',$_SESSION['utiNom'],'</strong></a>
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

echo '<p>Agendas suivis : </p>';

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
  <li><a href = \"agenda.php?utiIDagenda=$id\"><p>$nom</p></a>
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
if (!isset($_SESSION['utiIDagenda'])){$_SESSION['utiIDagenda'] = $_SESSION['utiID'];}
$utiIDagenda = (isset($_GET['utiIDagenda'])) ? $_GET['utiIDagenda'] :  $_SESSION['utiIDagenda'];
$_SESSION['utiIDagenda'] = $utiIDagenda;

$sql ="
SELECT utiNom
FROM utilisateur
WHERE utiID = $utiIDagenda";
$req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);
$res = mysqli_fetch_assoc($req);
$utiNomAgenda = $res['utiNom'];


//TODO Sélection période
$periodeDebut = $lundi;
$periodeFin = $dimanche;

$premierJourSemaine = -1;
for ($i=0; $i < 7; $i++) {
  $jours[JOURS_SEMAINE[$i]] = ($_SESSION['utiJours'] >> $i) % 2;
  if($premierJourSemaine == -1 && $jours[JOURS_SEMAINE[$i]] == 1)
  {$premierJourSemaine = $i;}
}

$nbJours = 0;
foreach ($jours as $key => $value) {
  $nbJours += $value;
}

$colWidth = (int) (678 / $nbJours);
$colHeight = 40;
$topOffset = 29;


$jour = $lundi % 100;
$mois = (int)(($lundi % 10000 ) / 100);
$annee = (int)($lundi / 10000);
$dateLundi = "$jour/$mois/$annee";
$jour = $dimanche % 100;
$mois = (int)(($dimanche % 10000 ) / 100);
$annee = (int)($dimanche / 10000);
$dateDimanche = "$jour/$mois/$annee";


echo
'<section id="bcCentre">
<p id="titreAgenda">
<a href="agenda.php?sem=-1" class="flechegauche"><img src="../images/fleche_gauche.png" alt="picto fleche gauche"></a>
<strong>',
'Semaine du ', $dateLundi ,' au ', $dateDimanche,
'</strong> pour <strong>',
$utiNomAgenda,
'</strong>
<a href="agenda.php?sem=1" class="flechedroite"><img src="../images/fleche_droite.png" alt="picto fleche droite"></a>
</p>
<section id="agenda">
<div id="intersection"></div>';

$style = 'style = "width : '.$colWidth.'px;"';
$firstDay = 1;

$height = $colHeight+2;
$width = $colWidth-8;
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
  if ($j == $premierJourSemaine)
  {
    echo '<div ',$style,' class="col-jour border-TRB border-L">';
  }
  else
  {
    echo '<div ',$style,' class="col-jour border-TRB">';
  }


  $dateJourAgenda = $lundi + $j;
  $public = ($_SESSION['utiID'] == $utiIDagenda) ? '' : 'AND catPublic = 1';
  $heureMin = $_SESSION['utiHeureMin']*100;
  $heureMax = $_SESSION['utiHeureMax']*100;


  $sql = "
  SELECT rdvID, catCouleurFond, catCouleurBordure, rdvLibelle, rdvHeureDebut, rdvHeureFin
  FROM utilisateur, rendezvous, categorie
  WHERE rdvIDUtilisateur = utiID
  AND utiID = $utiIDagenda
  AND rdvIDCategorie = catID
  AND rdvDate = $dateJourAgenda
  AND rdvHeureDebut BETWEEN $heureMin AND $heureMax
  AND rdvHeureFin BETWEEN $heureMin AND $heureMax

  $public
  ORDER BY rdvHeureDebut, rdvHeureFin
  ";

  $req = mysqli_query($GLOBALS['bd'], $sql) or fd_bd_erreur($sql);


  while ($rdv = mysqli_fetch_assoc($req))
  {
    $top = ($topOffset + ($height *  (($rdv['rdvHeureDebut'] / 100) - $_SESSION['utiHeureMin']))).'px';
    $catCouleurFond = htmlentities($rdv['catCouleurFond'], ENT_QUOTES, 'UTF-8');
    $catCouleurBordure = htmlentities($rdv['catCouleurBordure'], ENT_QUOTES, 'UTF-8');
    $rdvLibelle = $rdv['rdvLibelle'];
    $rdvID = $rdv['rdvID'];
    $rdvHeight = ($height * (($rdv['rdvHeureFin'] - $rdv['rdvHeureDebut']) / 100.0)).'px';
    $rdvStyle = "style = \"
    color: #000000;
    background-color: #$catCouleurFond;
    border: 3px solid #$catCouleurBordure;
    margin: 1px;
    position: absolute;
    top: $top;
    height : $rdvHeight;
    width: $width;\"
    ";

    echo "<a $rdvStyle class=\"rendezvous\" href = \"rendezvous.php?rdvID=$rdvID\">$rdvLibelle</a>";
  }



  for ($i = $_SESSION['utiHeureMin'] ; $i < $_SESSION['utiHeureMax']; $i++)
  {
    echo '<a href="rendezvous.php?jour=',$jour,'&heure=',$i,'00"></a>';
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
