<?php
session_start();
include '../includes/conn.php';

// Check of we een edit willen doen
if (isset($_GET['action'], $_GET['id']) && $_GET['action'] === 'edit') {
    $id = (int)$_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM artikel WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $artikel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$artikel) {
        $_SESSION['error'] = "Artikel niet gevonden.";
        header("Location: artikel-beheer.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $titel = trim($_POST['titel'] ?? '');
        $beschrijving = trim($_POST['beschrijving'] ?? '');
        $link_bron = trim($_POST['link_bron'] ?? '');
        $bronnen_tekst = trim($_POST['bronnen_tekst'] ?? '');
        $bron_auteur = trim($_POST['bron_auteur'] ?? '');
        $auteur_id = intval($_POST['auteur_id'] ?? 0);
        $brond_datum = trim($_POST['brond_datum'] ?? '');
        $seizoenen = trim($_POST['seizoenen'] ?? '');
        $actief = isset($_POST['actief']) ? 1 : 0;

        // Afbeelding uploaden
        if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] === 0) {
            $uploadDir = '../img/';
            $fileName = basename($_FILES['afbeelding']['name']);
            $uploadFile = $uploadDir . $fileName;
            move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadFile);
            $afbeelding = $fileName;
        } else {
            $afbeelding = $artikel['afbeelding'];
        }

        if ($titel === '') {
            $_SESSION['error'] = "Titel is verplicht.";
            header("Location: artikel-bewerken.php?action=edit&id=$id");
            exit;
        }

        $stmt = $conn->prepare("UPDATE artikel SET 
            titel = :titel,
            beschrijving = :beschrijving,
            link_bron = :link_bron,
            bronnen_tekst = :bronnen_tekst,
            bron_auteur = :bron_auteur,
            auteur_id = :auteur_id,
            brond_datum = :brond_datum,
            seizoenen = :seizoenen,
            actief = :actief,
            afbeelding = :afbeelding
            WHERE id = :id
        ");
        $stmt->execute([
            ':titel' => $titel,
            ':beschrijving' => $beschrijving,
            ':link_bron' => $link_bron,
            ':bronnen_tekst' => $bronnen_tekst,
            ':bron_auteur' => $bron_auteur,
            ':auteur_id' => $auteur_id,
            ':brond_datum' => $brond_datum,
            ':seizoenen' => $seizoenen,
            ':actief' => $actief,
            ':afbeelding' => $afbeelding,
            ':id' => $id
        ]);

        $_SESSION['success'] = "Artikel succesvol bijgewerkt.";
        header("Location: artikel-bewerken.php?action=edit&id=$id");
        exit;
    }
} else {
    $_SESSION['error'] = "Ongeldige actie.";
    header("Location: artikel-beheer.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Artikel bewerken</title>
    <link rel="stylesheet" href="cms.css">
</head>

<body>
    <div class="add-wrap">
        <div class="add-form-panel">
            <div class="add-title">
                <div class="add-top">
                    <h1>Artikel bewerken</h1>
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="add-alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="add-alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form class="add-form" method="post" enctype="multipart/form-data">
                <div class="left-col">
                    <label>Afbeelding</label>
                    <?php if (!empty($artikel['afbeelding'])): ?>
                        <img src="../img/<?= htmlspecialchars($artikel['afbeelding']) ?>" alt="Afbeelding" style="max-width:100%; margin-bottom:10px;">
                    <?php endif; ?>
                    <input type="file" name="afbeelding">

                    <label>Titel *</label>
                    <input type="text" name="titel" value="<?= htmlspecialchars($artikel['titel']) ?>" required>

                    <label>Beschrijving</label>
                    <textarea name="beschrijving"><?= htmlspecialchars($artikel['beschrijving']) ?></textarea>

                    <label>Link bron</label>
                    <input type="text" name="link_bron" value="<?= htmlspecialchars($artikel['link_bron']) ?>">
                </div>

                <div class="right-col">
                    <label>Bronnen tekst</label>
                    <input type="text" name="bronnen_tekst" value="<?= htmlspecialchars($artikel['bronnen_tekst']) ?>">

                    <label>Bron / auteur</label>
                    <input type="text" name="bron_auteur" value="<?= htmlspecialchars($artikel['bron_auteur']) ?>">

                    <label>Auteur ID</label>
                    <input type="number" name="auteur_id" value="<?= htmlspecialchars($artikel['auteur_id']) ?>">

                    <label>Brond datum</label>
                    <input type="text" name="brond_datum" value="<?= htmlspecialchars($artikel['brond_datum']) ?>">

                    <label>Seizoenen</label>
                    <input type="text" name="seizoenen" value="<?= htmlspecialchars($artikel['seizoenen']) ?>">

                    <label><input type="checkbox" name="actief" <?= $artikel['actief'] ? 'checked' : '' ?>> Actief</label>
                </div>
                                <div class="add-btn-container">
                    <div class="hotspot-drop"></div>
                    <button type="button" class="hotspot-btn">Hotspot</button>
                </div>
            </form>
            <div class="add-btn-container">
                <button type="submit" class="add-btn-save">Opslaan</button>
                <a href="product-beheer.php" class="add-btn-cancel">Annuleren</a>
            </div>
        </div>
    </div>
</body>

</html>