// ---------- Dark Mode (with persistence) ----------
(function () {
  const STORAGE_KEY = "esraa-theme";
  const body = document.body;
  const saved = localStorage.getItem(STORAGE_KEY);

  // Respect saved preference or system preference
  if (saved === "dark" || (!saved && window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
    body.classList.add("dark");
  }

  const toggle = document.getElementById("darkToggle");
  if (toggle) {
    toggle.addEventListener("click", () => {
      body.classList.toggle("dark");
      localStorage.setItem(STORAGE_KEY, body.classList.contains("dark") ? "dark" : "light");
      const icon = toggle.querySelector("i");
      if (icon) {
        icon.classList.toggle("bi-moon");
        icon.classList.toggle("bi-sun");
      }
    });

    // Set correct icon initially
    const icon = toggle.querySelector("i");
    if (icon) {
      const isDark = body.classList.contains("dark");
      icon.classList.toggle("bi-moon", !isDark);
      icon.classList.toggle("bi-sun", isDark);
    }
  }
})();

// ---------- Load Projects from JSON ----------
(function () {
  const container = document.getElementById("projectsContainer");
  if (!container) return;

  fetch("projects.json", { cache: "no-store" })
    .then((res) => {
      if (!res.ok) throw new Error("Failed to load projects.json");
      return res.json();
    })
    .then((projects) => {
      container.innerHTML = projects
        .map(
          (p) => `
          <div class="col-lg-4 col-md-6">
            <div class="portfolio-wrap">
              <img src="${p.image}" class="img-fluid lazy" alt="${p.title}" loading="lazy" />
              <div class="p-3">
                <h5 class="mb-1">${p.title}</h5>
                <p class="muted mb-2">${p.description}</p>
                <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="${p.link}">
                  <i class="bi bi-github me-1"></i>View Code
                </a>
              </div>
            </div>
          </div>`
        )
        .join("");
    })
    .catch((err) => {
      console.error(err);
      container.innerHTML = `<div class="col-12"><div class="alert alert-warning">Couldn’t load projects right now.</div></div>`;
    });
})();
