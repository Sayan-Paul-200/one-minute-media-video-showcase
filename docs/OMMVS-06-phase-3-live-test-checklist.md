# OMMVS Phase 3 Live Test Checklist

This checklist is for validating Phase 3 of `docs/OMMVS-03-master-implementation-plan.md` on a live testing or staging site before production deployment.

Phase 3 scope:

- Related-video calculation engine.
- Pure PHP class `OMMVS_Related_Videos`.
- Method `OMMVS_Related_Videos::get_related_ids()`.
- Unit-like development checks for the related-video mapping rules.

Not expected yet:

- Elementor Video Grid widget.
- Frontend video cards rendered by the plugin.
- Footer modal.
- Page JSON.
- Hash-based plugin modal opening.
- Related-video frontend rendering.
- Any visible change to existing Elementor/static pages.
- Any admin screen for related-video testing.
- Any AJAX endpoint or REST endpoint for related videos.

Important testing note:

- Phase 3 is intentionally almost invisible in WordPress admin and frontend.
- The main live-site goal is to confirm the new class is present, loads safely, and does not break the already-tested Phase 2 admin/data features.
- The related-video math itself can be verified with an optional temporary command if SSH, WP-CLI, or another safe staging-only PHP execution method is available.

## 1. Pre-Upload Package Checks

Before uploading the plugin ZIP or folder to the live test site:

- Confirm the plugin folder name is `one-minute-media-video-showcase`.
- Confirm the plugin root contains `one-minute-media-video-showcase.php`.
- Confirm `includes/class-ommvs-related-videos.php` exists.
- Confirm `includes/class-one-minute-media-video-showcase.php` exists.
- Confirm `includes/class-ommvs-fields.php` still exists.
- Confirm `includes/class-ommvs-settings.php` still exists.
- Confirm `includes/class-ommvs-cpt-video.php` still exists.
- Confirm `docs/OMMVS-03-master-implementation-plan.md` contains Phase 3.
- Confirm the plugin ZIP does not wrap the plugin inside an extra nested folder.

Expected result:

- WordPress can detect and load the plugin files.
- The Phase 3 related-video class is included in the upload package.
- No Phase 2 files are accidentally omitted.

## 2. Site Preparation Checks

On the live test site:

- Confirm WordPress admin is accessible.
- Confirm you have an Administrator user available.
- Confirm the plugin can be updated, deactivated, and reactivated safely.
- Confirm ACF Free is active if you are also regression-testing Video CPT fields.
- Confirm Elementor pages still load before updating the plugin.
- Confirm there is a recent backup or disposable staging copy.

Expected result:

- The test site is safe to modify.
- Existing Phase 2 setup is still present before testing Phase 3.

## 3. Plugin Update And Activation Smoke Test

Go to WordPress Admin > Plugins.

- Upload/update the plugin.
- Activate the plugin if needed.
- Confirm activation completes without fatal error.
- Confirm there is no WordPress recovery mode email.
- Confirm the WordPress dashboard remains accessible.
- Confirm the frontend homepage remains accessible.
- Confirm one existing Elementor video page remains accessible.

Expected result:

- Plugin loads normally after Phase 3 changes.
- No new visible frontend feature appears yet.
- No fatal error mentions `OMMVS_Related_Videos` or `class-ommvs-related-videos.php`.

## 4. Related Class Load Checks

Phase 3 loads a new PHP class from the core plugin class.

Check after update:

- Open WordPress Admin > Dashboard.
- Open Video Case Studies > All Video Case Studies.
- Open Pages > All Pages.
- Open Settings > 1MM Video Showcase.
- Refresh each screen once.

Expected result:

- No fatal error appears.
- No white screen appears.
- No admin notice says a related-video file or class is missing.
- Existing admin screens still behave as before.

If PHP logs are available, search for:

```text
OMMVS_Related_Videos
class-ommvs-related-videos.php
Failed opening required
Cannot declare class OMMVS_Related_Videos
Call to undefined method OMMVS_Related_Videos::get_related_ids
```

Expected result:

- None of these errors appear.

## 5. Phase 2 Regression: Video CPT Checks

Go to Video Case Studies > Add New or edit an existing test video.

- Confirm the edit screen loads.
- Confirm `Video Showcase Details` appears.
- Confirm Hash Slug field appears.
- Confirm Active toggle appears.
- Confirm Default Card Title appears.
- Confirm Default Card Description appears.
- Confirm Default Card Thumbnail appears.
- Confirm Modal Title appears.
- Confirm Production Overview appears.
- Confirm Video Provider appears.
- Confirm Video ID appears for Vimeo/YouTube.
- Confirm Video URL appears only when Video Provider is Direct URL.
- Save/reload a disposable test video if needed.

Expected result:

- Phase 3 did not break Video Case Study field registration.
- ACF Free behavior remains the same as Phase 2.

## 6. Phase 2 Regression: Page Placement Checks

Go to Pages > Add New or edit a staging-only test page.

- Confirm the `1MM Video Showcase` metabox appears.
- Confirm Featured Videos section appears.
- Confirm More Videos section appears.
- Add one Featured Videos row.
- Select a Video Case Study.
- Add one More Videos row.
- Select a Video Case Study.
- Save and reload.
- Confirm both selected videos persist.
- Confirm page placement rows still show only:
  - drag handle
  - Video Case Study selector
  - Remove Row
- Confirm page placement rows do not show card title override, description override, or thumbnail override controls.

Expected result:

- Page placement storage still works.
- Phase 3 did not alter admin placement behavior.
- Page placements remain simple selection/order rows.

## 7. Phase 2 Regression: Featured Max-6 Checks

On a staging-only test page:

- Add Featured Videos rows until there are six.
- Confirm six rows are allowed.
- Try to add a seventh row.

Expected result:

- Six Featured Videos are valid.
- The seventh row is not added.
- The limit warning appears only when attempting to exceed six.

## 8. Phase 2 Regression: Reorder Checks

On a staging-only test page with at least three Featured Videos:

- Drag a lower row to the top using the row handle icon.
- Drag another row to the middle.
- Save and reload.

Expected result:

- Reordered rows persist exactly.
- No selected video is lost during drag/reorder.

## 9. Phase 2 Regression: Settings Page Checks

Go to Settings > 1MM Video Showcase.

- Confirm the settings page loads.
- Confirm default/global modal fields appear.
- Confirm Creative Bullet List add/remove still works.
- Confirm Creative Bullet List reorder still works.
- Confirm Modal Fallback Thumbnail picker still works.
- Save and reload if using disposable test values.

Expected result:

- Phase 3 did not break plugin-owned settings.
- No missing class or missing file error appears.

## 10. Frontend Negative Checks

Phase 3 should not change frontend behavior yet.

Load these frontend pages:

- Homepage or a normal content page.
- `/brand-video-production/` if available.
- One Services video page.
- One Industries video page.
- One existing Elementor popup page.
- One existing hash URL, for example `/brand-video-production/#nick-kyrgios`.

Expected result:

- Existing Elementor/static video sections still work as before.
- Existing legacy popup behavior remains governed by legacy scripts.
- No plugin-owned video grid appears yet.
- No plugin-owned modal appears yet.
- No plugin-owned page JSON appears yet.
- No new related-card frontend behavior appears yet.
- No console errors mention `ommvs` related-video code.

## 11. Optional Developer Logic Verification With WP-CLI

Use this only if the live test site supports WP-CLI and you are comfortable running a read-only staging verification command.

From the WordPress root, run:

```bash
wp eval '
$cases = array(
    "featured current A"       => array(array(1, 2, 3, 4, 5, 6), 1, 3, array(2, 3, 4)),
    "featured current B"       => array(array(1, 2, 3, 4, 5, 6), 2, 3, array(1, 3, 4)),
    "featured current C"       => array(array(1, 2, 3, 4, 5, 6), 3, 3, array(1, 2, 4)),
    "featured current D"       => array(array(1, 2, 3, 4, 5, 6), 4, 3, array(1, 2, 3)),
    "featured current E"       => array(array(1, 2, 3, 4, 5, 6), 5, 3, array(1, 2, 3)),
    "non-featured current"     => array(array(1, 2, 3, 4, 5, 6), 99, 3, array(1, 2, 3)),
    "fewer than limit current" => array(array(1, 2), 1, 3, array(2)),
    "fewer than limit other"   => array(array(1, 2), 99, 3, array(1, 2)),
    "empty featured list"      => array(array(), 99, 3, array()),
    "normalize featured IDs"   => array(array(1, 1, 2, 0, 3), 1, 3, array(2, 3)),
    "limit two"                => array(array(1, 2, 3, 4, 5, 6), 99, 2, array(1, 2)),
    "limit zero"               => array(array(1, 2, 3, 4, 5, 6), 99, 0, array()),
);

foreach ($cases as $label => $case) {
    $actual = OMMVS_Related_Videos::get_related_ids($case[0], $case[1], $case[2]);

    if ($actual !== $case[3]) {
        WP_CLI::error($label . " failed: expected " . wp_json_encode($case[3]) . ", got " . wp_json_encode($actual));
    }

    WP_CLI::line($label . ": passed -> " . wp_json_encode($actual));
}

WP_CLI::success("All Phase 3 related-video checks passed.");
'
```

Expected result:

- Each case prints `passed`.
- Final line says all Phase 3 related-video checks passed.
- No files are created.
- No database writes are performed.

## 12. Optional Developer Logic Verification Without WP-CLI

Use this only on staging if WP-CLI is unavailable and you have a safe temporary PHP execution method.

Run the same cases from Section 11 in a temporary, non-persistent environment that loads WordPress and the active plugin.

Expected result:

- The class `OMMVS_Related_Videos` exists.
- The method `get_related_ids()` returns the expected arrays.
- The temporary execution method is removed/disabled immediately after testing.
- No temporary test file remains in the plugin folder.

Do not use this optional path on production.

## 13. Expected Related-Video Rule Matrix

Use this table when reviewing any development output or optional command output.

```text
Featured list: [A, B, C, D, E, F]

Current A -> B, C, D
Current B -> A, C, D
Current C -> A, B, D
Current D -> A, B, C
Current E -> A, B, C
Current F -> A, B, C
Current More Video -> A, B, C
Unknown current video -> A, B, C
```

Numeric equivalent:

```text
[1,2,3,4,5,6], current 1  -> [2,3,4]
[1,2,3,4,5,6], current 2  -> [1,3,4]
[1,2,3,4,5,6], current 3  -> [1,2,4]
[1,2,3,4,5,6], current 4  -> [1,2,3]
[1,2,3,4,5,6], current 5  -> [1,2,3]
[1,2,3,4,5,6], current 6  -> [1,2,3]
[1,2,3,4,5,6], current 99 -> [1,2,3]
```

## 14. Edge Case Checks

Confirm the intended logic mentally or through optional command output:

- Empty featured list returns no related videos.
- One featured video returns no related videos for that same current video.
- Two featured videos can return only one or two related videos depending on current video.
- Duplicate IDs in the featured list are treated as one ID.
- Zero or invalid IDs are ignored.
- Current video is never included in its own related list.
- `limit = 2` returns at most two IDs.
- `limit = 0` returns no IDs.

Expected result:

- The related engine is safe for incomplete page data.
- The related engine does not require exactly six Featured Videos to work.

## 15. Data Model Compatibility Checks

Inspect a staging-only test page in the editor.

- Featured Videos still represent the source list for related videos.
- More Videos do not define the source list for related videos.
- More Videos can still be selected and ordered for later frontend use.
- A More Videos item should later receive related videos from the page's Featured Videos list.

Expected result:

- Editors understand that Featured Videos are the related-video source list.
- More Videos are additional display placements only.

## 16. Error Log Checks

After all Phase 3 tests, check PHP logs.

Look for:

- Fatal errors.
- Parse errors.
- Missing include errors for `class-ommvs-related-videos.php`.
- Undefined class errors for `OMMVS_Related_Videos`.
- Undefined method errors for `get_related_ids`.
- Redeclared class errors for `OMMVS_Related_Videos`.
- Warnings from existing Phase 2 files.

Expected result:

- No new plugin-related fatal errors or warnings appear.

## 17. Browser Console Checks

On the tested frontend pages and admin screens:

- Open browser developer tools.
- Check the Console tab.
- Refresh the page once.

Expected result:

- No new JavaScript errors appear.
- No frontend `ommvs` related-video behavior is expected yet.
- Existing static/legacy scripts continue behaving as before.

## 18. Deactivation / Reactivation Safety Checks

Deactivate the plugin.

- Confirm WordPress admin remains accessible.
- Confirm frontend pages still load.
- Reactivate the plugin.
- Confirm Video Case Studies menu returns.
- Confirm page placement metaboxes return.
- Confirm Settings > 1MM Video Showcase returns.

Expected result:

- Deactivation does not delete data.
- Reactivation loads the related-video class safely.

## 19. Cleanup After Testing

After testing:

- Delete only disposable test pages if no longer needed.
- Delete only disposable Video Case Study posts if no longer needed.
- Remove test media only if it is not used elsewhere.
- Remove/disable any temporary staging-only PHP execution method used for optional logic checks.
- Keep real migration data that should continue into Phase 4.

Expected result:

- No temporary logic-check artifact remains.
- Real staging migration data remains intact.

## 20. Pass / Fail Summary

Use this summary while testing:

```text
Plugin package contains class-ommvs-related-videos.php: Pass / Fail
Plugin activates without fatal error: Pass / Fail
Dashboard loads after activation: Pass / Fail
Video Case Studies admin still works: Pass / Fail
Page placement metabox still works: Pass / Fail
Featured Videos max-6 behavior still works: Pass / Fail
Page placement reorder still works: Pass / Fail
Settings page still works: Pass / Fail
Existing Elementor pages still work: Pass / Fail
No plugin-owned frontend grid/modal/json appears yet: Pass / Fail
Optional related logic command passes all cases: Pass / Fail / Not Run
No PHP errors mentioning OMMVS_Related_Videos: Pass / Fail
No browser console regressions: Pass / Fail
Deactivation/reactivation safe: Pass / Fail
```

## 21. Issue Report Template

For every failure, record:

```text
Date/time:
Tester:
Site URL:
User role:
Browser:
Plugin version/commit:
ACF Free active? Yes / No
WP-CLI available? Yes / No
Exact step:
Expected result:
Actual result:
Screenshot/log:
Browser console error:
PHP error log entry:
Optional command output:
Likely source:
Blocking production? Yes / No
```

## 22. Production Readiness Gate

Do not deploy Phase 3 to production until:

- Plugin activates cleanly on the live test site.
- No PHP errors mention `OMMVS_Related_Videos`.
- Existing Video Case Study admin screens still work.
- Existing page placement metaboxes still work.
- Existing global settings page still works.
- Existing Elementor/static frontend pages still behave as before.
- No plugin-owned frontend grid, modal, JSON, or related rendering appears yet.
- Optional logic verification passes, or local Subphase 3.2 verification output is accepted as sufficient for this pure PHP engine.
