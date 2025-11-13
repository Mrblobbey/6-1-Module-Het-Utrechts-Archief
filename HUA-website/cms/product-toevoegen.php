<?php
include '../connectie.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $naam = trim($_POST['naam']);
        $prijs = trim($_POST['prijs']);
        $beschrijving = trim($_POST['beschrijving']);
        $opsomming = trim($_POST['opsomming']);

        // ✅ Converteer prijs naar geldig decimaal
        $prijs = str_replace(',', '.', $prijs);           // vervang komma door punt
        $prijs = preg_replace('/[^0-9.]/', '', $prijs);  // verwijder alles behalve cijfers en punt
        $prijs = floatval($prijs);                        // maak er float van

        if ($prijs <= 0) {
            throw new Exception("Prijs moet een positief getal zijn.");
        }

        // Uploadmap en bestandsnaam
        $uploadDir = "../img/"; 
        $bestandsnaam = null;

        if (!empty($_FILES['afbeelding']['name'])) {
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0777, true)) {
                    throw new Exception("Kon uploadmap niet aanmaken.");
                }
            }

            $bestandsnaam = basename($_FILES['afbeelding']['name']);
            if (!move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadDir . $bestandsnaam)) {
                throw new Exception("Upload van afbeelding mislukt.");
            }
        }

        // Database insert
        $stmt = $conn->prepare("INSERT INTO producten (naam, prijs, beschrijving, opsommingen, img) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt->execute([$naam, $prijs, $beschrijving, $opsomming, $bestandsnaam ? 'img/' . $bestandsnaam : null])) {
            throw new Exception("Database insert mislukt.");
        }

        // Alleen redirect als alles gelukt is
        header('Location: producten-cms.php');
        exit;
    }
} catch (Exception $e) {
    // Foutmelding op de pagina tonen
    echo "<pre style='color:red;'>Fout opgetreden: " . $e->getMessage() . "</pre>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Nieuw product</title>
    <link rel="stylesheet" href="cms.css">
    <link rel="icon" href="/img/favicon.png" type="image/png">
</head>
<body class="container-achtergrond">
    <h4>Nieuw product toevoegen</h4>
    <div class="invulveld-toevoegen">
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="naam" placeholder="Naam*" required>
            <input type="text" name="prijs" placeholder="Prijs*" required>

            <label for="beschrijving">Beschrijving*</label>
            <textarea name="beschrijving" id="beschrijving" rows="4" placeholder="Voeg hier een korte beschrijving toe..." required></textarea>

            <label for="opsomming">Opsomming (bijv. producteigenschappen)</label>
            <textarea name="opsomming" id="opsomming" rows="3" placeholder="Bijv. - Materiaal: Glas
- LED-verlichting
- Hoogte: 20cm"></textarea>

            <label class="pb-label">Afbeelding</label>
            <img id="preview-image" src="#" alt="Preview afbeelding" class="pb-image" style="display:none;">
            <input type="file" name="afbeelding" id="afbeelding-input" class="pb-file" accept="image/*">

            <button type="submit">Opslaan</button>
        </form>
    </div>

    <div class="pb-back-link">
        <a href="producten-cms.php">&larr; Terug naar overzicht</a>
    </div>

    <script>
        const fileInput = document.getElementById('afbeelding-input');
        const previewImage = document.getElementById('preview-image');

        fileInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const imageURL = URL.createObjectURL(file);
                previewImage.src = imageURL;
                previewImage.style.display = 'block';
            } else {
                previewImage.src = '#';
                previewImage.style.display = 'none';
            }
        });
    </script>
</body>
</html>
