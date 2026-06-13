# Current Pages With Video Card Sections

This document records the current known page groups that contain video-card sections on the 1 Minute Media website.

Production site:

```txt
https://www.1minutemedia.com.au/
```

Testing/staging site:

```txt
https://test61.autocomputation.com/
```

## Important note

This list is based on the current Services and Industries submenu structure observed during planning.

The final plugin must not hardcode this list.

The list may change in the future:

```txt
New pages may be added.
Existing pages may be removed.
Some pages may stop using video grids.
Some pages may start using video grids later.
```

The plugin should work on any page where the editor configures video placements and inserts the custom Elementor video-grid widget.

---

## Services pages

The following Services submenu pages are currently understood to contain video-card sections.

```txt
1. Brand Videos
2. Animated Videos
3. Training Videos
4. Explainer Videos
5. Case Studies & Interviews
6. TVC & Broadcast
7. Product Videos
8. Event Videos
9. Motion Graphics
10. Social Media
```

### Expected URL patterns

Exact production URLs should be verified during migration.

Likely examples include:

```txt
/brand-video-production/
/animated-video-production/
/training-video-production/
/explainer-video-production/
/case-study-video-production/
/tvc-broadcast-video-production/
/product-video-production/
/event-video-production/
/motion-graphics/
/social-media-video-production/
```

These URL patterns are examples only. The agent/developer must verify actual page slugs in WordPress.

---

## Industries pages

The following Industries submenu pages are currently understood to contain video-card sections.

```txt
1. Healthcare & Medical Video Production
2. Government, NGO & NFP Video Production
3. Saas and Business Video Production
4. Finance & Legal Video Production
5. Recruitment Video Production
6. Retail Video Production
7. Food & Beverage Video Production
8. Design and Fashion Videos
```

### Expected URL patterns

Exact production URLs should be verified during migration.

Likely examples include:

```txt
/healthcare-medical-video-production/
/government-ngo-nfp-video-production/
/saas-business-video-production/
/finance-legal-video-production/
/recruitment-video-production/
/retail-video-production/
/food-beverage-video-production/
/design-and-fashion-video-production/
```

These URL patterns are examples only. The agent/developer must verify actual page slugs in WordPress.

---

## Known pilot/test pages

### Brand Video Production

Path:

```txt
/brand-video-production/
```

Purpose:

```txt
Main pilot page for the temporary related-video rollout.
Recommended first page for plugin migration.
```

Known behavior:

```txt
Has a first Featured Videos section containing six video cards.
Has a second More Samples / More Videos section.
Temporary rollout was tested across both sections.
Direct hash behavior was tested.
Scroll restoration issue was identified and fixed.
```

### Design and Fashion Videos

Path:

```txt
/design-and-fashion-video-production/
```

Purpose:

```txt
Previously considered/tested for pilot work.
Useful as a later validation page because it may share videos with other pages while having a different page-specific Featured Videos list.
```

---

## Page structure expectation

Most video pages follow this pattern:

```txt
Section 1: Featured Videos
- Usually six video cards.
- Always the source for related-video logic.

Section 2: More Videos / More Samples
- Optional.
- May contain many additional video cards.
- Must not be used as the related-video source.
```

Some pages may not have a second section.

The final plugin must support both cases.

---

## Featured Videos requirement

For each page using the new plugin, the page should have a page-level ordered Featured Videos list.

Recommended validation:

```txt
Maximum featured videos: 6
Recommended featured videos: exactly 6
Minimum for frontend fallback: 1-3, depending on design decision
```

The related-video rule works with fewer than six, but the current design expects six featured cards.

---

## More Videos requirement

The More Videos section is optional.

When present, it should be page-specific and manually ordered by the editor.

More Videos can contain any reusable Video CPT entries that are not already used in the Featured Videos list on the same page.

Recommended validation:

```txt
Do not allow the same video to appear twice on the same page across Featured Videos and More Videos.
```

---

## Migration priority

Recommended migration order:

```txt
1. Brand Video Production
2. One page where a shared video appears but the Featured Videos list differs
3. One page with no More Videos section
4. One page with a large More Videos section
5. Remaining Services pages in small batches
6. Remaining Industries pages in small batches
```

---

## Future-proofing requirement

The plugin must not rely on the navigation menu to decide which pages use videos.

Instead, the plugin should rely on:

```txt
Page-level video placement fields
Presence of the custom Elementor video-grid widget
Auto-render modal when required
```
