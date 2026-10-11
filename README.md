# Foggy Dew Farm: Draft Website Prototype

A static, single-page prototype for Foggy Dew Farm (Whitesville, New York).
No build step, no framework, no backend.

## Run locally

```bash
python3 -m http.server 8000 --bind 127.0.0.1
```

Then open <http://127.0.0.1:8000/>.

## Structure

```
index.html            All page sections (nav, hero, farm, lavender, crafts, experiences, camping, contact, footer)
                      plus an inline SVG sprite (knot emblem, lavender sprig, activity glyphs)
css/styles.css        Design tokens (colours, type scale, spacing) at the top, then components and sections
js/main.js            Mobile nav, scroll-spy, reveal animations, image fallback, CTA → form topic, prototype form
assets/images/        Placeholder photos (see CREDITS.md)
assets/favicon.svg    Temporary knot favicon
CREDITS.md            Placeholder photo sources and licences
```

## Replacing photos

Every image is referenced by a descriptive filename (`hero.jpg`, `drum.jpg`, `stream.jpg`, …).
To swap in real farm photography, overwrite the file in `assets/images/` with the same name,
or update the `src`/`alt` in `index.html`. If an image fails to load, its frame shows a labelled
placeholder instead of a broken-image icon.

Unused alternates kept for convenience: `lavender-farm.jpg`, `basket-fair.jpg`, `camp-forest.jpg`.

## Placeholders still to fill

Text shown in dashed `[square brackets]` is placeholder content: address, email, phone, visiting
hours, willow varieties/pricing, basket styles, workshop dates/pricing, and the wider lavender range.

## Known limitations

- The enquiry form validates input but **does not send anything**. It needs a form service or backend.
- Fonts load from Google Fonts. Offline, the page falls back to system serif and sans-serif fonts.
- Placeholder photos include NonCommercial/NoDerivatives licences. Replace them before going public.
- `<meta name="robots" content="noindex">` is set deliberately for the draft.
