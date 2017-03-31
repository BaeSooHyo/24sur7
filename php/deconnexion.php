<?php

  session_destroy();
  $_SESSION = array();
  header('Location: ../php/deconnexion.php');
  exit;

?>
