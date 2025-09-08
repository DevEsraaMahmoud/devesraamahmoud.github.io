/* main.js - Esraa Portfolio
 * Dark Mode + Load projects from GitHub + (optional) projects.json merge
 */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", () => {
    initTheme();
    loadProjectsSmart();
  });

  // ---------------- THEME ----------------
  function initTheme() {
    const STORAGE_KEY = "esraa-theme";
    const toggle = document.getElementById("darkToggle");
    const body = document.body;

    try {
      const saved = localStorage.getItem(STORAGE_KEY);
      const prefersDark = window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches;
      if (saved === "dark" || (!saved && prefersDark)) body.classList.add("dark");
    } catch (_) {}

    syncIcon();

    toggle?.addEventListener("click", () => {
      const isDark = document.body.classList.toggle("dark");
      try { localStorage.setItem("esraa-theme", isDark ? "dark" : "light"); } catch (_) {}
      syncIcon();
    });

    function syncIcon() {
      const icon = toggle?.querySelector("i");
      if (!icon) return;
      const isDark = document.body.classList.contains("dark");
      icon.classList.toggle("bi-moon", !isDark);
      icon.classList.toggle("bi-sun", isDark);
    }
  }

  // ---------------- PROJECTS (GitHub + optional projects.json) ----------------
  async function loadProjectsSmart() {
    const container = document.getElementById("projectsContainer");
    if (!container) return;

    const GITHUB_USERNAME = "DevEsraaMahmoud";

    // أي مشاريع مهمة تحبي تضمني ظهورها أولاً (اكتبي أسماء الريبو بالضبط):
    const IMPORTANT_REPOS = [
      "EcommApp",
      "laravel-tenancy",
      "laravel-content-management-APIs",
      "ZRestaurant"
    ];

    try {
      // 1) هات من GitHub (repos API)
      const ghRepos = await fetchGitHubRepos(GITHUB_USERNAME);

      // 2) فلترة واختيار الأهم
      const selected = pickImportantRepos(ghRepos, IMPORTANT_REPOS, { max: 8 });

      // 3) (اختياري) ضُمّ projects.json لو موجود
      const localProjects = await loadLocalProjectsJSON().catch(() => []);
      const merged = mergeProjects(selected, localProjects);

      // 4) ارسم الكروت
      renderProjects(container, merged);
    } catch (err) {
      console.error("[projects] error:", err);
      container.innerHTML = `<div class="col-12"><div class="alert alert-warning">Couldn’t load projects. Check your connection or GitHub username.</div></div>`;
    }
  }

  // ---- helpers ----
  async function fetchGitHubRepos(username) {
    // REST API: list public repos (paginated). نجيب أول صفحتين للكفاية.
    const perPage = 100;
    const urls = [
      `https://api.github.com/users/${encodeURIComponent(username)}/repos?per_page=${perPage}&type=owner&sort=updated`,
      `https://api.github.com/users/${encodeURIComponent(username)}/repos?per_page=${perPage}&page=2&type=owner&sort=updated`
    ];

    const pages = await Promise.all(urls.map(u => fetch(u, { headers: { "Accept": "application/vnd.github+json" } })));
    const bad = pages.find(r => !r.ok);
    if (bad) {
      const txt = await bad.text();
      throw new Error(`GitHub API failed: ${bad.status} ${txt}`);
    }
    const arrays = await Promise.all(pages.map(r => r.json()));
    const repos = arrays.flat().filter(Boolean);

    // تخلّصي من fork/archive
    return repos.filter(r => !r.fork && !r.archived);
  }

  function pickImportantRepos(repos, priorityNames = [], { max = 8 } = {}) {
    const byName = new Map(repos.map(r => [r.name.toLowerCase(), r]));

    // اجمع priority أولاً بالترتيب اللي كاتباه
    const priority = [];
    for (const name of priorityNames) {
      const r = byName.get(String(name).toLowerCase());
      if (r) priority.push(r);
    }

    // بعد كده كمّلي الأفضل شهرة وحداثة (حسب stars ثم pushed_at) ويفضّل PHP/Laravel/Vue
    const others = repos
      .filter(r => !priority.includes(r))
      .sort((a, b) => {
        const scoreA = repoScore(a);
        const scoreB = repoScore(b);
        return scoreB - scoreA;
      });

    const combined = [...priority, ...others].slice(0, max);

    // حوّليها لصيغة الكارت
    return combined.map(toProjectCardData);
  }

  function repoScore(r) {
    // وزن بسيط: نجوم + (حديثness) + بونص لو PHP/Laravel/Vue
    const stars = r.stargazers_count || 0;
    const daysSincePush = (Date.now() - new Date(r.pushed_at).getTime()) / (1000 * 60 * 60 * 24);
    const freshness = Math.max(0, 180 - daysSincePush); // حد أقصى 180 يوم تأثير
    const techBonus = /php|laravel|vue|livewire|filament|inertia/i.test(
      [r.language, ...(r.topics || [])].join(" ")
    ) ? 50 : 0;
    return stars * 3 + freshness + techBonus;
  }

  function toProjectCardData(r) {
    // استخدمي homepage لو موجود كرابط ديمو، وإلا GitHub
    const link = r.homepage && r.homepage.trim() ? r.homepage : r.html_url;
    // وصف لطيف: استخدمي description لو موجود
    const desc = r.description || "";
    // صورة افتراضية (بدليها لو عندك صور حقيقية لكل مشروع)
    const image = guessImageForRepo(r.name);

    return {
      title: r.name,
      description: desc,
      image,
      link
    };
  }

  function guessImageForRepo(name) {
    // تقدير اسم الصورة بنفس اسم الريبو (لو حاطّة صور)، غير كده رجّعي placeholder
    const slug = name.replace(/\s+/g, "-");
    const candidates = [
      `assets/img/projects/${slug}.png`,
      `assets/img/projects/${slug}.jpg`,
      `assets/img/projects/${slug}.webp`
    ];
    // هنرجّع أول احتمال، ولو الصورة مش موجودة هيظهر Broken — يُفضّل تحطي placeholder.png
    return candidates[0] || "assets/img/projects/placeholder.png";
  }

  async function loadLocalProjectsJSON() {
    const res = await fetch(new URL("projects.json", location.href).toString(), { cache: "no-store" });
    if (!res.ok) throw new Error("projects.json not found");
    const ct = res.headers.get("content-type") || "";
    const data = ct.includes("application/json") ? await res.json() : JSON.parse(await res.text());
    if (!Array.isArray(data)) throw new Error("projects.json must be an array");
    return data.map(normalizeLocalProject);
  }

  function normalizeLocalProject(p) {
    return {
      title: String(p.title || "Project"),
      description: String(p.description || ""),
      image: String(p.image || "assets/img/projects/placeholder.png"),
      link: String(p.link || "#")
    };
  }

  function mergeProjects(a, b) {
    // دمج بدون تكرار حسب العنوان (مع الحفاظ على ترتيب a ثم b)
    const seen = new Set();
    const merged = [];
    for (const item of [...a, ...b]) {
      const key = item.title.toLowerCase();
      if (seen.has(key)) continue;
      seen.add(key);
      merged.push(item);
    }
    return merged;
  }

  function renderProjects(container, projects) {
    if (!projects || projects.length === 0) {
      container.innerHTML = `<div class="col-12"><div class="alert alert-info mb-0">No projects yet.</div></div>`;
      return;
    }
    container.innerHTML = projects.map(projectCardHTML).join("");
  }

  function projectCardHTML(p) {
    const title = escapeHTML(p.title);
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
              <i class="bi bi-link-45deg me-1"></i>Open
            </a>
          </div>
        </div>
      </div>`;
  }

  // escaping helpers
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
