# 1MM Video Showcase — Reference Artifacts

This folder contains raw reference material from the existing static Elementor video/popup system, the successful pilot implementation, and the temporary multi-page rollout work.

These files are provided for context only. They are not the final architecture.

The final production implementation should follow the main plugin architecture described in the primary documentation:

- `docs/OMMVS-01-current-context.md`
- `docs/OMMVS-02-client-requirements.md`
- `docs/OMMVS-03-master-implementation-plan.md`

## Sites

Production site:

```txt
https://www.1minutemedia.com.au/
```

Testing/staging site:

```txt
https://test61.autocomputation.com/
```

## Purpose of this folder

Use this folder to understand the current legacy system and the successful pilot behavior before implementing the new plugin-driven system.

This reference material helps explain:

- How the old static Elementor video-card sections were structured.
- How the old Elementor popup-per-video system worked.
- How URL hashes were mapped to Elementor popup IDs.
- How the old per-popup inline scripts worked.
- How the pilot solved page-specific related videos using client-side JavaScript.
- Which CSS classes were temporarily added during the pilot rollout.
- Which Services and Industries pages currently contain video-card sections.
- Which legacy popup IDs and hashes need to be preserved during migration.

## Important architectural warning

Do not rebuild the final plugin by copying the legacy Elementor popup-per-video architecture.

The old architecture is the source of the scalability problem:

- Video cards were manually created on each Elementor page.
- Each video opened a separate Elementor popup.
- Related videos were manually placed inside each popup.
- Inline scripts existed inside popup HTML widgets.
- A global footer script mapped URL hashes to popup IDs.
- The same video could appear on many pages, but the static popup could not naturally know the current page context.

The new plugin must replace that static model with:

- A reusable Video CPT.
- Page-specific video placement data.
- A custom Elementor video-grid widget.
- One reusable modal rendered once in the footer.
- Page video data rendered as JSON.
- Hash-based modal opening handled by plugin JavaScript.
- Related videos calculated from the current page context.

## Approved final architecture

The approved direction is:

```txt
Video CPT = reusable video/popup content
Page placement data = page-specific featured/more video ordering
Elementor widget = frontend grid rendering inside page layout
Footer modal = one reusable modal shell
Page JSON = all current-page video data
JavaScript = modal opening/closing, hash routing, iframe loading, related card rendering
```

The important business rule is:

```txt
A video is global, but related videos are page-context-specific.
```

Therefore, related videos must not be stored directly on the Video CPT.

Related videos must be calculated from the current page's ordered Featured Videos list.

## Recommended reference folder structure

The intended structure is:

```txt
docs/reference/
├── README.md
├── legacy/
│   ├── old-global-popup-hash-script.js
│   ├── old-inline-popup-script-example.html
│   └── legacy-popup-hash-map-current.json
├── pilot/
│   ├── working-pilot-script-brand-video-production.js
│   ├── reusable-rollout-script-current.js
│   ├── pilot-test-results.md
│   └── rollout-class-names.md
├── html-samples/
│   ├── featured-video-section-sample.html
│   ├── more-samples-video-section-sample.html
│   └── existing-elementor-popup-sample.html
├── screenshots/
│   ├── video-card-sample.png
│   ├── popup-sample.png
│   ├── services-submenu-pages.png
│   └── industries-submenu-pages.png
└── inventories/
    ├── current-video-pages-list.md
    ├── known-popup-id-hash-map.md
    └── migration-notes.md
```

Some files may be added progressively during migration.

## How the agent should use this folder

The agentic IDE AI should use this folder as reference material only.

The agent should:

1. Read the main documentation first.
2. Use this folder to inspect exact legacy examples and pilot artifacts.
3. Preserve important legacy behavior such as URL hashes.
4. Avoid copying old static popup logic into the new plugin.
5. Use the reference files to verify migration requirements.
6. Treat the pilot scripts as proof-of-concept logic, not final production architecture.

## Current temporary rollout system

During the temporary rollout, the site used:

- The old global footer `popupHashMap` script.
- Existing Elementor popup templates.
- Manually added CSS classes on static Elementor sections/cards/popups.
- A new central JavaScript snippet that dynamically replaced related videos based on the current page's first six featured videos.

That temporary system successfully validated the business requirement.

However, it still required large manual work across pages and popups, so it is not the final solution.

## Final plugin implementation goal

The final plugin should remove the need for:

- Manually adding `js-featured-video-source` to Elementor sections.
- Manually adding `js-video-card` to video-card columns.
- Manually adding related-video classes to every popup.
- Maintaining one Elementor popup per video.
- Maintaining inline popup scripts.
- Manually managing related videos inside static popups.
- Maintaining duplicate hash maps in multiple scripts.

The final plugin should allow editors to:

1. Create reusable video case studies in WordPress admin.
2. Assign videos to a page's Featured Videos list.
3. Assign videos to a page's More Videos list when needed.
4. Reorder videos per page.
5. Use a custom Elementor widget to render grids.
6. Let the plugin automatically render one modal and all required JSON data.

## Direct hash URL requirement

The final system must preserve URLs such as:

```txt
/brand-video-production/#nick-kyrgios
```

Important technical note:

```txt
URL hashes are not sent to WordPress/PHP during the initial request.
```

Therefore, JavaScript must read `window.location.hash` after page load and open the correct modal using the page JSON data.

Expected behavior:

```txt
/brand-video-production/#nick-kyrgios
→ opens Nick Kyrgios modal
→ related videos are calculated from Brand Video Production page's Featured Videos list

/design-and-fashion-video-production/#nick-kyrgios
→ opens the same Nick Kyrgios video
→ related videos are calculated from Design & Fashion page's Featured Videos list
```

## Related videos rule

Given a page Featured Videos list:

```txt
A, B, C, D, E, F
```

Expected related videos:

```txt
Open A → B, C, D
Open B → A, C, D
Open C → A, B, D
Open D → A, B, C
Open E → A, B, C
Open F → A, B, C
Open any More Videos item → A, B, C
```

This rule must be enforced by the page-data/related-video logic, not by manually stored related-video fields on each Video CPT.

## Legacy coexistence during migration

During migration, old and new systems may coexist.

Old static pages may continue to use:

- Static Elementor cards.
- Elementor popups.
- Old hash routing.
- Temporary rollout script.

New migrated pages should use:

- Video CPT entries.
- Page placement fields.
- The custom Elementor video-grid widget.
- One auto-rendered modal.
- Plugin-owned JSON and frontend JavaScript.

The new plugin JavaScript should bind only to plugin-specific selectors such as `.ommvs-video-card` and `#ommvs-video-modal`, so it does not interfere with legacy Elementor popup behavior.

## Migration priority

The first migrated page should be:

```txt
/brand-video-production/
```

This page was the successful pilot page and is the safest first target for the new plugin architecture.

After that, migrate a second page where a shared video appears in a different featured-video context, to validate that the same video can show different related videos depending on the page.
