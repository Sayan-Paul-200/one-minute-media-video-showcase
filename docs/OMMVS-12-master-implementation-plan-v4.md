# OMMVS-12: Master Implementation Plan V4

## Purpose

This document defines the V4 implementation plan for improving the WordPress admin editing experience for `Video Case Study` records.

V4 begins after successful V3 testing. The plugin architecture remains the same:

- Video Case Study CPT.
- Video Category taxonomy.
- Page-level Featured Videos and More Videos placements.
- Elementor `1MM Video Grid` widget.
- One shared footer modal.
- Page JSON.
- JavaScript-owned hash/modal behavior.

V4 is not a frontend rewrite and not a data-model change. It is an admin UX layer so editors can create, review, and maintain Video Case Study content with more confidence and less friction.

## Branch Baseline

Current V4 branch:

```text
feature/ommvs-v4-video-admin-ui
```

V4 branches from the accepted V3 branch:

```text
feature/ommvs-v3-client-feedback
```

Latest accepted V3 baseline observed before creating this branch:

```text
e21762d Client feedbacks
```

## Current Codebase State

### Completed And Accepted

- Original Phases -1 through 11 from `docs/OMMVS-03-master-implementation-plan.md`.
- V2 Phases 0 through 11 from `docs/OMMVS-09-master-implementation-plan-v2.md`.
- V3 Phases 0 through 6 from `docs/OMMVS-11-master-implementation-plan-v3.md`.
- `/brand-video-production/` V3 regression testing.

### Paused

The original migration continuation phases remain paused while V4 admin UI planning and implementation are active:

- Original Phase 12: Second migration page with shared videos.
- Original Phase 13: Batch migration.
- Original Phase 14: Final cleanup after all pages are migrated.

Reason: V4 directly improves the admin workflow that will be used for the remaining migrations. Completing this before more migration work reduces repeated training, correction, and QA friction.

## Current Admin Architecture

### `OMMVS_Fields`

File:

```text
includes/class-ommvs-fields.php
```

Current responsibilities:

- Defines all Video CPT meta keys.
- Registers ACF Free field group when ACF is active.
- Registers plugin-owned fallback Video CPT metabox when ACF is unavailable.
- Saves fallback fields directly to the same post meta keys.
- Registers and saves page placement metaboxes.
- Provides placement selector options.
- Provides field-level validation helpers and admin notices.

Current Video CPT fields:

- Hash Slug: `ommvs_hash_slug`
- Active: `ommvs_is_active`
- Default Card Title: `ommvs_card_title`
- Default Card Description: `ommvs_card_description`
- Default Card Thumbnail: `ommvs_card_thumbnail`
- Modal Title: `ommvs_modal_title`
- Modal Content: `ommvs_modal_overview`
- Vimeo Video URL: `ommvs_video_url`
- Related Thumbnail Override: `ommvs_related_thumbnail`
- Admin Notes: `ommvs_admin_notes`

ACF is optional. When ACF is active, it renders the primary Video CPT field UI. When ACF is inactive, the plugin renders fallback fields.

### `OMMVS_Taxonomy_Video_Category`

File:

```text
includes/class-ommvs-taxonomy-video-category.php
```

Current responsibilities:

- Registers `ommvs_video_category`.
- Replaces the default checkbox taxonomy metabox with a single-select metabox.
- Allows zero or one category per Video Case Study.
- Supports inline category creation from the Video CPT edit screen.
- Enforces single-category assignment on save.
- Keeps legacy multiple-category data safe by collapsing normal saves and leaving Data Health diagnostics available.

### `OMMVS_CPT_Video`

File:

```text
includes/class-ommvs-cpt-video.php
```

Current responsibilities:

- Registers `video_case_study`.
- Removes the slug metabox.
- Adds admin list columns for thumbnail, hash slug, active status, video category, Vimeo URL status, and Modal Content status.

### Admin Assets

Files:

```text
admin/class-one-minute-media-video-showcase-admin.php
admin/js/one-minute-media-video-showcase-admin.js
admin/css/one-minute-media-video-showcase-admin.css
```

Current responsibilities:

- Load admin CSS on page edit screens, settings, Data Health, Video CPT list, and Video CPT edit screens.
- Load admin JS on page edit screens, settings, Video CPT edit screens, and fallback Video CPT edit screens.
- Load Select2 only on page edit screens.
- Load media only where thumbnail/media controls need it.
- Support page placement row add/remove/reorder/search.
- Support fallback thumbnail selection.
- Support inline Video Category creation.

### Data Health

File:

```text
includes/class-ommvs-data-health.php
```

Current diagnostics:

- Missing required Video Case Study fields.
- Multiple categories.
- Duplicate hashes.
- Duplicate page placements.
- Placement integrity issues.

## V4 Improvement Goal

Make the Video Case Study admin experience feel polished, guided, and editor-friendly in both ACF-active and ACF-inactive environments.

The admin should be able to understand:

- What each field controls.
- Which fields affect cards, modals, categories, and migration notes.
- Whether a video is ready to use.
- What URL/hash will open the modal.
- Whether Vimeo input is valid.
- Which thumbnail will appear where.
- What to do next when data is incomplete.

## Non-Goals

V4 must not:

- Change frontend card rendering.
- Change frontend modal rendering.
- Change page JSON shape.
- Change URL hash behavior.
- Require ACF.
- Require ACF Pro.
- Change stored meta keys.
- Run database migrations.
- Rebuild the Video CPT in React.
- Replace WordPress admin conventions with a custom app.
- Resume original Phase 12/13/14 migration work before V4 is accepted.

## Implementation Principles

- Keep ACF optional.
- Use the same meta keys regardless of ACF state.
- Improve ACF UI and fallback UI in parallel.
- Prefer WordPress-native admin patterns.
- Keep enhancements scoped to `video_case_study` screens.
- Keep runtime selectors plugin-owned with `ommvs-*`.
- Add helper classes only where they reduce `OMMVS_Fields` bloat.
- Do not hide important WordPress controls editors already understand.
- Make validation helpful, not noisy.

## Phase V4-0: Baseline And Planning

### Goal

Lock the V4 branch and source-of-truth plan before code changes.

### Tasks

1. Confirm V3 is tested and accepted.
2. Create the V4 branch from the accepted V3 baseline.
3. Create this V4 master implementation plan.
4. Confirm original Phases 12-14 remain paused.
5. Confirm V4 work is admin-only unless a later phase explicitly says otherwise.

### Files

```text
docs/OMMVS-12-master-implementation-plan-v4.md
```

### Acceptance Criteria

- V4 branch exists.
- V4 plan is committed and pushed.
- Next implementation phase is V4-1.

## Phase V4-1: Define Admin UX Model And Field Grouping

### Goal

Define the exact editor workflow before changing field markup.

### Proposed Field Groups

Recommended Video Case Study edit structure:

1. **Readiness**
   - Active status.
   - Required data status.
   - Hash/modal link preview.
   - Vimeo URL status.
   - Data Health shortcut.

2. **Card Content**
   - Default Card Title.
   - Default Card Description.
   - Default Card Thumbnail.

3. **Modal Content**
   - Modal Title.
   - Video Category.
   - Modal Content editor.
   - CTA note that CTA is global.

4. **Video Source**
   - Vimeo Video URL.
   - Detected Vimeo ID preview.

5. **Related Card / Optional Media**
   - Related Thumbnail Override.

6. **Migration Notes**
   - Admin Notes.
   - Legacy popup/hash references if needed.

### Tasks

1. Decide whether the readiness panel should be a side metabox or a top notice-style panel.
2. Decide exact helper copy and labels.
3. Decide if ACF fields should use tabs, accordions, or plain grouped fields.
4. Define fallback UI section order to match ACF UI.

### Acceptance Criteria

- Editor workflow is agreed before implementation.
- ACF-active and ACF-inactive experiences will present the same conceptual sections.

## Phase V4-2: Improve ACF-Active Video CPT Field UI

### Goal

Make the ACF-rendered Video CPT fields clearer and more friendly without making ACF mandatory.

### Files To Modify

```text
includes/class-ommvs-fields.php
admin/css/one-minute-media-video-showcase-admin.css
```

Possibly:

```text
admin/js/one-minute-media-video-showcase-admin.js
```

### Planned Changes

1. Reorganize the ACF field group using ACF Free-compatible UI helpers:
   - Tabs or grouping fields where supported.
   - Clear section labels.
   - Better field instructions.
2. Use field wrappers to create cleaner two-column layouts where appropriate:
   - Hash Slug and Active.
   - Card Title and Card Description.
   - Card Thumbnail and Related Thumbnail Override.
3. Improve Modal Content instructions:
   - Tell editors to use normal headings, paragraphs, and lists.
   - Avoid pasted Elementor markup.
4. Improve Vimeo Video URL instructions:
   - Show accepted examples.
   - Mention only Vimeo URLs are accepted.
5. Add CSS scoped to the ACF field group on Video CPT edit screens:
   - Better spacing.
   - Softer section separation.
   - Cleaner media previews.
   - More readable instruction text.

### Boundaries

- Do not change meta keys.
- Do not change validation rules.
- Do not require ACF Pro.
- Do not move Video Category taxonomy into ACF.

### Acceptance Criteria

- With ACF active, the Video Case Study editor feels organized and guided.
- Fields still save to the same keys.
- ACF validation still rejects non-Vimeo URLs.
- Video Category single-select metabox still works.

## Phase V4-3: Redesign Plugin-Owned Fallback Video CPT UI

### Goal

Make the no-ACF fallback UI pleasant and production-ready instead of looking like a basic table fallback.

### Files To Modify

```text
includes/class-ommvs-fields.php
admin/css/one-minute-media-video-showcase-admin.css
admin/js/one-minute-media-video-showcase-admin.js
```

### Planned Changes

1. Replace the fallback `form-table` layout with plugin-owned section cards.
2. Match the same conceptual grouping from V4-1:
   - Readiness.
   - Card Content.
   - Modal Content.
   - Video Source.
   - Optional Media.
   - Migration Notes.
3. Improve fallback controls:
   - Large readable fields.
   - Better thumbnail preview layout.
   - Active toggle styled as a clear status control.
   - Modal Content editor with comfortable spacing.
4. Keep existing save/sanitize methods.
5. Keep accessibility:
   - Real labels.
   - Descriptions tied visually to fields.
   - Keyboard-operable buttons.

### Boundaries

- No schema change.
- No frontend change.
- No ACF dependency.

### Acceptance Criteria

- With ACF inactive, editors still get a polished Video CPT experience.
- Every field saves correctly.
- Thumbnail chooser/remover still works.
- Modal Content editor still saves rich text.

## Phase V4-4: Add Video Readiness Panel

### Goal

Give admins an at-a-glance summary of whether a Video Case Study is ready for page placement and frontend use.

### Proposed New Class

Consider adding:

```text
includes/class-ommvs-video-admin-ui.php
```

Responsibilities:

- Render a read-only readiness metabox.
- Centralize admin-only helper output.
- Avoid adding more UI-only logic to `OMMVS_Fields`.

### Planned Checks

Show compact statuses for:

- Active.
- Hash Slug.
- Default Card Title.
- Default Card Description.
- Default Card Thumbnail.
- Video Category.
- Modal Title.
- Modal Content.
- Vimeo Video URL.

Optional helpful output:

- Hash preview: `#nick-kyrgios`.
- Modal URL preview when permalink context is available.
- Detected Vimeo ID.
- Link to Data Health.

### Files To Modify

Likely:

```text
includes/class-ommvs-video-admin-ui.php
includes/class-one-minute-media-video-showcase.php
admin/css/one-minute-media-video-showcase-admin.css
```

### Acceptance Criteria

- Editors can quickly tell if a video is ready.
- Panel is read-only and does not duplicate save logic.
- Panel works with ACF active or inactive.

## Phase V4-5: Add Friendly Inline Admin Hints

### Goal

Reduce avoidable data-entry mistakes while editors are filling out a Video Case Study.

### Planned Enhancements

1. Hash Slug helper:
   - Show preview with leading `#`.
   - Warn if the editor types a leading `#`, while save logic still strips it.

2. Vimeo URL helper:
   - Show detected Vimeo ID when URL is valid.
   - Show a friendly warning for non-Vimeo URLs before save.

3. Thumbnail helper:
   - Show card thumbnail usage note.
   - Explain related thumbnail override is optional.

4. Modal Content helper:
   - Show a short authoring guide:
     - Use headings.
     - Use normal paragraphs.
     - Use normal bullet lists.

### Files To Modify

```text
admin/js/one-minute-media-video-showcase-admin.js
admin/css/one-minute-media-video-showcase-admin.css
includes/class-ommvs-fields.php
```

### Boundaries

- Client-side hints are advisory only.
- Server-side validation remains authoritative.
- Do not block saving beyond existing validation paths.

### Acceptance Criteria

- Editors get useful feedback before saving.
- No JavaScript errors when ACF is inactive.
- No JavaScript errors when ACF is active.

## Phase V4-6: Improve Video Case Studies List Table Workflow

### Goal

Make the Video Case Studies list table more useful for migration and ongoing maintenance.

### Current Columns

Already present:

- Thumbnail.
- Hash slug.
- Active status.
- Video Category.
- Vimeo URL status.
- Modal Content status.

### Planned Enhancements

1. Add optional admin filters:
   - Active / Inactive.
   - Video Category.
   - Missing required fields.
   - Invalid Vimeo URL.
2. Make status badges visually consistent with the readiness panel.
3. Consider row action shortcuts:
   - View Data Health.
   - Copy hash.
4. Keep native WordPress bulk edit behavior safe.

### Files To Modify

```text
includes/class-ommvs-cpt-video.php
admin/css/one-minute-media-video-showcase-admin.css
admin/js/one-minute-media-video-showcase-admin.js
```

### Acceptance Criteria

- Admins can find incomplete videos faster.
- Existing list columns remain useful.
- No public behavior changes.

## Phase V4-7: Admin Visual Polish And Accessibility Pass

### Goal

Make the final Video CPT admin UI cohesive, responsive, and accessible.

### Tasks

1. Review Video CPT editor at common admin widths:
   - Wide desktop.
   - Narrow WordPress admin column layout.
   - Small laptop.
2. Confirm metabox controls do not overflow.
3. Confirm all custom controls are keyboard-operable.
4. Confirm focus states are visible.
5. Confirm labels and descriptions are clear.
6. Confirm admin CSS is scoped and does not affect unrelated post types.

### Files To Modify

```text
admin/css/one-minute-media-video-showcase-admin.css
admin/js/one-minute-media-video-showcase-admin.js
```

### Acceptance Criteria

- Video CPT edit screen feels polished with ACF active.
- Video CPT edit screen feels polished with ACF inactive.
- Category inline creation remains reliable.
- No admin layout overflow.
- No unrelated admin screens are visually affected.

## Phase V4-8: Regression And Documentation

### Goal

Verify V4 admin improvements and document the approved editing workflow before resuming migration work.

### Test Plan

ACF active:

- Open Video Case Study editor.
- Confirm grouped ACF UI.
- Save all fields.
- Confirm single-category metabox works.
- Confirm inline category creation works.
- Confirm readiness panel reflects field state.

ACF inactive:

- Deactivate ACF on a test environment.
- Confirm fallback UI appears.
- Save all fields.
- Confirm media picker works.
- Confirm readiness panel reflects field state.

List table:

- Confirm columns and filters work.
- Confirm incomplete videos are easy to identify.

Frontend smoke test:

- `/brand-video-production/` grids still render.
- Card click opens modal.
- Direct hash opens modal.
- Modal content still renders.

### Documentation

Add or update a short editor-facing admin guide if needed:

```text
docs/OMMVS-13-video-cpt-admin-guide-v4.md
```

### Acceptance Criteria

- V4 admin UI is accepted.
- Original Phase 12 can resume after V4 passes regression.

## Proposed Implementation Order

1. V4-0 Baseline and planning.
2. V4-1 Define admin UX model and field grouping.
3. V4-2 Improve ACF-active Video CPT field UI.
4. V4-3 Redesign plugin-owned fallback Video CPT UI.
5. V4-4 Add Video Readiness panel.
6. V4-5 Add friendly inline admin hints.
7. V4-6 Improve list table workflow.
8. V4-7 Admin visual polish and accessibility pass.
9. V4-8 Regression and documentation.

## Key Risks

### ACF UI Customization Limits

ACF Free can render fields and some layout helpers, but it is not a full custom admin application. V4 should improve ACF field organization without fighting ACF too hard.

### Duplicate UI Between ACF And Fallback

Because ACF is optional, improvements must be mirrored conceptually in fallback UI. Shared helper methods and shared CSS tokens should be preferred where practical.

### Overloading `OMMVS_Fields`

`OMMVS_Fields` already owns a large amount of behavior. If V4 adds readiness panels or richer admin-only helpers, a dedicated `OMMVS_Video_Admin_UI` class is preferred.

### Admin CSS Leakage

Admin CSS must remain scoped to `video_case_study`, OMMVS settings, Data Health, and page placement screens.

### Migration Delay

V4 should improve admin usability without becoming an open-ended redesign. The goal is to support migration work, not pause migration indefinitely.

## Success Definition

V4 is successful when an admin can open a Video Case Study and immediately understand:

- What content is missing.
- What will appear on the card.
- What will appear in the modal.
- What category is assigned.
- Whether the Vimeo URL is valid.
- Whether the video is safe to place on a page.

The plugin should still behave exactly the same on the frontend after V4.
