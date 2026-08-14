# Web Application Scalability Heuristics

A systematized, bilingual (EN/ES) catalog of design heuristics for building scalable web applications, derived from a frequency analysis across 17 academic and industry sources.

**Live site:** https://scalability-heuristics.projects.gzgroup.dev/

**Research article (PDF):** [docs/heuristicas-escalabilidad-web.pdf](docs/heuristicas-escalabilidad-web.pdf)

## What is this?

This project is the interactive companion to a research article on web application scalability. Instead of leaving the findings buried in a PDF, the goal was to turn them into something practical: a browsable catalog anyone can consult when designing or reviewing the architecture of a web system.

Each heuristic includes:
- A short description and a detailed explanation of the reasoning behind it
- A consensus index showing how frequently it appeared across the reviewed sources (academic papers and industry engineering blogs/docs)
- A real-world example illustrating the heuristic in practice (primary heuristics)

The 36 heuristics are split into:
- **16 primary heuristics** — consensus ≥ 15% across sources
- **20 secondary heuristics** — consensus < 15%, still relevant but less universally cited

## Research paper

This catalog is the applied output of a real research paper, developed following the scientific method (systematic literature review + quantitative frequency analysis), submitted to **JIC-Nacional 2026, X Congreso IESTEC** (Jornada de Iniciación Científica) with SENACYT support.

> **Systematization of scalability heuristics for web applications: a frequency analysis of technical and industrial literature**
> Andrew Acosta, Michael Orocu, Anthony Romero, Kaisy Casasola, Juan Chen — *Universidad Tecnológica de Panamá, sede David, Chiriquí*
> Advisor: Celsa Sánchez — *Facultad de Ingeniería de Sistemas Computacionales, UTP, Centro Regional de Chiriquí*

The study analyzed 17 academic and industry sources (2015–2026) in depth, extracted 145 raw heuristics, normalized them into 74 unique heuristics across 10 design categories, and applied a 15% consensus threshold (≥3 of 17 sources) to arrive at the final catalog. 56.3% of the resulting heuristics are backed by both academic and industry sources.

Full methodology, results tables, and references: [docs/heuristicas-escalabilidad-web.pdf](docs/heuristicas-escalabilidad-web.pdf)

## Why this exists

This started as a research project and became a small open contribution to the community: a free, structured reference that consolidates scattered scalability advice (caching, statelessness, load balancing, database design, resilience patterns, etc.) into one place, ranked by how much consensus each idea actually has in the literature — rather than by opinion alone.

It's also published as part of a professional practice portfolio.

## Features

- **Bilingual, real translations** — English (default) and Spanish, hand-translated (not machine-translated), toggled client-side and persisted in `localStorage`.
- **Fully responsive** — usable from mobile to desktop, with a collapsible sidebar on small screens.
- **No database, no user input** — the catalog is static data rendered server-side in PHP, which keeps the attack surface minimal.
- **Deep linking** — every heuristic has its own URL hash (e.g. `#H01`) so it can be shared or bookmarked directly.
- **Keyboard navigation** — arrow keys move between heuristics, `Esc` returns home.

## Tech stack

- PHP 8.2 (server-rendered, no framework — the site has no database or user input, so a full MVC framework wasn't necessary)
- Vanilla CSS (no build step)
- Vanilla JavaScript (no build step, no dependencies)
- Docker (`php:8.2-apache`) for a consistent runtime in any environment
- Deployed via [Dokploy](https://dokploy.com/) (Traefik + automatic HTTPS/Let's Encrypt)

## Project structure

```
.
├── index.php       # All markup, data (heuristics) and i18n dictionary
├── style.css        # All styling, including responsive breakpoints
├── function.js       # View navigation, language toggle, mobile menu, keyboard nav
├── Dockerfile       # php:8.2-apache image
├── robots.txt       # Crawler rules
└── sitemap.xml       # Sitemap for search engine indexing
```

## Running locally

```bash
docker build -t scalability-heuristics .
docker run -p 8080:80 scalability-heuristics
```

Then open `http://localhost:8080`.

No environment variables, database, or additional setup is required — the app is fully static content served through PHP.

## Deployment

The site is deployed with [Dokploy](https://dokploy.com/), which handles the Docker build and provisions an HTTPS certificate automatically via Traefik/Let's Encrypt. Any Docker-compatible host (Render, Railway, Fly.io, a VPS with Dokploy/Coolify, etc.) works the same way — just point it at this `Dockerfile`.

## Getting indexed by Google

This repo already includes what's needed for indexing:

- **`<meta name="description">` and Open Graph tags** in `index.php`'s `<head>`, so the page shows a proper title/description in search results and when shared on social media.
- **`robots.txt`** — tells crawlers they're allowed to index the site and points them to the sitemap.
- **`sitemap.xml`** — lists the site's URL for search engines.

## License

Feel free to fork, adapt, or reference this catalog for your own projects or research, with attribution.

## Authors

**Research team** — Universidad Tecnológica de Panamá, sede David, Chiriquí
Andrew Acosta, Michael Orocu, Anthony Romero, Kaisy Casasola, Juan Chen

**Academic advisor:** Celsa Sánchez — Facultad de Ingeniería de Sistemas Computacionales, UTP, Centro Regional de Chiriquí

This repository (interactive catalog, i18n, and deployment) was built as the applied companion to the research paper above.
