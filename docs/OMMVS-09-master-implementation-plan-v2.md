# OMMVS-09: Master Implementation Plan V2

## Purpose

This document defines the implementation plan for the urgent client requirement changes captured in `docs/OMMVS-08-client-requirements-v2.md`.

It is a V2 adjustment layer on top of the completed Phases -1 through 11 from `docs/OMMVS-03-master-implementation-plan.md`.

The existing plugin architecture remains correct. The work here updates the content model, admin UI, page JSON, modal rendering, and validation rules before migration continues.

## Current Plugin State

The plugin currently includes:

- `video_case_study` CPT.
- ACF Free field registration plus plugin-owned fallback fields.
- Page-level Featured Videos and More Videos placement metaboxes.
- Related-video engine based on current page Featured Videos.
- Page data JSON builder.
- Conditional public asset loading.
- Elementor `1MM Video Grid` widget.
- Footer modal renderer and template.
- JavaScript modal controller with hash behavior, iframe creation, related-card switching, and focus handling.
- Frontend card and modal CSS.
- Admin columns, notices, and Data Health page.

Current assumptions that must change:

- Featured Videos are capped at 6.
- Modal content is split between per-video overview and global creative settings.
- Video playback supports Vimeo, YouTube, and Direct URL providers.
- Related cards include a `WATCH NOW` overlay.
- Related cards primarily use card title.
- Page placement selectors are native non-searchable selects.
- Data Health expects provider/ID/direct URL fields and warns on fewer than 6 Featured Videos.

## Pause Decision For Phases 12-14

Pause the remaining original migration phases now:

- Phase 12: Second migration page with shared videos.
- Phase 13: Batch migration.
- Phase 14: Final cleanup after all pages are migrated.

This is the safest path.

Reasons:

- Phase 12 and Phase 13 would multiply data entry based on an outdated field model.
- The category and Modal Content changes affect every migrated Video Case Study.
- The Vimeo-only field changes the validation and page JSON contract.
- The unlimited Featured Videos change affects page placement storage, related maps, frontend rendering, and Data Health.
- Final cleanup must wait until the revised V2 behavior is accepted on the first migrated page.

Resume migration only after the V2 implementation passes regression testing on `/brand-video-production/`.

## Implementation Principles

- Extend the current plugin; do not rebuild from scratch.
- Preserve existing hashes and public URLs.
- Preserve existing Video Case Study posts and page placements wherever possible.
- Prefer backward-compatible meta-key reuse when it prevents data loss.
- Keep admin-facing labels aligned with the client's language.
- Keep old provider data readable during transition, but hide deprecated provider controls from normal editing.
- Keep unmigrated Elementor/static pages working.
- Do not introduce frontend behavior through legacy `js-popup-*` selectors.
- Keep all new selectors plugin-owned with `ommvs-*` naming.

## Phase V2-0: Documentation, Branch Hygiene, And Baseline

### Goal

Lock the revised requirements and establish a clean baseline before code changes.

### Tasks

1. Review `OMMVS-08-client-requirements-v2.md`.
2. Confirm current Phase 11 staging behavior is still successful.
3. Commit any completed Phase 10/11 work before implementation if it has not already been committed.
4. Create a V2 implementation branch if needed.
5. Treat `/brand-video-production/` as the V2 regression page.

### Acceptance Criteria

- V2 requirements are approved.
- The team agrees that original Phases 12-14 are paused.
- The current working tree is understood before code edits begin.

## Phase V2-1: Add Video Category Taxonomy

### Goal

Add a plugin-owned taxonomy for Video Case Study categories and make it available in admin, REST, page data, and Data Health.

### Proposed Files

Add:

```text
includes/class-ommvs-taxonomy-video-category.php
```

Modify:

```text
includes/class-one-minute-media-video-showcase.php
includes/class-one-minute-media-video-showcase-activator.php
includes/class-ommvs-cpt-video.php
includes/class-ommvs-page-data.php
includes/class-ommvs-data-health.php
```

### Taxonomy Contract

```text
Taxonomy slug: ommvs_video_category
Object type: video_case_study
Public: false
Show UI: true
Show in REST: true
Show admin column: true
Hierarchical: true
Rewrite: false
Query var: false
```

### Admin Behavior

- Editors can assign a Video Category on Video Case Study edit screens.
- Admin list table can show the category, either through native taxonomy column or custom column.
- Data Health warns if no category is assigned.
- Data Health warns if multiple categories are assigned and the frontend only displays one.

### Frontend Data

Each video in page JSON should include:

```json
"category": {
  "id": 123,
  "name": "Brand, TV Ad",
  "slug": "brand-tv-ad"
}
```

### Acceptance Criteria

- `Video Categories` appears under Video Case Studies.
- Categories can be assigned to Video Case Study posts.
- Page JSON includes category data.
- No public category archive is exposed.

## Phase V2-2: Refactor Video Field Model

### Goal

Replace the client-facing Production Overview/provider model with Modal Content and Vimeo Video URL.

### Proposed Files

Modify:

```text
includes/class-ommvs-fields.php
includes/class-ommvs-page-data.php
includes/class-ommvs-data-health.php
admin/class-one-minute-media-video-showcase-admin.php
admin/css/one-minute-media-video-showcase-admin.css
admin/js/one-minute-media-video-showcase-admin.js
```

### Field Contract

Keep existing stable fields:

```text
ommvs_hash_slug
ommvs_is_active
ommvs_card_title
ommvs_card_description
ommvs_card_thumbnail
ommvs_modal_title
ommvs_related_thumbnail
ommvs_admin_notes
```

Change client-facing meaning:

```text
ommvs_modal_overview -> Modal Content
ommvs_video_url -> Vimeo Video URL
```

Deprecate and hide from normal admin editing:

```text
ommvs_video_provider
ommvs_video_id
```

Recommended code approach:

- Add a class constant alias such as `FIELD_MODAL_CONTENT` mapped to the existing `ommvs_modal_overview` meta key.
- Keep `FIELD_MODAL_OVERVIEW` temporarily for backward compatibility if existing code still references it.
- Treat `FIELD_VIDEO_URL` as the canonical Vimeo URL field.
- Stop rendering provider and video ID fields in ACF and fallback metaboxes.
- Stop saving provider and video ID from fallback forms.

### ACF Free Field Updates

Change the video field group:

- Rename `Production Overview` to `Modal Content`.
- Use ACF WYSIWYG with full toolbar where available.
- Enable media upload if supported.
- Rename `Video URL` to `Vimeo Video URL`.
- Remove provider select, YouTube option, Direct URL option, and Video ID field from the normal UI.

### Fallback Field Updates

When ACF is unavailable:

- Render Modal Content with `wp_editor()` instead of a plain textarea.
- Enable media buttons.
- Keep sanitization through `wp_kses_post()`.
- Render one Vimeo Video URL input.

### Vimeo URL Validation

Accept only Vimeo URLs.

Valid examples:

```text
https://vimeo.com/879662317
https://vimeo.com/879662317?fl=pl&fe=cm
https://player.vimeo.com/video/879662317
```

Validation should:

- Require `http` or `https`.
- Require a Vimeo host.
- Extract a numeric Vimeo video ID from known Vimeo URL patterns.
- Store the original clean URL.
- Fail safely when no ID can be extracted.

### Acceptance Criteria

- Admin users see Modal Content and Vimeo Video URL.
- Admin users do not see provider or ID fields.
- Invalid non-Vimeo URLs are rejected or flagged.
- Existing Phase 11 content is not deleted by the field rename.

## Phase V2-3: Simplify Global Settings

### Goal

Remove obsolete global creative content controls from the active settings UI.

### Proposed Files

Modify:

```text
includes/class-ommvs-settings.php
includes/class-ommvs-page-data.php
admin/js/one-minute-media-video-showcase-admin.js
admin/css/one-minute-media-video-showcase-admin.css
```

### Keep In Settings

```text
CTA button text
CTA button URL
Modal fallback thumbnail
```

### Deprecate In Settings UI

```text
Production Overview label
Creative section title
Creative bullet list
```

### Compatibility

- Do not delete old saved options during this phase.
- Page JSON may omit deprecated settings or keep them unused for one release.
- Frontend JS must no longer build modal content from these settings.

### Acceptance Criteria

- Settings page is simpler and matches the new content model.
- CTA still renders correctly.
- Deprecated settings no longer affect modal body content.

## Phase V2-4: Remove Featured Limit And Improve Placement Selector

### Goal

Allow unlimited Featured Videos and improve page placement selection with search.

### Proposed Files

Modify:

```text
includes/class-ommvs-fields.php
includes/class-ommvs-page-data.php
includes/class-ommvs-data-health.php
admin/class-one-minute-media-video-showcase-admin.php
admin/js/one-minute-media-video-showcase-admin.js
admin/css/one-minute-media-video-showcase-admin.css
```

Possibly add vendor assets:

```text
admin/vendor/select2/
```

### Featured Videos

Remove:

- `FEATURED_VIDEOS_MAX = 6` as an active limit.
- UI block for adding a 7th Featured Video.
- Server-side truncation to 6 rows.
- Notices saying Featured Videos are limited to 6.
- Data Health warning for fewer than 6 Featured Videos.
- Page data truncation to 6 Featured Videos.

Keep:

- Ordered placement rows.
- Duplicate video validation.
- Inactive/missing field warnings.
- Related-video source rule.

### Related Behavior With Unlimited Featured Videos

The existing related behavior should still return up to 3 related videos unless changed later.

Rules remain:

- Related videos come only from the current page's Featured Videos list.
- If current video is in the Featured list, exclude the current video.
- If current video is in More Videos, use the first Featured Videos.
- Use the whole Featured list as source, not only the first 6.

### Searchable Selector

Recommended implementation:

- Bundle a small plugin-owned Select2-compatible library or Select2 itself under plugin admin assets.
- Enqueue it only on page edit screens where the `1MM Video Showcase` metabox appears.
- Enhance only placement row selects.
- Search by rendered Video Case Study post title.
- Reinitialize searchable selects after adding a new row.
- Destroy/reinitialize safely during row cloning/reindexing.
- Keep the underlying `<select>` as the saved form field.

Fallback:

- If enhancement fails, the native select still works.

### Acceptance Criteria

- Featured Videos can exceed 6 rows.
- Save/reload preserves all Featured rows.
- Page JSON contains all Featured IDs.
- Related maps use all Featured IDs as source.
- Placement selector has a search box and filters by Video CPT title.
- No Select2/search assets load on unrelated admin screens.

## Phase V2-5: Update Page Data Schema

### Goal

Expose the revised video model to frontend JS.

### Proposed Files

Modify:

```text
includes/class-ommvs-page-data.php
includes/class-ommvs-related-videos.php
```

### Revised Video JSON Shape

Recommended shape:

```json
{
  "id": 123,
  "hash": "nick-kyrgios",
  "category": {
    "id": 456,
    "name": "Brand, TV Ad",
    "slug": "brand-tv-ad"
  },
  "card": {
    "title": "Tennis Majors With Nick Kyrgios, TV Ad",
    "description": "...",
    "thumbnail": {}
  },
  "modal": {
    "title": "Tennis Majors",
    "content": "<h3>Production Overview</h3>...",
    "vimeoUrl": "https://vimeo.com/879662317",
    "vimeoId": "879662317"
  },
  "relatedCard": {
    "thumbnail": {}
  }
}
```

### Transition Compatibility

During implementation, JS may temporarily support:

- `modal.content` first.
- fallback to old `modal.overview` only if needed.

The long-term canonical field is `modal.content`.

### Acceptance Criteria

- JSON parses safely.
- JSON includes category, modal content, Vimeo URL, and derived Vimeo ID.
- JSON does not require provider selection.
- Hash map and related map remain stable.

## Phase V2-6: Update Modal Template And JavaScript

### Goal

Render the revised modal content model and Vimeo-only video behavior.

### Proposed Files

Modify:

```text
templates/modal.php
public/js/one-minute-media-video-showcase-public.js
```

### Modal Template

Add a category placeholder:

```html
<p class="ommvs-modal__category" data-ommvs-modal-category></p>
```

Replace old split content placeholders with one content placeholder:

```html
<div class="ommvs-modal__content" data-ommvs-modal-content></div>
```

Remove or stop using:

```text
data-ommvs-modal-overview-label
data-ommvs-modal-overview
data-ommvs-modal-creative-title
data-ommvs-modal-creative-list
```

### JavaScript

Update modal rendering:

- Title: `video.modal.title`.
- Category: `video.category.name`.
- Content: `video.modal.content`.
- CTA: global settings.
- Iframe: Vimeo only.
- Related cards: modal title plus category.

Update iframe builder:

- Use `modal.vimeoId` when provided.
- Otherwise attempt to parse a Vimeo ID from `modal.vimeoUrl`.
- Do not build YouTube or Direct URL iframes.
- Do not throw if Vimeo data is invalid.

Update related card builder:

- Remove `WATCH NOW` text dependency.
- Use `video.modal.title` for title.
- Use `video.category.name` below title.
- Keep whole related card clickable.
- Continue updating URL hash on related-card click.

### Acceptance Criteria

- Modal title/category match client expectation.
- Modal body content comes from Modal Content.
- CTA still works.
- Vimeo videos autoplay as before.
- Related-card clicks still switch video in the same modal and update hash.
- No old provider assumptions are required by the frontend.

## Phase V2-7: Update Frontend And Admin Styling

### Goal

Polish the revised modal, related cards, and searchable admin selector.

### Proposed Files

Modify:

```text
public/css/one-minute-media-video-showcase-public.css
admin/css/one-minute-media-video-showcase-admin.css
```

### Frontend CSS

Remove:

```css
.ommvs-modal__related-card-media::after {
  content: "WATCH NOW";
}
```

Add styling for:

- Modal category under title.
- Modal Content headings, paragraphs, and lists.
- Related card category text.
- Related card hover/focus states without overlay text.

### Admin CSS

Add or adjust styling for:

- Searchable placement selector.
- Modal Content editor in fallback metabox.
- Simplified settings page.

### Acceptance Criteria

- Related cards no longer show `WATCH NOW`.
- Modal category looks intentional.
- Modal Content remains readable with headings and lists.
- Admin searchable selector feels native and not oversized.

## Phase V2-8: Data Health And Admin Columns

### Goal

Make admin diagnostics reflect the V2 requirements.

### Proposed Files

Modify:

```text
includes/class-ommvs-data-health.php
includes/class-ommvs-cpt-video.php
admin/css/one-minute-media-video-showcase-admin.css
```

### Data Health Updates

Required fields:

- Hash slug.
- Card title.
- Card description.
- Valid card thumbnail attachment.
- Video Category.
- Modal title.
- Modal Content.
- Valid Vimeo Video URL.

Remove checks for:

- Video provider.
- Video ID.
- Direct URL provider.
- Pages with fewer than 6 Featured Videos.

Keep checks for:

- Duplicate hashes.
- Duplicate page placements.
- Missing/inactive videos used in page placements.

Add warnings for:

- Multiple Video Categories assigned to a single video, if the frontend displays only one.

### Admin Column Updates

Replace or update:

```text
Video provider -> Vimeo URL / Video Category / Content status
```

Recommended columns:

- Thumbnail.
- Hash slug.
- Active.
- Video Category.
- Vimeo URL status.

### Acceptance Criteria

- Data Health only reports issues relevant to V2.
- Video list table helps editors identify missing category/Vimeo data.

## Phase V2-9: Migrate Existing Phase 11 Data

### Goal

Update the already migrated `/brand-video-production/` videos to the V2 model.

### Work Type

Manual staging admin data update, unless a small one-off helper is approved.

### Tasks

1. Create Video Category terms used by the migrated page.
2. For each Video Case Study, assign the correct category.
3. Split old modal title/category values.
4. Convert Vimeo Provider + Video ID into Vimeo Video URL.
5. Move per-video overview/creative content into Modal Content.
6. Save all posts.
7. Run Data Health.
8. Fix all V2 issues.

### Known Example

```text
Post: Tennis Majors With Nick Kyrgios, TV Ad
Modal Title: Tennis Majors
Category: Brand, TV Ad
Vimeo Video URL: https://vimeo.com/879662317
```

### Acceptance Criteria

- All `/brand-video-production/` videos pass V2 Data Health.
- All old hashes still open the correct modal.
- No unmigrated Elementor page is affected.

## Phase V2-10: Regression Testing On `/brand-video-production/`

### Goal

Confirm the V2 model works on the first migrated page before migration resumes.

### Tests

Admin tests:

- Create/edit Video Category terms.
- Edit a Video Case Study with Modal Content and Vimeo URL.
- Confirm invalid Vimeo URLs are flagged.
- Add more than 6 Featured Videos to a page and save.
- Search for videos in placement selectors.
- Reorder placement rows.
- Run Data Health.

Frontend tests:

- Grid cards still render card title and card description.
- Hashes still open modals.
- Modal shows modal title and category separately.
- Modal body shows Modal Content.
- CTA button works.
- Vimeo iframe opens and stops on close.
- Related cards show modal title and category.
- Related cards do not show `WATCH NOW`.
- Related-card switching still updates hash.
- More Videos still derive related cards from Featured Videos.

Regression tests:

- Old static Elementor sections still work on unmigrated pages.
- Public assets remain conditionally loaded.
- No JavaScript errors on pages without OMMVS modal.

### Acceptance Criteria

- `/brand-video-production/` passes V2 testing.
- Client approves the revised editor workflow and modal display.

## Phase V2-11: Resume Original Migration Plan

### Goal

Continue rollout only after V2 is accepted.

### Resume Point

Resume the original plan at Phase 12, but reinterpret it under the V2 model.

Updated migration rules:

- Every Video Case Study needs a Video Category.
- Every Video Case Study needs Modal Content.
- Every Video Case Study needs Vimeo Video URL.
- Featured Videos can contain more than 6 videos.
- Page selectors use searchable controls.
- Related-card UI follows the V2 modal title/category design.

### Acceptance Criteria

- Second page migration begins only after V2 regression is stable.
- Batch migration uses the V2 data model from the start.

## Risks

### Existing migrated data may need manual cleanup

Some Phase 11 posts may have category text embedded in Modal Title. These must be split carefully to avoid changing visible card titles or hashes.

### Meta-key compatibility must be handled deliberately

Renaming `ommvs_modal_overview` to a new database key would create migration risk. Reusing the existing meta key with a new label is safer unless a formal migration script is approved.

### Searchable selector introduces an admin asset dependency

If Select2 or a similar library is bundled, it must be scoped tightly to page edit screens to avoid conflicts with WordPress, Elementor, ACF, or WooCommerce.

### Vimeo URL parsing needs strict but practical validation

Vimeo supports multiple URL formats. Validation must accept common Vimeo page/player URLs while rejecting YouTube, arbitrary direct files, and malformed values.

### Unlimited Featured Videos affects performance and UX

Large placement lists could make the page metabox long. Searchable selectors and compact row styling become more important.

## First Implementation Step

After approval of these V2 documents, start with:

```text
Phase V2-1: Add Video Category Taxonomy
```

Do not resume original Phase 12 until V2-10 is complete.
