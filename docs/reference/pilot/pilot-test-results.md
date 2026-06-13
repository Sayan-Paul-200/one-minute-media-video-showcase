# Pilot Test Results — Brand Video Production Page

This document records the successful pilot implementation and test results for the temporary related-video solution on the staging site.

## Test environment

Production site:

```txt
https://www.1minutemedia.com.au/
```

Testing/staging site:

```txt
https://test61.autocomputation.com/
```

Pilot page:

```txt
/brand-video-production/
```

Full pilot URL:

```txt
https://test61.autocomputation.com/brand-video-production/
```

## Purpose of the pilot

The pilot was built to validate the client's core requirement before rebuilding the system as a custom plugin.

The main question was:

```txt
Can the same static Elementor popup show different related videos depending on the page where it was opened?
```

The pilot proved that this is possible.

The temporary solution used client-side JavaScript to detect the current page's first six featured videos and dynamically replace the three related videos inside the currently opened Elementor popup.

## Current legacy system during the pilot

During the pilot, the site still used the existing static system:

- Static Elementor pages.
- Static Elementor video-card sections.
- One Elementor popup per video.
- Existing popup templates with static main video content.
- Existing related-video areas inside each popup.
- Existing old per-popup inline scripts in some popup HTML widgets.
- Existing old global footer hash script from Scripts Inserter.

The pilot did not remove the old system. It layered a central dynamic script on top of it.

## Key legacy scripts involved

### Old global footer hash script

The old global footer script handled:

```txt
URL hash → open Elementor popup
Elementor popup show → update URL hash
Elementor popup hide → clear URL hash
```

Example:

```txt
/brand-video-production/#nick-kyrgios
→ opens Elementor popup 22840
```

During the pilot, this old script remained active.

### Old inline popup scripts

Some popups contained an old HTML widget script that:

- Closed the current popup when `.popup-f` was clicked.
- Used IDs such as `click-a`, `click-b`, and `click-c`.
- Replaced one hash with another hash during related-video clicks.

These old inline scripts were not removed during the first pilot stage.

The new pilot script used capture-phase click interception to prevent these old inline scripts from hijacking the new dynamic related-video behavior.

## Pilot classes added to the page

The pilot required temporary classes on the static Elementor page.

### First featured video section

The parent section containing the first six featured videos received:

```txt
js-featured-video-source
```

Each of the six featured video-card columns received:

```txt
js-video-card
```

These classes allowed the pilot script to discover the current page's first six featured videos.

### Popup related-video areas

Each popup that needed dynamic related videos received:

```txt
js-popup-related-list
js-popup-related-card
js-popup-related-image
js-popup-related-button
```

These classes allowed the pilot script to replace the related video image, caption, and button target.

## Featured Videos list tested on the pilot page

On `/brand-video-production/`, the first six featured videos were tested as the source list.

The known first-six source list was:

```txt
1. Tennis Majors / Nick Kyrgios
2. FleetPartners
3. The Langham Hotels
4. Whiskey & Wealth
5. Heinemann
6. Australian Financial Review
```

The related-video logic was expected to use this list only.

## More Videos section tested

The second section on the page was also tested.

This section appears under:

```txt
#more-samples
```

Examples of second-section videos included:

```txt
- OzHarvest
- Ippudo International
- UNSW Aviation
- Professor Justin Yurbery / Affirm Press
- AI Media
- The Kids' Cancer Project
- P&O Carnival Cruises
- 1 Minute Media Animation Showreel
- triSearch
- Medmate
- 1 Minute Media Showreel
```

The second-section video cards did not become the source of related videos.

For any video opened from the second section, the expected related videos were the first three videos from the first Featured Videos section.

## Expected related-video logic tested

Given the Featured Videos list:

```txt
A = Nick Kyrgios
B = FleetPartners
C = The Langham Hotels
D = Whiskey & Wealth
E = Heinemann
F = Australian Financial Review
```

Expected behavior:

```txt
Open A → related: B, C, D
Open B → related: A, C, D
Open C → related: A, B, D
Open D → related: A, B, C
Open E → related: A, B, C
Open F → related: A, B, C
Open any second-section video → related: A, B, C
```

## Test result summary

The pilot passed the core functional tests.

Confirmed working:

```txt
First-section video cards opened the correct popup.
Second-section video cards opened the correct popup.
Related videos dynamically changed based on the first six featured videos on the current page.
Related videos excluded the current video when the current video was in the featured list.
Videos outside the first-six featured list showed the first three featured videos as related videos.
Related-video cards inside a popup opened the correct next popup.
The same popup could show page-context related videos.
Direct hash URLs continued to work with the old global hash script active.
Popup close scroll-jumping was fixed in the final pilot script.
```

## Direct hash URL test

The old global hash script and the new pilot script worked together.

Expected flow:

```txt
User opens /brand-video-production/#nick-kyrgios
Page loads
Old global hash script opens popup 22840
New pilot script detects Elementor popup show event
New pilot script populates related videos from the current page's first-six Featured Videos list
```

This behavior was accepted as the temporary bridge solution.

## Scroll-jump issue and fix

During testing, one issue was detected:

```txt
Closing a popup caused irregular auto-scrolling.
```

Observed behavior:

```txt
Closing a popup triggered from the first section caused the page to scroll downward.
Closing a popup triggered from the second section caused the page to scroll upward.
```

Root cause:

```txt
Duplicate popup close/hash handling combined with Elementor/browser focus restoration.
```

Both the old global hash script and the new pilot script were managing hash/close behavior. Elementor also attempted to restore focus to the original trigger element when the modal closed.

Fix applied:

```txt
The pilot script stopped owning normal hash show/hide behavior while the old global script remained active.
The pilot script remembered scroll position before popup opening.
The pilot script restored scroll position after popup close.
The restoration ran multiple times across a short interval to overcome delayed focus restoration.
```

After the fix, popup close behavior was stable.

## Temporary dependencies during the pilot

The successful pilot depended on:

```txt
Old global footer popupHashMap script remaining active.
Existing Elementor popup templates remaining active.
Manual classes added to Elementor sections/cards/popups.
Pilot JavaScript running after the old global hash script.
```

These are acceptable for the temporary rollout but should not be part of the final plugin architecture.

## What the pilot proved

The pilot proved these important business and technical points:

1. Page-specific related videos are possible.
2. The related list must be based on the current page's first-six Featured Videos list.
3. Related videos cannot be stored statically inside reusable popups.
4. The same video popup can show different related videos depending on the page context.
5. Hash-based URLs can still be preserved.
6. One centralized logic layer is superior to many per-popup scripts.
7. The current static Elementor system is too labor-intensive for 20+ pages and 100+ popups.

## Why the pilot is not the final architecture

Although the pilot worked, it still required:

```txt
Manual class additions on many pages.
Manual class additions inside many popups.
Existing Elementor popups for every video.
Old global hash maps.
Old per-popup inline scripts in some cases.
Static Elementor content duplication.
```

This is why the final implementation must move to:

```txt
Video CPT
Page-level video placements
Custom Elementor grid widget
Single auto-rendered modal
Page JSON data
Plugin-owned hash/modal controller
```

## Final conclusion

The pilot successfully validated the client's requirement.

The next implementation should not extend the static popup approach further. It should use the pilot as behavioral proof and rebuild the system as a proper custom plugin.
