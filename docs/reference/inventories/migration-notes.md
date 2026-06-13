# Migration Notes — Static Elementor System to 1MM Video Showcase Plugin

This document provides practical migration guidance for moving from the current static Elementor popup system to the new plugin-driven video showcase system.

## Production and staging sites

Production site:

```txt
https://www.1minutemedia.com.au/
```

Testing/staging site:

```txt
https://test61.autocomputation.com/
```

All plugin development and migration testing should happen on staging first.

Do not perform large-scale migration directly on production.

---

## Current legacy system

The current site uses:

```txt
Static Elementor pages
Static video-card sections
One Elementor popup per video
Static related-video cards inside each popup
Old per-popup inline HTML widget scripts
Old global footer popupHashMap script
Temporary rollout script on some pages
```

This worked for the pilot but is too manual for 20+ pages and 100+ popups.

---

## Approved new architecture

The approved plugin architecture is:

```txt
Video CPT
Page-level Featured Videos and More Videos placements
Custom Elementor Video Grid widget
One auto-rendered modal in footer
One current-page JSON data object
Plugin-owned frontend JS for modal/hash/related behavior
```

The modal should be rendered automatically once in the footer when the video-grid widget is used.

No separate Elementor modal widget should be required.

---

## Core business rule

A video is reusable/global.

A page's video order is page-specific.

Related videos are based on the current page context.

Therefore:

```txt
Do not store related videos on the Video CPT.
Do not hardcode related videos in the modal.
Do not create one popup per video in the final system.
```

---

## Related-video rule

Given Featured Videos:

```txt
A, B, C, D, E, F
```

Expected behavior:

```txt
Open A → B, C, D
Open B → A, C, D
Open C → A, B, D
Open D → A, B, C
Open E → A, B, C
Open F → A, B, C
Open any More Videos item → A, B, C
```

This must work even if the same video appears on multiple pages.

Example:

```txt
/brand-video-production/#nick-kyrgios
→ Opens Nick Kyrgios modal
→ Related videos come from Brand Video Production page

/design-and-fashion-video-production/#nick-kyrgios
→ Opens same Nick Kyrgios modal
→ Related videos come from Design and Fashion page
```

---

## Hash URL requirement

The final plugin must preserve the current URL hash behavior.

Example:

```txt
/brand-video-production/#nick-kyrgios
```

Important technical note:

```txt
The hash is not sent to WordPress/PHP during the initial server request.
```

Therefore JavaScript must read:

```js
window.location.hash
```

and open the correct modal using the page JSON data rendered by PHP.

---

## Recommended first migration page

Start with:

```txt
/brand-video-production/
```

Reasons:

```txt
It was the successful pilot page.
Its first-six Featured Videos list is already understood.
Its More Videos section was already tested.
The related-video behavior was validated across both sections.
The scroll restoration issue was found and fixed during pilot testing.
```

---

## Migration phases

### Phase 1 — Build plugin foundation

Implement:

```txt
Plugin constants
Video CPT
ACF fields or equivalent field registration
Global modal settings
Related-video calculation class
Page-data builder class
```

Do not begin migrating all pages yet.

### Phase 2 — Build Elementor widget

Implement:

```txt
Custom Elementor category if needed
1MM Video Grid widget
Source control: Featured Videos / More Videos
Frontend card template
Basic frontend CSS matching current card design
```

### Phase 3 — Build modal renderer

Implement:

```txt
Auto-render modal once in wp_footer
Render page JSON data once
Enqueue frontend JS/CSS only when needed
```

### Phase 4 — Build frontend JS controller

Implement:

```txt
Click card → open modal
Hash URL → open modal
Open modal → update hash
Close modal → clear hash
Load iframe only on open
Destroy iframe on close
Render related cards from page data
Handle related-card click
Handle Escape key
Handle close button
Restore scroll position
```

### Phase 5 — Migrate Brand Video Production on staging

Tasks:

```txt
Create Video CPT entries for all videos used on the page.
Add page-level Featured Videos placements.
Add page-level More Videos placements.
Replace static Featured Videos section with 1MM Video Grid widget.
Replace static More Videos section with 1MM Video Grid widget.
Verify modal output.
Verify direct hash URLs.
Verify related-video logic.
Verify scroll behavior.
Verify iframe stops on close.
```

### Phase 6 — Migrate second validation page

Choose a page where a shared video appears but the Featured Videos list differs.

Purpose:

```txt
Validate same video + different page context + different related videos.
```

### Phase 7 — Batch migration

After two pages pass:

```txt
Migrate Services pages in small batches.
Migrate Industries pages in small batches.
Do not migrate all pages at once.
```

---

## Coexistence strategy

During migration, old and new systems may coexist.

### Old/static pages may continue using

```txt
Static Elementor video cards
Existing Elementor popups
Old global popupHashMap script
Temporary rollout script if still active
```

### New/plugin-powered pages should use

```txt
Video CPT entries
Page placement fields
1MM Video Grid widget
Auto-rendered plugin modal
Plugin-owned JS/CSS
```

The new plugin JS should bind only to plugin-owned selectors such as:

```txt
.ommvs-video-card
#ommvs-video-modal
```

This avoids interfering with old Elementor popup pages during migration.

---

## What not to remove too early

Do not remove these globally until migration is complete:

```txt
Existing Elementor popups
Old global popupHashMap script
Old static video sections on unmigrated pages
Temporary rollout script if needed for unmigrated pages
```

Remove legacy components page by page only after each page is fully migrated and tested.

---

## Page migration checklist

For each page:

```txt
1. Confirm current static video card list.
2. Identify first-six Featured Videos in exact order.
3. Identify More Videos in exact order if present.
4. Verify every video has or receives a Video CPT entry.
5. Preserve legacy hash slug for every existing video.
6. Add Featured Videos placement data to the page.
7. Add More Videos placement data to the page if applicable.
8. Add the 1MM Video Grid widget for Featured Videos.
9. Add the 1MM Video Grid widget for More Videos if applicable.
10. Verify frontend card rendering.
11. Verify card click opens modal.
12. Verify direct hash opens modal.
13. Verify related videos are page-context-specific.
14. Verify related-card click opens the next modal.
15. Verify URL hash updates/clears correctly.
16. Verify scroll position remains stable after close.
17. Verify iframe/video stops after modal close.
18. Test desktop, tablet, and mobile.
19. Compare visual layout with the old Elementor design.
20. Only then remove or hide old static sections for that page.
```

---

## Video CPT migration checklist

For each legacy video/popup:

```txt
1. Create or find Video CPT entry.
2. Set internal admin title.
3. Set legacy hash slug without leading #.
4. Add default card thumbnail.
5. Add default card title.
6. Add default card description.
7. Add popup title.
8. Add popup production overview text.
9. Add video provider and video ID/URL.
10. Verify active status.
11. Verify thumbnail alt text where applicable.
12. Verify hash uniqueness.
```

---

## Page placement checklist

For each page:

```txt
Featured Videos:
- Add up to six Video CPT entries.
- Preserve exact order from the current design.
- Add placement overrides only when page-specific card copy differs from the video default.

More Videos:
- Add additional Video CPT entries.
- Preserve current visual order.
- Add placement overrides only when needed.
```

Recommended validation:

```txt
Do not allow the same video in Featured and More sections on the same page.
Warn if Featured Videos has fewer than six entries.
Do not allow inactive videos in placement lists.
```

---

## Hash migration notes

Existing hashes must be preserved where possible.

Examples:

```txt
nick-kyrgios
fleetpartners
the-langham-hotels
Whiskey&Wealth
POCruises
AIMedia
Medmate
```

Do not normalize old hashes without backwards-compatible aliases.

For new videos, prefer clean lowercase slugs:

```txt
new-video-title
example-brand-case-study
```

Potential future enhancement:

```txt
Primary hash slug + legacy hash aliases
```

---

## Handling videos not assigned to the current page

Phase 1 recommendation:

```txt
If a direct hash points to a video not included in the current page JSON, do not open the modal.
```

Reason:

```txt
The selected architecture renders all current-page video data at once as JSON.
Videos not assigned to the current page will not be in that JSON.
```

Future enhancement:

```txt
AJAX fallback to load any global video by hash.
Related videos would still come from the current page's Featured Videos list.
```

Do not build this enhancement unless the client explicitly requires it.

---

## Performance notes

The modal should not load all video iframes on page load.

Required behavior:

```txt
Create/load iframe only when modal opens.
Remove iframe or clear src when modal closes.
```

Benefits:

```txt
Faster page load.
No hidden videos playing after close.
Less browser memory usage.
Better mobile performance.
```

---

## Elementor integration notes

The plugin should provide a custom Elementor widget:

```txt
1MM Video Grid
```

Controls should include:

```txt
Source: Featured Videos / More Videos
Columns
Show/hide description
Basic style options if needed
```

The modal should auto-render once in the footer when a page uses the widget.

Editors should not need to place a separate modal widget.

---

## Admin/editor experience goal

The final workflow should be:

```txt
1. Add/edit videos once under Video Case Studies.
2. Edit a page.
3. Select Featured Videos and More Videos in page fields.
4. Drag to reorder videos.
5. Place or configure the 1MM Video Grid Elementor widget.
6. The frontend automatically handles modal and related videos.
```

No manual editing of 100+ popups.

No manual related-card updates.

No technical classes added by editors.

---

## Final cleanup after migration

After all pages are migrated and approved:

```txt
1. Disable/remove temporary rollout script.
2. Retire old Elementor popup usage for migrated video system.
3. Remove old per-popup inline scripts.
4. Remove old global popupHashMap script if fully replaced by plugin hash routing.
5. Archive legacy popup templates if no longer used.
6. Turn off plugin debug logging.
7. Confirm no old static video sections remain on migrated pages.
```

---

## Acceptance criteria for migration completion

Migration is complete when:

```txt
All target Services and Industries pages use the plugin video grid.
All required videos exist as Video CPT entries.
All current page video orders are represented in page placement data.
Direct hash URLs work for assigned videos.
Related videos are correct per page context.
Only one modal structure is used for plugin-powered pages.
No manual popup related cards are required.
Video iframes load only on modal open and stop on modal close.
Desktop/tablet/mobile layouts are approved.
Old static popup system is no longer needed for migrated pages.
```
