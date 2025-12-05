
<?php

include 'includes/conn.php';

$stmt = $conn->query("SELECT * FROM artikel");
$artikelen = $stmt->fetchAll(PDO::FETCH_ASSOC);

// echo '<pre>';
// print_r($artikelen);
// echo '<pre>';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Het Utrechts Archief</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php
    include 'includes/header.php'
    ?>

    <main>

        <div class="panorama">
            <div class="panorama-content">

                <div class="panorama-fotos" id="panoramaFotos">
                    <?php foreach ($artikelen as $artikel): ?>
                        <?php
                        $artikel['height'] = $artikel['height'] * 1.36;
                        $artikel['margin_left'] = $artikel['margin_left'] * 1.5;
                        $artikel['margin_top'] = $artikel['margin_top'] * 1.5;
                        $styleAttr = sprintf(
                            'height: %spx; margin-left: %spx; margin-top: %spx;',
                            $artikel['height'],
                            $artikel['margin_left'],
                            $artikel['margin_top']
                        );
                        ?>
                        <div class="panorama-img-wrapper" style="z-index: <?= $artikel['z_index']; ?>">
                            <!-- <div id="Hotspot_<?php echo ($artikel["id"]) ?>"></div> -->
                            <img
                                src="img/<?= htmlspecialchars($artikel['afbeelding'], ENT_QUOTES) ?>"
                                alt="<?= htmlspecialchars($artikel['alt'], ENT_QUOTES) ?>"
                                style="<?= $styleAttr ?>">

                            <?php if (isset($artikel['x']) && isset($artikel['y'])): ?>
                                <?php
                                $scaledX = $artikel['x'] * 4.5;
                                $scaledY = $artikel['y'] * 4.5;
                                ?>
                                <div class="point-wrapper"
                                    style="top: <?= $scaledY ?>px; left: <?= $scaledX ?>px; position:absolute;"
                                    data-id="<?= htmlspecialchars($artikel['id'], ENT_QUOTES) ?>"
                                    data-beschrijving="<?= htmlspecialchars($artikel['beschrijving'], ENT_QUOTES) ?>"
                                    data-link="<?= htmlspecialchars($artikel['link_bron'], ENT_QUOTES) ?>">
                                    <i></i>

                                    <div class="hotspot-popup">
                                        <h4 class="hotspot-beschrijving">
                                            <?= htmlspecialchars($artikel['beschrijving'], ENT_QUOTES) ?>
                                        </h4>

                                        <?php if (!empty($artikel['link_bron'])): ?>
                                            <p class="hotspot-link">
                                                <a href="<?= htmlspecialchars($artikel['link_bron'], ENT_QUOTES) ?>"
                                                    target="_blank"
                                                    rel="noopener noreferrer">
                                                    Bekijken →
                                                </a>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="panorama-minimap" id="panoramaMinimap">
                    <div class="panorama-minimap-viewport" id="panoramaMinimapViewport"></div>
                </div>
                <!-- pijltjes -->
                <button class="panorama-arrow panorama-arrow-left" type="button" aria-label="Scroll naar links">
                    ‹
                </button>
                <button class="panorama-arrow panorama-arrow-right" type="button" aria-label="Scroll naar rechts">
                    ›
                </button>

            </div>
        </div>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">

            <div class="footer-columns">

                <!-- bezoek plus onderzoek-->
                <div class="footer-column">
                    <h3 class="footer-title">Plan een bezoek</h3>
                    <ul class="footer-linklist">
                        <li><a href="#">Expo - Hamburgerstraat 28</a></li>
                        <li><a href="#">Studiezaal - Alexander Numankade 199 - 201</a></li>
                    </ul>

                    <h4 class="footer-subtitle">Onderzoek</h4>
                    <ul class="footer-linklist">
                        <li><a href="#">Archieven doorzoeken</a></li>
                        <li><a href="#">Beeldmateriaal bekijken</a></li>
                        <li><a href="#">Bouwtekeningen</a></li>
                        <li><a href="#">Personen zoeken</a></li>
                    </ul>
                </div>

                <!-- over ons-->
                <div class="footer-column">
                    <h3 class="footer-title">Over ons</h3>
                    <ul class="footer-linklist">
                        <li><a href="#">Nieuws</a></li>
                        <li><a href="#">Agenda</a></li>
                        <li><a href="#">Uw materiaal in ons archief</a></li>
                        <li><a href="#">Contact</a></li>
                        <li><a href="#">Toegankelijkheid</a></li>
                        <li><a href="#">Auteursrecht en disclaimer</a></li>
                        <li><a href="#">Privacyverklaring</a></li>
                        <li><a href="#">ANBI</a></li>
                        <li><a href="#" class="footer-link-italic">English</a></li>
                    </ul>
                </div>

                <!-- contact en sociaal -->
                <div class="footer-column-contact">
                    <h3 class="footer-title">Contact</h3>
                    <ul class="footer-contact-list">
                        <li>
                            <span class="footer-contact-icon">📞</span>
                            <span>(030) 286 66 11</span>
                        </li>
                        <li>
                            <span class="footer-contact-icon">@</span>
                            <a href="mailto:inlichtingen@hetutrechtsarchief.nl">
                                inlichtingen@hetutrechtsarchief.nl
                            </a>
                        </li>
                        <li>
                            <span class="footer-contact-icon">✉️</span>
                            <span>Postadres: Postbus 131, 3500 AC Utrecht</span>
                        </li>
                        <li>
                            <span class="footer-contact-icon">💬</span>
                            <span>Chat: di t/m do 9.00 - 13.00 uur</span>
                        </li>
                    </ul>

                    <div class="footer-social">
                        <p class="footer-subtitle">Volg ons op</p>
                        <div class="footer-social-links">
                            <a href="#" aria-label="Facebook">F</a>
                            <a href="#" aria-label="Instagram">I</a>
                            <a href="#" aria-label="YouTube">Y</a>
                            <a href="#" aria-label="RSS">RSS</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- nieuwsbrief blok -->
            <div class="footer-newsletter">
                <h3 class="footer-newsletter-title">Blijf op de hoogte van het laatste nieuws</h3>
                <form action="#" method="post" class="footer-newsletter-form">
                    <input type="email" name="email" placeholder="E-mailadres">
                    <button type="submit">Verstuur</button>
                </form>
            </div>

            <!-- onderaan kleine gegevens -->
            <div class="footer-kvk">
                <p>IBAN: NL66RABO0123881641</p>
                <p>KvK: 62047302</p>
                <p>BTW: NL807024594B01</p>
            </div>

        </div>
    </footer>
    <script src="script/header.js"></script>
    <script src="script/script.js"></script>
</body>

</html>