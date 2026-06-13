# OMMVS Phase -1 Through Phase 1 Live Test Checklist

This checklist is for validating the plugin on a live testing or staging site before any production deployment. It covers only the work completed through Phase 1 of `docs/OMMVS-03-master-implementation-plan.md`.

Current expected feature scope:

- Documentation and reference audit completed.
- Plugin foundation constants added.
- Future directories added and protected.
- `video_case_study` CPT class added.
- CPT loaded through the core plugin loader on `init`.
- Activation registers the CPT and flushes rewrite rules.
- Deactivation flushes rewrite rules and does not delete content.

Not expected yet:

- ACF field groups.
- Page-level Featured Videos or More Videos placement fields.
- Elementor Video Grid widget.
- Modal renderer.
- Page JSON.
- Frontend hash/modal controller.
- Migration/import tooling.
- Legacy Elementor popup cleanup.

## 1. Pre-Upload Package Checks

Use these checks before uploading the plugin ZIP or folder to the test site.

- Confirm the plugin folder name is `one-minute-media-video-showcase`.
- Confirm the plugin root contains `one-minute-media-video-showcase.php`.
- Confirm the plugin root contains `includes/class-ommvs-cpt-video.php`.
- Confirm the plugin root contains `includes/elementor/index.php`.
- Confirm the plugin root contains `templates/index.php`.
- Confirm the plugin root still contains the existing scaffold folders: `admin/`, `includes/`, `public/`, `languages/`, and `docs/`.
- Confirm the plugin ZIP does not wrap the plugin inside an extra nested folder.
- Confirm no unrelated development files are required for the plugin to load.

Expected result:

- WordPress can detect the plugin from `one-minute-media-video-showcase.php`.

## 2. Plugin Listing Checks

Go to WordPress Admin > Plugins.

- Confirm the plugin appears as `1 Minute Media Video Showcase`.
- Confirm the plugin description says it adds reusable video case studies, Elementor-powered video grids, page-specific related video logic, and a single dynamic video popup system.
- Confirm version displays as `1.0.0`.
- Confirm author displays as `Sayan Paul`.
- Confirm there is no plugin listing warning or parse error.

Expected result:

- Plugin appears normally in the Plugins screen and can be activated.

## 3. Activation Checks

Activate the plugin from WordPress Admin > Plugins.

- Confirm activation completes without fatal error.
- Confirm there is no white screen.
- Confirm there is no WordPress recovery mode email.
- Confirm the admin dashboard remains accessible after activation.
- Confirm the frontend homepage remains accessible after activation.
- Confirm an existing Elementor page remains accessible after activation.
- Confirm no manual permalink save is required for the plugin to activate cleanly.

Expected result:

- Plugin activates successfully.
- No content is changed.
- No frontend visual change should be expected yet, aside from any pre-existing boilerplate asset behavior.

## 4. Phase 0 Foundation Checks

These checks validate the Phase 0 implementation.

### 4.1 Constants

If you can inspect the deployed files, confirm `one-minute-media-video-showcase.php` defines:

- `ONE_MINUTE_MEDIA_VIDEO_SHOWCASE_VERSION`
- `OMMVS_VERSION`
- `OMMVS_PLUGIN_FILE`
- `OMMVS_PLUGIN_DIR`
- `OMMVS_PLUGIN_URL`
- `OMMVS_PLUGIN_BASENAME`
- `OMMVS_TEXT_DOMAIN`

Expected result:

- Constants exist after the original version constant.
- Existing `ONE_MINUTE_MEDIA_VIDEO_SHOWCASE_VERSION` remains present.
- Plugin activation does not fatal because of constants.

### 4.2 Future Directory Guards

If directory browsing can be tested or inspected:

- Visit or inspect `/wp-content/plugins/one-minute-media-video-showcase/includes/elementor/`.
- Visit or inspect `/wp-content/plugins/one-minute-media-video-showcase/templates/`.
- Confirm each directory contains an `index.php`.

Expected result:

- Direct directory browsing is protected consistently with the scaffold.
- No directory file listing is exposed by these new directories.

## 5. Phase 1 CPT Admin Menu Checks

After activation, inspect the WordPress admin menu.

- Confirm a left-admin menu item appears named `Video Case Studies`.
- Confirm the menu uses a video-style Dashicon.
- Click `Video Case Studies`.
- Confirm the list table loads without fatal error.
- Confirm the list table title uses `Video Case Studies`.
- Confirm the Add New button/action is available.

Expected result:

- The `video_case_study` CPT is registered on `init`.
- Admin can reach the CPT management screen.

## 6. Add/Edit Video Case Study Checks

Go to Video Case Studies > Add New.

- Confirm the edit screen loads.
- Confirm the title field is available.
- Confirm the Featured Image panel is available.
- Confirm the Page Attributes or Order field support is available if the editor exposes it.
- Add a test title, for example `OMMVS Test Video Case Study`.
- Set a featured image if possible.
- Publish the post.
- Confirm publishing succeeds without fatal error.
- Edit the post again.
- Confirm updates save successfully.
- Move the post to Trash.
- Restore it from Trash.
- Permanently delete only the test post if you no longer need it.

Expected result:

- Users can create, edit, publish, trash, restore, and delete `Video Case Study` posts.
- No ACF fields are expected yet.
- No modal/card fields are expected yet.

## 7. CPT Visibility And Public Route Checks

Because this CPT is admin-managed content only, public single/archive pages should not be exposed.

Create or use one test Video Case Study post, then check:

- Confirm there is no public `View` flow that exposes a normal single template.
- Try visiting `/video_case_study/`.
- Try visiting `/video-case-study/`.
- Try visiting a likely test post URL if WordPress displays one.
- Try searching the frontend site for the exact test post title.

Expected result:

- No public archive is available.
- No public single template is available.
- CPT content does not appear in normal frontend search results.
- If WordPress shows a preview/edit-only admin URL, that is acceptable; a public visitor should not receive a normal single post page.

## 8. REST / Block Editor Compatibility Checks

The CPT is configured with `show_in_rest => true`.

- Confirm the edit screen uses the block editor or otherwise loads without REST errors.
- Open browser dev tools on the Video Case Study edit screen.
- Confirm there are no repeated REST API errors related to `video_case_study`.
- If REST routes are visible to your role, confirm the CPT can be managed in the editor.

Expected result:

- The editor loads normally.
- No REST-related fatal errors or broken editor state.

## 9. Activation Rewrite Flush Checks

This verifies Subphase 1.3 activation behavior.

- Deactivate the plugin.
- Reactivate the plugin.
- Confirm activation succeeds without requiring Settings > Permalinks > Save.
- Confirm the Video Case Studies menu appears immediately after reactivation.
- Confirm existing test Video Case Study posts are still present.

Expected result:

- CPT registration is available immediately after activation.
- Existing CPT content remains intact.

## 10. Deactivation Safety Checks

Deactivate the plugin from WordPress Admin > Plugins.

- Confirm deactivation succeeds without fatal error.
- Confirm the admin dashboard still loads.
- Confirm frontend pages still load.
- Confirm existing Elementor/static pages continue to work.
- Reactivate the plugin.
- Confirm previous test Video Case Study posts are still present.

Expected result:

- Deactivation flushes rewrite rules.
- Deactivation does not delete posts or meta.
- Reactivation restores the CPT admin UI.

## 11. Legacy Site Regression Checks

Because Phase 1 should not affect the old Elementor implementation, run a quick smoke test on existing pages.

Test at least:

- Homepage or a normal content page.
- `/brand-video-production/` if available.
- One Services page with video cards.
- One Industries page with video cards.
- One page with existing Elementor popup behavior.
- One old direct hash URL, for example `/brand-video-production/#nick-kyrgios`, if it exists on the test site.

Expected result:

- Existing pages still load.
- Existing static Elementor video cards are not replaced yet.
- Existing Elementor popups are not intentionally changed by this phase.
- Existing hash behavior should remain governed by the old site scripts, not by this plugin yet.

Important:

- If old popup/hash behavior is already broken before this plugin is active, record it as a pre-existing legacy issue.
- Phase 1 does not implement new modal/hash behavior.

## 12. Frontend Asset And Visual Checks

The current scaffold still contains boilerplate admin/public enqueue behavior.

- Load a normal frontend page while logged out or in an incognito window.
- Confirm there is no visible layout break after plugin activation.
- Confirm no plugin modal appears.
- Confirm no `ommvs` video grid appears.
- Confirm no page JSON script such as `#ommvs-page-data` is expected yet.
- Confirm no `#ommvs-video-modal` is expected yet.

Expected result:

- No new frontend feature UI appears in Phase 1.
- Existing static site behavior remains intact.

Note:

- Conditional asset loading is a later phase. Do not fail Phase 1 just because boilerplate public assets are still enqueued globally, unless they visibly break the site.

## 13. Negative Checks For Not-Yet-Built Features

These should not exist yet:

- No ACF field group for Video Case Study details.
- No page-level Featured Videos repeater.
- No page-level More Videos repeater.
- No Elementor widget named `1MM Video Grid` or similar.
- No single reusable frontend modal.
- No generated page video JSON.
- No plugin-owned hash opening behavior.
- No migration UI.
- No automatic deletion of Elementor popups.

Expected result:

- Their absence is correct for Phase 1.

## 14. Error Log Checks

Check the site error logs after activation, CPT creation, deactivation, and reactivation.

Look for:

- PHP fatal errors.
- PHP warnings from `OMMVS_CPT_Video`.
- Missing include errors for `class-ommvs-cpt-video.php`.
- Undefined constant errors for `OMMVS_PLUGIN_DIR`.
- Rewrite flush errors.
- REST API errors on the edit screen.

Expected result:

- No new PHP errors related to this plugin.

## 15. User Role Checks

If possible, test with at least Administrator and Editor roles.

Administrator:

- Can see Video Case Studies.
- Can add/edit/delete Video Case Study posts.

Editor:

- Behavior should follow standard post-like capabilities.
- If Editors can manage posts on the site, they may be able to manage Video Case Studies.

Expected result:

- CPT capabilities behave like standard posts because `capability_type` is `post` and `map_meta_cap` is enabled.

## 16. Rollback Checks

Before production deployment, confirm rollback is simple.

- Deactivate plugin.
- Confirm old Elementor/static pages still work.
- Remove plugin from the test site only if needed.
- Confirm no production content depends on this plugin yet.

Expected result:

- Because Phase 1 does not migrate frontend pages, rollback should be low risk.

## 17. Pass / Fail Summary

Use this section while testing.

```text
Plugin uploaded correctly: Pass / Fail
Plugin appears in Plugins screen: Pass / Fail
Plugin activates without fatal error: Pass / Fail
Phase 0 constants present: Pass / Fail
Future directory index files present: Pass / Fail
Video Case Studies admin menu appears: Pass / Fail
Add Video Case Study screen loads: Pass / Fail
Featured image support works: Pass / Fail
Test Video Case Study publishes: Pass / Fail
Test Video Case Study edits/saves: Pass / Fail
No public archive exposed: Pass / Fail
No public single exposed: Pass / Fail
Frontend search excludes CPT content: Pass / Fail
Deactivate succeeds without fatal error: Pass / Fail
Reactivation preserves CPT content: Pass / Fail
Existing Elementor pages still work: Pass / Fail
Existing hash URLs still behave as before: Pass / Fail
No unexpected modal/grid/page JSON exists yet: Pass / Fail
No new PHP errors in logs: Pass / Fail
Rollback/deactivation is safe: Pass / Fail
```

## 18. Issues To Record

For every failure, record:

```text
Date/time:
Tester:
Site URL:
User role:
Browser:
Plugin version/commit:
Exact step:
Expected result:
Actual result:
Screenshot/log:
Likely source:
Blocking production? Yes / No
```

## 19. Production Readiness Gate

Do not deploy this phase to production until:

- Plugin activates successfully on the test site.
- Video Case Studies admin menu appears.
- A test Video Case Study can be created and edited.
- Deactivation and reactivation preserve CPT content.
- Existing Elementor pages and legacy video popups still behave as before.
- No plugin-related fatal errors, warnings, or missing file errors appear in logs.

