<?php
/**
 * actions/logout.php
 * Encerra a sessão do usuário.
 */
session_start();
session_unset();
session_destroy();
header('Location: ../index.php');
exit;
