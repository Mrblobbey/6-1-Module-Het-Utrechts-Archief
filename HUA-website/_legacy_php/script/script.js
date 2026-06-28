// hotspot klik
// document.addEventListener("DOMContentLoaded", function () {
//     const hotspots = document.querySelectorAll(".point-wrapper");

//     hotspots.forEach((hotspot) => {
//         hotspot.addEventListener("click", (e) => {
//             e.stopPropagation();
//             const isOpen = hotspot.classList.contains("point-wrapper-open");

//             hotspots.forEach((h) => h.classList.remove("point-wrapper-open"));

//             if (!isOpen) {
//                 hotspot.classList.add("point-wrapper-open");
//             }
//         });
//     });

//     document.addEventListener("click", () => {
//         hotspots.forEach((h) => h.classList.remove("point-wrapper-open"));
//     });
// });


// hotspot klikken & info

// HOTSPOT → INFOPANEEL
document.addEventListener("DOMContentLoaded", function () {
    const hotspots = document.querySelectorAll(".point-wrapper");
    const panel    = document.getElementById("panoInfo");

    if (!panel || !hotspots.length) return;

    const titleEl       = document.getElementById("infoTitle");
    const descriptionEl = document.getElementById("infoDescription");
    const catalogusEl   = document.getElementById("infoCatalogus");
    const linkSectionEl = document.getElementById("infoLinkSection");
    const linkEl        = document.getElementById("infoLink");
    const closeBtn      = panel.querySelector(".info-close");

    function openPanelForHotspot(hotspot) {
        const catalogus    = hotspot.dataset.catalogus || "";
        const beschrijving = hotspot.dataset.beschrijving || "";
        const link         = hotspot.dataset.link || "";

        if (titleEl)       titleEl.textContent       = catalogus || "Gegevens";
        if (catalogusEl)   catalogusEl.textContent   = catalogus ? `${catalogus}` : "Onbekend";
        if (descriptionEl) descriptionEl.textContent = beschrijving;

        if (linkEl && linkSectionEl) {
            if (link) {
                linkEl.href = link;
                linkSectionEl.style.display = "";
            } else {
                linkSectionEl.style.display = "none";
            }
        }

        panel.classList.add("info-open");
    }

    hotspots.forEach((hotspot) => {
        hotspot.addEventListener("click", (e) => {
            e.stopPropagation();
            openPanelForHotspot(hotspot);
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            panel.classList.remove("info-open");
        });
    }

    document.addEventListener("click", (e) => {
        if (!panel.contains(e.target)) {
            panel.classList.remove("info-open");
        }
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

// img toevoegen
const fileInput = document.getElementById("fileInput");
const preview = document.getElementById("preview");

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
    const okBtn = overlay.querySelector('.intro-ok');

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

// Intro + colofon popup + ?-knop
document.addEventListener("DOMContentLoaded", function () {
    const overlay = document.getElementById("introOverlay");
    const closeBtns = overlay ? overlay.querySelectorAll(".intro-close, .intro-ok") : [];
    const helpBtn = document.getElementById("openIntro");
    const STORAGE_KEY = "panoramaIntroSeen";

    if (!overlay) return;

    // Eerste bezoek: automatisch tonen
    if (!localStorage.getItem(STORAGE_KEY)) {
        overlay.style.display = "flex";
    }

    function hideOverlay() {
        overlay.style.display = "none";
        try {
            localStorage.setItem(STORAGE_KEY, "1");
        } catch (e) {
            // localStorage kan geblokkeerd zijn, negeren
        }
    }

    // X-knop en 'Ik begrijp het' sluiten de overlay
    closeBtns.forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.stopPropagation();
            hideOverlay();
        });
    });

    // Klik buiten de modal sluit ook
    overlay.addEventListener("click", function (e) {
        if (e.target === overlay) {
            hideOverlay();
        }
    });

    // ? knop opent altijd weer de overlay
    if (helpBtn) {
        helpBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            overlay.style.display = "flex";
        });
    }
});

// waypoint knop – streep toggelen
document.addEventListener("DOMContentLoaded", function () {
    const wpBtn = document.querySelector(".pano-waypoints");

    if (!wpBtn) {
        console.warn("Geen .pano-waypoints knop gevonden");
        return;
    }

    wpBtn.addEventListener("click", function (e) {
        e.stopPropagation();
        wpBtn.classList.toggle("is-off");
        console.log("waypoints toggled, heeft is-off:", wpBtn.classList.contains("is-off"));
    });
});

// Minimap with thumbnails
function setupMinimap(viewer) {
    const minimapBottom = document.getElementById("minimap-bottom");
    const minimapThumbnails = document.getElementById("minimap-thumbnails");
    const viewportIndicator = document.getElementById("minimap-viewport-indicator");

    if (!viewer || !minimapBottom || !minimapThumbnails || !viewportIndicator) return;

    const thumbnails = minimapThumbnails.querySelectorAll(".minimap-thumb");
    let isUpdatingFromScroll = false;
    let dragging = false;

    function clamp(v, min, max) {
        return Math.max(min, Math.min(max, v));
    }

    function updateViewportIndicator() {
        const totalWidth = viewer.scrollWidth;
        const viewerWidth = viewer.clientWidth;
        const scrollLeft = viewer.scrollLeft;
        if (totalWidth <= 0) return;

        const minimapWidth = minimapThumbnails.clientWidth;

        // Hoeveel deel van de panorama is zichtbaar?
        const ratioVisible = viewerWidth / totalWidth;
        const visibleWidth = minimapWidth * ratioVisible;

        // Maximaal scrollbare afstand in de hoofd-panorama
        const maxScroll = Math.max(1, totalWidth - viewerWidth);
        const ratioScroll = scrollLeft / maxScroll;

        // De indicator kan niet over de hele minimap, maar alleen tot maxLeft
        const maxLeft = Math.max(0, minimapWidth - visibleWidth);

        // 👉 Belangrijk: gebruik maxLeft i.p.v. minimapWidth
        const left = ratioScroll * maxLeft;

        viewportIndicator.style.width = visibleWidth + "px";
        viewportIndicator.style.left = left + "px";
    }


    viewer.addEventListener("scroll", () => {
        if (!isUpdatingFromScroll) updateViewportIndicator();
    });

    thumbnails.forEach((thumb, i) => {
        thumb.addEventListener("click", () => {
            const wrappers = viewer.querySelectorAll(".panorama-img-wrapper");
            const targetWrapper = wrappers[i];
            if (!targetWrapper) return;

            const img = targetWrapper.querySelector("img");
            const marginLeft = img ? parseFloat(getComputedStyle(img).marginLeft) || 0 : 0;

            const totalWidth = viewer.scrollWidth;
            const viewerWidth = viewer.clientWidth;
            const maxScroll = Math.max(0, totalWidth - viewerWidth);

            let targetScroll = targetWrapper.offsetLeft + marginLeft;
            targetScroll = clamp(targetScroll, 0, maxScroll);

            viewer.scrollTo({
                left: targetScroll,
                behavior: "smooth"
            });
        });
    });

    viewportIndicator.addEventListener("mousedown", (e) => {
        dragging = true;
        viewportIndicator.classList.add("dragging");
        e.preventDefault();
    });

    document.addEventListener("mousemove", (e) => {
        if (!dragging) return;

        const rect = minimapThumbnails.getBoundingClientRect();
        const minimapWidth = rect.width;
        const totalWidth = viewer.scrollWidth;
        const viewerWidth = viewer.clientWidth;
        const maxScroll = Math.max(0, totalWidth - viewerWidth);

        let x = e.clientX - rect.left;
        x = clamp(x, 0, minimapWidth);

        const percent = x / minimapWidth;

        isUpdatingFromScroll = true;
        let targetScroll = percent * totalWidth - viewerWidth / 2;
        targetScroll = clamp(targetScroll, 0, maxScroll);
        viewer.scrollLeft = targetScroll;

        updateViewportIndicator();

        requestAnimationFrame(() => {
            isUpdatingFromScroll = false;
        });
    });

    document.addEventListener("mouseup", () => {
        if (!dragging) return;
        dragging = false;
        viewportIndicator.classList.remove("dragging");
    });

    minimapThumbnails.addEventListener("click", (e) => {
        if (e.target === viewportIndicator || viewportIndicator.contains(e.target)) return;

        const rect = minimapThumbnails.getBoundingClientRect();
        const minimapWidth = rect.width;
        const totalWidth = viewer.scrollWidth;
        const viewerWidth = viewer.clientWidth;
        const maxScroll = Math.max(0, totalWidth - viewerWidth);

        let x = e.clientX - rect.left;
        x = clamp(x, 0, minimapWidth);
        const percent = x / minimapWidth;

        let targetScroll = percent * totalWidth - viewerWidth / 2;
        targetScroll = clamp(targetScroll, 0, maxScroll);

        viewer.scrollTo({
            left: targetScroll,
            behavior: "smooth"
        });
    });

    setTimeout(updateViewportIndicator, 200);
    window.addEventListener("resize", updateViewportIndicator);
}

document.addEventListener("DOMContentLoaded", function () {
    const viewer = document.getElementById("panoramaFotos");
    if (viewer) setupMinimap(viewer);
});


document.addEventListener("DOMContentLoaded", function () {
    const pano    = document.getElementById("panoramaFotos");
    const playBtn = document.querySelector(".pano-play");

    if (!pano || !playBtn) return;

    let isPlaying     = false;
    let lastTimestamp = null;
    const speed       = 150; // pixels per seconds

    function step(timestamp) {
        if (!isPlaying) return;

        if (!lastTimestamp) {
            lastTimestamp = timestamp;
        }

        let deltaSec = (timestamp - lastTimestamp) / 1000;

        //  Fix: delta time limiteren zodat autoplay NIET stopt bij haperingen
        deltaSec = Math.min(deltaSec, 0.05); // max 50ms per frame
        lastTimestamp = timestamp;

        const maxScroll = pano.scrollWidth - pano.clientWidth;

        pano.scrollLeft += speed * deltaSec;

        if (pano.scrollLeft >= maxScroll) {
            pano.scrollLeft = maxScroll;
            isPlaying = false;
            playBtn.classList.remove("is-playing");
            return;
        }

        requestAnimationFrame(step);
    }

    playBtn.addEventListener("click", function (e) {
        e.stopPropagation();

        if (!isPlaying) {
            isPlaying     = true;
            lastTimestamp = null;
            playBtn.classList.add("is-playing");
            requestAnimationFrame(step);
        } else {
            isPlaying = false;
            playBtn.classList.remove("is-playing");
        }
    });

    //  Stop autoplay als gebruiker zelf door panorama scrollt
    pano.addEventListener("mousedown", () => {
        isPlaying = false;
        playBtn.classList.remove("is-playing");
    });

    pano.addEventListener("wheel", () => {
        isPlaying = false;
        playBtn.classList.remove("is-playing");
    });

    //  Stop autoplay bij pijltjes
    const leftArrow  = document.querySelector(".panorama-arrow-left");
    const rightArrow = document.querySelector(".panorama-arrow-right");

    [leftArrow, rightArrow].forEach(btn => {
        if (btn) {
            btn.addEventListener("click", () => {
                isPlaying = false;
                playBtn.classList.remove("is-playing");
            });
        }
    });
});

// werkende polly 

document.addEventListener("DOMContentLoaded", function () {
    const hotspots = document.querySelectorAll(".point-wrapper, .hotspot-area");
    const panel    = document.getElementById("panoInfo");

    if (!panel || !hotspots.length) return;

    const titleEl       = document.getElementById("infoTitle");
    const catalogusEl   = document.getElementById("infoCatalogus");
    const descriptionEl = document.getElementById("infoDescription");
    const linkSectionEl = document.getElementById("infoLinkSection");
    const linkEl        = document.getElementById("infoLink");
    const closeBtn      = panel.querySelector(".info-close");

    function openPanelForHotspot(hotspot) {
        const catalogus    = hotspot.dataset.catalogus || "";
        const beschrijving = hotspot.dataset.beschrijving || "";
        const link         = hotspot.dataset.link || "";

        if (titleEl)     titleEl.textContent     = catalogus ? `Catalogusnummer ${catalogus}` : "Catalogusnummer onbekend";
        if (catalogusEl) catalogusEl.textContent = catalogus;
        if (descriptionEl) descriptionEl.textContent = beschrijving;

        if (linkEl && linkSectionEl) {
            if (link) {
                linkEl.href = link;
                linkSectionEl.style.display = "";
            } else {
                linkSectionEl.style.display = "none";
            }
        }

        panel.classList.add("info-open");
    }

    hotspots.forEach((hotspot) => {
        hotspot.addEventListener("click", (e) => {
            e.preventDefault();  // belangrijk voor <area href="#">
            e.stopPropagation();
            openPanelForHotspot(hotspot);
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            panel.classList.remove("info-open");
        });
    }

    document.addEventListener("click", (e) => {
        if (!panel.contains(e.target)) {
            panel.classList.remove("info-open");
        }
    });
});