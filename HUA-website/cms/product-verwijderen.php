<?php
include '../connectie.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    $stmt = $conn->prepare("DELETE FROM producten WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: producten-cms.php");
exit;
?>
