<?php
session_start();
include '../includes/conn.php';

$error = '';

if ((isset($_POST["naam_achternaam"])) && (isset($_POST["wachtwoord"]))) {
    $achternaam = $_POST["naam_achternaam"];
    $password = $_POST["wachtwoord"];

    $stmt = $conn->prepare("SELECT * FROM gebruikers WHERE naam_achternaam=:naam_achternaam LIMIT 1");
    $stmt->bindParam(':naam_achternaam', $achternaam);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        $error = "Gebruikersnaam of wachtwoord is onjuist.";
    } else {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($password, $user['wachtwoord'])) {
            $error = "Gebruikersnaam of wachtwoord is onjuist.";
        } else {
            $_SESSION["login"] = true;
            header('Location: product-beheer.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS</title>
    <link rel="stylesheet" href="cms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<header>
    <img src="../img/image.png" alt="header">
</header>

<body>
    <div class="inlog-container">
        <h1>Inloggen</h1>
        <h2>Toegang tot het inhoud beheer systeem</h2>
        <form method="POST">
            <?php if (!empty($error)): ?>
                <div class="error-message" id="errorMessage">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="input-box">
                <input type="text" name="naam_achternaam" required placeholder="Gebruikersnaam*" />
                <i class="fa-solid fa-user"></i>
            </div>

            <div class="input-box">
                <input type="password" name="wachtwoord" required placeholder="Wachtwoord*" />
                <i class="fa-solid fa-lock"></i>
            </div>
            <div class="inlog-button">
                <input type="submit" value="Inloggen">

            </div>
        </form>
        <h2>Wachtwoord vergeten? Neem
            <a href="mailto:d.mateman18@gmail.com">contact</a> op met de ICT</h2>
    </div>
    <script>
        setTimeout(function() {
            var errorDiv = document.getElementById('errorMessage');
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        }, 5000);
    </script>

</body>

</html>