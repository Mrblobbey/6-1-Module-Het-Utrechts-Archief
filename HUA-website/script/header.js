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

const btn = document.getElementById("search-toggle");
const img = document.getElementById("search_icon_img");
const x = document.getElementById("search-close");

btn.addEventListener("click", function () {
    const isHidden = window.getComputedStyle(img).display === "none";

    if (!isHidden) {
        img.style.display = "none";   // vergrootglas verbergen
        x.style.display = "inline";   // kruis tonen
    } else {
        img.style.display = "inline"; // vergrootglas tonen
        x.style.display = "none";     // kruis verbergen
    }
});


