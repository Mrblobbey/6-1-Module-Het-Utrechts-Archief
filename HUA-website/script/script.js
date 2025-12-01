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


//img toevoegen

const fileInput = document.getElementById("fileInput");
const preview = document.getElementById("preview");

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


