<?php
include '../conn.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); 

    $stmt = $conn->prepare("DELETE FROM producten WHERE id = :id");
    $stmt->execute(['id' => $id]);
}

header("Location: producten-beheer.php");
exit;
?>
