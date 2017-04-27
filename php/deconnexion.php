<?php
  include('bibli_24sur7.php');
  session_destroy();
  $_SESSION = array();
  fd_redirige('identification.php');
?>
