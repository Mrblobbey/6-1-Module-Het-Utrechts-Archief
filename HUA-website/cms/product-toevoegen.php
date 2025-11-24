<?php
session_start();
include '../includes/conn.php';

function e($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $naam = trim($_POST['naam'] ?? '');
    $beschrijving = trim($_POST['beschrijving'] ?? '');

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

    $sql = "INSERT INTO product (naam, beschrijving, afbeelding)
            VALUES (:naam, :beschrijving, :afbeelding)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':naam' => $naam,
        ':beschrijving' => $beschrijving,
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

    <div class="header">
        <img src="../img/image.png" alt="header">
    </div>

    <div class="add-wrap">
        <?php if ($success): ?><div class="add-alert-success"><?= e($success) ?></div><?php endif; ?>
        <?php if ($error): ?><div class="add-alert-error"><?= e($error) ?></div><?php endif; ?>

        <div class="add-form-panel">
            <h1>Nieuw product toevoegen</h1>
            <form method="post" enctype="multipart/form-data" class="add-form">
                <div class="left-col">
                    <label>Afbeelding</label>
                    <input type="file" name="afbeelding">

                    <label>Titel *</label>
                    <input type="text" name="naam" placeholder="bla bla bla" required>

                    <label>Beschrijving</label>
                    <textarea name="beschrijving" placeholder="bla bla bla" required></textarea>
                </div>

                <div class="right-col">
                    <label>Link_bron</label>
                    <input type="text" name="link_bron" placeholder="https://..." required>

                    <label>Bronnen_text</label>
                    <input type="text" name="bronnen_text" placeholder="bla bla bla" required>

                    <label>Bron_auteur</label>
                    <input type="text" name="bron_auteur" placeholder="bla bla bla" required>

                    <label>Bron_datum</label>
                    <input type="text" name="bron_datum" placeholder="18-04-2008" required>
                </div>

                <div class="add-btn-container">
                    <div class="hotspot-drop"></div>
                    <button type="button" class="hotspot-btn">Hotspot</button>
                </div>
            </form>
            <div class="add-delete-buttons">
                <button type="submit" class="add-btn-save" form="productForm">Toevoegen</button>
                <a href="product-beheer.php" class="add-btn-cancel">Annuleren</a>
            </div>
        </div>
    </div>
</body>

</html>