# 1MM Video Showcase — Master Implementation Plan for Agentic IDE AI

## 1. Purpose of this document

This document is the master implementation plan for building the `one-minute-media-video-showcase` WordPress plugin.

The implementing agent must treat this as the primary task roadmap.

The plugin should provide:

```text
- Video Case Study custom post type
- Page-specific video placement fields
- Related-video calculation engine
- Custom Elementor Video Grid widget
- One auto-rendered modal in the footer
- Page video data JSON
- Frontend modal/hash controller
- Frontend card/modal CSS
- Admin validation and migration-safe behavior
```

The implementation must preserve compatibility with the current static site during gradual migration.

Production site:

```text
https://www.1minutemedia.com.au/
```

Testing/staging site:

```text
https://test61.autocomputation.com/
```

---

## 2. Required documentation intake

Before coding, the agent must read the main documentation:

```text
docs/OMMVS-01-current-context.md
docs/OMMVS-02-client-requirements.md
docs/OMMVS-03-master-implementation-plan.md
```

The agent must also inspect the reference folder:

```text
docs/reference/README.md
```

The reference folder contains raw supporting artifacts:

```text
docs/reference/
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

Important rule:

```text
The reference folder is not the final architecture. It exists to document the legacy system, pilot system, and migration constraints.
```

The final plugin must not recreate the old Elementor popup-per-video approach.

---

## 3. Existing scaffold structure

The starting codebase is a WordPress Plugin Boilerplate-style scaffold with an added documentation folder.

Current observed structure:

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

The scaffold should be extended, not replaced.

---

## 4. Target final plugin structure

Add feature-specific classes and templates while keeping the existing scaffold.

Recommended final structure:

```text
one-minute-media-video-showcase/
├── one-minute-media-video-showcase.php
├── README.txt
├── uninstall.php
├── index.php
├── LICENSE.txt
│
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
│
├── includes/
│   ├── class-one-minute-media-video-showcase.php
│   ├── class-one-minute-media-video-showcase-loader.php
│   ├── class-one-minute-media-video-showcase-i18n.php
│   ├── class-one-minute-media-video-showcase-activator.php
│   ├── class-one-minute-media-video-showcase-deactivator.php
│   ├── index.php
│   │
│   ├── class-ommvs-cpt-video.php
│   ├── class-ommvs-fields.php
│   ├── class-ommvs-settings.php
│   ├── class-ommvs-related-videos.php
│   ├── class-ommvs-page-data.php
│   ├── class-ommvs-modal-renderer.php
│   ├── class-ommvs-assets.php
│   └── class-ommvs-elementor.php
│
├── includes/elementor/
│   ├── index.php
│   └── class-ommvs-widget-video-grid.php
│
├── templates/
│   ├── index.php
│   ├── video-card.php
│   └── modal.php
│
├── admin/
│   ├── class-one-minute-media-video-showcase-admin.php
│   ├── css/one-minute-media-video-showcase-admin.css
│   ├── js/one-minute-media-video-showcase-admin.js
│   ├── partials/one-minute-media-video-showcase-admin-display.php
│   └── index.php
│
├── public/
│   ├── class-one-minute-media-video-showcase-public.php
│   ├── css/one-minute-media-video-showcase-public.css
│   ├── js/one-minute-media-video-showcase-public.js
│   ├── partials/one-minute-media-video-showcase-public-display.php
│   └── index.php
│
└── languages/
    └── one-minute-media-video-showcase.pot
```

Use the short prefix `OMMVS` for new feature classes.

---

## 5. Implementation principles

The agent must follow these principles:

```text
1. Do not break existing static Elementor pages during migration.
2. Keep plugin selectors isolated from old Elementor popup selectors.
3. Do not depend on Elementor Pro popups in the new plugin system.
4. Render one custom modal shell in the footer.
5. Render all page video data as JSON on the current page.
6. Keep hash-based URLs working.
7. Load iframes only when the modal opens.
8. Destroy/reset iframes when the modal closes.
9. Preserve scroll position after modal close.
10. Calculate related videos from the current page's featured video list.
11. Enqueue frontend assets only where the Video Grid widget/modal is required.
12. Fail safely when Elementor or ACF is missing.
13. Avoid copying temporary rollout selectors into the final plugin.
```

The following selectors belong to the temporary rollout only and must not be required by the final plugin:

```text
.js-featured-video-source
.js-video-card
.js-popup-related-list
.js-popup-related-card
.js-popup-related-image
.js-popup-related-button
.popup-f
#click-a
#click-b
#click-c
```

---

## 6. Phase -1 — Documentation and reference audit

### Subphase -1.1 — Read main documentation

Tasks:

```text
1. Read docs/OMMVS-01-current-context.md.
2. Read docs/OMMVS-02-client-requirements.md.
3. Read docs/OMMVS-03-master-implementation-plan.md.
4. Summarize the approved architecture before coding.
```

Acceptance criteria:

```text
- Agent understands that the final system is CPT + page placements + Elementor widget + one modal.
- Agent understands that related videos are page-context-specific.
- Agent understands that URL hashes remain client-side and must be handled by JS.
```

---

### Subphase -1.2 — Audit reference folder

Tasks:

```text
1. Read docs/reference/README.md.
2. Inspect docs/reference/inventories/current-video-pages-list.md.
3. Inspect docs/reference/inventories/known-popup-id-hash-map.md.
4. Inspect docs/reference/inventories/migration-notes.md.
5. Inspect docs/reference/legacy/old-global-popup-hash-script.js.
6. Inspect docs/reference/legacy/legacy-popup-hash-map-current.json.
7. Inspect docs/reference/pilot/pilot-test-results.md.
8. Inspect docs/reference/pilot/rollout-class-names.md.
9. Inspect docs/reference/html-samples/*.html only for legacy markup understanding.
```

Acceptance criteria:

```text
- Agent can identify which files are legacy reference files.
- Agent can identify which files are current implementation requirements.
- Agent does not use old static markup as the final plugin output structure.
```

---

### Subphase -1.3 — Produce readiness report

Before coding, the agent must produce a concise readiness report covering:

```text
1. Existing scaffold summary.
2. Key files/classes discovered.
3. Proposed new files/classes to add.
4. Dependencies and assumptions.
5. Risks or unclear points.
6. Recommended first implementation phase.
7. Exact files planned for first modification.
```

Acceptance criteria:

```text
- No code is written before this analysis is complete.
```

---

## 7. Phase 0 — Safety, setup, and codebase preparation

### Subphase 0.1 — Create a working branch

Tasks:

```text
1. Create a new feature branch.
2. Do not implement directly on production.
3. Keep the current scaffold intact.
4. Commit the initial scaffold state before changes.
```

Acceptance criteria:

```text
- Repository has a clean starting commit.
- Feature branch exists.
```

---

### Subphase 0.2 — Add plugin constants

File:

```text
one-minute-media-video-showcase.php
```

Tasks:

Add constants after the existing version constant.

Suggested constants:

```php
define( 'OMMVS_VERSION', '1.0.0' );
define( 'OMMVS_PLUGIN_FILE', __FILE__ );
define( 'OMMVS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'OMMVS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'OMMVS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'OMMVS_TEXT_DOMAIN', 'one-minute-media-video-showcase' );
```

Keep the existing `ONE_MINUTE_MEDIA_VIDEO_SHOWCASE_VERSION` unless refactoring all references at once.

Acceptance criteria:

```text
- Constants exist.
- Existing plugin activation still works.
- No fatal errors.
```

---

### Subphase 0.3 — Create new directories and index files

Tasks:

Create:

```text
includes/elementor/
templates/
```

Add blank `index.php` files to both directories.

Acceptance criteria:

```text
- Direct directory browsing is protected consistently with the existing scaffold.
```

---

## 8. Phase 1 — Register the Video Case Study CPT

### Subphase 1.1 — Create CPT class

Create file:

```text
includes/class-ommvs-cpt-video.php
```

Class:

```php
class OMMVS_CPT_Video {}
```

Responsibilities:

```text
- Register video_case_study CPT.
- Configure admin labels.
- Configure admin menu icon.
- Enable featured image support.
- Enable REST API visibility if useful.
```

Recommended CPT args:

```text
post_type: video_case_study
public: false
show_ui: true
show_in_menu: true
show_in_rest: true
publicly_queryable: false
has_archive: false
supports: title, thumbnail, page-attributes
menu_icon: dashicons-video-alt3
```

Acceptance criteria:

```text
- Admin menu shows Video Case Studies.
- User can add/edit Video Case Study posts.
- No public single/archive pages are exposed by default.
```

---

### Subphase 1.2 — Load CPT class from core plugin class

File:

```text
includes/class-one-minute-media-video-showcase.php
```

Tasks:

```text
1. Require class-ommvs-cpt-video.php in load_dependencies().
2. Instantiate OMMVS_CPT_Video.
3. Register its hooks through the loader.
```

Suggested hook:

```text
init -> register_post_type
```

Acceptance criteria:

```text
- CPT registers on init.
- Plugin still loads without fatal errors.
```

---

### Subphase 1.3 — Update activator/deactivator

Files:

```text
includes/class-one-minute-media-video-showcase-activator.php
includes/class-one-minute-media-video-showcase-deactivator.php
```

Tasks:

On activation:

```text
1. Load CPT class.
2. Register CPT.
3. Flush rewrite rules.
```

On deactivation:

```text
1. Flush rewrite rules.
2. Do not delete content or meta.
```

Acceptance criteria:

```text
- Activation creates/registers CPT without needing a manual permalink save.
- Deactivation does not delete data.
```

---

## 9. Phase 2 — Field system and data model

### Important decision

Recommended approach:

```text
Use ACF Pro for field groups and repeaters.
```

If ACF Pro is not available, the agent must pause and report that custom metaboxes/repeatable fields are required as an alternative implementation path.

---

### Subphase 2.1 — Create fields class

Create file:

```text
includes/class-ommvs-fields.php
```

Class:

```php
class OMMVS_Fields {}
```

Responsibilities:

```text
- Register local ACF field groups if ACF is active.
- Add admin notices if ACF is missing.
- Define field name constants or static methods for consistent meta access.
```

Acceptance criteria:

```text
- Class loads safely if ACF is absent.
- No fatal error when acf_add_local_field_group() is unavailable.
```

---

### Subphase 2.2 — Register Video CPT field group

Fields for `video_case_study`:

```text
Group: Video Showcase Details
Location: Post Type == video_case_study
```

Fields:

```text
Identity
- ommvs_hash_slug: Text, required
- ommvs_is_active: True/False, default true

Card Defaults
- ommvs_card_title: Text, required
- ommvs_card_description: Textarea, required
- ommvs_card_thumbnail: Image, required, return array or ID consistently

Modal Content
- ommvs_modal_title: Textarea or WYSIWYG-lite, required
- ommvs_modal_overview: WYSIWYG or textarea, required
- ommvs_video_provider: Select, required: vimeo, youtube, url
- ommvs_video_id: Text, conditional for vimeo/youtube
- ommvs_video_url: URL, conditional for generic URL

Optional
- ommvs_related_thumbnail: Image, optional
- ommvs_admin_notes: Textarea, optional
```

Hash slug should be stored without the leading `#`.

Acceptance criteria:

```text
- Fields appear on Video Case Study edit screen.
- Fields save correctly.
```

---

### Subphase 2.3 — Register page placement field group

Fields for pages:

```text
Group: 1MM Video Showcase
Location: Post Type == page
```

Fields:

```text
Featured Videos
- ommvs_featured_videos: Repeater, max 6
  - video: Post Object, post_type video_case_study, required
  - card_title_override: Text, optional
  - card_description_override: Textarea, optional
  - thumbnail_override: Image, optional

More Videos
- ommvs_more_videos: Repeater
  - video: Post Object, post_type video_case_study, required
  - card_title_override: Text, optional
  - card_description_override: Textarea, optional
  - thumbnail_override: Image, optional
```

Acceptance criteria:

```text
- Fields appear on page edit screen.
- Editor can add/reorder featured videos.
- Editor can add/reorder more videos.
- Featured videos cannot exceed 6 rows.
```

---

### Subphase 2.4 — Register global settings fields

Create file:

```text
includes/class-ommvs-settings.php
```

Class:

```php
class OMMVS_Settings {}
```

Use an ACF Options Page if ACF Pro supports it.

Settings fields:

```text
Production Overview label
Creative section title
Creative bullet list repeater
CTA button text
CTA button URL
Modal fallback thumbnail optional
```

Defaults:

```text
Production Overview
1 Minute Media Creative
Pre-Production
Screen Design
Live Action Multi-Cam Filming
Motion Graphics
Editing
Get A Quick Quote
/quote-form/
```

Acceptance criteria:

```text
- Admin can update global modal labels and CTA.
- Defaults are used if settings are empty.
```

---

### Subphase 2.5 — Add validation

Use ACF validation hooks where possible.

Validation tasks:

```text
1. Enforce unique hash slug across video_case_study posts.
2. Warn/prevent duplicate videos on the same page across Featured and More Videos.
3. Warn if Featured Videos count is below expected design count.
4. Prevent inactive videos from being selected if practical.
5. Validate required video fields before using data on frontend.
```

Acceptance criteria:

```text
- Duplicate hash slugs are blocked.
- Duplicate page placement is blocked or clearly warned.
```

---

## 10. Phase 3 — Related-video engine

### Subphase 3.1 — Create related videos class

Create file:

```text
includes/class-ommvs-related-videos.php
```

Class:

```php
class OMMVS_Related_Videos {}
```

Core method:

```php
public static function get_related_ids( array $featured_ids, int $current_video_id, int $limit = 3 ): array
```

Rule:

```text
Given featured list A, B, C, D, E, F:
A -> B, C, D
B -> A, C, D
C -> A, B, D
D/E/F -> A, B, C
More-section video -> A, B, C
```

Acceptance criteria:

```text
- Method returns correct results for featured videos.
- Method returns first 3 featured IDs for non-featured videos.
- Method works with fewer than 3 featured videos.
```

---

### Subphase 3.2 — Add unit-like development checks

If no formal test framework exists, create temporary internal test cases or comments during development.

Test cases:

```text
featured = [1,2,3,4,5,6]
current 1 -> [2,3,4]
current 2 -> [1,3,4]
current 3 -> [1,2,4]
current 4 -> [1,2,3]
current 99 -> [1,2,3]
featured = [1,2] current 1 -> [2]
```

Acceptance criteria:

```text
- Related logic is isolated and reliable.
```

---

## 11. Phase 4 — Page data builder

### Subphase 4.1 — Create page data class

Create file:

```text
includes/class-ommvs-page-data.php
```

Class:

```php
class OMMVS_Page_Data {}
```

Responsibilities:

```text
1. Read current page's featured video placements.
2. Read current page's more video placements.
3. Build normalized video data.
4. Build hash map.
5. Build related map.
6. Include global modal settings.
7. Return one array ready for JSON encoding.
```

Acceptance criteria:

```text
- Given a page ID, class returns a complete data array.
- Empty pages return a safe empty structure.
```

---

### Subphase 4.2 — Normalize placement data

For every placement row, determine:

```text
video ID
hash
card title
card description
thumbnail
modal title
modal overview
video provider
video ID or URL
```

Override logic:

```text
If placement card title override exists, use it.
Otherwise use Video CPT default card title.

If placement card description override exists, use it.
Otherwise use Video CPT default card description.

If placement thumbnail override exists, use it.
Otherwise use Video CPT default card thumbnail.
```

Acceptance criteria:

```text
- Page-specific card overrides work.
- Video modal content remains global.
```

---

### Subphase 4.3 — Build hash map

Hash slug should be stored without the `#` in the CPT.

JSON hash map example:

```json
{
  "nick-kyrgios": 22840,
  "fleetpartners": 24544
}
```

Acceptance criteria:

```text
- Direct hash lookup can be performed client-side.
- Hash map includes all videos assigned to the page.
```

---

### Subphase 4.4 — Build related map

For every video assigned to the page, build a related list using the related-video engine.

Example:

```json
{
  "22840": [24544, 24520, 36282],
  "24544": [22840, 24520, 36282]
}
```

Acceptance criteria:

```text
- Every featured video has a related map entry.
- Every more video has a related map entry.
- Related videos come only from the current page's featured list.
```

---

## 12. Phase 5 — Asset system

### Subphase 5.1 — Create assets class

Create file:

```text
includes/class-ommvs-assets.php
```

Class:

```php
class OMMVS_Assets {}
```

Responsibilities:

```text
- Register public CSS.
- Register public JS.
- Enqueue assets only when needed.
- Optionally expose asset handles.
```

Recommended handles:

```text
one-minute-media-video-showcase-public
```

Acceptance criteria:

```text
- Assets are not unnecessarily loaded on pages without the video grid.
- Assets load on pages where the widget is rendered.
```

---

### Subphase 5.2 — Modify existing public enqueue behavior

Current public class enqueues assets globally.

Change behavior:

```text
1. Register assets on wp_enqueue_scripts.
2. Do not enqueue immediately on every page.
3. Enqueue from widget render or modal renderer when the modal is required.
```

Acceptance criteria:

```text
- No frontend JS/CSS loads on unrelated pages.
- Video grid pages load required assets.
```

---

## 13. Phase 6 — Elementor integration

### Subphase 6.1 — Create Elementor integration class

Create file:

```text
includes/class-ommvs-elementor.php
```

Class:

```php
class OMMVS_Elementor {}
```

Responsibilities:

```text
1. Detect Elementor availability.
2. Register custom Elementor category.
3. Register Video Grid widget.
4. Avoid fatal errors if Elementor is inactive.
```

Hooks may include:

```text
elementor/elements/categories_registered
elementor/widgets/register
```

Acceptance criteria:

```text
- Elementor widget appears when Elementor is active.
- Plugin does not fatal if Elementor is inactive.
```

---

### Subphase 6.2 — Create Video Grid widget

Create file:

```text
includes/elementor/class-ommvs-widget-video-grid.php
```

Class:

```php
class OMMVS_Widget_Video_Grid extends \Elementor\Widget_Base {}
```

Widget metadata:

```text
Name: ommvs-video-grid
Title: 1MM Video Grid
Icon: eicon-video-camera
Category: 1 Minute Media
```

Controls:

```text
Content tab:
- Source: Featured Videos / More Videos
- Show description: yes/no
- Empty message optional

Layout tab:
- Desktop columns
- Tablet columns
- Mobile columns

Style tab, phase 1 minimal:
- Card gap
- Border radius
```

Acceptance criteria:

```text
- Widget appears in Elementor.
- Editor can select Featured or More Videos source.
- Widget renders cards from current page placement data.
```

---

### Subphase 6.3 — Render card template

Create file:

```text
templates/video-card.php
```

Expected markup shape:

```html
<a href="#nick-kyrgios" class="ommvs-video-card" data-video-id="22840" data-video-hash="nick-kyrgios">
  <span class="ommvs-video-card__media">
    <img src="..." alt="...">
    <span class="ommvs-video-card__play" aria-hidden="true"></span>
  </span>
  <span class="ommvs-video-card__body">
    <span class="ommvs-video-card__title">...</span>
    <span class="ommvs-video-card__description">...</span>
  </span>
</a>
```

Requirements:

```text
- Escape URLs and attributes.
- Allow HTML only where explicitly intended.
- Use plugin-specific class names only.
```

Acceptance criteria:

```text
- Card design can be styled independently from Elementor static cards.
- Card click can be handled by plugin JS.
```

---

## 14. Phase 7 — Auto-render modal and JSON in footer

### Subphase 7.1 — Create modal renderer class

Create file:

```text
includes/class-ommvs-modal-renderer.php
```

Class:

```php
class OMMVS_Modal_Renderer {}
```

Responsibilities:

```text
1. Track whether modal is required on current page.
2. Store current page ID when required.
3. Enqueue frontend assets.
4. Output JSON and modal HTML once in wp_footer.
```

Methods:

```php
public static function mark_required( int $page_id ): void
public function render(): void
public static function is_required(): bool
```

Acceptance criteria:

```text
- Modal is rendered once even if page has two Video Grid widgets.
- No modal is rendered on pages without the widget.
```

---

### Subphase 7.2 — Create modal template

Create file:

```text
templates/modal.php
```

Modal should include placeholders for:

```text
Close button
Modal title
Production Overview label
Overview text
Creative section title
Creative bullet list
CTA button
Video container
Related video slots/container
```

Suggested root:

```html
<div id="ommvs-video-modal" class="ommvs-modal" aria-hidden="true" role="dialog" aria-modal="true">
```

Acceptance criteria:

```text
- Modal shell renders in footer.
- Modal is hidden by default.
- Modal can be populated entirely by JS.
```

---

### Subphase 7.3 — Output page data JSON

In modal renderer, output:

```html
<script type="application/json" id="ommvs-page-data">
  {...}
</script>
```

Use:

```php
wp_json_encode()
```

Escape safely for inline script context.

Acceptance criteria:

```text
- JSON is valid.
- JSON contains current page data.
- JS can parse it.
```

---

## 15. Phase 8 — Frontend JavaScript modal controller

### Subphase 8.1 — Replace placeholder JS

File:

```text
public/js/one-minute-media-video-showcase-public.js
```

Responsibilities:

```text
1. Parse #ommvs-page-data JSON.
2. Cache modal DOM nodes.
3. Listen for .ommvs-video-card clicks.
4. Open modal by video ID.
5. Open modal by hash on initial page load.
6. Update URL hash on modal open.
7. Clear hash on modal close.
8. Render modal content.
9. Render related cards.
10. Load video iframe only when opened.
11. Destroy iframe on close.
12. Preserve scroll position.
13. Handle related card clicks.
14. Handle Escape key.
15. Handle close button and overlay close.
```

Acceptance criteria:

```text
- Clicking cards opens modal.
- Direct hash opens modal.
- Related videos work.
- Modal close stops video.
```

---

### Subphase 8.2 — Implement hash behavior

Required behavior:

```text
Open modal -> push/replace URL hash.
Close modal -> remove hash without page jump.
Initial page load with hash -> open matching modal.
```

Important:

```text
Use history.pushState or history.replaceState.
Do not cause scroll jumps.
Remember and restore scroll position.
```

Acceptance criteria:

```text
/brand-video-production/#nick-kyrgios opens modal on page load.
Closing the modal clears the hash.
Page does not jump unexpectedly.
```

---

### Subphase 8.3 — Implement video iframe builder

Support providers:

```text
vimeo
youtube
url
```

For Vimeo:

```text
https://player.vimeo.com/video/{videoId}?autoplay=1&playsinline=1&autopause=0&title=0&portrait=0&byline=0
```

For YouTube:

```text
https://www.youtube.com/embed/{videoId}?autoplay=1&rel=0
```

On close:

```text
Remove iframe from DOM or clear container innerHTML.
```

Acceptance criteria:

```text
- Video plays when modal opens.
- Video stops when modal closes.
- Multiple hidden iframes are not created.
```

---

### Subphase 8.4 — Implement related cards in modal

For current video ID:

```text
relatedIds = pageData.relatedMap[currentVideoId]
```

Render up to 3 related cards from `pageData.videos`.

On related card click:

```text
Open selected video in same modal.
Update hash.
Re-render main video and related cards.
```

Acceptance criteria:

```text
- Related cards match server-generated relatedMap.
- Same modal stays open when switching videos.
```

---

### Subphase 8.5 — Accessibility and focus handling

Minimum requirements:

```text
- Modal root has role dialog and aria-modal true.
- aria-hidden is updated.
- Escape key closes modal.
- Focus moves to close button or modal when opened.
- Focus returns reasonably on close.
- Body scroll lock is handled.
```

Acceptance criteria:

```text
- Keyboard user can close modal.
- Modal does not trap page in broken focus state.
```

---

## 16. Phase 9 — Frontend CSS

### Subphase 9.1 — Card grid styling

File:

```text
public/css/one-minute-media-video-showcase-public.css
```

Style:

```text
.ommvs-video-grid
.ommvs-video-card
.ommvs-video-card__media
.ommvs-video-card__play
.ommvs-video-card__body
.ommvs-video-card__title
.ommvs-video-card__description
```

Must approximate current card design:

```text
- Thumbnail on top
- Play icon overlay
- Title below
- Description below
- Responsive grid
```

Acceptance criteria:

```text
- Cards visually match current design closely enough for first migration.
- Grid works on desktop/tablet/mobile.
```

---

### Subphase 9.2 — Modal styling

Style:

```text
#ommvs-video-modal
.ommvs-modal
.ommvs-modal__overlay
.ommvs-modal__dialog
.ommvs-modal__close
.ommvs-modal__content
.ommvs-modal__text
.ommvs-modal__video
.ommvs-modal__related
```

Must approximate current modal design:

```text
- Dark overlay
- Black modal container
- Left text column
- Right video column
- Related cards under video
- Responsive mobile layout
```

Acceptance criteria:

```text
- Modal visually resembles existing Elementor popup.
- Mobile/tablet behavior is acceptable.
```

---

## 17. Phase 10 — Admin UX and validation improvements

### Subphase 10.1 — Improve admin columns

For Video Case Study list table, add columns:

```text
Thumbnail
Hash slug
Active status
Video provider
```

Acceptance criteria:

```text
- Admin can quickly identify videos.
```

---

### Subphase 10.2 — Add admin notices

Warn if dependencies missing:

```text
- Elementor missing: widget unavailable.
- ACF missing: fields unavailable.
```

Acceptance criteria:

```text
- Missing dependency does not fatal the site.
- Admin gets a clear message.
```

---

### Subphase 10.3 — Add data health helper

Optional but useful.

Add an admin page or debug tool listing:

```text
Videos missing required fields
Duplicate hashes
Pages with duplicate video placements
Pages with fewer than expected featured videos
```

Acceptance criteria:

```text
- Migration errors can be detected before frontend testing.
```

---

## 18. Phase 11 — First migration page

### Subphase 11.1 — Prepare video data for /brand-video-production/

Tasks:

```text
1. Create Video Case Study posts for all videos on the page.
2. Set hash slugs to match current hashes.
3. Add card default content.
4. Add modal content.
5. Add video provider/ID.
6. Use docs/reference/inventories/known-popup-id-hash-map.md for legacy hash guidance.
```

Acceptance criteria:

```text
- All videos needed on the page exist as CPT entries.
```

---

### Subphase 11.2 — Configure page placements

For `/brand-video-production/`, populate:

```text
Featured Videos: six videos in correct order
More Videos: remaining video cards in correct order
```

Acceptance criteria:

```text
- Page data builder returns correct featuredIds and moreIds.
```

---

### Subphase 11.3 — Replace static Elementor sections

In Elementor:

```text
1. Keep existing section headings and layout wrappers if needed.
2. Replace first static card grid with 1MM Video Grid widget, Source = Featured Videos.
3. Replace second static card grid with 1MM Video Grid widget, Source = More Videos.
```

Acceptance criteria:

```text
- Frontend displays dynamic cards.
- Old cards are not duplicated.
```

---

### Subphase 11.4 — Test full behavior

Test:

```text
1. Click each featured video.
2. Click each more video.
3. Direct hash URL opens modal.
4. Related videos match the rule.
5. Same-modal related switching works.
6. Close stops video.
7. Close preserves scroll.
8. Mobile layout works.
```

Acceptance criteria:

```text
- /brand-video-production/ is fully plugin-powered and stable on staging.
```

---

## 19. Phase 12 — Second migration page with shared videos

### Subphase 12.1 — Choose validation page

Choose a page where a shared video, such as Nick Kyrgios, appears but the first six featured videos differ.

Purpose:

```text
Validate that the same video shows different related videos depending on current page context.
```

Acceptance criteria:

```text
- Same hash on different pages opens same video with different related context.
```

---

### Subphase 12.2 — Migrate and test

Repeat migration process:

```text
1. Configure page placements.
2. Add Video Grid widgets.
3. Test card clicks.
4. Test direct hash URLs.
5. Test related map.
```

Acceptance criteria:

```text
- Page-context related behavior is proven.
```

---

## 20. Phase 13 — Batch migration

### Subphase 13.1 — Migrate pages in small groups

Suggested order:

```text
1. Brand Video Production already validated.
2. Shared-video validation page.
3. One page with no More Videos section.
4. One page with many More Videos.
5. Five-page batch.
6. Remaining pages.
```

Acceptance criteria:

```text
- No broad rollout before representative edge cases are validated.
```

---

### Subphase 13.2 — Retire old static pieces page by page

After a page is migrated:

```text
1. Remove or hide old static video sections from that page.
2. Do not use old Elementor popups for that page's plugin-rendered videos.
3. Keep old popups available only for unmigrated pages if still needed.
```

Acceptance criteria:

```text
- Migrated pages do not depend on old Elementor popup classes or old inline popup scripts.
```

---

## 21. Phase 14 — Final cleanup after all pages are migrated

### Subphase 14.1 — Disable old related popup scripts

Once all relevant pages use the plugin:

```text
1. Disable the temporary reusable JS rollout snippet.
2. Remove old per-popup inline scripts if popups are no longer used.
3. Remove or archive old Elementor popup templates if safe.
```

Acceptance criteria:

```text
- No duplicate popup/related-video systems remain active.
```

---

### Subphase 14.2 — Retire old global popupHashMap script

The new plugin owns hash routing on migrated pages.

After full migration:

```text
1. Disable old global popupHashMap script.
2. Confirm direct hash URLs still work through plugin.
3. Confirm old Elementor Pro popup hash logic is no longer needed.
```

Acceptance criteria:

```text
- One hash/modal system remains active.
```

---

### Subphase 14.3 — Disable debug mode and optimize

Tasks:

```text
1. Remove console logs or guard them behind debug flag.
2. Set debug to false in production JS.
3. Minify assets if the deployment process supports it.
4. Confirm no PHP notices/warnings.
```

Acceptance criteria:

```text
- Production frontend is clean.
```

---

## 22. Edge cases the implementation must handle

### Same video on multiple pages

Expected:

```text
Same video content, different related videos based on page context.
```

### Same video in Featured on one page and More Videos on another

Expected:

```text
Related videos still come from the current page's Featured Videos list.
```

### Page has no More Videos section

Expected:

```text
Only Featured grid renders. Modal still works.
```

### Page has fewer than six featured videos

Expected:

```text
Frontend should still work. Admin should warn.
```

### Hash is for video assigned to current page

Expected:

```text
Modal opens automatically.
```

### Hash is unknown on current page

Phase 1 expected:

```text
Do not open modal. Leave page as-is or optionally remove hash later.
```

### Video provider data missing

Expected:

```text
Do not render broken iframe. Show safe fallback or skip modal video.
```

---

## 23. Required frontend selectors

Use plugin-specific selectors only.

Recommended selectors:

```text
.ommvs-video-grid
.ommvs-video-card
.ommvs-video-card__media
.ommvs-video-card__play
.ommvs-video-card__title
.ommvs-video-card__description

#ommvs-page-data
#ommvs-video-modal
.ommvs-modal
.ommvs-modal__overlay
.ommvs-modal__dialog
.ommvs-modal__close
.ommvs-modal__title
.ommvs-modal__overview-label
.ommvs-modal__overview
.ommvs-modal__creative-title
.ommvs-modal__creative-list
.ommvs-modal__cta
.ommvs-modal__video-container
.ommvs-modal__related
.ommvs-modal__related-card
```

Do not rely on old selectors such as:

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

Those are only for the temporary static rollout system.

---

## 24. Required security practices

PHP:

```text
- Escape all frontend output.
- Use esc_url for URLs.
- Use esc_attr for attributes.
- Use esc_html for plain text.
- Use wp_kses_post only where intentional HTML is allowed.
- Use wp_json_encode for JSON.
- Do not trust post meta blindly.
```

JavaScript:

```text
- Validate data exists before rendering.
- Do not inject untrusted HTML except fields intentionally allowed as HTML.
- Prefer textContent for plain text.
- Clean iframe container on close.
```

---

## 25. Required compatibility behavior

The new plugin must not break:

```text
- Elementor editor loading
- Elementor frontend rendering
- Old static Elementor pages during migration
- Old global hash script on old pages
- Non-video pages
```

Assets should be loaded only when required.

---

## 26. Final acceptance checklist

The project is complete when:

```text
1. Video Case Study CPT works.
2. ACF/page fields work.
3. Global modal settings work.
4. Elementor Video Grid widget works.
5. Featured grid renders from page data.
6. More Videos grid renders from page data.
7. Modal auto-renders once in footer.
8. JSON data renders correctly.
9. Card click opens modal.
10. Direct hash opens modal.
11. URL hash updates on modal open.
12. URL hash clears on modal close.
13. Related videos are correct per page context.
14. Same video on different pages has different related videos when expected.
15. Video iframe loads only on open.
16. Video iframe stops on close.
17. Scroll position is preserved after close.
18. Mobile/tablet/desktop layouts are acceptable.
19. Old static pages continue working during migration.
20. Old static popup system can be retired after full migration.
21. The final plugin no longer depends on temporary rollout classes.
22. The documentation/reference folder remains useful for migration and future maintenance.
```

