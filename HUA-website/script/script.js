// dropdown elementen
const dropdowns = document.querySelectorAll(
    ".onderzoek_nav, .ontdekken_dropdown, .onderwijs_dropdown, .vakgenoten_dropdown, .overons_dropdown"
);

if (dropdowns.length > 0) {
    dropdowns.forEach((item) => {
        const button = item.querySelector(".main-nav__link");
        if (!button) return;

        button.addEventListener("click", (event) => {
            event.stopPropagation();

            const isOpen = item.classList.contains("dropdown_open");

            dropdowns.forEach((d) => {
                d.classList.remove("dropdown_open");
                const btn = d.querySelector(".main-nav__link");
                if (btn) {
                    btn.setAttribute("aria-expanded", "false");
                }
            });

            if (!isOpen) {
                item.classList.add("dropdown_open");
                button.setAttribute("aria-expanded", "true");
            }
        });
    });

    document.addEventListener("click", (e) => {
        if (!e.target.closest(".main-nav")) {
            dropdowns.forEach((d) => {
                d.classList.remove("dropdown_open");
                const btn = d.querySelector(".main-nav__link");
                if (btn) {
                    btn.setAttribute("aria-expanded", "false");
                }
            });
        }
    });
}

// zoek elementen
const searchToggle = document.querySelector(".search-toggle");
const searchBar = document.querySelector("#site-search");
const searchClose = document.querySelector(".search-close");

if (searchToggle && searchBar) {
    searchToggle.addEventListener("click", () => {
        const isHidden = searchBar.hasAttribute("hidden");

        if (isHidden) {
            searchBar.removeAttribute("hidden");
            searchToggle.setAttribute("aria-expanded", "true");
        } else {
            searchBar.setAttribute("hidden", "");
            searchToggle.setAttribute("aria-expanded", "false");
        }
    });
}

if (searchClose && searchBar && searchToggle) {
    searchClose.addEventListener("click", () => {
        searchBar.setAttribute("hidden", "");
        searchToggle.setAttribute("aria-expanded", "false");
    });
}
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