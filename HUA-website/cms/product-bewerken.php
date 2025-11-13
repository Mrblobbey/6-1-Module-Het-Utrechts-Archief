<!DOCTYPE html>
<html>

<head>
    <title>Product Bewerken</title>
    <link rel="stylesheet" href="cms.css">
</head>
<?php
include '../connectie.php';

if (!isset($_GET['id'])) {
    die("Geen product ID opgegeven.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM producten WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Product niet gevonden.");
}

$popupMelding = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = $_POST['naam'];
    $prijs = $_POST['prijs'];
    $targetDir = "../img/";

    if (!is_dir($targetDir))
        mkdir($targetDir, 0777, true);

    if (!empty($_FILES['afbeelding']['name'])) {
        $targetFile = $targetDir . basename($_FILES['afbeelding']['name']);
        move_uploaded_file($_FILES['afbeelding']['tmp_name'], $targetFile);
        $afbeelding = basename($_FILES['afbeelding']['name']);
    } else {
        $afbeelding = $product['img'];
    }

    $update = $conn->prepare("UPDATE producten SET naam = :naam, prijs = :prijs, img = :img WHERE id = :id");
    $update->execute([
        'naam' => $naam,
        'prijs' => $prijs,
        'img' => $afbeelding,
        'id' => $id
    ]);

    $popupMelding = "Product bijgewerkt!";
}
?>

<body class="container-achtergrond">
    <h1 class="pb-title">Product Bewerken</h1>
    <form action="" method="post" enctype="multipart/form-data" class="pb-form-container">
        <label class="pb-label">Naam:</label>
        <input type="text" name="naam" value="<?= htmlspecialchars($product['naam']) ?>" class="pb-input" required>

        <label class="pb-label">Prijs:</label>
        <input type="number" step="0.01" name="prijs" value="<?= $product['prijs'] ?>" class="pb-input" required>

        <label class="pb-label">Beschrijving*</label>
        <textarea name="beschrijving" id="beschrijving" rows="4"
            placeholder="Voeg hier een korte beschrijving toe..."><?= htmlspecialchars($product['beschrijving'] ?? '') ?></textarea>

        <label class="pb-label">Opsomming (bijv. producteigenschappen)</label>
        <textarea name="opsomming" id="opsomming" rows="3"
            placeholder="Bijv. - Materiaal: Glas&#10;- LED-verlichting&#10;- Hoogte: 20cm"><?= htmlspecialchars($product['opsommingen'] ?? '') ?></textarea>

        <label class="pb-label">Huidige Afbeelding:</label>
        <img id="preview-image" src="../<?= htmlspecialchars($product['img']) ?>" alt="Huidige afbeelding"
            class="pb-image">

        <label class="pb-label">Nieuwe Afbeelding (optioneel):</label>
        <input type="file" name="afbeelding" id="afbeelding-input" class="pb-file" accept="image/*">

        <button type="submit" class="pb-button">Opslaan</button>
    </form>

    <div id="popup" class="popup center">
        <div class="icon">
            <img src="../img/check.svg" class="check-icon">
        </div>
        <div class="titel">Succes!!</div>
        <div class="description"></div>
        <div class="dismiss-btn">
            <button id="dismiss-popup-btn">Sluiten</button>
        </div>
    </div>

    <div class="pb-back-link">
        <a href="producten-cms.php">&larr; Terug naar overzicht</a>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const popup = document.getElementById("popup");
            const dismissBtn = document.getElementById("dismiss-popup-btn");
            const fileInput = document.getElementById("afbeelding-input");
            const previewImage = document.getElementById("preview-image");


            fileInput.addEventListener("change", function (event) {
                const file = event.target.files[0];
                if (file) {
                    const newImageURL = URL.createObjectURL(file);
                    previewImage.src = newImageURL;
                }
            });

            <?php if (isset($popupMelding)): ?>
                setTimeout(function () {
                    popup.querySelector(".description").textContent = "<?= addslashes($popupMelding) ?>";
                    popup.classList.add("active");
                }, 50);
            <?php endif; ?>

            dismissBtn.addEventListener("click", function () {
                popup.classList.remove("active");
                setTimeout(function () {
                    window.location.href = "producten-cms.php";
                }, 300);
            });
        });
    </script>

</body>

</html>