# Foggy Dew Farm: Draft Website Prototype

A single-page prototype for Foggy Dew Farm (Whitesville, New York), plus an
Articles section with a small PHP admin area for posting articles.
No build step, no framework, no database.

## Run locally

The articles pages need PHP 8.x (with the standard `json`, `fileinfo` and `mbstring` extensions):

```bash
php -S 127.0.0.1:8000
```

Then open <http://127.0.0.1:8000/> for the site and <http://127.0.0.1:8000/admin/> to
write articles.

## Structure

```
index.php             All page sections (nav, hero, what's new, farm, lavender, crafts, experiences, camping, contact, footer)
                      plus an inline SVG sprite (knot emblem, lavender sprig, activity glyphs)
css/styles.css        Design tokens (colours, type scale, spacing) at the top, then components and sections
js/main.js            Mobile nav, scroll-spy, reveal animations, image fallback, CTA → form topic, prototype form
assets/images/        Placeholder photos (see CREDITS.md)
assets/favicon.svg    Temporary knot favicon
CREDITS.md            Placeholder photo sources and licences

news.php              News page: every published article, newest first
article.php           Single article (?slug=..., or /news/slug on Apache)
.htaccess             Apache: pretty /news/slug URLs, index.html → / redirect, blocks *.json downloads
includes/content.php  Read/write the JSON content store, slugs, image uploads, escaping
includes/partials/    Header, footer, SVG sprite and article card shared by the PHP pages
backend/config.php    Site constants and data paths (optional git-ignored config.local.php)
data/articles.json    Source of truth for articles
data/admin-credentials.json  Created on first visit to /admin/ (git-ignored)
assets/uploads/       Images uploaded through the admin area (scripts can never run here)
tools/build-static.php  Renders the PHP pages to static HTML for the GitHub Pages draft
.github/workflows/pages.yml  Builds and deploys the draft to GitHub Pages on push to main
admin/                Password-protected article editor
```

## Articles and the admin area

- Visit `/admin/`. The first visit asks you to choose a username and password (stored
  hashed in `data/admin-credentials.json`); after that it is a normal login.
- **Articles → + New Article**: title, date, author, category, featured image, excerpt,
  body (blank line between paragraphs) and an optional link to a homepage section.
- **Published** controls visibility: unticked articles are drafts, visible only in `/admin`.
- The homepage's **What's New in Foggy Dew** section shows the three newest published
  articles, newest on the left. Publishing a newer article pushes the oldest one off the row
  automatically (it stays on the News page). The section is hidden if nothing is published.
- Articles are stored in `data/articles.json`. There is no database, so a backup is just a
  copy of `data/` and `assets/uploads/`.
- To reset a forgotten password, delete `data/admin-credentials.json` and visit `/admin/` again.
- The three articles marked "(Example)" are placeholders. Delete them from the admin area.

Security: admin forms are CSRF-protected, failed logins get a growing delay, uploads are
limited to JPEG/PNG/WEBP/GIF under 5MB (checked by content) and saved with random names,
and `data/`, `backend/` and `admin/includes/` deny all web access on Apache.

## Hosting

**Live site (Blacknight, Apache + PHP):** upload the files as they are. Everything works,
including `/admin/`.

**Draft site (GitHub Pages):** GitHub Pages can't run PHP, so on every push to `main` the
GitHub Action in `.github/workflows/pages.yml` runs `tools/build-static.php`, which renders the
homepage, News page and each published article to plain HTML (`news/<slug>.html`) and deploys
that. Drafts, `admin/`, `data/` and the PHP source are not published. One-time setup: repo
**Settings → Pages → Build and deployment → Source: GitHub Actions**.

`/admin/` can't run on GitHub Pages, so for the draft site:

1. Run `php -S 127.0.0.1:8000` and write or edit articles at <http://127.0.0.1:8000/admin/>.
2. Commit `data/articles.json` and any new images in `assets/uploads/`, and push to `main`.
3. The Action rebuilds the draft site within a minute or two.

To preview the static version locally: `php tools/build-static.php` (writes to `_site/`,
git-ignored), then `python3 -m http.server 8001 --directory _site`. The web server needs write access to `data/` and `assets/uploads/`.

## Replacing photos

Every image is referenced by a descriptive filename (`hero.jpg`, `drum.jpg`, `stream.jpg`, …).
To swap in real farm photography, overwrite the file in `assets/images/` with the same name,
or update the `src`/`alt` in `index.php`. If an image fails to load, its frame shows a labelled
placeholder instead of a broken-image icon.

Unused alternates kept for convenience: `lavender-farm.jpg`, `basket-fair.jpg`, `camp-forest.jpg`.

## Placeholders still to fill

Text shown in dashed `[square brackets]` is placeholder content: address, email, phone, visiting
hours, willow varieties/pricing, basket styles, workshop dates/pricing, and the wider lavender range.

## Known limitations

- The enquiry form validates input but **does not send anything**. It needs a form service or backend.
- Fonts load from Google Fonts. Offline, the page falls back to system serif and sans-serif fonts.
- Placeholder photos include NonCommercial/NoDerivatives licences. Replace them before going public.
- `<meta name="robots" content="noindex">` is set deliberately for the draft (on the articles pages too).
- `php -S` ignores `.htaccess`, so locally the pretty `/news/slug` URLs and the JSON/data
  access rules don't apply. The site's own `article.php?slug=...` links work either way.
