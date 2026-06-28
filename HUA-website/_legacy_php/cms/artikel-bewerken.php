<?php
ob_start();
session_start();
include '../includes/conn.php';
include '../includes/login-true.php';
 
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Ongeldige actie.";
    header("Location: product-beheer.php");
    exit;
}
 
$hotspot_id = (int)$_GET['id'];
 
$stmt = $conn->prepare("SELECT * FROM artikelaanvullend WHERE id = :id");
$stmt->execute([':id' => $hotspot_id]);
$hotspot = $stmt->fetch(PDO::FETCH_ASSOC);
 
if (!$hotspot) {
    $_SESSION['error'] = "Hotspot niet gevonden.";
    header("Location: product-beheer.php");
    exit;
}
 
$stmt = $conn->prepare("SELECT afbeelding FROM artikel WHERE id = :id");
$stmt->execute([':id' => $hotspot['artikel_id']]);
$artikel = $stmt->fetch(PDO::FETCH_ASSOC);
$afbeelding = $artikel['afbeelding'] ?? null;
 
$images = $conn->query("SELECT id, afbeelding FROM artikel ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $beschrijving = trim($_POST['beschrijving'] ?? '');
    $link_bron = trim($_POST['link_bron'] ?? '');
    $x = isset($_POST['x']) ? (int)$_POST['x'] : 0;
    $y = isset($_POST['y']) ? (int)$_POST['y'] : 0;
    $afbeelding_id = isset($_POST['afbeelding_id']) ? (int)$_POST['afbeelding_id'] : $hotspot['artikel_id'];
 
    if ($beschrijving === '') {
        $_SESSION['error'] = "Beschrijving is verplicht.";
        header("Location: edit-hotspot.php?id=" . $hotspot_id);
        exit;
    }
 
    $huidige_imgs = [
        $hotspot['afbeelding1'],
        $hotspot['afbeelding2'],
        $hotspot['afbeelding3'],
        $hotspot['afbeelding4']
    ];
 
    $nieuwe_imgs = [];
 
    for ($i = 0; $i < 4; $i++) {
        // verwijderen
        if (isset($_POST['remove_' . $i])) {
            $nieuwe_imgs[$i] = null;
            continue;
        }
 
        // nieuwe upload
        if (!empty($_FILES['hotspot_afbeelding']['name'][$i])) {
            $tmp = $_FILES['hotspot_afbeelding']['tmp_name'][$i];
            $name = time() . '_' . basename($_FILES['hotspot_afbeelding']['name'][$i]);
            move_uploaded_file($tmp, "../img/" . $name);
            $nieuwe_imgs[$i] = $name;
            continue;
        }
 
        // niks gewijzigd
        $nieuwe_imgs[$i] = $huidige_imgs[$i];
    }
 
    $stmt = $conn->prepare("UPDATE artikelaanvullend SET
        beschrijving = :beschrijving,
        link_bron = :link_bron,
        x = :x,
        y = :y,
        artikel_id = :artikel_id,
        afbeelding1 = :af1,
        afbeelding2 = :af2,
        afbeelding3 = :af3,
        afbeelding4 = :af4
        WHERE id = :id
    ");
    $stmt->execute([
        ':beschrijving' => $beschrijving,
        ':link_bron' => $link_bron,
        ':x' => $x,
        ':y' => $y,
        ':artikel_id' => $afbeelding_id,
        ':af1' => $nieuwe_imgs[0],
        ':af2' => $nieuwe_imgs[1],
        ':af3' => $nieuwe_imgs[2],
        ':af4' => $nieuwe_imgs[3],
        ':id' => $hotspot_id
    ]);
 
    $_SESSION['success'] = "Hotspot succesvol bijgewerkt.";
    header("Location: product-beheer.php");
    exit;
}
 
include '../includes/header.php';
?>
 
<!DOCTYPE html>
<html lang="nl">
 
<head>
    <meta charset="UTF-8">
    <title>Hotspot bewerken</title>
    <link rel="stylesheet" href="cms.css">
</head>
 
<body>
    <div class="add-wrap">
        <div class="add-form-panel">
 
            <div class="add-title">
                <h1>Hotspot bewerken</h1>
            </div>
 
            <?php if (!empty($_SESSION['error'])): ?>
                <div class="add-alert-error"><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
 
            <?php if (!empty($_SESSION['success'])): ?>
                <div class="add-alert-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>
 
            <form method="post" enctype="multipart/form-data" >
 
                <!-- LINKS: info + hotspot -->
                <div class="left-col" style="flex:1; display:flex; flex-direction:column;">
                    <label>Beschrijving</label>
                    <textarea name="beschrijving" required><?= htmlspecialchars($hotspot['beschrijving']) ?></textarea>
 
                    <label>Link bron</label>
                    <input type="text" name="link_bron" value="<?= htmlspecialchars($hotspot['link_bron']) ?>">
 
 
                    <input type="hidden" name="x" id="input-x" value="<?= (int)$hotspot['x'] ?>">
                    <input type="hidden" name="y" id="input-y" value="<?= (int)$hotspot['y'] ?>">
                    <h3>Afbeeldingen (max 4)</h3>
                    <div class="image-grid">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                            <div class="image-card">
                                <?php if (!empty($hotspot['afbeelding' . ($i + 1)])): ?>
                                    <img src="../img/<?= htmlspecialchars($hotspot['afbeelding' . ($i + 1)]) ?>" class="thumb">
                                <?php else: ?>
                                    <div class="thumb placeholder">Geen afbeelding</div>
                                <?php endif; ?>
 
                                <label class="file-label">
                                    Upload afbeelding
                                    <input type="file" name="hotspot_afbeelding[]" accept="image/*">
                                </label>
                                <label class="delete-label">
                                    <input type="checkbox" name="remove_<?= $i ?>"> Verwijderen
                                </label>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
 
 
                <!-- RECHTS: 4 afbeeldingen -->
                <div class="right-col" style="flex:1;">
                    <select name="afbeelding_id" id="afbeeldingSelect">
                        <option value="">-- Kies een basis afbeelding --</option>
                        <?php foreach ($images as $img): ?>
                            <option value="<?= $img['id'] ?>" <?= $img['id'] == $hotspot['artikel_id'] ? 'selected' : '' ?>><?= $img['afbeelding'] ?></option>
                        <?php endforeach; ?>
                    </select>
 
                    <div id="hotspot-wrapper" style="position:relative; display:block; margin-top:10px;">
                        <img id="hotspot-image" src="../img/<?= htmlspecialchars($afbeelding) ?>" style="max-width:400px; display:block;">
                        <div id="hotspot" style="width:25px; height:25px; background:red; border-radius:50%; position:absolute; top:<?= (int)$hotspot['y'] ?>px; left:<?= (int)$hotspot['x'] ?>px; cursor:grab;"></div>
                    </div>
                    <div class="add-cancel-btn-container" style="margin-top:20px;">
                        <button type="submit" class="add-btn-save">Opslaan</button>
                        <a href="product-beheer.php" class="add-btn-cancel">Annuleren</a>
                    </div>
                </div>
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
 
            x = Math.max(0, Math.min(x, rect.width - hotspot.offsetWidth));
            y = Math.max(0, Math.min(y, rect.height - hotspot.offsetHeight));
 
            hotspot.style.left = x + "px";
            hotspot.style.top = y + "px";
 
            inputX.value = Math.round(x);
            inputY.value = Math.round(y);
        });
    </script>
 
    <script src="../script/header.js"></script>
    <script src="../script/script.js"></script>
</body>
 
</html>
 