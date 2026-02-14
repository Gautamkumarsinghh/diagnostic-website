<?php
session_start();
session_unset();
session_destroy();

// Hamesha Home page par redirect karein
// ../ ka matlab folder se bahar nikalna
header("Location: ../index.php");
exit();
?>