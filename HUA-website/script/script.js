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
  const scrollAmount = 300;

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


