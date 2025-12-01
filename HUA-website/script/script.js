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


//hostspot placement
const hotspot = document.getElementById("hotspot");
const img = document.getElementById("img-hotspot");
const inputX = document.getElementById("input-x");
const inputY = document.getElementById("input-y");

let offsetX, offsetY, dragging = false;

hotspot.addEventListener("mousedown", (e) => {
    dragging = true;
    offsetX = e.offsetX;
    offsetY = e.offsetY;
});

document.addEventListener("mouseup", () => dragging = false);

document.addEventListener("mousemove", (e) => {
    if (!dragging) return;

    const rect = img.getBoundingClientRect();
    let x = e.clientX - rect.left - offsetX;
    let y = e.clientY - rect.top - offsetY;

    x = Math.max(0, Math.min(x, rect.width - 20));
    y = Math.max(0, Math.min(y, rect.height - 20));

    hotspot.style.left = x + "px";
    hotspot.style.top = y + "px";

    inputX.value = Math.round(x);
    inputY.value = Math.round(y);
});
