# OMMVS-13 - Video Case Study Admin Guide V4

## Purpose

This guide documents the V4 admin workflow for creating, reviewing, and maintaining `Video Case Study` records in the 1MM Video Showcase plugin.

V4 is an admin usability layer. It does not change the frontend data model, modal behavior, Elementor widget behavior, page placement storage, or migration rules.

Original migration Phases 12, 13, and 14 remain paused until the regression checklist in this document passes.

## Video Case Study Editing Workflow

### 1. Open Or Create The Video

Go to `Video Case Studies` in WordPress admin, then add a new video or edit an existing one.

Use the WordPress post title as an internal admin title. The frontend card and modal can use their own plugin fields, so the post title does not need to be visitor-facing.

### 2. Complete Setup

In the `Setup` tab or fallback section:

- `Hash Slug`: enter the legacy modal hash without the leading `#`.
- `Active`: keep enabled only when the video is ready to be used in page placements.

The inline hash hint previews the frontend hash as `#your-hash`. If a leading `#` is typed, the plugin warns that the stored value should not include it.

### 3. Complete Card Content

In the `Card Content` tab or fallback section:

- `Default Card Title`: the title shown on video cards and in page placement selectors.
- `Default Card Description`: the short card copy shown in the grid.
- `Default Card Thumbnail`: the image used by frontend video cards.

The native WordPress `Featured Image` box may still be visible. It is optional and useful for admin familiarity, but the plugin frontend uses `Default Card Thumbnail`.

### 4. Complete Modal Content

In the `Modal Content` tab or fallback section:

- `Modal Title`: the title shown inside the modal.
- `Modal Content`: the rich body content shown in the modal.

Use normal WordPress editor content: headings, paragraphs, and bullet lists. Avoid pasting raw Elementor layout markup unless you are preserving a legacy popup during migration.

The modal CTA button is controlled globally in `Settings > 1MM Video Showcase`.

### 5. Choose One Video Category

Use the `Video Categories` sidebar panel.

A video may have no category or one category. To add a new category from the edit screen, use `+ Add New Video Category`; the new term is added and selected automatically. For broader category maintenance, use the `Manage Video Categories` link.

If legacy or external edits leave multiple categories on a video, the editor will collapse normal saves to one category and Data Health will continue to report remaining records that need review.

### 6. Add The Vimeo Source

In the `Video Source` tab or fallback section, enter one Vimeo URL.

Accepted examples:

- `https://vimeo.com/879662317`
- `https://vimeo.com/879662317?fl=pl&fe=cm`
- `https://player.vimeo.com/video/879662317`

The inline hint shows the detected Vimeo ID when the URL is valid. Server-side validation remains the source of truth.

### 7. Add Optional Related Media

In the `Related / Optional Media` tab or fallback section:

- `Related Thumbnail Override`: optional image used only for related cards in the modal.

If this field is empty, related cards fall back to `Default Card Thumbnail`.

### 8. Add Migration Notes

Use `Migration Notes` for internal-only context such as legacy popup IDs, verification notes, or staging migration details.

These notes are not rendered on the frontend.

## Readiness Panel

The `Video Readiness` sidebar panel is read-only. It checks the saved video record and tells editors whether the video is ready for placement.

The panel checks:

- Active state.
- Hash slug.
- Default card title.
- Default card description.
- Default card thumbnail attachment.
- Video category.
- Modal title.
- Modal content.
- Vimeo URL and detected Vimeo ID.

Use `Ready for placement` as the normal target state. `Needs attention` means at least one item should be corrected before using the video in page placements.

## List Table Workflow

The `Video Case Studies` list table includes migration and maintenance helpers:

- Thumbnail, hash, active status, category, Vimeo URL status, and modal content status columns.
- Filters for active status, category, missing required fields, and Vimeo URL status.
- `Copy hash` row action when a video has a hash slug.
- `Data Health` row action for quick access to the full diagnostic page.

Use the filters to find incomplete, inactive, uncategorized, or invalid Vimeo records before large migration passes.

## Data Health Workflow

Go to `Video Case Studies > Data Health` for the full diagnostic report.

Review and resolve or document:

- Missing required Video Case Study fields.
- Duplicate hashes.
- Multiple category assignments.
- Duplicate page placements.
- Placement integrity issues such as missing, invalid, trashed, or inactive referenced videos.

Data Health is read-only. It does not change stored data.

## Page Placement Reminder

Page placements are edited from the `1MM Video Showcase` metabox on WordPress pages.

The selector displays `Default Card Title`, with search support for both Default Card Title and the internal Video CPT post title. Featured Videos and More Videos remain ordered page-specific lists.

## V4 Regression Checklist

Use the Status column as `Pass`, `Fail`, or `Not tested`.

### Static Checks

| Check | Status | Notes |
| --- | --- | --- |
| `php -l includes/class-ommvs-fields.php` | Pass | Local check passed on 2026-06-23. |
| `php -l includes/class-ommvs-taxonomy-video-category.php` | Pass | Local check passed on 2026-06-23. |
| `php -l includes/class-ommvs-video-admin-ui.php` | Pass | Local check passed on 2026-06-23. |
| `php -l includes/class-ommvs-cpt-video.php` | Pass | Local check passed on 2026-06-23. |
| `php -l includes/class-one-minute-media-video-showcase.php` | Pass | Local check passed on 2026-06-23. |
| `php -l admin/class-one-minute-media-video-showcase-admin.php` | Pass | Local check passed on 2026-06-23. |
| `node --check admin/js/one-minute-media-video-showcase-admin.js` | Pass | Local check passed on 2026-06-23. |
| `node --check public/js/one-minute-media-video-showcase-public.js` | Pass | Local check passed on 2026-06-23. |
| `git diff --check` | Pass | Local check passed on 2026-06-23; Git reported only LF-to-CRLF warnings. |

### ACF-Active Video Editor

| Check | Status | Notes |
| --- | --- | --- |
| `Video Showcase Details` uses tabs instead of one long flat field stack. | Not tested | |
| Existing field values appear correctly after reload. | Not tested | |
| Hash preview and leading-`#` warning work. | Not tested | |
| Default Card Thumbnail choose/remove works. | Not tested | |
| Modal Content editor supports headings, paragraphs, and lists. | Not tested | |
| Vimeo hint detects valid Vimeo IDs and warns for invalid values. | Not tested | |
| Video Category sidebar allows zero or one category. | Not tested | |
| Inline category creation adds and selects a new category. | Not tested | |
| Video Readiness reflects saved field state after save/reload. | Not tested | |
| Native Featured Image remains visible and does not confuse frontend thumbnail usage. | Not tested | |

### No-ACF Fallback Video Editor

| Check | Status | Notes |
| --- | --- | --- |
| Fallback section-card UI appears when ACF is inactive. | Not tested | |
| All fields save and reload using the same meta keys as ACF mode. | Not tested | |
| Media picker works for Default Card Thumbnail and Related Thumbnail Override. | Not tested | |
| Modal Content rich editor saves normal WordPress content. | Not tested | |
| Invalid Vimeo URL is not stored; valid Vimeo URL is stored. | Not tested | |
| Video Category sidebar and inline add still work. | Not tested | |
| Video Readiness panel appears and stays read-only. | Not tested | |

### List Table

| Check | Status | Notes |
| --- | --- | --- |
| Columns show Thumbnail, Hash slug, Active, Video Category, Vimeo URL, and Modal Content. | Not tested | |
| Active status filter works. | Not tested | |
| Video Category and No category filters work. | Not tested | |
| Missing required fields filter works. | Not tested | |
| Vimeo Valid, Missing, and Invalid filters work. | Not tested | |
| Combined filters intersect results correctly. | Not tested | |
| Native search, pagination, trash, bulk edit, and post status tabs still work. | Not tested | |
| Copy hash copies `#hash-slug` and shows feedback. | Not tested | |
| Data Health row action opens the diagnostic page. | Not tested | |

### Accessibility And Admin Polish

| Check | Status | Notes |
| --- | --- | --- |
| Keyboard tab order is usable on Video CPT edit screens. | Not tested | |
| Focus states are visible for custom buttons, tabs, links, selects, and row actions. | Not tested | |
| Video Category inline add form stays contained in the sidebar. | Not tested | |
| Readiness panel rows, badges, and code values wrap without clipping. | Not tested | |
| ACF-active and fallback editors remain usable at narrow admin widths. | Not tested | |
| No console errors on Video CPT edit, Video CPT list, page edit, settings, or Data Health screens. | Not tested | |

### Frontend Smoke Test

| Check | Status | Notes |
| --- | --- | --- |
| `/brand-video-production/` Featured and More grids render. | Not tested | |
| Clicking a video card opens the modal. | Not tested | |
| Direct hash URL opens the modal within the accepted timing target. | Not tested | |
| Modal title, category, content, CTA, Vimeo iframe, and related cards render. | Not tested | |
| Close button, overlay, Escape, Back behavior, and iframe cleanup still work. | Not tested | |
| No frontend console errors on OMMVS and non-OMMVS pages. | Not tested | |

## Acceptance

V4 is accepted when:

- All static checks pass.
- ACF-active and no-ACF editor workflows pass.
- List-table filters and row actions pass.
- Accessibility and admin polish checks pass.
- Frontend smoke tests pass on `/brand-video-production/`.
- Any remaining issues are documented with an owner and severity.

After V4 acceptance, the original migration plan can resume from the next approved phase.
