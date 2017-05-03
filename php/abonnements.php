<?php
ob_start();
include ('bibli_24sur7.php');
session_start();
fd_verifie_session();

fd_html_head(APP_NOM_APPLICATION.' | Abonnements', '../css/style.css');
fd_html_bandeau(APP_PAGE_ABONNEMENTS);



fd_html_pied();
ob_flush();
ob_end_flush();

?>
