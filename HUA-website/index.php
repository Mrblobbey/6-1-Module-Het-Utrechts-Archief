<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Het Utrechts Archief</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="site-header">
        <div class="header-inner">

            <!-- Zoek-icoon links -->
            <button class="search-toggle" aria-expanded="false" aria-controls="site-search">
                <span class="search_icon" aria-hidden="true">
                    <img src="img/search_icon.png" alt="Zoek icoon" class="search_icon_img">
                </span>
                <span class="search_openen_">Zoeken openen</span>
            </button>

            <!-- Hoofd navigatie -->
            <nav class="main-nav" aria-label="Hoofdmenu">
                <ul class="main-nav__list">

                    <!-- Nav dropdown -->
                    <li class="onderzoek_nav">
                        <button class="main-nav__link" aria-expanded="false">
                            <span class="dropdown_arrow">></span>
                            <span>Onderzoek</span>
                        </button>

                        <ul class="dropdown-menu">
                            <li><a href="#">Collecties</a></li>
                            <li><a href="#">Zoek op onderwerp</a></li>
                            <li><a href="#">Studiezaal</a></li>
                            <li><a href="#">Bouwdossiers</a></li>
                            <li><a href="#">Archiefstuk inzien</a></li>
                            <li><a href="#">Verzoek tot digitaliseren</a></li>
                            <li><a href="#">Helpt u mee?</a></li>
                            <li><a href="#">Open data</a></li>
                            <li><a href="#">Openbaarheid</a></li>
                        </ul>
                    </li>


                    <!-- ONTDEKKEN -->
                    <li class="ontdekken_dropdown">
                        <button class="main-nav__link" aria-expanded="false">
                            <span class="dropdown_arrow">></span>
                            <span>Ontdekken</span>
                        </button>

                        <ul class="dropdown-menu">

                        </ul>
                    </li>

                    <!-- ONDERWIJS -->
                    <li class="onderwijs_dropdown">
                        <button class="main-nav__link" aria-expanded="false">
                            <span class="dropdown_arrow">></span>
                            <span>Onderwijs</span>
                        </button>

                        <ul class="dropdown-menu">

                        </ul>
                    </li>

                    <!-- VAKGENOTEN -->
                    <li class="vakgenoten_dropdown">
                        <button class="main-nav__link" aria-expanded="false">
                            <span class="dropdown_arrow">></span>
                            <span>Vakgenoten</span>
                        </button>

                        <ul class="dropdown-menu">

                        </ul>
                    </li>

                    <!-- OVER ONS -->
                    <li class="overons_dropdown">
                        <button class="main-nav__link" aria-expanded="false">
                            <span class="dropdown_arrow">></span>
                            <span>Over ons</span>
                        </button>

                        <ul class="dropdown-menu">

                        </ul>
                    </li>

                    <!-- CONTACT (geen dropdown) -->
                    <li class="contact_nav">
                        <a href="#" class="main-nav__link main-nav__link--plain">Contact</a>
                    </li>

                    <!-- ENGLISH (schuin) -->
                    <li class="english_nav">
                        <a href="#" class="main-nav__link main-nav__link--italic">English</a>
                    </li>
                </ul>
            </nav>

            <!-- Logo rechts -->
            <a href="#" class="site-logo">
                <!-- vervang src door jouw logo -->
                <img src="img/Logo_ingeklapt.png" alt="Het Utrechts Archief Logo">
            </a>
        </div>

        <!-- Uitklapbare zoekbalk -->
        <div class="search-bar" id="site-search" hidden>
            <button class="search-close" aria-label="Zoeken sluiten">
                ✕
            </button>

            <form action="#" method="get" class="search-bar__form">
                <input type="search" name="q" placeholder="Ik ben op zoek naar…" />
                <button type="submit">Zoeken ›</button>
            </form>
        </div>
    </header>

    <main>

        <div class="panorama">
            <div class="panorama-content">

                <div class="panorama-fotos">

                    <!-- img 1 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/1.jpg" alt="Panorama Image 1"
                            style="height: 500px; z-index: 1; margin-left: 0px; margin-top: 0px;">
                    </div>

                    <!-- img 2 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/2.jpg" alt="Panorama Image 2"
                            style="height: 500px; z-index: 2; margin-left: 0px; margin-top: 0px;">
                    </div>

                    <!-- img 3 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/3.jpg" alt="Panorama Image 3"
                            style="height: 497.5px; z-index: 3; margin-left: -40px; margin-top: -1px;">
                    </div>

                    <!-- img 4 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/4.jpg" alt="Panorama Image 4"
                            style="height: 500px; z-index: 4; margin-left: -43px; margin-top: -5px;">

                        <div class="point-wrapper hotspot-4-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 4-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 5 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/5.jpg" alt="Panorama Image 5"
                            style="height: 506px; z-index: 5; margin-left: -56px; margin-top: -8px;">
                    </div>

                    <!-- img 6 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/6.jpg" alt="Panorama Image 6"
                            style="height: 511px; z-index: 6; margin-left: -60px; margin-top: -12px;">
                    </div>

                    <!-- img 7 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/7.jpg" alt="Panorama Image 7"
                            style="height: 523px; z-index: 8; margin-left: -71px; margin-top: -13px;">
                    </div>

                    <!-- img 8 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/8.jpg" alt="Panorama Image 8"
                            style="height: 502px; z-index: 7; margin-left: -44px; margin-top: -6px;">
                    </div>

                    <!-- img 9 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/9.jpg" alt="Panorama Image 9"
                            style="height: 514px; z-index: 9; margin-left: -37px; margin-top: -12px;">
                    </div>

                    <!-- img 10 + 4 hotspots -->
                    <div class="panorama-img-wrapper">
                        <img src="img/10.jpg" alt="Panorama Image 10"
                            style="height: 511px; z-index: 10; margin-left: -44px; margin-top: -11px;">

                        <div class="point-wrapper hotspot-10-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 10-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>

                        <div class="point-wrapper hotspot-10-2">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 10-2</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>

                        <div class="point-wrapper hotspot-10-3">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 10-3</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>

                        <div class="point-wrapper hotspot-10-4">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 10-4</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 11 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/11.jpg" alt="Panorama Image 11"
                            style="height: 515px; z-index: 11; margin-left: -62px; margin-top: -13px;">

                        <div class="point-wrapper hotspot-11-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 11-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 12 + 2 hotspots -->
                    <div class="panorama-img-wrapper">
                        <img src="img/12.jpg" alt="Panorama Image 12"
                            style="height: 518px; z-index: 12; margin-left: -60px; margin-top: -11px;">

                        <div class="point-wrapper hotspot-12-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 12-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>

                        <div class="point-wrapper hotspot-12-2">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 12-2</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 13 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/13.jpg" alt="Panorama Image 13"
                            style="height: 515.5px; z-index: 13; margin-left: -37px; margin-top: -11px;">

                        <div class="point-wrapper hotspot-13-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 13-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 14 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/14.jpg" alt="Panorama Image 14"
                            style="height: 509px; z-index: 14; margin-left: -45px; margin-top: -6px;">

                        <div class="point-wrapper hotspot-14-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 14-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 15 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/15.jpg" alt="Panorama Image 15"
                            style="height: 506px; z-index: 15; margin-left: -59px; margin-top: -4px;">
                    </div>

                    <!-- img 16 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/16.jpg" alt="Panorama Image 16"
                            style="height: 505px; z-index: 16; margin-left: -54px; margin-top: 1px;">
                    </div>

                    <!-- img 17 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/17.jpg" alt="Panorama Image 17"
                            style="height: 508px; z-index: 17; margin-left: -36px; margin-top: 1px;">
                    </div>

                    <!-- img 18 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/18.jpg" alt="Panorama Image 18"
                            style="height: 515px; z-index: 18; margin-left: -40px; margin-top: 1.5px;">
                    </div>

                    <!-- img 19 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/19.jpg" alt="Panorama Image 19"
                            style="height: 526px; z-index: 19; margin-left: -41px; margin-top: -3px;">
                    </div>

                    <!-- img 20 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/20.jpg" alt="Panorama Image 20"
                            style="height: 534px; z-index: 21; margin-left: -38px; margin-top: -6px;">
                    </div>

                    <!-- img 21 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/21.jpg" alt="Panorama Image 21"
                            style="height: 526px; z-index: 20; margin-left: -30px; margin-top: 7px;">
                    </div>

                    <!-- img 22 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/22.jpg" alt="Panorama Image 22"
                            style="height: 542px; z-index: 22; margin-left: -43px; margin-top: -5px;">
                    </div>

                    <!-- img 23 + 2 hotspots -->
                    <div class="panorama-img-wrapper">
                        <img src="img/23.jpg" alt="Panorama Image 23"
                            style="height: 528px; z-index: 23; margin-left: -40px; margin-top: 2px;">

                        <div class="point-wrapper hotspot-23-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 23-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>

                        <div class="point-wrapper hotspot-23-2">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 23-2</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 24 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/24.jpg" alt="Panorama Image 24"
                            style="height: 506px; z-index: 24; margin-left: -34px; margin-top: 16px;">

                        <div class="point-wrapper hotspot-24-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 24-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 25 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/25.jpg" alt="Panorama Image 25"
                            style="height: 524px; z-index: 25; margin-left: -30px; margin-top: 1px;">
                    </div>

                    <!-- img 26 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/26.jpg" alt="Panorama Image 26"
                            style="height: 510.5px; z-index: 26; margin-left: -35px; margin-top: 12px;">

                        <div class="point-wrapper hotspot-26-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 26-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 27 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/27.jpg" alt="Panorama Image 27"
                            style="height: 527px; z-index: 27; margin-left: -42px; margin-top: 5px;">
                    </div>

                    <!-- img 28 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/28.jpg" alt="Panorama Image 28"
                            style="height: 540px; z-index: 28; margin-left: -48px; margin-top: -4px;">
                    </div>

                    <!-- img 29 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/29.jpg" alt="Panorama Image 29"
                            style="height: 534px; z-index: 29; margin-left: -44px; margin-top: -1px;">
                    </div>

                    <!-- img 30 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/30.jpg" alt="Panorama Image 30"
                            style="height: 531px; z-index: 30; margin-left: -53px; margin-top: 5px;">
                    </div>

                    <!-- img 31 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/31.jpg" alt="Panorama Image 31"
                            style="height: 540px; z-index: 32; margin-left: -47px; margin-top: 1px;">

                        <div class="point-wrapper hotspot-31-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 31-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 32 + 1 hotspot -->
                    <div class="panorama-img-wrapper">
                        <img src="img/32.jpg" alt="Panorama Image 32"
                            style="height: 535px; z-index: 31; margin-left: -48px; margin-top: -4px;">

                        <div class="point-wrapper hotspot-32-1">
                            <i></i>
                            <div class="hotspot-popup">
                                <h4 class="hotspot-title">Hotspot 32-1</h4>
                                <p class="hotspot-text">Tekst voor deze hotspot.</p>
                            </div>
                        </div>
                    </div>

                    <!-- img 33 -->
                    <div class="panorama-img-wrapper">
                        <img src="img/33.jpg" alt="Panorama Image 33"
                            style="height: 539px; z-index: 33; margin-left: -45px; margin-top: -2px;">
                    </div>

                </div>
                <!-- hotspot -->
                <div class="point-wrapper">
                    <i></i>
                    <div class="hotspot-popup">
                        <h4 class="hotspot-title">Titel hotspot</h4>
                        <p class="hotspot-text">Korte uitleg over dit punt.</p>
                    </div>
                </div>
                <div class="panorama-scroll"></div>
            </div>

    </main>

    <footer class="site-footer">
        <div class="footer-inner">

            <div class="footer-columns">

                <!-- kolom: Plan een bezoek + Onderzoek -->
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

                <!-- kolom: Over ons -->
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

                <!-- kolom: Contact + socials -->
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
    <script src="script/script.js"></script>
</body>

</html>