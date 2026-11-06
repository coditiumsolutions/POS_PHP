(function () {
  var sidebar = document.getElementById("posSidebar");
  var backdrop = document.getElementById("posBackdrop");
  var toggle = document.getElementById("posSidebarToggle");
  var storageKey = "posSidebarCollapsed";

  function setCollapsed(collapsed) {
    if (!sidebar || !toggle) return;
    sidebar.classList.toggle("collapsed", collapsed);
    toggle.setAttribute("aria-label", collapsed ? "Expand sidebar" : "Collapse sidebar");
    toggle.setAttribute("title", collapsed ? "Expand sidebar" : "Collapse sidebar");
    toggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
    var icon = toggle.querySelector("i");
    if (icon) icon.className = collapsed ? "bi bi-chevron-right" : "bi bi-list";
    try { localStorage.setItem(storageKey, collapsed ? "1" : "0"); } catch (e) {}
  }

  if (sidebar && toggle) {
    var saved = false;
    try { saved = localStorage.getItem(storageKey) === "1"; } catch (e) { saved = false; }
    setCollapsed(saved);
    toggle.addEventListener("click", function () {
      setCollapsed(!sidebar.classList.contains("collapsed"));
    });
  }

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

  document.querySelectorAll("[data-side-nav-twistee]").forEach(function (btn) {
    btn.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();
      var group = btn.closest("[data-side-nav-group]");
      if (!group) return;
      var willOpen = !group.classList.contains("open");
      group.classList.toggle("open", willOpen);
      btn.setAttribute("aria-expanded", willOpen ? "true" : "false");
    });
  });

  document.addEventListener("click", function () { closeTopDropdowns(null); });
})();
