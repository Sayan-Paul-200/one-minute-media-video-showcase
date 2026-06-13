# Temporary Rollout Class Names

This document lists the CSS classes used during the temporary static Elementor rollout and pilot testing.

These classes were used to bridge the old static Elementor architecture with a new centralized JavaScript behavior.

They are not intended to be required in the final CPT + Elementor widget + single modal plugin architecture.

## Important distinction

Temporary rollout classes:

```txt
Used only for the old static Elementor pages and existing Elementor popups.
```

Final plugin architecture:

```txt
Should not require editors to manually add these classes across pages/popups.
```

The final plugin should render its own markup through the custom Elementor widget and modal renderer.

---

## Page-level source classes

### Featured video source section

Class:

```txt
js-featured-video-source
```

Applied to:

```txt
The parent Elementor section containing the first six featured video cards on a page.
```

Purpose:

```txt
Allows the temporary rollout script to identify the current page's first-six Featured Videos list.
```

Important rule:

```txt
Only the first Featured Videos section should receive this class.
The second More Videos section must not receive this class.
```

Why:

```txt
The first-six Featured Videos list is the source of related-video logic.
The More Videos section is not the source of related videos.
```

---

### Featured video card column

Class:

```txt
js-video-card
```

Applied to:

```txt
Each of the six featured video-card Elementor columns inside the .js-featured-video-source section.
```

Purpose:

```txt
Allows the temporary rollout script to collect the six video cards in order.
```

The script extracts:

```txt
Popup ID from the Elementor popup-open link.
Title from the card heading.
Thumbnail from the card image/background.
Hash from optional data attributes, fallback map, button ID, or generated title slug.
```

Important rule:

```txt
Do not add js-video-card to the second More Videos section during the temporary rollout.
```

---

## Optional override attributes for temporary rollout

The temporary rollout script can auto-detect most data from the card markup.

However, these attributes may be added to a featured video-card column when detection fails.

### Popup ID override

Attribute:

```txt
data-popup-id
```

Example Elementor custom attribute:

```txt
data-popup-id|22840
```

Use when:

```txt
The card has no normal Elementor popup-open link.
The card has multiple popup links.
The script detects the wrong popup ID.
```

### Popup hash override

Attribute:

```txt
data-popup-hash
```

Example Elementor custom attribute:

```txt
data-popup-hash|#nick-kyrgios
```

Use when:

```txt
The hash is missing from the map.
The generated fallback hash does not match the legacy URL hash.
The button ID is not useful.
```

### Video title override

Attribute:

```txt
data-video-title
```

Example Elementor custom attribute:

```txt
data-video-title|Tennis Majors With Nick Kyrgios, TV Ad
```

Use when:

```txt
The script cannot reliably extract the card title from the heading markup.
```

### Video thumbnail override

Attribute:

```txt
data-video-thumb
```

Example Elementor custom attribute:

```txt
data-video-thumb|https://example.com/wp-content/uploads/video-thumb.jpg
```

Use when:

```txt
The script cannot reliably extract the thumbnail from an image tag or background image.
```

---

## Popup related-video classes

These classes were added inside existing Elementor popups.

Any popup that needed dynamic related videos had to receive these classes.

### Related list wrapper

Class:

```txt
js-popup-related-list
```

Applied to:

```txt
The Elementor inner section that contains the three related video cards inside the popup.
```

Purpose:

```txt
Defines the related-video area that the temporary script should update.
```

---

### Related card column

Class:

```txt
js-popup-related-card
```

Applied to:

```txt
Each related-video card column inside the related-video inner section.
```

Expected count:

```txt
3 related card columns.
```

Purpose:

```txt
Defines each replaceable related-video slot.
```

---

### Related image widget

Class:

```txt
js-popup-related-image
```

Applied to:

```txt
Each related-video Elementor Image widget inside a related card column.
```

Purpose:

```txt
Allows the script to replace the related thumbnail and image caption.
```

The script updates:

```txt
img src
img alt
figcaption text
related popup data attributes
```

It also removes old `srcset`/`sizes` values to prevent the previous responsive image from overriding the new image.

---

### Related button widget

Class:

```txt
js-popup-related-button
```

Applied to:

```txt
Each related-video Elementor Button widget inside a related card column.
```

Purpose:

```txt
Allows the script to replace the button target popup and intercept related-video navigation.
```

The script updates:

```txt
Button link href
Data attributes for target popup ID/hash
ARIA label
```

---

## Old classes/IDs still present in legacy popups

Some legacy popups also had:

```txt
popup-f
click-a
click-b
click-c
```

These belonged to the old per-popup inline scripts.

During the pilot, the new script did not rely on these old IDs/classes.

The new script intercepted related-card clicks in capture phase to prevent old inline scripts from overriding dynamic related-video behavior.

---

## Temporary rollout behavior

For a popup to be dynamically updated during the temporary rollout:

```txt
The page must have .js-featured-video-source and six .js-video-card columns.
The opened popup must have .js-popup-related-list.
The popup's related slots must have .js-popup-related-card.
Each related card should have .js-popup-related-image and .js-popup-related-button.
```

If a popup does not have the related classes:

```txt
The popup still opens.
Its related videos remain static/hardcoded.
The temporary script logs that no dynamic related list was found.
```

---

## Final plugin replacement

The final plugin should replace the need for these classes.

Final plugin classes should be plugin-owned and rendered automatically, for example:

```txt
ommvs-video-grid
ommvs-video-card
ommvs-modal
ommvs-modal-related-card
```

Editors should not need to manually add technical classes to Elementor sections or popups after migration.

---

## Migration note

These temporary classes are useful only during the coexistence period.

Old/static pages may continue using them while migration is in progress.

New/plugin-powered pages should use:

```txt
1MM Video Grid Elementor widget
Auto-rendered footer modal
Plugin-rendered markup
Plugin-owned frontend JavaScript
```
