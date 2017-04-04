<?php
ob_start();
include ('bibli_24sur7.php');

fd_html_head('24sur7 | Agenda', '../css/style.css');
fd_html_bandeau(APP_PAGE_AGENDA);
echo '<section id="bcContenu"><div class="aligncenter">';
fd_html_calendrier();
echo '</div></section>';
fd_html_pied();

?>
