# 1MM Video Showcase — Client Requirements Specification

## 1. Purpose of this document

This document defines the complete client requirement for the new 1 Minute Media dynamic video showcase system.

The implementation target is a custom WordPress plugin:

```text
Plugin Title: 1 Minute Media Video Showcase
Plugin Slug: one-minute-media-video-showcase
Text Domain: one-minute-media-video-showcase
```

The plugin will power video showcase grids, modal/popup behavior, hash URL support, and related-video logic for the 1 Minute Media website.

Production site:

```text
https://www.1minutemedia.com.au/
```

Testing/staging site:

```text
https://test61.autocomputation.com/
```

This document should be read together with:

```text
docs/OMMVS-01-current-context.md
docs/OMMVS-03-master-implementation-plan.md
docs/reference/README.md
```

The reference files under `/docs/reference/` contain legacy examples, pilot scripts, sample HTML, screenshots, and migration inventories. They support this requirement document but do not override the approved architecture.

---

## 2. High-level business requirement

The client needs a maintainable, dynamic video showcase system that replaces the current static Elementor implementation.

The current static system requires manual editing of many Elementor pages and 100+ Elementor popups. It is not scalable because:

```text
- A video may appear on multiple pages.
- A video's popup content is globally reusable.
- Related videos must change depending on the current page.
- Static Elementor popups cannot naturally know the page context.
- Manually editing related cards inside every popup is slow and error-prone.
```

The new system must allow editors/admins to:

```text
1. Create reusable video case studies once.
2. Assign videos to different pages in custom orders.
3. Render video grids through Elementor.
4. Open videos in a single reusable modal.
5. Show related videos based on the current page context.
6. Support direct URL hash links such as /brand-video-production/#nick-kyrgios.
7. Preserve legacy hashes where possible during migration.
8. Migrate page by page without breaking existing static pages.
```

---

## 3. Core architectural decisions already approved

The following decisions are approved and must guide the implementation:

```text
1. Keep the current URL hash architecture.
2. Render all page video data at once as JSON.
3. Use one reusable modal/popup structure.
4. Auto-render the modal once in the footer.
5. Build a custom Elementor widget.
6. Use a custom plugin, not WPCode snippets, for production.
7. Support gradual migration with old and new systems coexisting.
```

The chosen modal architecture is:

```text
Option A — Auto-render modal once in footer.
```

The custom Elementor widget architecture is:

```text
Elementor controls layout.
The plugin controls video data, grid rendering, modal behavior, and related-video logic.
```

---

## 4. Documentation and reference requirements

The implementation agent must study the documentation before coding.

Canonical planning documents:

```text
docs/OMMVS-01-current-context.md
docs/OMMVS-02-client-requirements.md
docs/OMMVS-03-master-implementation-plan.md
```

Reference artifacts:

```text
docs/reference/README.md
docs/reference/legacy/old-global-popup-hash-script.js
docs/reference/legacy/legacy-popup-hash-map-current.json
docs/reference/legacy/old-inline-popup-script-example.html
docs/reference/pilot/working-pilot-script-brand-video-production.js
docs/reference/pilot/reusable-rollout-script-current.js
docs/reference/pilot/pilot-test-results.md
docs/reference/pilot/rollout-class-names.md
docs/reference/html-samples/featured-video-section-sample.html
docs/reference/html-samples/more-samples-video-section-sample.html
docs/reference/html-samples/existing-elementor-popup-sample.html
docs/reference/screenshots/video-card-sample.png
docs/reference/screenshots/popup-sample.png
docs/reference/screenshots/services-submenu-pages.png
docs/reference/screenshots/industries-submenu-pages.png
docs/reference/inventories/current-video-pages-list.md
docs/reference/inventories/known-popup-id-hash-map.md
docs/reference/inventories/migration-notes.md
```

Important rule:

```text
Reference artifacts explain the legacy and pilot systems. They are not the final plugin design.
```

The final plugin must not require manual addition of pilot classes such as:

```text
js-featured-video-source
js-video-card
js-popup-related-list
js-popup-related-card
js-popup-related-image
js-popup-related-button
```

Those are temporary rollout artifacts only.

---

## 5. Current page pattern that must be supported

Most relevant pages have one or both of the following video sections.

### Section 1: Featured videos

The first section contains six featured video cards.

This first six-video list is the source of the related-video logic.

The editor/admin must be able to choose the order of these six videos per page.

### Section 2: More videos / more samples

Many pages also contain a second section with additional video cards.

This section may be absent on some pages.

Videos in this section can open modals, but related videos should still come from the current page's first featured-video list.

---

## 6. Pages currently in scope

The pages in scope are mainly those listed under the Services and Industries menus.

The implementation must support the current pages and future pages without hardcoded page slugs.

The current page inventory is maintained in:

```text
docs/reference/inventories/current-video-pages-list.md
```

### Services pages currently in scope

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

### Industries pages currently in scope

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

Future pages may be added or removed. The plugin must not require code changes for every new page.

---

## 7. Video card content requirements

Each frontend video card must support:

```text
A. Thumbnail image
B. Play icon overlay
C. Card title
D. Card description
E. Click action that opens the video modal
F. Hash URL for the video
```

Example card title:

```text
Tennis Majors With Nick Kyrgios, TV Ad
```

Example card description:

```text
The King was in the house last Summer! Our International Ads filled the grandstands for the showdown in LAX UTS.
```

The card title and card description must be editable independently from the modal/popup title and modal overview text.

The plugin should support page-level card overrides:

```text
If a placement override exists, use it.
Otherwise use the Video CPT default card content.
```

---

## 8. Modal/popup content requirements

Each video modal must support:

```text
A. URL hash attached to the browser location
B. Modal title
C. Constant label: Production Overview
D. Modal overview/description text
E. Constant label: 1 Minute Media Creative
F. Constant creative bullet list
G. Constant Get A Quick Quote button
H. Main video player
I. Three related video cards
```

Example modal title:

```text
Tennis Majors
Brand, TV Ad
```

Example modal overview:

```text
Tennis Majors approached us for a series of branded TV Ads, to bring in the crowds for the Summer Season...
```

The following should be globally configurable rather than repeated per video:

```text
Production Overview label
1 Minute Media Creative label
Creative bullet list
CTA button text
CTA button URL
```

The modal should visually approximate the existing Elementor popup:

```text
- Dark overlay
- Black modal panel
- Left text column
- Right main video column
- Three related cards below the main video
- Responsive mobile/tablet layout
```

---

## 9. Reusable video content requirement

A video can appear on multiple pages.

Example:

```text
The Nick Kyrgios video can appear in the first featured section on several pages, and it may also appear in the second more-videos section on another page.
```

The popup/modal content for the video should be maintained once globally.

However, the related videos shown inside the modal must change depending on the page where the modal is opened.

Therefore:

```text
Video content is global.
Video placement/order is page-specific.
Related videos are page-context-specific.
```

---

## 10. Page-specific ordering requirement

Each page must allow the admin/editor to define:

```text
Featured Videos
- Ordered list
- Usually exactly 6 videos
- Used as the source for related-video calculation

More Videos
- Optional ordered list
- Unlimited or configurable count
- Does not define related-video source
```

The same video can be assigned to multiple pages with different position/order.

The system must prevent or warn against duplicate videos on the same page.

---

## 11. Related videos requirement

Related videos must always come from the current page's featured video list.

Given a page featured list:

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
Open any More Videos item -> A, B, C
```

The related videos must be recalculated per page context.

This means the related videos cannot be stored as static fields on the Video CPT.

---

## 12. Direct hash URL requirement

The existing URL hash behavior must be preserved.

Example:

```text
https://www.1minutemedia.com.au/brand-video-production/#nick-kyrgios
```

Expected behavior:

```text
1. Page loads.
2. JavaScript reads window.location.hash.
3. The modal opens automatically for the matching video.
4. Related videos are calculated from that current page's featured videos.
```

The same video hash may be opened on different pages.

Example:

```text
/brand-video-production/#nick-kyrgios
/design-and-fashion-video-production/#nick-kyrgios
```

Both should open the Nick Kyrgios modal, but related videos should come from each page's own featured-video list.

Important technical constraint:

```text
URL hashes are not sent to PHP/WordPress. JavaScript must read the hash after page load.
```

Legacy hashes should be preserved where possible. The known hash inventory is maintained in:

```text
docs/reference/inventories/known-popup-id-hash-map.md
```

---

## 13. Modal behavior requirements

The new modal must:

```text
1. Open when a plugin-rendered video card is clicked.
2. Open when a matching direct URL hash is present on load.
3. Update the URL hash when a modal opens.
4. Clear the URL hash when a modal closes.
5. Preserve/restore scroll position when closing.
6. Load the video iframe only when the modal opens.
7. Destroy or reset the video iframe when the modal closes so video playback stops.
8. Render three related video cards dynamically.
9. Support related-card clicks within the modal.
10. Support Escape key close.
11. Support close button click.
12. Support overlay click close if enabled.
13. Be accessible enough for production use.
14. Avoid conflicts with Elementor Pro popups on unmigrated pages.
```

---

## 14. Elementor integration requirements

The client/site team should still be able to use Elementor for page layout.

Elementor should control:

```text
- Page sections
- Headings
- Spacing
- Layout around the video grid
- Other non-video page content
```

The plugin should provide a custom Elementor widget:

```text
1MM Video Grid
```

The widget should allow the editor to render:

```text
Featured Videos
More Videos
```

The widget should read page-level video assignments and render the correct cards.

The editor should not need to manually add a separate modal widget. The plugin should auto-render the modal once in the footer when the page uses the video grid.

---

## 15. Auto-render footer modal requirement

The chosen architecture is Option A:

```text
Auto-render modal once in footer.
```

This means:

```text
1. If a page contains the plugin video grid, the plugin marks the modal as required.
2. In wp_footer, the plugin outputs one modal shell.
3. In wp_footer, the plugin also outputs the current page video JSON.
4. The plugin enqueues required JS and CSS only when needed.
```

The editor should not need to remember to add a separate modal widget.

---

## 16. JSON data requirement

The plugin should render all video data needed by the current page as JSON.

The JSON should include:

```text
Page ID
Featured video IDs
More video IDs
Hash map
Video data map
Related map
Global modal settings
```

Example shape:

```json
{
  "pageId": 123,
  "featuredIds": [22840, 24544, 24520, 36282, 24541, 24538],
  "moreIds": [24514, 24523, 24495],
  "hashMap": {
    "nick-kyrgios": 22840,
    "fleetpartners": 24544
  },
  "videos": {
    "22840": {
      "id": 22840,
      "hash": "nick-kyrgios",
      "cardTitle": "Tennis Majors With Nick Kyrgios, TV Ad",
      "cardDescription": "...",
      "thumbnail": "https://...",
      "modalTitle": "Tennis Majors<br>Brand, TV Ad",
      "overview": "...",
      "videoProvider": "vimeo",
      "videoId": "879662317"
    }
  },
  "relatedMap": {
    "22840": [24544, 24520, 36282]
  },
  "settings": {
    "productionOverviewLabel": "Production Overview",
    "creativeTitle": "1 Minute Media Creative",
    "creativeBullets": ["Pre-Production", "Screen Design", "Editing"],
    "ctaText": "Get A Quick Quote",
    "ctaUrl": "https://..."
  }
}
```

---

## 17. Admin data requirements

The plugin should create a Video Case Study CPT.

Suggested CPT slug:

```text
video_case_study
```

Each video post should store:

```text
Identity
- Hash slug
- Active/inactive flag

Card defaults
- Default card thumbnail
- Default card title
- Default card description

Modal content
- Modal title
- Production overview text
- Video provider
- Video URL or provider-specific ID

Optional
- Related thumbnail override
- Admin notes
- Categories/tags for future filtering
```

Each relevant page should store:

```text
Featured Videos
- Max 6 rows
- Ordered manually
- Each row references a Video Case Study
- Optional card title override
- Optional card description override
- Optional thumbnail override

More Videos
- Optional rows
- Ordered manually
- Each row references a Video Case Study
- Optional card title override
- Optional card description override
- Optional thumbnail override
```

The preferred field implementation is ACF Pro local field groups because repeaters and page-level post-object fields are required.

If ACF Pro is not available, implementation must pause and report that custom metabox/repeater implementation is required.

---

## 18. Validation requirements

The admin should prevent or warn about bad data.

### Video CPT validation

Required fields:

```text
Hash slug
Default card title
Default thumbnail
Modal title
Production overview text
Video provider and video ID/URL
```

Hash slugs must be unique.

### Page placement validation

The plugin should prevent or warn when:

```text
A page has duplicate videos in Featured and More Videos.
Featured Videos exceeds 6 items.
A selected video is inactive.
A selected video has missing critical fields.
```

Preferred requirement for Featured Videos:

```text
Exactly 6 videos where the design expects 6.
```

The frontend should still degrade gracefully if fewer than 6 are present.

---

## 19. Migration requirements

The new system must be migrated gradually.

During migration, the old static system and the new plugin system must coexist.

Old pages may continue using:

```text
Static Elementor video cards
Elementor Pro popups
Old global popupHashMap script
Old per-popup scripts
Temporary rollout script if still active on specific pages
```

New migrated pages should use:

```text
Plugin Video CPT data
Page-level video assignments
1MM Video Grid Elementor widget
Plugin auto-rendered modal
Plugin frontend JS
```

The first migrated page should be:

```text
/brand-video-production/
```

because it was already used successfully for the pilot.

The second migrated page should be a page where at least one shared video appears in a different featured order, to validate page-context related-video behavior.

Migration notes are maintained in:

```text
docs/reference/inventories/migration-notes.md
```

---

## 20. Non-goals for the first production phase

Do not build unnecessary complexity in phase 1.

Not required initially:

```text
AJAX fallback for hashes not present on the current page
Public single pages for video case studies
Video archive page
Advanced filtering by category
Analytics dashboard
Bulk migration UI
Full Gutenberg block version
Complex Elementor style controls
Automatic migration of 100+ old Elementor popups
Deleting old Elementor popups during first build
```

These can be considered later.

---

## 21. Success criteria

The implementation is successful when:

```text
1. Admin can create reusable video case studies.
2. Admin can assign/reorder featured and more videos per page.
3. Elementor widget renders featured video grid.
4. Elementor widget renders more-videos grid when needed.
5. One modal is auto-rendered once in the footer.
6. Clicking any plugin-rendered card opens the correct modal.
7. Direct hash URLs open the correct modal.
8. Related videos are correct for the current page context.
9. Same video on different pages shows different related videos where page featured lists differ.
10. Closing the modal stops video playback.
11. Closing the modal preserves scroll position.
12. Old static pages continue working during migration.
13. New plugin-powered pages do not require old Elementor popups.
14. New plugin-powered pages do not require temporary rollout classes.
15. Assets are only loaded when needed.
16. Missing dependencies do not fatal the site.
```

