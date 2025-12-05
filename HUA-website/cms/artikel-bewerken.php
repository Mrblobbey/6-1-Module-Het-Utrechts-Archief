<?php
session_start();
include '../includes/conn.php';
include '../includes/header.php';
include '../includes/login-true.php';

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
        $catalogusnummer = trim($_POST['catalogusnummer'] ?? '');
        $beschrijving = trim($_POST['beschrijving'] ?? '');
        $link_bron = trim($_POST['link_bron'] ?? $artikel['link_bron']);
        $actief = isset($_POST['actief']) ? 1 : 0;

        $afbeelding = $artikel['afbeelding'];
        if (!empty($_FILES['afbeelding']['name']) && $_FILES['afbeelding']['error'] === 0) {
            $uploadDir = '../img/';
            $fileName = time() . '_' . basename($_FILES['afbeelding']['name']);
            move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadDir . $fileName);
            $afbeelding = $fileName;
        }

        if ($catalogusnummer === '') {
            $_SESSION['error'] = "Titel is verplicht.";
            header("Location: product-beheer.php");
            exit;
        }


        $stmt = $conn->prepare("UPDATE artikel SET 
        catalogusnummer = :catalogusnummer,
        beschrijving = :beschrijving,
        link_bron = :link_bron,
        actief = :actief,
        afbeelding = :afbeelding,
        x = :x,
        y = :y
        WHERE id = :id
    ");
        $stmt->execute([
            ':catalogusnummer' => $catalogusnummer,
            ':beschrijving' => $beschrijving,
            ':link_bron' => $link_bron,
            ':actief' => $actief,
            ':afbeelding' => $afbeelding,
            ':x' => $_POST['x'] ?? 0,
            ':y' => $_POST['y'] ?? 0,
            ':id' => $id
        ]);

        $_SESSION['success'] = "Artikel succesvol bijgewerkt.";
        header("Location: product-beheer.php");
        exit;
    }
} else {
    $_SESSION['error'] = "Ongeldige actie.";
    header("Location: beheer-beheer.php");
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
                            <div class="hotspot-container" style="position:relative; display:inline-block; max-width:100%;">
                                <img id="img-hotspot" src="../img/<?= htmlspecialchars($artikel['afbeelding']) ?>" style="width:100%; display:block;">
                                <div id="hotspot"
                                    style="
            width:20px;
            height:20px;
            background:red;
            border-radius:50%;
            position:absolute;
            cursor:pointer;
            left: <?= (int)$artikel['x'] ?>px;
            top: <?= (int)$artikel['y'] ?>px;
        ">
                                </div>
                            </div>

                            <input type="hidden" name="x" id="input-x" value="<?= htmlspecialchars($artikel['x'] ?? 0) ?>">
                            <input type="hidden" name="y" id="input-y" value="<?= htmlspecialchars($artikel['y'] ?? 0) ?>">

                            <label>Titel *</label>
                            <input type="text" name="titel" value="<?= htmlspecialchars($artikel['catalogusnummer']) ?>" required>

                            <label>Link bron</label>
                            <input type="text" name="link_bron" value="<?= htmlspecialchars($artikel['link_bron']) ?>">

                            <label>Titel *</label>
                            <input type="text" name="catalogusnummer" value="<?= htmlspecialchars($artikel['catalogusnummer']) ?>" required>

                            <label>Beschrijving</label>
                            <textarea name="beschrijving"><?= htmlspecialchars($artikel['beschrijving']) ?></textarea>

                            <label><input type="checkbox" name="actief" <?= $artikel['actief'] ? 'checked' : '' ?>> Actief</label>

                            <div class="add-cancel-btn-container">
                                <button type="submit" class="add-btn-save">Opslaan</button>
                                <a href="product-beheer.php" class="add-btn-cancel">Annuleren</a>
                            </div>
                        </div>
                    </form>
                    <script>
                        const hotspot = document.getElementById("hotspot");
                        const img = document.getElementById("img-hotspot");
                        const inputX = document.getElementById("input-x");
                        const inputY = document.getElementById("input-y");

                        let dragging = false;
                        let offsetX = 0;
                        let offsetY = 0;

                        hotspot.addEventListener("mousedown", (e) => {
                            dragging = true;
                            offsetX = e.offsetX;
                            offsetY = e.offsetY;
                        });

                        document.addEventListener("mouseup", () => {
                            dragging = false;
                        });

                        document.addEventListener("mousemove", (e) => {
                            if (!dragging) return;

                            const rect = img.getBoundingClientRect();
                            let x = e.clientX - rect.left - offsetX;
                            let y = e.clientY - rect.top - offsetY;

                            x = Math.max(0, Math.min(x, rect.width - hotspot.offsetWidth));
                            y = Math.max(0, Math.min(y, rect.height - hotspot.offsetHeight));

                            hotspot.style.left = x + "px";
                            hotspot.style.top = y + "px";

                            inputX.value = Math.round(x);
                            inputY.value = Math.round(y);
                        });
                    </script>

                </div>
            </div>

            <script src="../script/header.js"></script>
            <script src="../script/script.js"></script>
</body>

</html>

<?php
include '../includes/header.php';
include '../includes/login-true.php';
?>