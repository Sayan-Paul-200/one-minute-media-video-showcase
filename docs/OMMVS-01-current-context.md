# 1MM Video Showcase — Current Website Context and Existing Static System

## 1. Purpose of this document

This document gives the implementing agent a full working context for the current 1 Minute Media video-card and popup system before building the new plugin architecture.

The production website is:

```text
https://www.1minutemedia.com.au/
```

All development and testing discussed so far has been performed on the staging/testing website:

```text
https://test61.autocomputation.com/
```

The new plugin codebase is named:

```text
one-minute-media-video-showcase
```

The final feature should replace the current static Elementor video-card and Elementor-popup workflow with a data-driven system powered by:

- A reusable Video Case Study custom post type.
- Page-specific video ordering.
- A custom Elementor widget for video grids.
- One reusable modal auto-rendered once in the footer.
- Page video data rendered as JSON.
- Hash-based modal opening that preserves the current URL architecture.

This document should be read together with:

```text
docs/OMMVS-02-client-requirements.md
docs/OMMVS-03-master-implementation-plan.md
docs/reference/README.md
```

The `/docs/reference/` folder contains raw artifacts, sample HTML, screenshots, inventories, legacy scripts, and pilot scripts. These reference files are provided for context only. They must not be treated as the final production architecture.

---

## 2. Current documentation structure

The project now contains a documentation folder at the plugin root.

Current documentation structure:

```text
docs/
├── README.md
├── OMMVS-01-current-context.md
├── OMMVS-02-client-requirements.md
├── OMMVS-03-master-implementation-plan.md
└── reference/
    ├── README.md
    ├── html-samples/
    │   ├── existing-elementor-popup-sample.html
    │   ├── featured-video-section-sample.html
    │   └── more-samples-video-section-sample.html
    ├── inventories/
    │   ├── current-video-pages-list.md
    │   ├── known-popup-id-hash-map.md
    │   └── migration-notes.md
    ├── legacy/
    │   ├── legacy-popup-hash-map-current.json
    │   ├── old-global-popup-hash-script.js
    │   └── old-inline-popup-script-example.html
    ├── pilot/
    │   ├── pilot-test-results.md
    │   ├── reusable-rollout-script-current.js
    │   ├── rollout-class-names.md
    │   └── working-pilot-script-brand-video-production.js
    └── screenshots/
        ├── industries-submenu-pages.png
        ├── popup-sample.png
        ├── services-submenu-pages.png
        └── video-card-sample.png
```

The agent must treat the three main markdown files as the canonical implementation documentation and the `/docs/reference/` folder as raw supporting evidence.

Important usage rule:

```text
Main docs = decisions, requirements, implementation direction.
Reference docs = legacy examples, pilot artifacts, migration evidence.
```

The final plugin must not copy the old Elementor popup-per-video architecture. The raw reference files exist only to help the agent understand the current site and preserve critical legacy behavior such as hashes.

---

## 3. Current plugin scaffold context

The plugin scaffold has already been created:

```text
one-minute-media-video-showcase
```

It follows a standard WordPress Plugin Boilerplate-style structure.

Current plugin structure observed in the IDE:

```text
one-minute-media-video-showcase/
├── admin/
│   ├── class-one-minute-media-video-showcase-admin.php
│   ├── css/
│   │   └── one-minute-media-video-showcase-admin.css
│   ├── index.php
│   ├── js/
│   │   └── one-minute-media-video-showcase-admin.js
│   └── partials/
│       └── one-minute-media-video-showcase-admin-display.php
├── docs/
│   ├── README.md
│   ├── OMMVS-01-current-context.md
│   ├── OMMVS-02-client-requirements.md
│   ├── OMMVS-03-master-implementation-plan.md
│   └── reference/
│       ├── README.md
│       ├── html-samples/
│       ├── inventories/
│       ├── legacy/
│       ├── pilot/
│       └── screenshots/
├── includes/
│   ├── class-one-minute-media-video-showcase-activator.php
│   ├── class-one-minute-media-video-showcase-deactivator.php
│   ├── class-one-minute-media-video-showcase-i18n.php
│   ├── class-one-minute-media-video-showcase-loader.php
│   ├── class-one-minute-media-video-showcase.php
│   └── index.php
├── languages/
│   └── one-minute-media-video-showcase.pot
├── public/
│   ├── class-one-minute-media-video-showcase-public.php
│   ├── css/
│   │   └── one-minute-media-video-showcase-public.css
│   ├── index.php
│   ├── js/
│   │   └── one-minute-media-video-showcase-public.js
│   └── partials/
│       └── one-minute-media-video-showcase-public-display.php
├── index.php
├── LICENSE.txt
├── one-minute-media-video-showcase.php
├── README.txt
└── uninstall.php
```

Current plugin header:

```text
Plugin Name: 1 Minute Media Video Showcase
Description: Adds reusable video case studies, Elementor-powered video grids, page-specific related video logic, and a single dynamic video popup system with URL hash support for the 1 Minute Media website.
Text Domain: one-minute-media-video-showcase
```

The scaffold currently has:

- Activation class.
- Deactivation class.
- Loader class.
- i18n class.
- Admin class with placeholder admin CSS/JS enqueues.
- Public class with placeholder public CSS/JS enqueues.
- Documentation and reference assets for the agent.

It does not yet contain the real feature implementation:

- Video Case Study CPT.
- ACF fields or custom metaboxes.
- Page-level video placement fields.
- Global modal settings.
- Elementor widget registration.
- Video Grid widget.
- Page data builder.
- Related-video engine.
- Modal renderer.
- JSON output.
- Frontend modal controller.
- Production CSS for grid/modal.

The scaffold must be extended, not replaced.

---

## 4. Current site navigation context

The pages currently understood to contain video-card sections are mainly the pages listed under the **Services** and **Industries** submenu items.

The submenu screenshots are stored in:

```text
docs/reference/screenshots/services-submenu-pages.png
docs/reference/screenshots/industries-submenu-pages.png
```

The page inventory is documented in:

```text
docs/reference/inventories/current-video-pages-list.md
```

### Services submenu pages currently in scope

The Services menu currently includes pages such as:

```text
Brand Videos
Animated Videos
Training Videos
Explainer Videos
Case Studies & Interviews
TVC & Broadcast
Product Videos
Event Videos
Motion Graphics
Social Media
```

These pages generally contain a first group of featured videos and, in many cases, an additional more-samples section.

### Industries submenu pages currently in scope

The Industries menu currently includes pages such as:

```text
Healthcare & Medical Video Production
Government, NGO & NFP Video Production
Saas and Business Video Production
Finance & Legal Video Production
Recruitment Video Production
Retail Video Production
Food & Beverage Video Production
Design and Fashion Videos
```

These pages also use video-card sections.

Important implementation rule:

```text
Do not hardcode these page slugs or menu labels in the plugin.
```

The page list may change in the future. The plugin should work on any page where an editor/admin configures video placements and inserts the custom Elementor Video Grid widget.

---

## 5. Existing static Elementor setup

The current website is built statically across many Elementor pages.

There are currently approximately:

```text
20+ pages
100+ Elementor popups
```

The current system is highly repetitive and manually maintained.

Each relevant page usually has:

1. A first video section containing six featured video cards.
2. A second optional video section containing more sample video cards.
3. One Elementor popup per video.
4. Static related-video cards inside each Elementor popup.
5. Old inline popup scripts inside some popup HTML widgets.
6. A global footer hash script that maps popup IDs to URL hashes.

This static model is the source of the scalability problem.

---

## 6. Current page video-card structure

Each static video card on the page contains:

```text
A. Thumbnail image or background image
B. Play button overlay
C. Video thumbnail/card title
D. Video thumbnail/card description
E. Elementor popup-open link
```

Example card content:

```text
Thumbnail title:
Tennis Majors With Nick Kyrgios, TV Ad

Thumbnail description:
The King was in the house last Summer! Our International Ads filled the grandstands for the showdown in LAX UTS.
```

Important: the card title and card description are not always the same as the popup title and popup description.

Raw legacy samples are stored in:

```text
docs/reference/html-samples/featured-video-section-sample.html
docs/reference/html-samples/more-samples-video-section-sample.html
```

These samples are useful for migration, but the new plugin should render its own plugin-specific card markup.

---

## 7. Current popup structure

Each video card currently opens a corresponding Elementor Pro popup.

Each popup contains:

```text
A. URL hash attached to the browser location
B. Popup title, different from card title in many cases
C. Popup production overview text, different from card description in many cases
D. Constant heading: Production Overview
E. Constant heading: 1 Minute Media Creative
F. Constant bullet list under 1 Minute Media Creative
G. Constant Get A Quick Quote button
H. Main video iframe, usually Vimeo
I. Three small related video cards below the main video
```

Example popup title:

```text
Tennis Majors
Brand, TV Ad
```

Example card title for the same video:

```text
Tennis Majors With Nick Kyrgios, TV Ad
```

Therefore, the new system must support separate fields for:

```text
Card title
Card description
Modal title
Modal production overview text
```

A raw legacy popup sample is stored in:

```text
docs/reference/html-samples/existing-elementor-popup-sample.html
```

The popup screenshot is stored in:

```text
docs/reference/screenshots/popup-sample.png
```

---

## 8. Current hash-based URL behavior

The current site uses URL hash routing for popups.

Example:

```text
https://www.1minutemedia.com.au/brand-video-production/#nick-kyrgios
```

When the URL is opened directly with the hash, the page should load and the correct video popup should open automatically.

Important technical note:

```text
The URL hash is not sent to WordPress/PHP during the initial HTTP request.
```

This means JavaScript must continue to read `window.location.hash` on page load and open the correct modal.

The new plugin must keep this URL-hash architecture.

---

## 9. Existing global footer hash script

The current static system has an old global footer script added through the Scripts Inserter plugin.

It contains a map similar to:

```js
const popupHashMap = {
  22840: '#nick-kyrgios',
  24544: '#fleetpartners',
  24520: '#the-langham-hotels',
  36282: '#Whiskey&Wealth',
  24541: '#heinemann',
  24538: '#afr'
  // many more popup IDs...
};
```

The script does these things:

```text
1. On page load, reads window.location.hash.
2. If the hash exists in the map, opens the corresponding Elementor Pro popup.
3. On Elementor popup show, updates the URL hash.
4. On Elementor popup hide, clears the hash.
```

The full old global hash script is stored in:

```text
docs/reference/legacy/old-global-popup-hash-script.js
```

A machine-readable legacy popup/hash reference is stored in:

```text
docs/reference/legacy/legacy-popup-hash-map-current.json
```

A human-readable inventory is stored in:

```text
docs/reference/inventories/known-popup-id-hash-map.md
```

During migration, this old hash script should remain active for old static pages. New plugin-powered pages should eventually stop depending on it.

---

## 10. Old per-popup inline scripts

Many Elementor popups contain old inline HTML widget scripts.

Those scripts typically:

```text
1. Close the current popup when a related card is clicked.
2. Attach event listeners to repeated IDs such as click-a, click-b, click-c.
3. Replace one hash with another hardcoded hash.
```

Example behavior:

```text
If current hash is #nick-kyrgios and user clicks related button A,
replace #nick-kyrgios with #professor-justin-yurbery.
```

These old scripts are fragile because:

- They use repeated IDs across popups.
- They rely on `setTimeout` timing.
- They hardcode per-popup relationships.
- They are duplicated across many Elementor popups.
- They are difficult to maintain and easy to break.

An example old inline popup script is stored in:

```text
docs/reference/legacy/old-inline-popup-script-example.html
```

The new plugin should remove the need for these scripts entirely.

---

## 11. Pilot requirement that was solved with JavaScript

The client requested a new related-video rule:

```text
Related videos inside a popup must be based on the current page's first featured video section, not on static popup content.
```

For a page with featured videos:

```text
A, B, C, D, E, F
```

The related-video rule is:

```text
Open A -> B, C, D
Open B -> A, C, D
Open C -> A, B, D
Open D -> A, B, C
Open E -> A, B, C
Open F -> A, B, C
Open any second-section video -> A, B, C
```

This rule is page-context-specific.

The same video may appear on multiple pages, and the related videos must change depending on the page where the video was opened.

---

## 12. What was done on the test pilot page

The successful pilot was performed on:

```text
https://test61.autocomputation.com/brand-video-production/
```

Earlier testing was also discussed for:

```text
https://test61.autocomputation.com/design-and-fashion-video-production/
```

The working pilot used a central JavaScript snippet and classes manually added to Elementor sections, video cards, and popups.

Detailed pilot results are stored in:

```text
docs/reference/pilot/pilot-test-results.md
```

The final working pilot script is stored in:

```text
docs/reference/pilot/working-pilot-script-brand-video-production.js
```

The reusable temporary rollout script is stored in:

```text
docs/reference/pilot/reusable-rollout-script-current.js
```

### Source section classes added to the page

The first six featured-video section received:

```text
js-featured-video-source
```

Each of the six featured video-card columns received:

```text
js-video-card
```

The temporary script extracted:

```text
- Popup ID from the existing Elementor popup-open link.
- Card title from the visible heading.
- Thumbnail from image/background style.
- Hash from a map or fallback.
```

### Popup related-area classes added to Elementor popups

For each popup that needed dynamic related cards, the following classes were added:

```text
Related videos inner section:
js-popup-related-list

Each related-video column:
js-popup-related-card

Each related image widget:
js-popup-related-image

Each related button widget:
js-popup-related-button
```

The full class reference is stored in:

```text
docs/reference/pilot/rollout-class-names.md
```

### Pilot script behavior

The pilot script:

```text
1. Collected the first six featured videos from the current page.
2. Listened for Elementor popup open events.
3. Detected which popup was open.
4. Calculated the correct related videos from the current page's first six featured videos.
5. Replaced the three related-video card images, captions, and links inside the open popup.
6. Intercepted related-card clicks to open the next popup.
7. Preserved scroll position when closing popups.
8. Allowed the old global hash script to continue owning hash routing during the pilot.
```

The pilot worked across both the first featured section and the second more-samples section.

---

## 13. Important pilot debugging discovery

An irregular scroll jump occurred when closing popups.

Root cause:

```text
The old global hash script and the new pilot script were both trying to manage popup show/hide URL hash behavior. Elementor/browser focus restoration after closing a modal then caused the page to scroll toward the triggering element.
```

Fix applied in the pilot:

```text
1. Let the old global footer script continue to own hash routing.
2. Remove duplicate hash show/hide handling from the pilot script.
3. Remember scroll position before popup open.
4. Restore scroll position after popup close.
```

The plugin's future modal controller must include similar scroll preservation logic, but it should eventually own hash routing itself on plugin-powered pages.

---

## 14. Why the static approach is not sustainable

The static Elementor approach requires excessive manual work:

```text
- Add classes to every page's first featured section.
- Add classes to every featured video card.
- Add multiple related-area classes inside many popups.
- Maintain old hash maps manually.
- Maintain related videos manually in 100+ popups.
- Remove or update old per-popup inline scripts.
```

Because the same video can appear on many pages with different page-context related videos, the related-video logic cannot belong to the popup itself.

The correct long-term model is:

```text
Video content is global.
Video placement/order is page-specific.
Related videos are calculated from the current page context.
```

---

## 15. Future target architecture summary

The agreed future architecture is:

```text
1. Keep the current URL hash architecture.
2. Render all page video data at once as JSON.
3. Use one reusable modal/popup structure.
4. Auto-render the modal once in the footer.
5. Build a custom Elementor widget for video grids.
6. Store reusable video content in a CPT.
7. Store page-specific video ordering as page-level placement data.
8. Calculate related videos from the current page's featured video order.
```

This means the future system should not create one Elementor popup per video.

---

## 16. Migration coexistence strategy

During migration, old static pages and new plugin-powered pages must coexist.

Old pages may still use:

```text
- Static Elementor cards
- Elementor Pro popups
- Old global popupHashMap script
- Old per-popup inline scripts
```

New plugin-powered pages should use:

```text
- 1MM Video Grid Elementor widget
- Plugin-generated card markup
- One plugin modal rendered in the footer
- Plugin-generated page JSON
- Plugin modal JavaScript
```

The plugin JavaScript should bind only to plugin-specific selectors such as:

```text
.ommvs-video-card
#ommvs-video-modal
#ommvs-page-data
```

This avoids conflicts with the old static Elementor system during gradual migration.

---

## 17. How the agent should use reference files

The agent should use `/docs/reference/` in this order:

1. Read `docs/reference/README.md` to understand the role of reference artifacts.
2. Inspect `docs/reference/inventories/current-video-pages-list.md` for the current page scope.
3. Inspect `docs/reference/inventories/known-popup-id-hash-map.md` before creating CPT hash data.
4. Inspect `docs/reference/legacy/old-global-popup-hash-script.js` to understand legacy hash behavior.
5. Inspect `docs/reference/html-samples/*.html` only to understand the old static markup.
6. Inspect `docs/reference/pilot/*.js` only to understand the successful proof of concept.
7. Do not copy the old static selectors into the final plugin frontend unless explicitly needed for migration diagnostics.

The final plugin should be independent of legacy selectors such as:

```text
.js-featured-video-source
.js-video-card
.js-popup-related-list
.js-popup-related-card
.popup-f
#click-a
#click-b
#click-c
```

Those selectors belong to the temporary static rollout system, not the final plugin architecture.

