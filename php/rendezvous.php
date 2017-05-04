<?php

/**
* 
*Page d'identification
*/
// Bufferisation des sorties
ob_start();

// Inclusion de la bibliothéque
include('bibli_24sur7.php');

session_start();
fd_bd_connexion();

	//_______________________________________________________________
	/**
	* Effectue les v&eacute;rifications de saisie et de connexion
	*
	* @return array 	tableau des erreurs d&eacute;tect&eacute;es
	*/
	function pbl_verif_rdv()
	{


		//On fait les v&eacute;rifications des donn&eacute;es
		$er = array();
		$Libelle = $_POST['txtLibelle'];
		$dateD = $_POST['selDate_d'];
		$dateM = $_POST['selDate_m'];
		$dateY = $_POST['selDate_y'];
		$Categorie = $_POST['selCat'];
		$HDebutH = $_POST['selHDebut_H'];
		$HDebutM = $_POST['selHDebut_M'];
		$HFinH = $_POST['selHFin_H'];
		$HFinM = $_POST['selHFin_M'];
		$EvJournee = $_POST['chkJournee'];

        //Journée entière présente ?
        if ($EvJournee==1)
        {
            $HDebutH = 0;
            $HDebutM = 0;
            $HFinH = 23;
            $HFinM = 59;
        }


        //Date
		if ( date("j", mktime(0, 0, 0, $dateM, -1, $dateY)) < $dateD )
		{
			array_push( $er, "La date n'est pas valide <br>");
		}
		
		//Libelle
		if ( (strlen($Libelle)) == 0) 
		{
			array_push($er, "Le libell&eacute; est obligatoire<br>");
		}

		$testDuree = (((int)$HFinM)+(((int)$HFinH)*60))-(((int)$HDebutM)+(((int)$HDebutH)*60)) ;

		//On v&eacute;rifie la dur&eacute;e du rendez-vous 
		if ( $testDuree < 15 )
		{
			array_push($er, "le rendez-vous doit durer au moins 15 minutes<br>");
		}


        $id = $_SESSION['utiID'];

        //On v&eacute;rifie si le rendez vous n'en chevauche pas un d&eacute;j&agrave; pr&eacute;sent
        $sql = "SELECT *
            FROM rendezvous
            WHERE rdvIDUtilisateur = $id";

        $res = mysqli_query($GLOBALS['bd'], $sql) OR fd_bd_erreur($sql);

        $testChev =0;

        //On vérifie pour chaque rendez-vous déjà présent si tout est en ordre
        while ($test = mysqli_fetch_assoc($res) ) {


            $rdvHeureFinM = (int)substr(fd_heure_claire($test['rdvHeureFin']), -1, 2);
            $rdvHeureFinH = (((int)fd_heure_claire($test['rdvHeureFin'])) -$rdvHeureFinM);
            $hFin2=($rdvHeureFinH*60)+($rdvHeureFinM);
            $rdvHeureDebutM = (int)substr(fd_heure_claire($test['rdvHeureDebut']), -1, 2);
            $rdvHeureDebutH = (((int)fd_heure_claire($test['rdvHeureDebut'])) -$rdvHeureDebutM);
            $hDeb2=($rdvHeureDebutH*60)+($rdvHeureDebutM);

           $hFin = (((int)$HFinM)+(((int)$HFinH)*60));
           $hDeb =(((int)$HDebutM)+(((int)$HDebutH)*60));

			echo ' hFin ' . $hFin . ' ';
            echo ' hFin2 ' . $hFin2 . ' ';
            echo ' hDeb ' . $hDeb . ' ';
            echo ' hDeb2 ' . $hDeb2 . ' ';

            if ( (($hDeb<$hFin2) && ($hDeb>$hDeb2)) || (($hFin<$hFin2) && ($hFin>$hDeb2)) )
            {
               $testChev=1;
            }
            if  (($hDeb<$hDeb2) && ($hFin>$hFin2))
            {
                $testChev=1;
            }

            mysqli_free_result($test);

        }

        if ( $testChev == 1 )
        {
            array_push($er, "le rendez-vous ne doit pas chevaucher un autre rendez-vous<br>");
        }

		return $er;
	}

	//TODO si modification ou ajout d'un rendezvous
	if ( 0 === '0')
	{
		$titre = 'Modification';
	}
	else
	{
		$titre = 'Nouvelle saisie';
	}

	$erreurs = array();
	
	
	if($_POST['btnAjouter'])
	{
		$Libelle = $_POST['txtLibelle'];
		$dateD = $_POST['selDate_j'];
		$dateM = $_POST['selDate_m'];
		$dateY = $_POST['selDate_a'];
		$Categorie = $_POST['selCat'];
		$HDebutH = $_POST['selHDebut_H'];
		$HDebutM = $_POST['selHDebut_M'];
		$HFinH = $_POST['selHFin_H'];
		$HFinM = $_POST['selHFin_M'];
		$EvJournee = $_POST['chkJournee'];
		$erreurs = pbl_verif_rdv();

		//Journée entière présente ?
		if ($EvJournee==='journee')
		{
            $HDebutH = 0;
            $HDebutM = 0;
            $HFinH = 23;
            $HFinM = 59;
		}


		if ( $erreurs == NULL) 
		{
			$dateF=($dateY . $dateM . $dateD);
			$dateF=(int)$dateF;
			$hD = (int)($HDebutH . $HDebutM);
			$hF = (int)($HFinH . $HFinM);
			$Lib= mysqli_real_escape_string($GLOBALS['bd'], $Libelle);

			//ajout d'un rendez-vous a la base de donnees
            $S = "INSERT INTO rendezvous SET
			rdvDate = $dateF,
			rdvHeureDebut = $hD,
			rdvHeureFin = $hF,
			rdvLibelle = $Lib,
			rdvIDCategorie = $Categorie,
			rdvIDUtilisateur = ". $_SESSION['utiID'];

            $R = mysqli_query($GLOBALS['bd'], $S) or fd_bd_erreur($S);

		}

		
	}
	else 
	{
		$Libelle ='';
		$dateD = '21';
		$dateM = '10';
		$dateY = '2017';
		$HDebutH = '0';
		$HDebutM ='0';
		$HFinH = '0';
		$HFinM = '0';
		$EvJournee = 0;
	}


//Affichage de la page html
fd_html_head('RendezVous | 24sur7');

//On affiche le bandeau avec les onglets non s&eacute;lectionn&eacute;s
fd_html_bandeau('x');




//On affiche la phrase avant le formulaire
echo '<main>';
echo '<section id="bcContenu"><div class="aligncenter"><section id="bcGauche">';
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
  <li><a href = \"agenda.php?utiIDagenda=$id\">$nom</a>
  <ul>
  ";
    }
    echo '
  <li>
    <div class = "categorie" style = "border: solid 2px #',$res['catCouleurBordure'],';	background-color: #',$res['catCouleurFond'],';" ></div>',
    $res['catNom'],'
  </li>';
}

echo '</section>';


$r = '<select class="bcListe" id="categorie" name="categorie">';
//On recupere les categories de l'utilisateur
$sql = "SELECT *
            FROM categorie
            WHERE catIDUtilisateur = 1" ;

$res = mysqli_query($GLOBALS['bd'], $sql) OR fd_bd_erreur($sql);

while ($test = mysqli_fetch_assoc($res) ) {
    if ($test['catID'] == NULL)
    {
        $r .='Acune categorie';
	} else
	{
        $r .= '<option value="' . $test['catID'] . '">' . (htmlentities(($test['catNom']), ENT_QUOTES, 'UTF-8')) . '</option>';
	}
	mysqli_free_result($test);
}
$r .= '</select>';


	//On affiche le formulaire
	echo	'<section id="bcCentreRDV">',
    			'<h1 id="titreRDV">' . $titre . '</h1>',
				'<form method="POST" action="rendezvous.php">',
				'<div class="formulaire">',
				'<table border="0" cellpadding="4" cellspacing="0">',
					fd_form_ligne('Libell&eacute; :', fd_form_input(APP_Z_TEXT, 'txtLibelle', (htmlentities(($Libelle), ENT_COMPAT, 'ISO-8859-1')) , '20')),
					fd_form_ligne('Date :',fd_form_date('selDate',$dateD ,$dateM,$dateY )),
					fd_form_ligne('Cat&eacute;gorie :',  $r),
					fd_form_ligne('Horaire d&eacute;but :',pb_form_heure('selHDebut',$HDebutH,$HDebutM)),
					fd_form_ligne('Horaire fin :',pb_form_heure('selHFin',$HFinH,$HFinM)),
					fd_form_ligne('Ou', fd_form_input(APP_Z_CHECKBOX, 'chkJournee', 'journee', $EvJournee). '<label for="chkJournee">Ev&eacute;nement sur une journ&eacute;e</label>'),
					fd_form_ligne( fd_form_input(APP_Z_SUBMIT, 'btnAjouter', 'Ajouter'), fd_form_input(APP_Z_RESET, 'btnAnnuler', 'Annuler')),
				'</table>',
				'</div>',
			'</form>';
			
	//Si il y a des erreurs, on les affiches apres le formulaire
	if ( $erreurs != NULL) 
	{
		//Affichage du d&eacute;but de la page html
		$tailleA = count($erreurs);
		echo '<b>Les erreurs suivantes ont &eacute;t&eacute; d&eacute;tect&eacute;es</b><br>';
		for ($i=0;$i < $tailleA;$i++)
		{
			echo "<p>$erreurs[$i]\n</p>";
		}
	}


	//On affiche les phrases apr�s le formulaire
	echo 	 '</section> <br><p id="lienRDV"><a href="agenda.php">', "Retour &agrave; l'agenda</a></p>",
		'</section> </div>',
	'</main>';	
	
//On affiche le pied de page
fd_html_pied();

mysqli_close($GLOBALS['bd']);

?>
