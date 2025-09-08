/* main.js - Esraa Portfolio
 * Handles: Dark Mode (with persistence) + Load projects.json safely
 */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initTheme();
    initProjects();
  });

  // ---------------- THEME ----------------
  function initTheme() {
    const STORAGE_KEY = "esraa-theme";
    const toggle = document.getElementById("darkToggle");
    const body = document.body;

    // pick initial theme: saved -> system -> light
    try {
      const saved = localStorage.getItem(STORAGE_KEY);
      const systemPrefersDark =
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches;

      if (saved === "dark" || (!saved && systemPrefersDark)) {
        body.classList.add("dark");
      } else {
        body.classList.remove("dark");
      }
    } catch (_) {
      // localStorage might fail in some contexts (privacy mode)
    }

    // set correct icon once
    syncToggleIcon(toggle, body.classList.contains("dark"));

    if (toggle) {
      toggle.addEventListener("click", () => {
        const isDark = body.classList.toggle("dark");
        syncToggleIcon(toggle, isDark);
        try {
          localStorage.setItem("esraa-theme", isDark ? "dark" : "light");
        } catch (_) {}
      });
    }
  }

  function syncToggleIcon(btn, isDark) {
    if (!btn) return;
    const icon = btn.querySelector("i");
    if (!icon) return;
    // ensure icons library classes exist in DOM
    icon.classList.toggle("bi-moon", !isDark);
    icon.classList.toggle("bi-sun", isDark);
  }

  // ---------------- PROJECTS ----------------
  function initProjects() {
    const container = document.getElementById("projectsContainer");
    if (!container) return;

    // Build absolute URL (avoids path issues on nested routes)
    const url = new URL("projects.json", window.location.href);

    // NOTE: fetch from file:// fails; use a local server or deploy.
    if (!("fetch" in window)) {
      showProjectsError(container, "Your browser doesn’t support fetch.");
      return;
    }

    fetch(url.toString(), { cache: "no-store" })
      .then((res) => {
        if (!res.ok) {
          throw new Error(`HTTP ${res.status} while loading projects.json`);
        }
        // Some hosts serve JSON with wrong MIME; still try to parse text->json
        const ct = res.headers.get("content-type") || "";
        return ct.includes("application/json") ? res.json() : res.text().then(JSON.parse);
      })
      .then((projects) => {
        if (!Array.isArray(projects)) {
          throw new Error("projects.json is not an array");
        }
        if (projects.length === 0) {
          showProjectsError(container, "No projects to show yet.");
          return;
        }
        container.innerHTML = projects.map(renderProjectCard).join("");
      })
      .catch((err) => {
        console.error("[projects] load failed:", err);
        showProjectsError(
          container,
          "Couldn’t load projects. Check projects.json path and run from a local server."
        );
      });
  }

  function renderProjectCard(p) {
    const title = escapeHTML(p.title || "Project");
    const desc = escapeHTML(p.description || "");
    const img = escapeAttr(p.image || "assets/img/projects/placeholder.png");
    const link = escapeAttr(p.link || "#");

    return `
      <div class="col-lg-4 col-md-6">
        <div class="portfolio-wrap">
          <img src="${img}" class="img-fluid lazy" alt="${title}" loading="lazy" />
          <div class="p-3">
            <h5 class="mb-1">${title}</h5>
            <p class="muted mb-2">${desc}</p>
            <a class="btn btn-sm btn-outline-primary" target="_blank" rel="noopener" href="${link}">
              <i class="bi bi-github me-1"></i>View Code
            </a>
          </div>
        </div>
      </div>`;
  }

  function showProjectsError(container, msg) {
    container.innerHTML = `
      <div class="col-12">
        <div class="alert alert-warning mb-0">${escapeHTML(msg)}</div>
      </div>`;
  }

  // -------- small helpers to avoid broken HTML on bad JSON content --------
  function escapeHTML(str) {
    return String(str)
      .replace(/&/g, "&amp;")
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
  }
  function escapeAttr(str) {
    return escapeHTML(str).replace(/'/g, "&#39;");
  }
})();
