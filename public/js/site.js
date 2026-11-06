(function () {
  function closeTopDropdowns(except) {
    document.querySelectorAll("[data-top-dropdown]").forEach(function (dropdown) {
      if (except && dropdown === except) return;
      dropdown.classList.remove("open");
      var menu = dropdown.querySelector(".pos-top-dropdown-menu");
      var twistee = dropdown.querySelector("[data-top-twistee]");
      if (menu) menu.hidden = true;
      if (twistee) twistee.setAttribute("aria-expanded", "false");
    });
  }

  document.querySelectorAll("[data-top-twistee]").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var dropdown = btn.closest("[data-top-dropdown]");
      if (!dropdown) return;
      var willOpen = !dropdown.classList.contains("open");
      closeTopDropdowns(willOpen ? dropdown : null);
      dropdown.classList.toggle("open", willOpen);
      var menu = dropdown.querySelector(".pos-top-dropdown-menu");
      if (menu) menu.hidden = !willOpen;
      btn.setAttribute("aria-expanded", willOpen ? "true" : "false");
    });
  });

  document.addEventListener("click", function () {
    closeTopDropdowns(null);
  });
})();
