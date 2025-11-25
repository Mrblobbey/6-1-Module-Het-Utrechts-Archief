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
                <img src="/HUA/6-1-Module-Het-Utrechts-Archief/HUA-website/img/Logo_ingeklapt.png" alt="Het Utrechts Archief Logo">
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