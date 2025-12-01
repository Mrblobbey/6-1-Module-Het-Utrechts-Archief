<?php
session_start();
include '../includes/conn.php';
include '../includes/login-true.php';
include '../includes/login-true.php';

$perPage = 5;
$perPage = 5;

function e($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'create') {
        $catalogusnummer = trim($_POST['catalogusnummer'] ?? '');
        $beschrijving = trim($_POST['beschrijving'] ?? '');
        $link_bron = trim($_POST['link_bron'] ?? '');
        $actief = isset($_POST['actief']) ? 1 : 0;

        if ($catalogusnummer === '') {
            $_SESSION['error'] = 'Titel is verplicht.';
            header('Location: artikel-beheer.php?action=new');
            exit;
        }

        $sql = "INSERT INTO artikel (catalogusnummer, beschrijving, link_bron, actief, afbeelding)
                VALUES (:catalogusnummer, :beschrijving, :link_bron, :actief, :afbeelding)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':catalogusnummer' => $catalogusnummer,
            ':beschrijving' => $beschrijving,
            ':link_bron' => $link_bron,
            ':actief' => $actief,
            ':afbeelding' => $afbeelding
        ]);
        $_SESSION['success'] = 'Artikel toegevoegd.';
        header('Location: artikel-beheer.php');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $catalogusnummer = trim($_POST['catalogusnummer'] ?? '');
        $beschrijving = trim($_POST['beschrijving'] ?? '');
        $link_bron = trim($_POST['link_bron'] ?? '');
        $actief = isset($_POST['actief']) ? 1 : 0;

        if ($catalogusnummer === '') {
            $_SESSION['error'] = 'Titel is verplicht.';
            header('Location: artikel-beheer.php?action=edit&id=' . $id);
            exit;
        }

        $sql = "UPDATE artikel SET catalogusnummer=:catalogusnummer, beschrijving=:beschrijving, link_bron=:linkbron, actief=:actief
                WHERE id = :id LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':catalogusnummer' => $catalogusnummer,
            ':beschrijving' => $beschrijving,
            ':actief' => $actief,
            ':actief' => $actief,
            ':link_bron' => $link_bron,
            ':id' => $id
        ]);
        $_SESSION['success'] = 'Artikel bijgewerkt.';
        header('Location: artikel-beheer.php');
        exit;
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
        $id = intval($_POST['id']);
        $stmt = $conn->prepare("DELETE FROM artikel WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $_SESSION['success'] = 'Artikel verwijderd.';
        header('Location: artikel-beheer.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] == 0) {
        $uploadDir = '../img/';
        $fileName = basename($_FILES['afbeelding']['name']);
        $uploadFile = $uploadDir . $fileName;
        move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadFile);
        $afbeelding = $fileName;
    } else {
        $afbeelding = null;
    }
    if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] == 0) {
        $uploadDir = '../img/';
        $fileName = basename($_FILES['afbeelding']['name']);
        $uploadFile = $uploadDir . $fileName;
        move_uploaded_file($_FILES['afbeelding']['tmp_name'], $uploadFile);
        $afbeelding = $fileName;
    } else {
        $afbeelding = null;
    }
}


$search = trim($_GET['q'] ?? '');
$page = max(1, intval($_GET['page'] ?? 1));
$action = $_GET['action'] ?? null;
$editId = isset($_GET['id']) ? intval($_GET['id']) : null;

$editArtikel = null;
if ($action === 'edit' && $editId) {
    $stmt = $conn->prepare("SELECT * FROM artikel WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $editId]);
    $editArtikel = $stmt->fetch(PDO::FETCH_ASSOC);
}

$where = '1=1';
$params = [];
if ($search !== '') {
    $where = "(catalogusnummer = :q OR beschrijving = :q)";
    $params[':q'] = $search;
}
$countStmt = $conn->prepare("SELECT COUNT(*) FROM artikel WHERE $where");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$pages = max(1, ceil($total / $perPage));
$offset = ($page - 1) * $perPage;

$listStmt = $conn->prepare("SELECT * FROM artikel WHERE $where ORDER BY id DESC LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) {
    if ($k === ':q') $listStmt->bindValue($k, $v);
}
$listStmt->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$listStmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
$listStmt->execute();
$artikelen = $listStmt->fetchAll(PDO::FETCH_ASSOC);

$success = $_SESSION['success'] ?? '';
$error = $_SESSION['error'] ?? '';
unset($_SESSION['success'], $_SESSION['error']);
?>
<!doctype html>
<html lang="nl">

<head>
    <meta charset="utf-8">
    <title>Artikel Beheer</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" href="cms.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <?php include '../includes/header.php'; ?>
    <?php include '../includes/header.php'; ?>
    <div class="wrap">
        <div class="titel">
            <div class="top">
                <h1>Artikel Beheer</h1>
                <a href="index.php">Uitloggen</a>
            </div>
            <a href="product-toevoegen.php" class="btn-new">Nieuw artikel</a>
        </div>
        <?php if ($success): ?>
            <div class="alert success" id="flash-message"><?php echo e($success); ?></div>
            <script>
                setTimeout(() => {
                    const msg = document.getElementById('flash-message');
                    if (msg) msg.style.display = 'none';
                }, 1200);
            </script>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <div class="controls">
            <form method="get" class="search-form" style="margin:0">
                <input type="search" name="q" placeholder="Zoeken..." value="<?php echo e($search); ?>">
                <input type="search" name="q" placeholder="Zoeken..." value="<?php echo e($search); ?>">
                <button type="submit" class="btn-search"><i class="fa fa-search"></i></button>
            </form>
            <div class="right-controls">
                <span class="count">Totaal: <?php echo $total; ?></span>
            </div>
        </div>

        <?php if ($action === 'new' || ($action === 'edit' && $editArtikel)): ?>
            <?php
            $isEdit = $action === 'edit' && $editArtikel;
            $formAction = $isEdit ? 'update' : 'create';
            $vals = $isEdit ? $editArtikel : [
                'catalogusnummer' => '',
                'beschrijving' => '',
                'link_bron' => '',
                'actief' => 1
            ];
            ?>
            <form method="post" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $isEdit ? 'update' : 'create'; ?>">
                <?php if ($isEdit): ?>
                    <input type="hidden" name="id" value="<?php echo (int)$editArtikel['id']; ?>">
                <?php endif; ?>

                <label>Afbeelding</label>
                <input type="file" name="afbeelding">

                <label>Titel *</label>
                <input type="text" name="catalogusnummer" required value="<?php echo e($vals['catalogusnummer']); ?>">

                <label>Beschrijving</label>
                <textarea name="beschrijving"><?php echo e($vals['beschrijving']); ?></textarea>

                <label>Link Bron</label>
                <input type="text" name="link_bron" value="<?php echo e($vals['link_bron']); ?>">

                <label><input type="checkbox" name="actief" <?php echo (isset($vals['actief']) && $vals['actief']) ? 'checked' : ''; ?>> Actief</label>

                <input type="hidden" name="x" id="input-x" value="<?php echo e($vals['x'] ?? 0); ?>">
                <input type="hidden" name="y" id="input-y" value="<?php echo e($vals['y'] ?? 0); ?>">

                <div style="margin-top:12px;">
                    <button type="submit" class="btn-save"><?php echo $isEdit ? 'Opslaan' : 'Toevoegen'; ?></button>
                    <a href="artikel-beheer.php" class="btn-cancel">Annuleren</a>
                </div>
            </form>

    </div>
<?php else: ?>
    <table id="articles-table">
        <thead>
            <tr>
                <th>Afbeelding</th>
                <th>catalogusnummer</th>
                <th>Beschrijving</th>
                <th>Link Bron</th>
                <th>Actief</th>
                <th class="actions">Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($artikelen) === 0): ?>
                <tr>
                    <td colspan="9" style="text-align:center">Geen artikelen gevonden.</td>
                </tr>
            <?php endif; ?>
            <?php foreach ($artikelen as $a): ?>
                <tr>
                    <td>
                        <?php if (!empty($a['afbeelding'])): ?>
                            <img src="../img/<?php echo e($a['afbeelding']); ?>" alt="Afbeelding" style="max-width:80px; max-height:60px;">
                        <?php else: ?>
                            Geen afbeelding
                        <?php endif; ?>
                    </td>

                    <td><?php echo e($a['catalogusnummer']); ?></td>

                    <td>
                        <a href="artikel-bewerken.php?action=edit&id=<?php echo (int)$a['id']; ?>">
                            Bekijk
                        </a>
                    </td>

                    <td>
                        <?php if (!empty($a['link_bron'])): ?>
                            <a href="<?php echo e($a['link_bron']); ?>" target="_blank" rel="noopener noreferrer">
                                Link
                            </a>
                        <?php else: ?>
                            Geen link
                        <?php endif; ?>
                    </td>

                    <td class="actions">
                        <div class="btns">
                            <a class="btn-edit" href="artikel-bewerken.php?action=edit&id=<?php echo (int)$a['id']; ?>">Bewerk</a>
                            <form class="verwijder-btn" method="post" style="display:inline"
                                onsubmit="return confirm('Weet je zeker dat je dit artikel wilt verwijderen?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo (int)$a['id']; ?>">
                                <button type="submit" class="btn-delete">Verwijder</button>
                            </form>
                        </div>
                    </td>
                </tr>

            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?q=<?php echo urlencode($search); ?>&page=<?php echo $page - 1; ?>" class="page-link">&laquo; Vorige</a>
        <?php endif; ?>
        <span>Pagina <?php echo $page; ?> / <?php echo $pages; ?></span>
        <?php if ($page < $pages): ?>
            <a href="?q=<?php echo urlencode($search); ?>&page=<?php echo $page + 1; ?>" class="page-link">Volgende &raquo;</a>
        <?php endif; ?>
    </div>
<?php endif; ?>

</div>
<script src="../script/header.js"></script>
<script src="../script/script.js"></script>
</body>

</html>