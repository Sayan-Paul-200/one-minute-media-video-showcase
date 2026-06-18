# OMMVS-11: Master Implementation Plan V3

## Purpose

This document defines the implementation plan for the client feedback captured in `docs/OMMVS-10-client-feedback-v3.md`.

V3 is a focused correction layer after successful V2 testing. It should be completed and regression-tested on `/brand-video-production/` before resuming original migration Phase 12.

## Current Codebase State

Current branch:

```text
feature/ommvs-v2-client-requirements
```

Current observed implementation:

- `OMMVS_Fields` owns Video CPT fields, page placement metaboxes, ACF field definitions, fallback editor, validation, and placement selector output.
- Modal Content is stored in `ommvs_modal_overview` through `OMMVS_Fields::FIELD_MODAL_CONTENT`.
- `OMMVS_Page_Data` outputs `modal.content` from the stored Modal Content meta after `wp_kses_post()`.
- Public JS injects modal content with `innerHTML`.
- `OMMVS_Taxonomy_Video_Category` registers a hierarchical taxonomy, so WordPress renders checkbox category selection.
- Page placement selectors are Select2-enhanced, but option labels currently come from Video CPT post titles.
- Admin JS appends new placement rows but does not scroll to the appended row.
- `OMMVS_Widget_Video_Grid` renders the empty message for all visitors when no source IDs exist.
- The widget border-radius control applies the same radius to `.ommvs-video-card` and `.ommvs-video-card__media`.
- `OMMVS_Assets` registers public JS with a `jquery` dependency even though `public/js/one-minute-media-video-showcase-public.js` is plain JavaScript.
- Footer modal rendering and page JSON remain single-instance gated by `OMMVS_Modal_Renderer`.

## Pause Decision

Pause original Phases 12, 13, and 14 until V3 passes regression testing.

Reason:

- V3 changes how editors author Modal Content.
- V3 changes category selection rules.
- V3 changes page placement labels and admin workflow.
- V3 changes public empty-grid behavior and hash opening performance.

Starting additional migrations now would risk repeating data-entry and QA work across more pages.

## Implementation Principles

- Extend the current V2 plugin; do not rebuild.
- Keep existing hashes and migrated Video Case Study posts.
- Keep Modal Content stored in the existing `ommvs_modal_overview` meta key.
- Prefer WordPress-native formatting and sanitization for rich text.
- Keep the frontend controller plain JavaScript.
- Keep Select2 scoped to page edit screens only.
- Keep public visitors from seeing admin/editor-only diagnostics.
- Keep all runtime selectors plugin-owned with `ommvs-*`.

## Phase V3-0: Baseline And Documentation

### Goal

Lock the V3 feedback and confirm the branch is clean before code changes.

### Tasks

1. Review `docs/OMMVS-10-client-feedback-v3.md`.
2. Review this implementation plan.
3. Confirm `/brand-video-production/` remains the V3 regression page.
4. Confirm original Phases 12-14 remain paused.
5. Check branch state.

### Verification

```bash
git status --short --branch
git log --oneline --decorate -3
```

### Acceptance Criteria

- V3 docs are accepted as the active source of truth.
- The next implementation step is Phase V3-1.

## Phase V3-1: Normalize Modal Content Formatting

### Goal

Make normal rich-text editor content render correctly without requiring pasted Elementor HTML.

### Files To Modify

```text
includes/class-ommvs-page-data.php
public/css/one-minute-media-video-showcase-public.css
```

### PHP Changes

Add a Modal Content formatting helper in `OMMVS_Page_Data`.

Recommended behavior:

1. Read raw `OMMVS_Fields::FIELD_MODAL_CONTENT`.
2. Sanitize with `wp_kses_post()`.
3. Normalize ordinary rich text with WordPress formatting:
   - Apply `wpautop()` so blank-line paragraphs become `<p>` tags.
   - Apply `shortcode_unautop()` if available.
   - Avoid stripping allowed headings, lists, links, and images.
4. Return the formatted HTML in `modal.content`.
5. Keep `modal.overview` as the same compatibility alias while it still exists.

Expected result:

```html
<h3>Production Overview</h3>
<p>First paragraph.</p>
<p>Second paragraph.</p>
```

must render as separate frontend paragraphs.

### CSS Changes

Update modal content styling for normal WordPress rich text:

- Add top spacing for headings that are not the first child.
- Keep first heading aligned near the legacy design.
- Style normal unordered lists as teal check lists.
- Keep normal ordered lists readable with numbers.
- Keep legacy Elementor icon-list compatibility rules, but do not require that structure.

Recommended selectors:

```css
#ommvs-video-modal .ommvs-modal__content h1:not(:first-child),
#ommvs-video-modal .ommvs-modal__content h2:not(:first-child),
#ommvs-video-modal .ommvs-modal__content h3:not(:first-child),
#ommvs-video-modal .ommvs-modal__content h4:not(:first-child) {
  margin-top: 28px;
}

#ommvs-video-modal .ommvs-modal__content ul:not(.elementor-icon-list-items) {
  list-style: none;
}

#ommvs-video-modal .ommvs-modal__content ul:not(.elementor-icon-list-items) > li::before {
  /* teal check icon */
}
```

### Test Plan

- Enter two paragraphs in Modal Content, save, and confirm two frontend paragraphs.
- Enter `h3`, paragraph, paragraph, `h3`, list, save, and confirm spacing.
- Confirm normal unordered lists match the legacy teal check-list direction.
- Confirm ordered lists still show ordered numbering.
- Confirm existing Elementor-like copied content does not collapse or become unreadable.

## Phase V3-2: Restrict Video Category To Single Selection

### Goal

Allow zero or one Video Category per Video Case Study through the normal edit UI.

### Files To Modify

```text
includes/class-ommvs-taxonomy-video-category.php
includes/class-one-minute-media-video-showcase.php
admin/css/one-minute-media-video-showcase-admin.css
```

### Implementation Plan

1. Keep taxonomy slug `ommvs_video_category`.
2. Keep category management UI under Video Case Studies.
3. Replace the native checkbox post metabox with a plugin-owned single-selection metabox:
   - Radio list or select control.
   - Include a `No category` option.
   - Preserve add-new category workflow only if it can remain clean; otherwise rely on the taxonomy management screen.
4. Add a save handler to enforce at most one selected term.
5. Keep Data Health multiple-category warning for legacy data or external edits.

Recommended approaches:

- Use taxonomy registration `meta_box_cb` pointing to a custom render callback, or remove the native taxonomy metabox and add a custom one.
- Hook `save_post_video_case_study` to read the chosen term and call `wp_set_object_terms()`.

### Acceptance Criteria

- Video edit screen does not show category checkboxes.
- Admin can select no category.
- Admin can select one category.
- Admin cannot select multiple categories from the standard UI.
- Existing multiple-category records are still reported by Data Health until corrected.

## Phase V3-3: Improve Page Placement Admin UX

### Goal

Make placement rows easier to identify and make appended rows visible immediately.

### Files To Modify

```text
includes/class-ommvs-fields.php
admin/js/one-minute-media-video-showcase-admin.js
admin/css/one-minute-media-video-showcase-admin.css
```

### Selector Label Changes

Update `OMMVS_Fields::get_video_options()`:

- Primary option label: `OMMVS_Fields::FIELD_CARD_TITLE`.
- Fallback: Video CPT post title.
- Final fallback: `Video #{id}`.
- Consider storing the CPT post title in a `data-ommvs-post-title` attribute for optional Select2 matcher support.

If keeping old V2 search behavior matters, implement a custom Select2 matcher that searches both:

- displayed Default Card Title
- hidden Video CPT post title

### Smooth Scroll Changes

Update admin JS `addRow()`:

1. Append the new row.
2. Reindex the section.
3. Initialize Select2 for the new row.
4. If the row is not fully visible, call `scrollIntoView({ behavior: 'smooth', block: 'center' })`.
5. Focus the new row's selector after a short delay.

### Acceptance Criteria

- Existing placement rows display Default Card Title.
- New placement rows display Default Card Title after selection.
- Search remains useful.
- Add Video scrolls to the appended row in both Featured Videos and More Videos.
- Drag/reorder still works.
- Save/reload preserves all rows.

## Phase V3-4: Fix Elementor Video Grid Empty State And Border Radius

### Goal

Prevent visitor-facing empty messages and correct border-radius behavior.

### Files To Modify

```text
includes/elementor/class-ommvs-widget-video-grid.php
public/css/one-minute-media-video-showcase-public.css
```

### Empty State Changes

Refactor widget rendering order:

1. Resolve page data and source IDs before marking the modal as required.
2. If no IDs exist:
   - In Elementor edit mode or for users who can edit the page, render the configured empty message.
   - For public visitors, render nothing.
3. Do not mark the modal as required for public empty grids.
4. Do not enqueue public modal behavior solely for an empty public grid.

Admin/editor detection should include:

- Elementor editor mode.
- Logged-in user with edit capability for the page.

### Border Radius Changes

Update Elementor style control selectors:

Current issue:

```php
'{{WRAPPER}} .ommvs-video-card, {{WRAPPER}} .ommvs-video-card__media' => 'border-radius: {{SIZE}}{{UNIT}};'
```

Required behavior:

```php
'{{WRAPPER}} .ommvs-video-card' => 'border-radius: {{SIZE}}{{UNIT}};'
'{{WRAPPER}} .ommvs-video-card__media' => 'border-radius: {{SIZE}}{{UNIT}} {{SIZE}}{{UNIT}} 0 0;'
```

### Acceptance Criteria

- Public visitors see no empty widget output.
- Admins/editors still see a helpful empty message.
- Empty public grids do not output modal/page JSON unnecessarily.
- Card border radius applies to the card.
- Thumbnail bottom corners remain square.

## Phase V3-5: Speed Up Direct Hash Modal Opening

### Goal

Ensure direct hash URLs open the modal quickly and reliably.

### Files To Modify

```text
includes/class-ommvs-assets.php
public/js/one-minute-media-video-showcase-public.js
includes/class-one-minute-media-video-showcase.php
```

Possibly modify:

```text
includes/class-ommvs-modal-renderer.php
```

### Root Fix Strategy

1. Remove the unnecessary `jquery` dependency from the public OMMVS controller.
   - The script is plain JavaScript.
   - This reduces the chance that jQuery defer/delay rules delay modal startup.
2. Add lightweight performance marks in development-safe form:
   - Time when the controller initializes.
   - Time when initial hash is detected.
   - Time when modal open state is applied.
   - Keep logs disabled by default unless a debug flag is enabled.
3. Consider adding a `script_loader_tag` filter for the OMMVS public script to discourage optimization delays.
   - Use scoped attributes only for the OMMVS handle.
   - Do not globally disable optimization.
4. If staging still delays the script, document a WP Rocket or optimization-plugin exclusion for:
   - `one-minute-media-video-showcase-public.js`
   - `#ommvs-page-data`
   - `#ommvs-video-modal`
5. Keep modal opening itself independent of Vimeo iframe load. The modal shell should become visible before or while the iframe loads.

### Important Note

URL hashes are not sent to PHP. PHP cannot know `#nick-kyrgios` during the original HTTP request. Hash opening must remain JavaScript-owned.

### Acceptance Criteria

- Direct hash URLs open the modal within 5 seconds on staging under normal cache conditions.
- Card click opening still works.
- Related-card switching still works.
- Closing by button, overlay, Escape, and Back behavior still works.
- No JavaScript errors on pages without OMMVS data.
- If an external optimization plugin is still delaying execution, the exact exclusion is documented.

## Phase V3-6: Data Health And Regression Checks

### Goal

Confirm the V3 fixes do not regress the accepted V2 behavior.

### Files To Modify

Possibly:

```text
includes/class-ommvs-data-health.php
docs/
```

No changes are required unless implementation reveals diagnostics that need updated wording.

### Regression Checklist

Admin:

- Video edit screen allows zero or one category.
- Modal Content saves normal rich text.
- Page placement selector displays Default Card Title.
- Add Video scrolls to the appended row.
- Data Health still reports missing V2 fields and legacy multiple-category records.

Frontend:

- `/brand-video-production/` video grids render.
- Empty grid is hidden from public visitors.
- Modal opens from card click.
- Modal opens from direct hash within target timing.
- Modal Content paragraphs, headings, and lists render correctly.
- Related cards still show modal title and category.
- Vimeo iframe opens and stops on close.
- Border radius behaves correctly.

### Acceptance Criteria

- Client feedback items are resolved.
- `/brand-video-production/` passes full V3 regression.
- Original Phase 12 can resume only after V3 is accepted.

## Proposed Implementation Order

1. V3-1 Modal Content formatting.
2. V3-2 Single Video Category selection.
3. V3-3 Page placement admin UX.
4. V3-4 Elementor widget empty state and border radius.
5. V3-5 Direct hash performance.
6. V3-6 Regression.

This order fixes editor-authored content first, then admin selection safety, then widget/frontend polish, then the direct-hash performance risk.

