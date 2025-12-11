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
        <!-- Instructie + colofon popup -->
        <div class="intro-overlay" id="introOverlay">
            <div class="intro-modal">
                <button class="intro-close" type="button" aria-label="Sluit uitleg">×</button>

                <h2>Panorama van Utrecht – Uitleg</h2>
                <ul>
                    <li>Gebruik de pijltjes links en rechts om door het panorama te schuiven.</li>
                    <li>Klik in de minimap rechtsonder om snel naar een andere plek te springen.</li>
                    <li>Dubbelklik op het panorama om in of uit te zoomen op de plek waar je klikt.</li>
                    <li>Gebruik de knoppen <strong>+</strong> en <strong>−</strong> om verder in of uit te zoomen.</li>
                    <li>Klik op het scherm-icoon om het panorama op leperello focus scherm te bekijken.</li>
                    <li>Klik op het op het vraag icon om de instructie weer te zien.</li>
                    <li>Klik op het Richtpunt icon om de hotspots aan/uit te zetten </li>
                    <li>Klik op de rode hotspots voor extra informatie.</li>
                </ul>

                <hr>

                <h3>Colofon</h3>
                <p>
                    <strong>Panorama van Utrecht</strong> – titelpagina van het panorama, getekend op lithostenen
                    door <strong>J. Bos</strong>, gedrukt bij <strong>P.W. van de Weijer</strong> en in juli 1859
                    uitgegeven door <strong>Wed. Herfkens en Zoon</strong>.
                </p>
                <p>
                    <strong>Catalogusnummer:</strong> 135001<br>
                    <strong>Datering:</strong> 1859 (01-01-1859 – 31-12-1859)
                </p>
                <p>
                    <strong>Auteursrecht:</strong> Publiek Domein 1.0 – u mag dit beeld downloaden, delen,
                    kopiëren en bewerken, ook voor commerciële doeleinden.
                </p>
                <button class="intro-ok" type="button" onclick="closeIntro()">Ik begrijp het</button>
            </div>
        </div>
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
                                style="<?= $styleAttr ?>"
                                usemap="#image-map-<?= $artikel['id'] ?>">

                            <?php if (isset($artikel['x']) && isset($artikel['y'])): ?>
                                <?php
                                $scaledX = $artikel['x'] * 2.95;
                                $scaledY = $artikel['y'] * 3;
                                ?>
                                <div class="point-wrapper"
                                    style="top: <?= $scaledY ?>px; left: <?= $scaledX ?>px; position:absolute;"
                                    data-catalogus="<?= htmlspecialchars($artikel['catalogusnummer'] ?? '', ENT_QUOTES) ?>"
                                    data-beschrijving="<?= htmlspecialchars($artikel['beschrijving'], ENT_QUOTES) ?>"
                                    data-link="<?= htmlspecialchars($artikel['link_bron'], ENT_QUOTES) ?>"
                                    data-foto1="img/<?= htmlspecialchars($artikel['afbeelding'], ENT_QUOTES) ?>">
                                    <i></i>

                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if ($artikel['id'] == 3): ?>
                            <map name="image-map-3">
                                <area
                                    class="hotspot-area"
                                    shape="poly"
                                    href="#"
                                    coords="249,205,246,229,214,194,199,214,188,192,162,203,155,192,152,202,116,204,105,221,103,363,1012,362,1006,221,975,220,956,184,947,195,907,194,882,162,856,161,835,129,829,151,785,153,772,125,769,152,692,150,692,129,676,112,661,126,666,150,600,151,499,77,494,61,479,60,474,74,384,152,394,175,324,200,338,204,335,221,250,221"

                                    data-id="3"
                                    data-catalogus="<?= htmlspecialchars($artikel['catalogusnummer'] ?? '', ENT_QUOTES) ?>"
                                    data-beschrijving="<?= htmlspecialchars($artikel['beschrijving'] ?? '', ENT_QUOTES) ?>"
                                    data-link="<?= htmlspecialchars($artikel['link_bron'] ?? '', ENT_QUOTES) ?>">
                            </map>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </div>


                <!-- HIER de knoppen -->
                <div class="panorama-controls">
                    <button type="button" class="pano-btn pano-zoom-in" aria-label="Zoom in">+</button>
                    <button type="button" class="pano-btn pano-zoom-out" aria-label="Zoom uit">−</button>
                    <button type="button" class="pano-btn pano-fullscreen" aria-label="Volledig scherm">⤢</button>
                    <button class="panorama-help-btn" id="openIntro">?</button>
                    <button type="button" class="pano-btn pano-waypoints"></button>
                    <button type="button" class="pano-btn pano-play"></button>
                </div>
                <!-- pijltjes -->
                <button class="panorama-arrow panorama-arrow-left" type="button" aria-label="Scroll naar links">
                    ‹
                </button>
                <button class="panorama-arrow panorama-arrow-right" type="button" aria-label="Scroll naar rechts">
                    ›
                </button>

                <!-- panorama mini map -->
                <div class="minimap-bottom" id="minimap-bottom">
                    <div class="minimap-thumbnails" id="minimap-thumbnails">
                        <?php for ($i = 1; $i <= 33; $i++): ?>
                            <img
                                src="img-afgesneden/<?php echo $i; ?>.jpg"
                                alt="Deel <?php echo $i; ?>"
                                class="minimap-thumb"
                                data-index="<?php echo $i; ?>">
                        <?php endfor; ?>

                        <div class="minimap-viewport-indicator" id="minimap-viewport-indicator"></div>
                    </div>
                </div>
                <!-- hotspot info paneel -->
                <div class="panorama-info-panel" id="panoInfo">
                    <button class="info-close" type="button" aria-label="Sluit informatie"></button>
                    <div class="info-content">
                        <div class="info-section">
                            <h2>Catalogus nummer</h2>
                            <p id="infoCatalogus"></p>
                        </div>

                        <div class="info-section">
                            <h3>Beschrijving</h3>
                            <p id="infoDescription"></p>
                        </div>

                        <div class="info-section" id="infoLinkSection">
                            <h3>Meer informatie</h3>
                            <p>
                                <a id="infoLink" href="#" target="_blank" rel="noopener noreferrer">
                                    Bekijken →
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
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
                        <li><a href="https://hetutrechtsarchief.nl/expo">Expo - Hamburgerstraat 28</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/studiezaal-en-depot">Studiezaal - Alexander Numankade 199 - 201</a></li>
                    </ul>

                    <h4 class="footer-subtitle">Onderzoek</h4>
                    <ul class="footer-linklist">
                        <li><a href="https://hetutrechtsarchief.nl/onderzoek/collecties">Archieven doorzoeken</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/beeldmateriaal-bekijken/?mode=gallery&view=horizontal&sort=random%7B1765441716711%7D%20asc">Beeldmateriaal bekijken</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/bouwtekeningen-en-woonomgeving">Bouwtekeningen</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/onderzoek/resultaten/personen-mais?mivast=39&mizig=100&miadt=39&miview=tbl&milang=nl">Personen zoeken</a></li>
                    </ul>
                </div>

                <!-- over ons-->
                <div class="footer-column">
                    <h3 class="footer-title">Over ons</h3>
                    <ul class="footer-linklist">
                        <li><a href="https://hetutrechtsarchief.nl/over-ons/nieuws">Nieuws</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/ontdekken/agenda">Agenda</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/uw-materiaal-in-ons-archief">Uw materiaal in ons archief</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/contact">Contact</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/toegankelijkheid">Toegankelijkheid</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/disclaimer-colofon">Auteursrecht en disclaimer</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/privacyverklaring">Privacyverklaring</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/anbi">ANBI</a></li>
                        <li><a href="https://hetutrechtsarchief.nl/english" class="footer-link-italic">English</a></li>
                    </ul>
                </div>

                <!-- contact en sociaal -->
                <div class="footer-column-contact">
                    <h3 class="footer-title">Contact</h3>
                    <ul class="footer-contact-list">
                        <li>
                            <span class="footer-contact-icon">📞</span>
                            <span>(030) 286 66 11 </span>
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
                            <a href="https://www.facebook.com/HetUtrechtsArchief" aria-label="Facebook">F</a>
                            <a href="https://www.instagram.com/hetutrechtsarchief/" aria-label="Instagram">I</a>
                            <a href="https://www.youtube.com/user/hetutrechtsarchief" aria-label="YouTube">Y</a>
                            <a href="https://hetutrechtsarchief.nl/over-ons/nieuws?format=feed" aria-label="RSS">RSS</a>
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