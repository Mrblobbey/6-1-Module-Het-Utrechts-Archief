<?php
    include '../connectie.php'
?>
<?php
$stmt = $conn->query("SELECT * FROM producten");
$producten = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<link rel='stylesheet' href="cms.css">
<html>
<head>
    <title>Producten CMS</title>
</head>
<body class="container-achtergrond">
    <div class="titel-product-beheer">
        <h1>Producten Beheer</h1>
        <a href="product-toevoegen.php">Nieuw product toevoegen</a>
    </div>
    <table>
        <tr>
         <th>ID</th><th>Naam</th><th>Prijs</th><th>Afbeelding</th><th>Acties</th>
        </tr>
        <?php foreach ($producten as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['naam']) ?></td>
            <td>€<?= number_format($p['prijs'], 2) ?></td>
            <td><img src="../<?= htmlspecialchars($p['img']) ?>" alt=""></td>
            <td>
                <a href="product-bewerken.php?id=<?= $p['id'] ?>">Bewerken</a> |
                <a href="product-verwijderen.php?id=<?= $p['id'] ?>" class="verwijder-link">Verwijderen</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const verwijderLinks = document.querySelectorAll('.verwijder-link');

    verwijderLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault(); 
            const bevestiging = confirm('Weet je zeker dat je dit product wilt verwijderen?');
            if (bevestiging) {
                window.location.href = link.href; 
            }
        });
    });
});
</script>

</body>
    <div class="pb-back-link">
        <a href="overzichtspagina.php">&larr; Terug naar overzicht</a>
    </div>
</html>
