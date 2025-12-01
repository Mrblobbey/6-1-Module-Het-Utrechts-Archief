<?php
session_start();
include '../includes/conn.php';
include '../includes/header.php';
include '../includes/login-true.php';

function e($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $naam = trim($_POST['naam'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $link_bron = trim($_POST['link_bron'] ?? '');
    $actief = isset($_POST['actief']) ? 1 : 0;

    if ($naam === '') {
        $_SESSION['error'] = 'Productnaam is verplicht.';
        header('Location: product-toevoegen.php');
        exit;
    }

    if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] == 0) {
        $uploadDir = '../img/';
        $fileName = time() . "_" . basename($_FILES['afbeelding']['name']);
        $uploadFile = $uploadDir . $fileName;
        move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadFile);
        $afbeelding = $fileName;
    } else {
        $afbeelding = null;
    }

    $sql = "INSERT INTO product (naam, beschrijving, link_bron, actief, afbeelding)
            VALUES (:naam, :beschrijving, :link_bron, :actief, :afbeelding)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':naam' => $naam,
        ':beschrijving' => $beschrijving,
        ':link_bron' => $link_bron,
        ':actief' => $actief,
        ':afbeelding' => $afbeelding
    ]);

    $_SESSION['success'] = 'Product succesvol toegevoegd.';
    header('Location: product-beheer.php');
    exit;
}

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>Product toevoegen</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="cms.css">
</head>

<body>
    <div class="add-wrap">
        <div class="add-form-panel">
            <div class="add-title">
                <div class="add-top">
                    <h1>Nieuw product toevoegen</h1>
                </div>

                <?php if ($success): ?>
                    <div class="add-alert-success"><?= e($success) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="add-alert-error"><?= e($error) ?></div>
                <?php endif; ?>
            </div>

            <form class="add-form" method="post" enctype="multipart/form-data">
                <div class="left-col">
                    <label>Afbeelding</label>
                    <img id="preview" src="" alt="Preview"style="max-width:100%; margin-bottom:10px; max-height:200px;">

                    <input type="file" name="afbeelding" id="fileInput" accept="image/*">


                    <label>Titel *</label>
                    <input type="text" name="naam" placeholder="bla bla bla" required>

                    <label>Beschrijving</label>
                    <textarea name="beschrijving" placeholder="bla bla bla"></textarea>
                </div>

                <div class="left-col">
                    <label>Link bron</label>
                    <input type="text" name="link_bron" placeholder="https://...">

                    <label><input type="checkbox" name="actief"> Actief</label>
                </div>

                <div class="add-btn-container">
                    <div class="hotspot-drop"></div>
                    <button type="button" class="hotspot-btn">Hotspot</button>
                </div>
            </form>

            <div class="add-cancel-btn-container">
                <button type="submit" form="productForm" class="add-btn-save">Toevoegen</button>
                <a href="product-beheer.php" class="add-btn-cancel">Annuleren</a>
            </div>
        </div>
    </div>

    <script src="../script/header.js"></script>
    <script src="../script/script.js"></script>
</body>

</html>