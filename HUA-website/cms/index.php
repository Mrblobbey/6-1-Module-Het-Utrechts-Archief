<?php
session_start();
include '../connectie.php';

if ((isset($_GET["gebruikersnaam"])) && (isset($_GET["password"]))) {
    $gebruikersnaam = $_GET["gebruikersnaam"];
    $password = $_GET["password"];

    // prepare sql and bind parameters
    $stmt = $conn->prepare("SELECT * FROM users WHERE gebruikersnaam=:gebruikersnaam LIMIT 1");
    $stmt->bindParam(':gebruikersnaam', $gebruikersnaam);
    $stmt->execute();

    if($stmt->rowCount() == 0){
        header('Location: index1.php');
        exit;
    }

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!password_verify($password, $user['password'])){
        header('Location: index.php');
        exit;
    }

    $_SESSION["login"] = true;
    header('Location: overzichtspagina.php');
    exit();
}



?>
<!DOCTYPE html>
<html lang="en">
<link href="cms.css" rel="stylesheet" type="text/css">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="container-achtergrond">
    <h2>CMS inloggen</h2>

    <div class="login-container">
        <form method="GET" action="index.php">
        <div class="input-box">
        <input type="text" name="gebruikersnaam" required placeholder="Gebruikersnaam" />
        <i class="fa-solid fa-user"></i>
        </div>

        <div class="input-box">
        <input type="password" name="password" required placeholder="Wachtwoord" />
        <i class="fa-solid fa-lock"></i>
        </div>
            <br>
            <input class="inlog-button" type="submit" name="submit" value="Inloggen" />
        </form>
    </div>
    <div class="pb-back-link">
        <a href="https://u240569.gluwebsite.nl/">&larr; Terug naar de webshop</a>
    </div>
</body>

</html>