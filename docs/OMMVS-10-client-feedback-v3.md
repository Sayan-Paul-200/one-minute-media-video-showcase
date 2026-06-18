# OMMVS-10: Client Feedback V3

## Purpose

This document records the client feedback received after V2 testing passed.

It supersedes the affected assumptions in:

- `docs/OMMVS-08-client-requirements-v2.md`
- `docs/OMMVS-09-master-implementation-plan-v2.md`
- the original migration continuation phases in `docs/OMMVS-03-master-implementation-plan.md`

The core architecture remains valid:

- Video Case Study CPT.
- Video Category taxonomy.
- Page-level Featured Videos and More Videos placements.
- Elementor `1MM Video Grid` widget.
- One shared footer modal.
- Page JSON.
- JavaScript-owned hash/modal behavior.

The new feedback is not a rebuild. It is a V3 correction layer for editor usability, frontend formatting, and direct-hash performance.

## Current Implementation Status

Completed and tested:

- Original Phases -1 through 11.
- V2 Phases 0 through 11.
- `/brand-video-production/` V2 regression testing.

Currently paused:

- Original Phase 12: Second migration page with shared videos.
- Original Phase 13: Batch migration.
- Original Phase 14: Final cleanup after all pages are migrated.

Pausing these phases remains the safest path. V3 changes affect the editorial model and the first migrated page's user experience. Continuing migration before V3 is accepted would multiply cleanup work across future migrated pages.

## Feedback Summary

### 1. Modal Content should be normal rich text, not pasted Elementor HTML

V2 supported pasted Elementor-like HTML because the first modal content was copied from legacy popups. The client now expects admins to use the Modal Content rich text editor normally.

The expected editor content is ordinary WordPress rich text:

```html
<h3>Production Overview</h3>
<p>First paragraph.</p>
<p>Second paragraph.</p>
<h3>1 Minute Media Creative</h3>
<ul>
  <li>Pre-Production</li>
  <li>Screen Design</li>
  <li>Editing</li>
</ul>
```

#### Current Issues

1. Blank-line-separated paragraphs entered in the rich editor can appear merged into one paragraph on the frontend.
2. Normal unordered lists do not match the legacy popup list style.
3. Later headings, such as `1 Minute Media Creative`, can sit too tightly against the preceding paragraph.

#### Root Cause

The current frontend data builder sends stored Modal Content after sanitization, but it does not consistently apply WordPress paragraph formatting before JSON output. If the editor stores plain text with blank lines instead of literal `<p>` tags, the frontend inserts it with `innerHTML`, and HTML collapses the whitespace.

The current CSS also styles Elementor icon-list markup more deliberately than normal WordPress `<ul><li>` markup. Normal lists therefore do not receive the teal check-list treatment from the legacy modal.

The current heading CSS gives headings a bottom margin but no reliable top spacing when a heading follows paragraph content.

#### Required Behavior

- Modal Content should be authored using the normal Visual/Text rich editor.
- Paragraph breaks created by blank lines must render as separate frontend paragraphs.
- Normal unordered lists should render like the legacy creative bullet list.
- Normal ordered lists should remain readable and should not be converted into check lists.
- H3/H4 headings inside Modal Content should have enough spacing above them when they follow body copy or lists.
- Legacy Elementor HTML can remain tolerated, but it should no longer be the primary expected content structure.

### 2. Video Category selection must allow at most one category

The current WordPress taxonomy metabox allows multiple categories because `ommvs_video_category` is registered as a hierarchical taxonomy, which renders checkboxes.

#### Required Behavior

- A Video Case Study may have zero categories.
- A Video Case Study may have one category.
- A Video Case Study must not be assigned multiple categories through the normal edit screen.
- Existing records that already have multiple categories should not break; Data Health can continue to warn until they are corrected.

### 3. Page placement metabox improvements

The `1MM Video Showcase` page metabox needs two improvements.

#### 3a. Placement selector labels

The placement selector currently displays the Video CPT post title. The client wants each row to display the `Default Card Title` custom field value.

Required behavior:

- Select option display text should use `Default Card Title`.
- If Default Card Title is empty, fallback to the Video CPT post title.
- If both are empty, fallback to a safe `Video #{id}` label.
- Search should remain useful. Prefer searching by displayed Default Card Title, with optional support for hidden post-title matching if needed.

#### 3b. Smooth scroll after Add Video

When a long Featured Videos or More Videos list already exists, clicking `Add Video` appends the new row at the end, but the admin may not see it.

Required behavior:

- After adding a row, scroll the new row into view when it is outside the current viewport.
- The behavior should work for both Featured Videos and More Videos.
- The new select should remain searchable after it is added.
- The new row should ideally receive focus so the editor knows where to continue.

### 4. Elementor Video Grid widget improvements

#### 4a. Empty message visibility

The current widget can show an empty message on the frontend when no page placements exist. This is useful for admins but poor for public visitors.

Required behavior:

- Public visitors should not see an empty message.
- Public visitors should not see an empty grid wrapper when no videos exist.
- Admins/editors should still see the empty message in Elementor editor and, where useful, while logged in with edit permissions.
- Empty grids should not unnecessarily require the modal, page JSON, or public modal behavior for visitors.

#### 4b. Border radius on video cards

The Elementor border-radius control currently affects both the full card and the media wrapper. This makes the thumbnail image rounded on the bottom corners, which is not the intended card shape.

Required behavior:

- Border radius should apply to the full video card.
- The thumbnail/media area should only have rounded top corners.
- The bottom corners of the thumbnail/media area should stay square so it connects cleanly to the card body.

### 5. Direct hash modal opening must be faster

In incognito testing, directly opening a URL such as:

```text
https://test61.autocomputation.com/brand-video-production/#nick-kyrgios
```

can take around 20 seconds before the modal appears.

Required behavior:

- Direct hash modal opening should complete within 5 seconds after page load under normal staging conditions.
- Existing hash URLs must remain unchanged.
- The modal should open from the hash as soon as the required page JSON, modal markup, and controller are available.
- The fix should not break click-to-open behavior, related-card switching, focus handling, or iframe cleanup.

#### Likely Root Cause

The current public controller is registered as a footer script with a `jquery` dependency even though the controller is plain JavaScript and does not use jQuery. If optimization plugins defer, delay, or reorder jQuery, the OMMVS controller can be delayed too.

The modal JSON and markup are output in `wp_footer`, and hash opening waits for the public controller to execute. A direct hash cannot be opened by PHP because URL fragments are never sent to the server.

The root fix should reduce dependency delay, protect the OMMVS controller from common script optimization delays where practical, and add timing diagnostics so the team can confirm whether the delay is script execution, page rendering, or Vimeo iframe loading.

## Revised Content Contract

### Modal Content

Admins should enter normal rich text:

- Headings: `h3` or `h4`.
- Paragraphs: normal paragraph blocks/blank lines.
- Lists: normal unordered list toolbar button.
- Optional links/media where needed.

The plugin should normalize this into safe frontend HTML using WordPress formatting rules.

### Video Category

Category remains optional but single-select:

- No category: allowed, but may be warned by Data Health if the video is intended for migration.
- One category: expected normal case.
- Multiple categories: prevented in the edit screen and warned by diagnostics if legacy data exists.

### Placement Selector

The editor-facing label is the Default Card Title, because that is the visible frontend card title and is easier for admins to match to the page.

## Non-Goals For V3

- Do not rebuild the plugin architecture.
- Do not reintroduce Elementor popup-per-video behavior.
- Do not change the public hash format.
- Do not add non-Vimeo providers.
- Do not start additional page migration before V3 regression passes.
- Do not remove legacy Elementor popups globally.
- Do not make Video Category mandatory at save time; no category is allowed.

## Acceptance Criteria

V3 is accepted when:

- Normal rich-text Modal Content renders paragraphs separately.
- Normal unordered lists match the legacy teal check-list direction.
- Modal Content headings have correct vertical spacing.
- Video Category edit UI allows zero or one selected category.
- Page placement rows display Default Card Title labels.
- Add Video scrolls the newly appended row into view.
- Empty Video Grid messages are visible only to admins/editors, not public visitors.
- Video Grid border radius no longer rounds the bottom corners of thumbnails.
- Direct hash modal opening is consistently under 5 seconds in staging tests, or any external optimization blocker is clearly identified with a documented exclusion.
- `/brand-video-production/` still passes V2 behavior after V3 changes.

