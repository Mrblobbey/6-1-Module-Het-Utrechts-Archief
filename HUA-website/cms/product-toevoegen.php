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

    $naam = trim($_POST['naam']);
    $beschrijving = trim($_POST['beschrijving']);
    $link_bron = trim($_POST['link_bron']);
    $afbeelding_id = intval($_POST['afbeelding_id']);
    $x = intval($_POST['x']);
    $y = intval($_POST['y']);

    if ($naam === '') {
        $_SESSION['error'] = 'Titel is verplicht.';
        header('Location: artikel-toevoegen.php');
        exit;
    }

    // Ophalen afbeelding-bestandsnaam
    $stmt = $conn->prepare("SELECT afbeelding FROM artikel WHERE id = :id");
    $stmt->execute([':id' => $afbeelding_id]);
    $row = $stmt->fetch();

    $sql = "INSERT INTO artikel (catalogusnummer, beschrijving, link_bron, afbeelding, x, y)
            VALUES (:naam, :beschrijving, :link_bron, :afbeelding, :x, :y)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':naam' => $naam,
        ':beschrijving' => $beschrijving,
        ':link_bron' => $link_bron,
        ':afbeelding' => $row['afbeelding'],
        ':x' => $x,
        ':y' => $y,
    ]);

    $_SESSION['success'] = 'Artikel + hotspot succesvol toegevoegd.';
    header('Location: product-toevoegen.php');
    exit;
}

// Alle beschikbare images ophalen
$images = $conn->query("SELECT id, afbeelding FROM artikel ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);


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
                    <div id="alert-success" class="add-alert-success"><?= e($success) ?></div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div id="alert-error" class="add-alert-error"><?= e($error) ?></div>
                <?php endif; ?>
            </div>

            <form method="post" enctype="multipart/form-data">

                <label>Catalogus nummer *</label>
                <input type="text" name="naam" placeholder="123456" required>

                <label>Link bron</label>
                <input type="text" name="link_bron" placeholder="https://..." required>

                <label>Beschrijving</label>
                <textarea name="beschrijving" placeholder="text..." required></textarea>

                <label>Kies afbeelding *</label><br>
                <select name="afbeelding_id" id="afbeeldingSelect">
                    <option value="" required>-- Kies een afbeelding --</option>
                    <?php foreach ($images as $img): ?>
                        <option value="<?= $img['id'] ?>"><?= $img['afbeelding'] ?></option>
                    <?php endforeach; ?>
                </select>


                <div id="hotspot-wrapper" style="position:relative; display:none; margin-top:20px;">
                    <img id="hotspot-image" src="" style="max-width:500px; display:block;">

                    <div id="hotspot"
                        style="
                width:25px; height:25px;
                background:red; border-radius:50%;
                position:absolute; top:0; left:0;
                cursor:grab;
             ">
                    </div>
                </div>

                <input type="hidden" name="x" id="input-x">
                <input type="hidden" name="y" id="input-y">
                <div class="add-cancel-btn-container" style="margin-top:20px;"> <button type="submit" class="add-btn-save">Toevoegen</button> <a href="product-beheer.php" class="add-btn-cancel">Annuleren</a> </div>
            </form>
        </div>
    </div>
    <script>
        const hotspot = document.getElementById("hotspot");
        const hotspotWrapper = document.getElementById("hotspot-wrapper");
        const hotspotImage = document.getElementById("hotspot-image");
        const afbeeldingSelect = document.getElementById("afbeeldingSelect");

        const inputX = document.getElementById("input-x");
        const inputY = document.getElementById("input-y");

        let dragging = false;
        let offsetX, offsetY;

        afbeeldingSelect.addEventListener("change", function() {
            if (!this.value) return;

            hotspotImage.src = "../img/" + this.options[this.selectedIndex].text;
            hotspotWrapper.style.display = "block";
        });

        hotspot.addEventListener("mousedown", (e) => {
            dragging = true;
            offsetX = e.offsetX;
            offsetY = e.offsetY;
        });

        document.addEventListener("mouseup", () => dragging = false);

        document.addEventListener("mousemove", (e) => {
            if (!dragging) return;

            const rect = hotspotImage.getBoundingClientRect();
            let x = e.clientX - rect.left - offsetX;
            let y = e.clientY - rect.top - offsetY;

            // Grenzen
            x = Math.max(0, Math.min(x, rect.width - hotspot.offsetWidth));
            y = Math.max(0, Math.min(y, rect.height - hotspot.offsetHeight));

            // Posities zetten
            hotspot.style.left = x + "px";
            hotspot.style.top = y + "px";

            // Opslaan
            inputX.value = Math.round(x);
            inputY.value = Math.round(y);
        });

        setTimeout(() => {
    const successAlert = document.getElementById("alert-success");
    const errorAlert = document.getElementById("alert-error");

    if (successAlert) successAlert.style.display = 'none';
    if (errorAlert) errorAlert.style.display = 'none';
}, 2000);

    </script>

    <script src="../script/header.js"></script>
    <script src="../script/script.js"></script>
</body>

</html>