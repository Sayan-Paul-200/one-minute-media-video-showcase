# OMMVS Phase 2 Live Test Checklist

This checklist is for validating Phase 2 of `docs/OMMVS-03-master-implementation-plan.md` on a live testing or staging site before production deployment.

Phase 2 scope:

- Field system foundation.
- ACF Free Video Case Study fields.
- Page-level Featured Videos and More Videos placement metaboxes.
- Global modal settings page.
- Field, placement, and settings validation.

Not expected yet:

- Elementor Video Grid widget.
- Frontend video cards rendered by the plugin.
- Footer modal.
- Page JSON.
- Hash-based modal opening.
- Related-video frontend rendering.
- Migration/import UI.
- Automatic removal of legacy Elementor popups.

Important dependency note:

- ACF Free should be active for Video Case Study fields in this phase.
- ACF Pro is not required.
- Page placement metaboxes and global settings are plugin-owned and should not require ACF Pro.

## 1. Pre-Upload Package Checks

Before uploading the plugin ZIP or folder to the live test site:

- Confirm the plugin folder name is `one-minute-media-video-showcase`.
- Confirm the plugin root contains `one-minute-media-video-showcase.php`.
- Confirm `includes/class-ommvs-fields.php` exists.
- Confirm `includes/class-ommvs-settings.php` exists.
- Confirm `admin/js/one-minute-media-video-showcase-admin.js` exists.
- Confirm `admin/css/one-minute-media-video-showcase-admin.css` exists.
- Confirm `docs/OMMVS-03-master-implementation-plan.md` reflects the ACF Free/custom metabox/settings approach.
- Confirm the plugin ZIP does not wrap the plugin inside an extra nested folder.

Expected result:

- WordPress can detect and load the plugin files.
- No required Phase 2 files are missing from the upload package.

## 2. Site Preparation Checks

On the live test site:

- Confirm WordPress admin is accessible.
- Confirm the plugin can be activated/deactivated safely.
- Confirm ACF Free is installed and active.
- Confirm Elementor pages still load before testing Phase 2.
- Confirm you have an Administrator user available for testing.
- Confirm there is a recent backup or disposable staging copy.

Expected result:

- The test site is safe to modify.
- ACF Free is available for Video CPT field registration.

## 3. Plugin Activation Smoke Test

Go to WordPress Admin > Plugins.

- Activate or update the plugin.
- Confirm activation completes without fatal error.
- Confirm there is no WordPress recovery mode email.
- Confirm the dashboard remains accessible.
- Confirm the frontend homepage remains accessible.
- Confirm an existing Elementor video page remains accessible.

Expected result:

- Plugin loads normally after Phase 2 changes.
- No frontend feature should visibly appear yet.

## 4. ACF Free Dependency Checks

With ACF Free active:

- Go to Video Case Studies > Add New.
- Confirm the Video Case Study edit screen loads.
- Confirm a field group named `Video Showcase Details` appears.
- Confirm there is no ACF Pro requirement notice.
- Confirm there are no PHP warnings on the edit screen.

Optional negative test on staging only:

- Temporarily deactivate ACF Free.
- Reload the dashboard.
- Confirm the site does not fatal.
- Confirm an admin warning appears saying ACF Free is not active.
- Reactivate ACF Free before continuing.

Expected result:

- With ACF Free active, Video CPT fields appear.
- Without ACF Free, the site should not fatal.
- ACF Pro should not be needed for Phase 2 testing.

## 5. Video Case Study Field Visibility Checks

Create a new test Video Case Study.

Confirm these fields appear:

- Hash Slug.
- Active toggle.
- Default Card Title.
- Default Card Description.
- Default Card Thumbnail.
- Modal Title.
- Production Overview.
- Video Provider.
- Video ID, visible for Vimeo and YouTube providers.
- Video URL, visible when Video Provider is set to Direct URL.
- Related Thumbnail Override.
- Admin Notes.

Expected result:

- All Phase 2 Video CPT fields appear on the edit screen.
- Image fields use the Media Library.
- Provider selection controls the relevant video ID or URL field.
- Do not mark Video URL as missing while Vimeo or YouTube is selected; switch Video Provider to Direct URL to test that field.

Troubleshooting note:

- If the edit screen shows many numbered `Add Image` fields, inspect ACF > Field Groups for a legacy or test field group assigned to Pages or Video Case Studies. This plugin does not register numbered image fields.

## 6. Video Case Study Save Checks

Create a complete test video with:

```text
Title: OMMVS Phase 2 Test Video A
Hash Slug: ommvs-test-video-a
Active: On
Default Card Title: Phase 2 Test Card A
Default Card Description: Test card description A.
Default Card Thumbnail: any test image
Modal Title: Phase 2 Test Modal A
Production Overview: Test production overview A.
Video Provider: Vimeo
Video ID: 123456789
Related Thumbnail Override: optional test image
Admin Notes: Phase 2 test record.
```

Then:

- Publish the post.
- Reload the edit screen.
- Confirm every value persists.
- Change the provider to YouTube and enter a YouTube-style test ID.
- Save and reload.
- Change the provider to Direct URL and enter a valid URL.
- Save and reload.
- Change back to Vimeo for later placement testing if preferred.

Expected result:

- Field values save and reload correctly.
- Image fields store and display selected images.
- The field group works with ACF Free only.
- The native WordPress Slug metabox should not be used for frontend hashes; the plugin-owned Hash Slug field is the canonical hash value.

## 7. Hash Slug Validation Checks

Use two test Video Case Study posts.

### 7.1 Leading Hash Character

- Edit a test video.
- Set Hash Slug to `#ommvs-test-video-a`.
- Attempt to save.

Expected result:

- Save is blocked by validation.
- Admin sees a message telling them to store the hash without `#`.

### 7.2 Duplicate Hash

- Create or edit Video A with Hash Slug `ommvs-duplicate-test`.
- Create or edit Video B with the same Hash Slug `ommvs-duplicate-test`.
- Attempt to save Video B.

Expected result:

- Save is blocked by validation.
- Admin sees a duplicate hash warning.
- The duplicate hash is not accepted.

### 7.3 Same Post Resave

- Reopen Video A.
- Keep the same Hash Slug.
- Save again.

Expected result:

- The original post can save with its own existing hash.
- It is not incorrectly detected as its own duplicate.

## 8. Required Video Field Health Checks

Create a deliberately incomplete test video:

```text
Title: OMMVS Incomplete Video
Hash Slug: ommvs-incomplete-video
Active: On
Leave one or more required frontend fields empty.
```

Examples of missing fields:

- Default Card Title.
- Default Card Description.
- Default Card Thumbnail.
- Modal Title.
- Production Overview.
- Video Provider or matching Video ID/URL.

Expected result:

- ACF required fields may block saving when empty.
- If an incomplete video is saved and later selected in a page placement, the page save should warn that required video data is missing.

## 9. Active / Inactive Video Checks

Create or edit a test video:

- Set Active to On.
- Save.
- Confirm it can be selected in page placements.
- Set Active to Off.
- Save.
- Use this video in a page placement validation test later.

Expected result:

- Active videos can remain in placements.
- Inactive videos should be removed from page placements during page save with a warning.

## 10. Page Placement Metabox Visibility Checks

Go to Pages > Add New or edit a staging-only test page.

- Confirm a metabox named `1MM Video Showcase` appears.
- Confirm the metabox contains `Featured Videos`.
- Confirm the metabox contains `More Videos`.
- Confirm both sections have an `Add Video` button.
- Confirm the page edit screen does not show a fatal error if no Video Case Studies exist.

Expected result:

- Page placement UI appears only on page edit screens.
- The metabox does not require ACF Pro.

## 11. Featured Videos Add / Save / Reload Checks

Prepare at least six active, complete Video Case Study posts.

On a test page:

- Add one Featured Videos row.
- Select a Video Case Study.
- Save the page.
- Reload the page edit screen.
- Confirm the selected video persists.
- Add rows until there are six Featured Videos.
- Save and reload.
- Confirm all six persist in the same order.

Expected result:

- Featured Videos save as ordered page-level placement data.
- Six rows are supported.
- The order survives save/reload.

## 12. Featured Videos Max-6 Checks

On a test page with six Featured Videos:

- Try to add a seventh Featured Videos row using the UI.
- Confirm the seventh row is not added.
- Confirm the limit warning appears only after attempting to add beyond six, not simply because six valid rows exist.
- If a seventh row can be forced through browser manipulation, save the page.
- Reload the page edit screen.

Expected result:

- The UI should prevent adding more than six Featured Videos without warning when exactly six valid rows exist.
- Server-side save should never store more than six Featured Videos.
- If extra rows are submitted, they should be ignored and a warning should appear.

## 13. Featured Videos Reorder Checks

On a test page with six Featured Videos:

- Drag the sixth video to the first position using the row handle icon.
- Drag another video to the middle using the row handle icon.
- Save the page.
- Reload the edit screen.

Expected result:

- Reordered Featured Videos persist exactly.
- No row values are lost during drag/reorder.
- The order after reload matches the order before save.

## 14. Placement Row Simplicity Checks

For at least one Featured Videos row:

- Confirm the row has one Video Case Study selector.
- Confirm the row has one drag/reorder handle.
- Confirm the row has one Remove Row control.
- Confirm there are no Card Title Override fields.
- Confirm there are no Card Description Override fields.
- Confirm there are no placement thumbnail override controls.
- Save the page.
- Reload the page edit screen.

Expected result:

- Page placements stay simple for admins.
- Card title, description, and thumbnail values will come from the selected Video Case Study defaults in later frontend phases.

## 15. More Videos Add / Save / Reload Checks

On the same test page:

- Add one More Videos row.
- Select a Video Case Study.
- Save and reload.
- Confirm it persists.
- Add several More Videos rows.
- Save and reload.
- Confirm all rows persist in order.

Expected result:

- More Videos rows are ordered and repeatable.
- More Videos are not capped at six.
- More Videos do not define related-video source; that behavior is later frontend work.

## 16. More Videos Reorder Checks

For More Videos:

- Drag rows into a new order using the row handle icon.
- Save and reload.

Expected result:

- More Videos order persists.

## 17. Placement Remove Checks

On the test page:

- Remove one Featured Videos row.
- Save and reload.
- Confirm the row is gone.
- Remove one More Videos row.
- Save and reload.
- Confirm the row is gone.
- Remove all rows from both sections.
- Save and reload.

Expected result:

- Removed rows do not return after reload.
- Empty placement groups are allowed.
- Ordinary pages with no placements should not show validation noise.

## 18. Duplicate Placement Validation Checks

On a test page:

- Add Video A to Featured Videos.
- Add the same Video A again in Featured Videos or More Videos.
- Save the page.
- Reload the edit screen.

Expected result:

- The first occurrence remains.
- Later duplicate placement is removed.
- Admin sees a warning that duplicate placements were removed.
- The warning lists the affected video.

## 19. Inactive Placement Validation Checks

Use the inactive test video from Section 9.

On a test page:

- Add the inactive video to Featured Videos or More Videos.
- Save the page.
- Reload the edit screen.

Expected result:

- Inactive video is removed from placements.
- Admin sees a warning that inactive videos were removed.

## 20. Missing Required Video Data Warning Checks

Use the incomplete test video from Section 8 if it can be saved.

On a test page:

- Add the incomplete video to Featured Videos or More Videos.
- Save the page.
- Reload the edit screen.

Expected result:

- The placement may remain if the video is active.
- Admin sees a warning listing missing required data.
- This warning helps editors complete Video CPT data before frontend migration.

## 21. Featured Count Warning Checks

On a test page:

- Add only one to five Featured Videos.
- Save the page.
- Reload the edit screen.

Expected result:

- Admin sees a warning that the page has fewer than six Featured Videos.
- This warning should appear only when the page has video placements.

Negative check:

- Save a normal page with no Featured Videos and no More Videos.

Expected result:

- No fewer-than-six warning appears on ordinary pages with no video placements.

## 22. Page Placement Override Negative Checks

On a page placement row:

- Confirm there is no Choose Thumbnail or Choose Override Thumbnail button.
- Confirm there is no thumbnail preview area.
- Confirm there are no page-row override fields.

Expected result:

- Page placement rows only select and order Video Case Study posts.
- Thumbnail selection remains available only where still required, such as Video Case Study fields and global settings fallback thumbnail.

## 23. Page Placement Admin Asset Checks

Use browser dev tools or page behavior.

Check page edit screens:

- Placement row add/remove works.
- Drag reorder works.

Check unrelated admin screens:

- Dashboard.
- Posts list.
- Media Library.
- Users screen.

Expected result:

- Plugin admin JS/CSS should be active only where needed.
- Unrelated admin screens should not show placement UI side effects.

## 24. Global Settings Page Visibility Checks

Go to WordPress Admin > Settings > 1MM Video Showcase.

- Confirm the settings page exists.
- Confirm the page title is `1MM Video Showcase Settings`.
- Confirm the section is `Global Modal Defaults`.
- Confirm fields appear for:
  - Production Overview Label.
  - Creative Section Title.
  - Creative Bullet List.
  - CTA Button Text.
  - CTA Button URL.
  - Modal Fallback Thumbnail.

Expected result:

- Settings page is available to Administrators.
- ACF Pro Options Page is not required.

## 25. Global Settings Defaults Checks

Before changing anything, inspect the settings page.

Expected default values:

```text
Production Overview
1 Minute Media Creative
Pre-Production
Screen Design
Live Action Multi-Cam Filming
Motion Graphics
Editing
Get A Quick Quote
/quote-form/
```

Expected result:

- Defaults appear when no custom settings have been saved.
- Empty settings should not result in blank modal labels.

## 26. Global Settings Save / Reload Checks

On Settings > 1MM Video Showcase:

- Change Production Overview Label.
- Change Creative Section Title.
- Change CTA Button Text.
- Change CTA Button URL to a valid relative path, for example `/contact/`.
- Save settings.
- Reload the settings page.

Expected result:

- All changed values persist.
- No fatal error or settings permission error appears.

## 27. Creative Bullet Repeatable Checks

On Settings > 1MM Video Showcase:

- Add a new creative bullet.
- Remove an existing creative bullet.
- Drag bullets into a new order.
- Save settings.
- Reload the page.

Expected result:

- Added bullets persist.
- Removed bullets stay removed unless all bullets are cleared.
- Reordered bullets persist in the saved order.

All-empty behavior:

- Remove all bullets or submit only empty bullet rows.
- Save and reload.

Expected result:

- Defaults should be restored safely so the modal never has an empty creative list.

## 28. Settings Thumbnail Picker Checks

On Settings > 1MM Video Showcase:

- Click Choose Thumbnail for Modal Fallback Thumbnail.
- Confirm the Media Library opens.
- Select an image.
- Save settings.
- Reload the page.
- Confirm preview persists.
- Remove the thumbnail.
- Save and reload.

Expected result:

- Fallback thumbnail stores as an attachment ID.
- Remove clears the setting.
- No ACF Pro media field is required.

## 29. Settings Validation Checks

### 29.1 Valid Relative CTA URL

- Set CTA Button URL to `/quote-form/`.
- Save.

Expected result:

- Value persists.

### 29.2 Valid Absolute CTA URL

- Set CTA Button URL to `https://example.com/quote-form/`.
- Save.

Expected result:

- Value persists.

### 29.3 Invalid CTA URL

- Set CTA Button URL to an invalid value such as `javascript:alert(1)`.
- Save.

Expected result:

- Invalid value is not stored.
- Default `/quote-form/` is restored.
- Admin should see a settings warning.

### 29.4 Invalid Fallback Thumbnail ID

This is optional and may require browser dev tools.

- Force the hidden fallback thumbnail input to a non-attachment ID.
- Save settings.

Expected result:

- Invalid ID is not stored.
- Fallback thumbnail is cleared.
- Admin should see a settings warning.

## 30. Settings Asset Checks

On Settings > 1MM Video Showcase:

- Confirm creative bullet add/remove works.
- Confirm creative bullet drag reorder works.
- Confirm thumbnail picker works.

On unrelated admin screens:

- Confirm no settings repeatable controls appear.
- Confirm no JavaScript console errors from `ommvsAdmin`.

Expected result:

- Settings assets load only where needed.
- Page placement scripts do not break the settings page.

## 31. Data Persistence Contract Checks

If you can inspect the database or use a safe admin/debug tool, confirm:

Video CPT meta keys:

```text
ommvs_hash_slug
ommvs_is_active
ommvs_card_title
ommvs_card_description
ommvs_card_thumbnail
ommvs_modal_title
ommvs_modal_overview
ommvs_video_provider
ommvs_video_id
ommvs_video_url
ommvs_related_thumbnail
ommvs_admin_notes
```

Page placement meta keys:

```text
ommvs_featured_videos
ommvs_more_videos
```

Settings option:

```text
ommvs_settings
```

Expected result:

- Meta and option keys match the implementation plan.
- Admin-facing placement rows use `video` as the only required row value.
- Per-row card title, description, and thumbnail overrides are deferred and should not appear in the page editor UI.

## 32. Existing Elementor Page Regression Checks

Phase 2 should not replace frontend rendering yet.

Test at least:

- Homepage or a normal content page.
- `/brand-video-production/` if available.
- One Services video page.
- One Industries video page.
- One existing Elementor popup page.
- One existing hash URL, for example `/brand-video-production/#nick-kyrgios`.

Expected result:

- Existing Elementor/static video sections still work as before.
- Existing popup behavior remains governed by legacy scripts.
- No plugin-owned video grid appears yet.
- No plugin-owned modal appears yet.
- No plugin-owned page JSON appears yet.

## 33. Frontend Negative Checks

Load a frontend page after configuring Phase 2 data.

Confirm these are not expected yet:

- No Elementor widget named `1MM Video Grid` on the frontend.
- No `#ommvs-video-modal`.
- No `#ommvs-page-data` JSON.
- No hash-based plugin modal opening.
- No related-card frontend rendering.

Expected result:

- Phase 2 is admin/data-model work only.
- Frontend visual behavior should not change yet.

## 34. Role And Permission Checks

Administrator:

- Can edit Video Case Studies.
- Can edit page placements.
- Can access Settings > 1MM Video Showcase.

Editor, if applicable:

- May be able to edit Video Case Studies depending on site capabilities.
- May be able to edit page placements on pages they can edit.
- Should not access the global settings page unless they have `manage_options`.

Expected result:

- Settings are admin-only.
- Page placement permissions follow page edit permissions.
- Video Case Study permissions follow post-like CPT capabilities.

## 35. Error Log Checks

After all tests, check PHP logs.

Look for:

- Fatal errors.
- Undefined class errors for `OMMVS_Fields` or `OMMVS_Settings`.
- Missing include errors for `class-ommvs-fields.php`.
- Missing include errors for `class-ommvs-settings.php`.
- ACF function errors.
- Settings API warnings.
- Save-related PHP warnings.
- JavaScript console errors on page edit or settings screens.

Expected result:

- No new plugin-related fatal errors or warnings.

## 36. Deactivation / Reactivation Safety Checks

Deactivate the plugin.

- Confirm the site does not fatal.
- Confirm frontend pages still load.
- Reactivate the plugin.
- Confirm Video Case Study posts still exist.
- Confirm Video CPT fields still display with ACF Free active.
- Confirm page placements still exist.
- Confirm global settings still exist.

Expected result:

- Deactivation does not delete Phase 2 data.
- Reactivation restores admin UI.

## 37. Cleanup After Testing

After testing:

- Delete only disposable test pages if no longer needed.
- Delete only disposable test Video Case Study posts if no longer needed.
- Remove test media only if it is not used elsewhere.
- Keep any real migration data that should continue into later phases.
- Record all failures before cleanup.

Expected result:

- Test artifacts do not confuse later migration work.
- Real staging migration data remains intact.

## 38. Pass / Fail Summary

Use this summary while testing:

```text
Plugin upload package contains Phase 2 files: Pass / Fail
Plugin activates without fatal error: Pass / Fail
ACF Free active and Video CPT fields appear: Pass / Fail
ACF Pro not required: Pass / Fail
Video CPT fields save/reload: Pass / Fail
Hash slug without # saves: Pass / Fail
Hash slug with # is blocked: Pass / Fail
Duplicate hash slug is blocked: Pass / Fail
Active toggle saves/reloads: Pass / Fail
Page metabox appears on pages: Pass / Fail
Featured Videos can be added: Pass / Fail
Featured Videos reorder persists: Pass / Fail
Featured Videos max 6 enforced: Pass / Fail
Page placement rows have no override controls: Pass / Fail
More Videos can be added: Pass / Fail
More Videos reorder persists: Pass / Fail
Duplicate placements removed or clearly warned: Pass / Fail
Inactive placements removed or clearly warned: Pass / Fail
Missing required video data warning appears: Pass / Fail
Fewer-than-six Featured warning appears when relevant: Pass / Fail
Ordinary pages do not show placement warnings: Pass / Fail
Settings page appears: Pass / Fail
Settings defaults appear: Pass / Fail
Settings save/reload: Pass / Fail
Creative bullets add/remove/reorder: Pass / Fail
Fallback thumbnail picker works: Pass / Fail
Invalid CTA URL corrected/warned: Pass / Fail
Settings assets do not affect unrelated screens: Pass / Fail
Existing Elementor pages still work: Pass / Fail
No frontend plugin grid/modal/json appears yet: Pass / Fail
No new PHP errors in logs: Pass / Fail
Deactivation/reactivation preserves Phase 2 data: Pass / Fail
```

## 39. Issue Report Template

For every failure, record:

```text
Date/time:
Tester:
Site URL:
User role:
Browser:
Plugin version/commit:
ACF Free active? Yes / No
Exact step:
Expected result:
Actual result:
Screenshot/log:
Browser console error:
PHP error log entry:
Likely source:
Blocking production? Yes / No
```

## 40. Production Readiness Gate

Do not deploy Phase 2 to production until:

- Plugin activates cleanly on the live test site.
- ACF Free Video CPT fields display and save.
- Page placement metaboxes display and save.
- Featured Videos max 6 is enforced.
- Duplicate hash slugs are blocked.
- Duplicate placements are removed or clearly warned.
- Inactive placements are removed or clearly warned.
- Global settings display defaults and save custom values.
- Existing Elementor pages still behave as before.
- No plugin-related fatal errors, PHP warnings, or browser console errors appear.
