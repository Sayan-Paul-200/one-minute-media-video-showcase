# OMMVS Phase 11.1 - Brand Video Production Data Prep

This document is the staging-entry worksheet for preparing reusable Video Case Study records for `/brand-video-production/`.

Phase 11.1 is a content migration step. It should be completed in WordPress admin on staging after deploying the current plugin build with Video CPT fields, admin columns, fallback fields, and Data Health.

## Entry Rules

- Create or update one `video_case_study` post per legacy popup/video.
- Search by title and hash before creating a new post.
- Store hash slugs without the leading `#`.
- Preserve legacy hash case and special characters exactly.
- Keep videos Draft or Inactive until all required modal/video fields are verified from the legacy Elementor popup.
- Set the native Featured Image to the same image used for Default Card Thumbnail where possible.
- Add `Legacy popup ID: {id}` to Admin Notes.
- Do not configure page placements in this phase.
- Do not replace static Elementor sections in this phase.

## Required Field Map

Each Video Case Study must have:

- WP post title: visible card title from `/brand-video-production/`.
- Hash Slug: legacy hash without `#`.
- Active: enabled only after strict verification.
- Default Card Title: visible card title.
- Default Card Description: visible card description.
- Default Card Thumbnail: matching legacy card/popup image from Media Library.
- Modal Title: copied from the legacy Elementor popup.
- Production Overview: copied from the legacy Elementor popup.
- Video Provider: `vimeo`, `youtube`, or `url`.
- Video ID or Video URL: copied from the legacy Elementor popup.
- Admin Notes: legacy popup ID and verification notes.

## Featured Videos

These six videos become the Featured Videos source list in Phase 11.2.

| Order | Popup ID | Hash Slug | Post Title / Default Card Title | Default Card Description | Modal / Video Verification |
|---:|---:|---|---|---|---|
| 1 | 22840 | `nick-kyrgios` | Tennis Majors With Nick Kyrgios, TV Ad | The King was in the house last Summer! Our International Ads filled the grandstands for the showdown in LAX UTS. | Verified from `existing-elementor-popup-sample.html`: modal title `Tennis Majors<br>Brand, TV Ad`; provider `vimeo`; video ID `879662317`; overview below. |
| 2 | 24544 | `fleetpartners` | FleetPartners, Brand Video | ASX-listed FleetPartners moves 88,000+ vehicles across ANZ each year. We keep their entire network engaged through an extensive range of video products. | Verify legacy popup before activating. |
| 3 | 24520 | `the-langham-hotels` | The Langham Hotels, Brand Video | When a luxury international resort requires first class staff, our brand videos attract excellent recruits. | Verify legacy popup before activating. |
| 4 | 36282 | `Whiskey&Wealth` | Whiskey & Wealth: The Spirit of Investment | Whiskey & Wealth needed to attract investors and talent. Our video assets have become the defining expression of their brand, driving global growth. | Verify legacy popup before activating. Preserve hash exactly. |
| 5 | 24541 | `heinemann` | Heinemann, Brand Video | Billion dollar global retailer, Heinemann, engaged our team for a series of branded recruitment videos that brought applicants from around the world. | Verify legacy popup before activating. |
| 6 | 24538 | `afr` | Australian Financial Review, Brand Video | We partnered with the AFR for a nationwide series to launch their Business Success Subscriptions! | Verify legacy popup before activating. |

### Verified Nick Kyrgios Modal Data

Use this only for popup `22840`.

- Modal Title: `Tennis Majors<br>Brand, TV Ad`
- Production Overview: `Tennis Majors approached us for a series of branded TV Ads, to bring in the crowds for the Summer Season.<br>From the minute we called "action", Australia's most entertaining tennis player swung into Kyrgios mode. The result? Game, set and match.`
- Video Provider: `vimeo`
- Video ID: `879662317`

## More Videos

These videos become the More Videos source list in Phase 11.2.

| Order | Popup ID | Hash Slug | Post Title / Default Card Title | Default Card Description | Modal / Video Verification |
|---:|---:|---|---|---|---|
| 1 | 24514 | `oz-harvest` | OzHarvest, Event Video | Ozharvest and Australia's leading chefs took to the streets to fight food waste. 1 Minute Media was there to capture the buzz. | Verify legacy popup before activating. |
| 2 | 24523 | `ippudo` | Ippudo International, Brand Ads | Our brand series takes Ippudo, the "Japanese Wonder" restaurant franchise to a global audience. | Verify legacy popup before activating. |
| 3 | 24495 | `unsw-aviation` | UNSW Aviation, Brand Video | We partnered with UNSW Aviation School for a series of brand videos to create buzz around the state-of-the-art aviation courses. | Verify legacy popup before activating. |
| 4 | 24535 | `professor-justin-yurbery` | Honouring Legacy & Driving MND Support | Partnering with Affirm Press since 2019 to promote their Australian stories, we were honoured to film Prof Yurbery as he reflected on his fight against Motor Neurone disease. | Verify legacy popup before activating. |
| 5 | 36380 | `AIMedia` | AI Media Secures Major Contracts | From startup to ASX-listed, our premium brand videos helped position AI-Media with clarity and credibility, contributing to major contract success. | Verify legacy popup before activating. Keep distinct from popup `24532` / `ai-media`. |
| 6 | 24503 | `the-kids-cancer-project` | The Kids' Cancer Project, TVC | Our partnership with TKCP has one goal: for kids with cancer to get better. Together we raise the funds that make this goal a reality. | Verify legacy popup before activating. |
| 7 | 26884 | `POCruises` | P&O Carnival Cruises, Case Study | Managing staff across travel, tourism and retail on a global stage requires a scientific approach. Our case studies explore the Mindset Program, rolled out with great success throughout Carnival Cruises worldwide network. | Verify legacy popup before activating. Preserve hash exactly. |
| 8 | 24529 | `1-minute-media-animation-showreel` | 1 Minute Media, Animation Showreel | Our branded animations simplify complex messages for serious organisations. AI Media, Energy Australia, NSW Health, NRL, Doordash and more. | Verify legacy popup before activating. |
| 9 | 24499 | `trisearch` | triSearch, Brand Explainers | triSearch has rapidly transformed conveyancing online, while our videos showcase every innovation of this great Australian SaaS success story. | Verify legacy popup before activating. |
| 10 | 24532 | `ai-media` | AI Media Secures Major Contracts | AI Media needed to showcase their accessibility solutions to win government and corporate contracts. Our videos helped secure key deals and drive growth. | Verify legacy popup before activating. Keep distinct from popup `36380` / `AIMedia`. |
| 11 | 36406 | `Medmate` | Medmate Launches Strong in Australia | Medmate needed to build trust fast entering Australia's competitive telehealth market. Our video helped expand reach and accelerate growth. | Verify legacy popup before activating. Preserve hash exactly. |
| 12 | 37195 | `1-minute-media-showreel` | 1 Minute Media, Showreel | From live action to animation, we shape complex ideas into clear, strategic video content for every platform. | Verify legacy popup before activating. This popup/hash appears in the known map and pilot scripts but not the older legacy JSON snapshot. |

## Verification Workflow

For each video:

1. Open the legacy Elementor popup by popup ID or by the static card link.
2. Copy modal title exactly, preserving intended line breaks with `<br>` only when needed.
3. Copy Production Overview text from the popup, not from the card.
4. Confirm the provider from the legacy video widget or iframe URL.
5. For Vimeo or YouTube, store only the video ID.
6. For Direct URL, store only a valid absolute `http` or `https` URL.
7. Choose the matching card thumbnail from Media Library.
8. Set Active only when all required fields are filled and verified.
9. Save the post.
10. Check the Video Case Studies list table for thumbnail, hash, status, and provider.

## Data Health Acceptance

After all 18 records are entered:

- Open `Video Case Studies > Data Health`.
- Confirm `Videos Missing Required Fields` has no issue for these 18 records.
- Confirm `Duplicate Hashes` has no issue for these 18 records.
- Confirm all intended records show `Active` in the Video Case Studies list table.
- Do not treat short Featured Video page warnings as Phase 11.1 failures; page placements are Phase 11.2.

## Phase Boundary

Phase 11.1 is complete when all 18 Video Case Study records exist on staging and pass strict field verification.

The following remain out of scope until later subphases:

- Page Featured Videos placement setup.
- Page More Videos placement setup.
- Elementor widget replacement.
- Removing or hiding old static sections.
- Removing old Elementor popups or legacy scripts.
