# Known Legacy Popup ID and Hash Map

This document records known legacy Elementor popup IDs and URL hashes from the current/static system.

The final plugin should preserve these hashes where possible so existing URLs continue to work.

## Important rules

1. Existing hashes should not be changed casually.
2. Legacy hashes may include uppercase letters or special characters.
3. New hashes should use cleaner lowercase slugs where possible.
4. The final Video CPT should store the hash slug without the leading `#`.
5. Hash slugs must be unique across videos.

Example:

```txt
Legacy URL: /brand-video-production/#nick-kyrgios
Stored hash slug: nick-kyrgios
```

## Machine-readable recommendation

When creating a JSON reference map, store hash slugs without the `#`:

```json
{
  "22840": "nick-kyrgios",
  "24544": "fleetpartners"
}
```

## Known map

| Popup ID | Legacy Hash | Suggested Stored Hash Slug | Known / Inferred Video Title | Notes |
|---:|---|---|---|---|
| 22840 | `#nick-kyrgios` | `nick-kyrgios` | Tennis Majors / Nick Kyrgios | Pilot page first featured video. |
| 24155 | `#valiant-national` | `valiant-national` | Valiant National | Existing legacy popup/hash. Verify title during migration. |
| 24487 | `#women-in-ai` | `women-in-ai` | Women in AI | Existing legacy popup/hash. Verify title during migration. |
| 24491 | `#vinnies` | `vinnies` | Vinnies | Existing legacy popup/hash. Verify title during migration. |
| 24495 | `#unsw-aviation` | `unsw-aviation` | UNSW Aviation | Brand Video page More Videos section. |
| 24499 | `#trisearch` | `trisearch` | triSearch | Brand Video page More Videos section. |
| 24503 | `#the-kids-cancer-project` | `the-kids-cancer-project` | The Kids' Cancer Project | Brand Video page More Videos section. |
| 24507 | `#the-voice-to-parliament` | `the-voice-to-parliament` | The Voice to Parliament | Existing legacy popup/hash. |
| 24511 | `#the-missing-link` | `the-missing-link` | The Missing Link | Existing legacy popup/hash. Verify title during migration. |
| 24514 | `#oz-harvest` | `oz-harvest` | OzHarvest | Brand Video page More Videos section. |
| 24517 | `#nsw-health-animation` | `nsw-health-animation` | NSW Health Animation | Existing legacy popup/hash. Verify title during migration. |
| 24520 | `#the-langham-hotels` | `the-langham-hotels` | The Langham Hotels | Pilot page featured video. |
| 24523 | `#ippudo` | `ippudo` | Ippudo International | Brand Video page More Videos section. |
| 24526 | `#edith-cowan-university` | `edith-cowan-university` | Edith Cowan University | Existing legacy popup/hash. Verify title during migration. |
| 24529 | `#1-minute-media-animation-showreel` | `1-minute-media-animation-showreel` | 1 Minute Media Animation Showreel | Brand Video page More Videos section. |
| 24532 | `#ai-media` | `ai-media` | AI Media | Brand Video page More Videos section. Legacy map also has `#AIMedia` for another popup. Verify distinction. |
| 24535 | `#professor-justin-yurbery` | `professor-justin-yurbery` | Professor Justin Yurbery / Affirm Press | Brand Video page More Videos section. |
| 24538 | `#afr` | `afr` | Australian Financial Review | Pilot page featured video. |
| 24541 | `#heinemann` | `heinemann` | Heinemann | Pilot page featured video. |
| 24544 | `#fleetpartners` | `fleetpartners` | FleetPartners | Pilot page featured video. |
| 26500 | `#dove` | `dove` | Dove | Existing legacy popup/hash. Verify title during migration. |
| 26530 | `#my-health-app` | `my-health-app` | My Health App | Existing legacy popup/hash. Verify title during migration. |
| 26552 | `#hv-fintech` | `hv-fintech` | HV Fintech | Existing legacy popup/hash. Verify title during migration. |
| 26884 | `#POCruises` | `POCruises` | P&O / Carnival Cruises | Legacy hash uses uppercase. Preserve for compatibility unless redirects/fallback aliases are added. |
| 28002 | `#routine-skin` | `routine-skin` | Routine Skin | Existing legacy popup/hash. Verify title during migration. |
| 28005 | `#Pedl-Bikes` | `Pedl-Bikes` | Pedl Bikes | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 28008 | `#AH-Beard` | `AH-Beard` | AH Beard | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 28011 | `#IRoad` | `IRoad` | iRoad | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 33621 | `#SSKB` | `SSKB` | SSKB | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 33737 | `#Banqeta` | `Banqeta` | Banqeta | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 33754 | `#DigitalFrontDoor` | `DigitalFrontDoor` | Digital Front Door | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 33772 | `#PrintLocker` | `PrintLocker` | Print Locker | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 33792 | `#ProductivityPack` | `ProductivityPack` | Productivity Pack | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 34033 | `#Refilled` | `Refilled` | Refilled | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 36165 | `#Officeworks` | `Officeworks` | Officeworks | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 36185 | `#GandM` | `GandM` | G&M | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 36199 | `#Beamtree` | `Beamtree` | Beamtree | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 36207 | `#CFCH` | `CFCH` | CFCH | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 36282 | `#Whiskey&Wealth` | `Whiskey&Wealth` | Whiskey & Wealth | Pilot page featured video. Legacy hash uses uppercase and ampersand. Preserve for compatibility. |
| 36358 | `#MadDogs&Englishmen` | `MadDogs&Englishmen` | Mad Dogs & Englishmen | Legacy hash uses uppercase and ampersand. Preserve for compatibility. |
| 36380 | `#AIMedia` | `AIMedia` | AI Media Secures Major Contracts | Brand Video page More Videos section. Legacy hash uses uppercase. Distinguish from popup `24532` / `#ai-media`. |
| 36406 | `#Medmate` | `Medmate` | Medmate | Brand Video page More Videos section. Legacy hash uses uppercase. |
| 36726 | `#CairnsCouncil` | `CairnsCouncil` | Cairns Council | Legacy hash uses uppercase. Preserve for compatibility unless aliases are added. |
| 37195 | `#1-minute-media-showreel` | `1-minute-media-showreel` | 1 Minute Media Showreel | Brand Video page More Videos section. |

## Notes about legacy hash quality

Some legacy hashes are clean and lowercase:

```txt
#nick-kyrgios
#the-langham-hotels
#heinemann
#afr
```

Some legacy hashes use uppercase or special characters:

```txt
#Whiskey&Wealth
#MadDogs&Englishmen
#POCruises
#AIMedia
#Medmate
```

For migration, preserve these legacy values to avoid breaking existing URLs.

For future new videos, prefer clean lowercase hashes:

```txt
whiskey-and-wealth
mad-dogs-englishmen
po-cruises
ai-media
medmate
```

If the client wants to normalize legacy hashes later, implement backwards-compatible aliases first.

## Alias strategy for future enhancement

The Video CPT may eventually support:

```txt
Primary hash slug
Legacy hash aliases
```

Example:

```txt
Primary: whiskey-and-wealth
Alias: Whiskey&Wealth
```

This would allow the frontend to support both:

```txt
/brand-video-production/#whiskey-and-wealth
/brand-video-production/#Whiskey&Wealth
```

Do not implement alias migration unless explicitly scoped.

## Migration use

During migration, use this file to create Video CPT entries and preserve hashes.

For each Video CPT entry, verify:

```txt
Legacy popup ID
Legacy hash
Video title
Card title
Card description
Popup title
Popup overview
Video embed URL/ID
Thumbnail
```

The final plugin should not depend on Elementor popup IDs, but this map is useful for migration and backwards compatibility.
