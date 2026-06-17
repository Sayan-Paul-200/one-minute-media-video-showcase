# OMMVS-08: Client Requirements V2

## Purpose

This document records the urgent client requirement changes received after Phases -1 through 11 of the original implementation plan were completed and tested.

It supersedes the affected assumptions in:

- `docs/OMMVS-02-client-requirements.md`
- `docs/OMMVS-03-master-implementation-plan.md`
- `docs/OMMVS-07-phase-11-1-brand-video-data-prep.md`

The existing architecture remains valid: Video Case Study CPT, page-level Featured/More placements, Elementor Video Grid widget, one footer modal, page JSON, and JavaScript-owned hash/modal behavior.

The data model and admin UI now need a V2 adjustment before migrating more pages.

## Implementation Status At Time Of Change

Completed and tested:

- Phase -1: Documentation and reference audit.
- Phase 0: Scaffold foundation.
- Phase 1: Video Case Study CPT.
- Phase 2: Field system and data model.
- Phase 3: Related-video engine.
- Phase 4: Page data builder.
- Phase 5: Asset system.
- Phase 6: Elementor Video Grid integration.
- Phase 7: Footer modal and page JSON.
- Phase 8: Frontend JavaScript modal controller.
- Phase 9: Frontend CSS.
- Phase 10: Admin UX and validation improvements.
- Phase 11: First migration page for `/brand-video-production/`.

Paused:

- Phase 12: Second migration page with shared videos.
- Phase 13: Batch migration.
- Phase 14: Final cleanup after all pages are migrated.

Pausing Phases 12-14 is recommended and safe. Those phases depend on the final data model and frontend contract, and continuing them before the V2 changes would create avoidable rework in every migrated page.

## Requirement Change Summary

### 1. Video category taxonomy is required

The legacy modal screenshot showed:

```text
Tennis Majors
Brand, TV Ad
```

The previous interpretation treated both lines as the modal title. The corrected interpretation is:

- `Tennis Majors` is the modal title.
- `Brand, TV Ad` is the video category.

The plugin must add a taxonomy for Video Case Studies and render the selected category in modal and related-card UI.

### 2. Production Overview is not global or split into constants

The previous model assumed:

- `Production Overview` label was global.
- `1 Minute Media Creative` label was global.
- Creative bullet list was global.
- Per-video field contained only overview body text.

The corrected requirement is:

- Everything from the `Production Overview` heading through the creative bullet list belongs to the individual video.
- This content must come from one rich `Modal Content` custom field.
- The field should behave like a WordPress content editor, with Visual/Code tabs and media buttons where possible.
- The CTA button remains a global/constant setting.

### 3. Vimeo-only video URL field

The client only needs Vimeo.

The previous provider model had:

- Video Provider
- Video ID
- Video URL

The corrected editor experience should have one video input:

```text
Vimeo Video URL
```

The field must accept Vimeo URLs only. The plugin should extract the Vimeo ID internally for iframe rendering.

### 4. Remove WATCH NOW from modal related cards

The modal related-card overlay text/button `WATCH NOW` should be removed.

The whole related card can remain clickable, but the visible label should not appear over the thumbnail.

### 5. Related cards use modal title and category

Related video cards inside the modal must display:

```text
Modal Title
Video Category
```

They should not use the Default Card Title as their primary label.

The Default Card Title remains the card-grid title used by the Elementor Video Grid.

### 6. Featured Videos are no longer capped at 6

The previous model capped Featured Videos at 6 rows and warned when a migrated page had fewer than 6 Featured Videos.

The corrected requirement is:

- Featured Videos can contain any number of videos.
- More Videos can still contain any number of videos.
- Related videos still come from the current page's ordered Featured Videos list.
- The modal should still display the configured related-card count, currently 3, unless the client separately requests a different count.

### 7. Page placement video selector must be searchable

The current page metabox uses a native select dropdown for each placement row.

The corrected admin requirement is:

- Each placement row needs a searchable video selector.
- Search should be by Video Case Study post title.
- The UI must remain easy for non-technical page editors.

## Revised Data Model

### Video Case Study CPT

The Video Case Study remains the reusable content source.

Required editorial data:

```text
Title
Hash Slug
Active status
Default Card Title
Default Card Description
Default Card Thumbnail
Video Category
Modal Title
Modal Content
Vimeo Video URL
Related Thumbnail Override, optional
Admin Notes, optional
```

### Video Category Taxonomy

Create a plugin-owned taxonomy for `video_case_study`.

Recommended taxonomy slug:

```text
ommvs_video_category
```

Recommended labels:

```text
Video Categories
Video Category
```

Frontend display expectation:

- The modal displays the category under the modal title.
- Modal related cards display the category under the modal title.
- If multiple terms are assigned, the plugin should render the first stable term and Data Health should warn that multiple categories create ambiguity.
- If no category is assigned, Data Health should warn.

### Modal Content Field

Client-facing label:

```text
Modal Content
```

This replaces the client-facing `Production Overview` field.

Recommended compatibility approach:

- Keep the existing internal meta key `ommvs_modal_overview` during the V2 transition to avoid deleting or losing existing Phase 11 data.
- Add a code-level alias/comment so new code treats this field as Modal Content.
- Update all labels, validation messages, Data Health output, and frontend JSON names to use `Modal Content`.

Modal Content should contain the full left-column rich content, for example:

```html
<h3>Production Overview</h3>
<p>...</p>
<h3>1 Minute Media Creative</h3>
<ul>
  <li>Pre-Production</li>
  <li>Screen Design</li>
  <li>Editing</li>
</ul>
```

The plugin should render this field as trusted admin-authored WordPress HTML, sanitized through WordPress HTML rules.

### Vimeo URL Field

Client-facing label:

```text
Vimeo Video URL
```

Recommended compatibility approach:

- Reuse the existing `ommvs_video_url` meta key.
- Deprecate and hide `ommvs_video_provider` and `ommvs_video_id` from the editor.
- Existing provider/ID data can be converted manually or by a small migration helper if needed.

Accepted examples:

```text
https://vimeo.com/879662317
https://vimeo.com/879662317?fl=pl&fe=cm
https://player.vimeo.com/video/879662317
```

Rejected examples:

```text
https://www.youtube.com/watch?v=E_TGT4gc_ng
https://example.com/video.mp4
879662317
```

The editor should store the full Vimeo URL. The plugin can derive the numeric Vimeo ID for the iframe.

### Global Settings

Keep:

- CTA button text
- CTA button URL
- Modal fallback thumbnail

Deprecate or remove from the active settings UI:

- Production Overview label
- Creative section title
- Creative bullet list

Saved values for deprecated settings may remain in the database temporarily but must not drive the new frontend modal content.

## Revised Frontend Behavior

### Modal Header

The modal header should render:

```text
Modal Title
Video Category
```

Example:

```text
Tennis Majors
Brand, TV Ad
```

### Modal Body

The modal body should render:

- The video iframe from the Vimeo Video URL.
- The rich Modal Content field.
- The global CTA button.
- The related cards generated from current-page Featured Videos.

The modal body should not assemble Production Overview and Creative bullet sections from global settings.

### Related Cards

Related cards should render:

- Related thumbnail.
- Modal Title.
- Video Category.

They should not render:

- `WATCH NOW` overlay text.
- Default Card Title as the main related-card label.

### Video Iframe

The modal should build only Vimeo iframes.

The current Vimeo embed target remains:

```text
https://player.vimeo.com/video/{vimeoId}?autoplay=1&playsinline=1&autopause=0&title=0&portrait=0&byline=0
```

If a valid Vimeo URL or ID cannot be derived, the plugin should not render a broken iframe.

## Revised Admin Behavior

### Video Edit Screen

The editor should see:

- Hash Slug
- Active
- Default Card Title
- Default Card Description
- Default Card Thumbnail
- Video Category
- Modal Title
- Modal Content rich editor
- Vimeo Video URL
- Related Thumbnail Override
- Admin Notes

The editor should not see:

- Video Provider
- Video ID
- Direct URL provider options
- YouTube provider options

### Page Edit Screen

The `1MM Video Showcase` page metabox should support:

- Unlimited Featured Videos.
- Unlimited More Videos.
- Searchable video selector in each row.
- Drag reorder.
- Remove row.

The editor should not see a six-video limit notice.

### Data Health

Data Health should be updated to report:

- Missing Video Category.
- Missing Modal Content.
- Invalid Vimeo Video URL.
- Duplicate hashes.
- Duplicate page placements.
- Multiple categories on one video, if the frontend displays only one.

Data Health should stop reporting:

- Missing Video Provider.
- Missing Video ID.
- Missing Direct URL.
- Pages with fewer than 6 Featured Videos.

## Migration Requirements

The already migrated `/brand-video-production/` page should become the regression page for V2.

For existing Video Case Studies:

1. Split any old modal title containing category text into:
   - Modal Title
   - Video Category
2. Convert any existing Vimeo Provider + Video ID data into a Vimeo Video URL.
3. Move or rewrite the left-column modal content into the Modal Content editor.
4. Assign exactly one Video Category where practical.
5. Run Data Health and resolve all V2 warnings.

Known corrected example:

```text
Old modal title: Tennis Majors<br>Brand, TV Ad
New modal title: Tennis Majors
Video category: Brand, TV Ad
Vimeo Video URL: https://vimeo.com/879662317
```

## Non-Goals For V2

- Do not restart the plugin architecture.
- Do not reintroduce Elementor popup-per-video behavior.
- Do not add YouTube support unless the client re-requests it.
- Do not migrate additional pages before V2 is stable.
- Do not remove legacy Elementor popups globally until the final cleanup phase.
- Do not change URL hash behavior.
- Do not change the approved related-video source rule: related videos come from the current page's Featured Videos list.

## Acceptance Criteria

V2 is accepted when:

- A Video Case Study can store a category, modal title, rich modal content, and Vimeo URL.
- The client no longer needs to select provider or manually enter a video ID.
- Featured Videos can exceed 6 rows and still render correctly.
- Page placement selectors are searchable by Video CPT title.
- Modal displays modal title and category separately.
- Modal content is rendered from the per-video Modal Content field.
- Related cards show modal title and category.
- Related cards no longer show `WATCH NOW`.
- `/brand-video-production/` still supports existing hashes like `/brand-video-production/#nick-kyrgios`.
- Data Health reflects the new V2 model.
- Existing unmigrated/static pages continue to work until intentionally replaced.
