// hotspot klik
document.addEventListener("DOMContentLoaded", function () {
    const hotspots = document.querySelectorAll(".point-wrapper");

    hotspots.forEach((hotspot) => {
        hotspot.addEventListener("click", (e) => {
            e.stopPropagation();
            const isOpen = hotspot.classList.contains("point-wrapper-open");

            hotspots.forEach((h) => h.classList.remove("point-wrapper-open"));

            if (!isOpen) {
                hotspot.classList.add("point-wrapper-open");
            }
        });
    });

    document.addEventListener("click", () => {
        hotspots.forEach((h) => h.classList.remove("point-wrapper-open"));
    });
});
// pijltjes werkend krijgen 
const panoramaFotos = document.querySelector(".panorama-fotos");
const arrowLeft = document.querySelector(".panorama-arrow-left");
const arrowRight = document.querySelector(".panorama-arrow-right");

if (panoramaFotos && arrowLeft && arrowRight) {
  const scrollAmount = 300; // hoeveel px per klik

    arrowLeft.addEventListener("click", function (e) {
        e.stopPropagation();
        panoramaFotos.scrollBy({
            left: -scrollAmount,
            behavior: "smooth"
        });
    });

    arrowRight.addEventListener("click", function (e) {
        e.stopPropagation();
        panoramaFotos.scrollBy({
            left: scrollAmount,
            behavior: "smooth"
        });
    });
}
// mini map
document.addEventListener('DOMContentLoaded', function () {
    const pano = document.getElementById('panoramaFotos');
    const minimap = document.getElementById('panoramaMinimap');
    const viewport = document.getElementById('panoramaMinimapViewport');

    if (!pano || !minimap || !viewport) {
        console.warn('Panorama of minimap elementen niet gevonden.');
        return;
    }

    function updateMinimap() {
        const scrollWidth = pano.scrollWidth;
        const clientWidth = pano.clientWidth;
        const scrollLeft = pano.scrollLeft;

        if (scrollWidth <= 0) return;

        const minimapWidth = minimap.clientWidth;

        const ratioVisible = clientWidth / scrollWidth;
        const viewportWidth = minimapWidth * ratioVisible;

        const ratioLeft = scrollLeft / scrollWidth;
        const viewportLeft = minimapWidth * ratioLeft;

        viewport.style.width = viewportWidth + 'px';
        viewport.style.left = viewportLeft + 'px';
    }

    pano.addEventListener('scroll', updateMinimap);
    window.addEventListener('resize', updateMinimap);

    minimap.addEventListener('click', function (e) {
        const rect = minimap.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const percentage = x / minimap.clientWidth;

        const targetScroll = percentage * pano.scrollWidth - (pano.clientWidth / 2);

        const maxScroll = pano.scrollWidth - pano.clientWidth;
        const newScrollLeft = Math.max(0, Math.min(targetScroll, maxScroll));

        pano.scrollTo({
            left: newScrollLeft,
            behavior: 'smooth'
        });
    });

    updateMinimap();
});


// img toevoegen
const fileInput = document.getElementById("fileInput");
const preview   = document.getElementById("preview");

if (fileInput && preview) {
    fileInput.addEventListener("change", function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();

            reader.addEventListener("load", function () {
                preview.setAttribute("src", this.result);
                preview.style.display = "block";
            });

            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
        }
    });
}

// panorama zoom met dubbelklikken
document.addEventListener('DOMContentLoaded', function () {

    const pano = document.getElementById('panoramaFotos');
    if (!pano) {
        console.warn('panoramaFotos niet gevonden voor zoom');
        return;
    }

    let zoomed = false;
    const zoomFactor = 2;

    pano.addEventListener('dblclick', function (e) {
        e.preventDefault();

        const rect = pano.getBoundingClientRect();

        // klikpositie (percentage)
        const clickX = (e.clientX - rect.left) / rect.width;
        const clickY = (e.clientY - rect.top) / rect.height;

        pano.style.setProperty("--zoom-x", (clickX * 100) + "%");
        pano.style.setProperty("--zoom-y", (clickY * 100) + "%");

        if (!zoomed) {
            pano.classList.add("zoomed");
            pano.style.transform = `scale(${zoomFactor})`;
        } else {
            pano.classList.remove("zoomed");
            pano.style.transform = "scale(1)";
        }

        zoomed = !zoomed;
    });
});

// Introductie-popup panorama + colofon
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('introOverlay');
    if (!overlay) return;

    const closeBtn = overlay.querySelector('.intro-close');
    const okBtn    = overlay.querySelector('.intro-ok');

    // check of gebruiker de uitleg al heeft gezien
    const alreadySeen = localStorage.getItem('panoramaIntroSeen') === '1';

    if (!alreadySeen) {
        overlay.style.display = 'flex';
        overlay.setAttribute('aria-hidden', 'false');
    }

    function hideIntro() {
        overlay.style.display = 'none';
        overlay.setAttribute('aria-hidden', 'true');
        localStorage.setItem('panoramaIntroSeen', '1'); // toon maar één keer per browser
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', hideIntro);
    }

    if (okBtn) {
        okBtn.addEventListener('click', hideIntro);
    }

    // klik naast de modal sluit ook
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            hideIntro();
        }
    });
});

//  Instructie + colofon popup 
document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('introOverlay');
    const closeBtn = overlay ? overlay.querySelector('.intro-close') : null;
    const STORAGE_KEY = 'panoramaIntroSeen';

    if (!overlay) return;

    // Alleen de eerste keer tonen (per browser)
    if (window.localStorage && localStorage.getItem(STORAGE_KEY) === '1') {
        overlay.style.display = 'none';
        return;
    }

    function hideOverlay() {
        overlay.style.display = 'none';
        try {
            localStorage.setItem(STORAGE_KEY, '1');
        } catch (e) {
            // niks; localStorage kan geblokkeerd zijn
        }
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', hideOverlay);
    }

    // Klik buiten de modal sluit ook
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) {
            hideOverlay();
        }
    });
});


//  Zoom in / uit + fullscreen 
document.addEventListener('DOMContentLoaded', function () {
    const pano = document.getElementById('panoramaFotos');
    if (!pano) return;

    const zoomInBtn = document.querySelector('.pano-zoom-in');
    const zoomOutBtn = document.querySelector('.pano-zoom-out');
    const fullscreenBtn = document.querySelector('.pano-fullscreen');

    let currentZoom = 1;
    const minZoom = 0.7;
    const maxZoom = 3;
    const zoomStep = 0.3;

    function applyZoom() {
        pano.style.transform = `scale(${currentZoom})`;
        if (currentZoom === 1) {
            pano.classList.remove('zoomed');
        } else {
            pano.classList.add('zoomed');
        }
    }

    if (zoomInBtn) {
        zoomInBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            currentZoom = Math.min(maxZoom, currentZoom + zoomStep);
            applyZoom();
        });
    }

    if (zoomOutBtn) {
        zoomOutBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            currentZoom = Math.max(1, currentZoom - zoomStep);
            applyZoom();
        });
    }

    // Dubbelklik zoom: inzoomen op klikpunt / terug naar 1x
    pano.addEventListener('dblclick', function (e) {
        e.preventDefault();

        const rect = pano.getBoundingClientRect();
        const clickX = (e.clientX - rect.left) / rect.width;
        const clickY = (e.clientY - rect.top) / rect.height;

        pano.style.setProperty('--zoom-x', (clickX * 100) + '%');
        pano.style.setProperty('--zoom-y', (clickY * 100) + '%');

        if (currentZoom === 1) {
            currentZoom = 2; // inzoomen
        } else {
            currentZoom = 1; // terug naar normaal
        }
        applyZoom();
    });

    // Fullscreen knop
    if (fullscreenBtn) {
        fullscreenBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const container = document.querySelector('.panorama') || pano;

            if (!document.fullscreenElement) {
                if (container.requestFullscreen) {
                    container.requestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });
    }
});
