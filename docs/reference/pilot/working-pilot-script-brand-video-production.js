(function () {
  "use strict";

  /**
   * 1 Minute Media — Brand Video Production page related-popup pilot
   * Scope: /brand-video-production/ only
   *
   * Important:
   * This pilot script does NOT own global hash routing while the old global
   * Scripts Inserter popupHashMap script is still active.
   *
   * Old global script:
   * - Opens popup from URL hash
   * - Updates URL hash on Elementor popup show
   * - Clears URL hash on Elementor popup hide
   *
   * This pilot script:
   * - Collects first 6 featured videos
   * - Dynamically replaces related popup cards
   * - Handles related-card popup-to-popup navigation
   * - Preserves scroll position when popups close
   */

  const CONFIG = {
    enabledPath: "/brand-video-production/",
    featuredSourceSelector: ".js-featured-video-source",
    featuredCardSelector: ".js-video-card",
    popupRelatedListSelector: ".js-popup-related-list",
    popupRelatedCardSelector: ".js-popup-related-card",
    popupRelatedImageSelector: ".js-popup-related-image",
    popupRelatedButtonSelector: ".js-popup-related-button",
    maxFeaturedVideos: 6,
    relatedCount: 3,
    debug: true,
  };

  /**
   * Keep these hashes consistent with your existing global footer popupHashMap.
   */
  const POPUP_HASH_MAP = {
    // First 6 featured videos
    22840: "#nick-kyrgios",
    24544: "#fleetpartners",
    24520: "#the-langham-hotels",
    36282: "#Whiskey&Wealth",
    24541: "#heinemann",
    24538: "#afr",

    // Second-section videos on /brand-video-production/
    24514: "#oz-harvest",
    24523: "#ippudo",
    24495: "#unsw-aviation",
    24535: "#professor-justin-yurbery",
    36380: "#AIMedia",
    24503: "#the-kids-cancer-project",
    26884: "#POCruises",
    24529: "#1-minute-media-animation-showreel",
    24499: "#trisearch",
    24532: "#ai-media",
    36406: "#Medmate",
    37195: "#1-minute-media-showreel",
  };

  const HASH_POPUP_MAP = Object.keys(POPUP_HASH_MAP).reduce(function (acc, id) {
    acc[POPUP_HASH_MAP[id]] = Number(id);
    return acc;
  }, {});

  let pageVideos = [];
  let currentPopupId = null;
  let isInternalPopupNavigation = false;
  let hasInitialized = false;

  /**
   * Scroll restoration state.
   *
   * Elementor/browser focus restoration after modal close can scroll the page
   * to the original trigger. Combined with duplicate history/hash updates,
   * this creates the irregular jump you observed.
   */
  let lastScrollX = 0;
  let lastScrollY = 0;
  let shouldRestoreScrollAfterClose = false;

  if ("scrollRestoration" in window.history) {
    window.history.scrollRestoration = "manual";
  }

  function log() {
    if (!CONFIG.debug) return;
    console.log.apply(
      console,
      ["[1MM Related Pilot]"].concat(Array.from(arguments)),
    );
  }

  function normalizePath(pathname) {
    return String(pathname || "").replace(/\/?$/, "/");
  }

  function isEnabledPage() {
    return normalizePath(window.location.pathname).endsWith(CONFIG.enabledPath);
  }

  function rememberScrollPosition() {
    lastScrollX = window.scrollX || window.pageXOffset || 0;
    lastScrollY = window.scrollY || window.pageYOffset || 0;
    shouldRestoreScrollAfterClose = true;

    log("Remembered scroll position:", {
      x: lastScrollX,
      y: lastScrollY,
    });
  }

  function restoreScrollPosition() {
    if (!shouldRestoreScrollAfterClose) return;

    const x = lastScrollX;
    const y = lastScrollY;

    /**
     * Restore more than once because Elementor/browser focus restoration
     * may happen shortly after the popup hide event.
     */
    window.requestAnimationFrame(function () {
      window.scrollTo(x, y);
    });

    window.setTimeout(function () {
      window.scrollTo(x, y);
    }, 60);

    window.setTimeout(function () {
      window.scrollTo(x, y);
      shouldRestoreScrollAfterClose = false;

      log("Restored scroll position:", {
        x: x,
        y: y,
      });
    }, 220);
  }

  function decodeHtmlEntities(value) {
    const textarea = document.createElement("textarea");
    textarea.innerHTML = value || "";
    return textarea.value;
  }

  function slugify(value) {
    return (
      "#" +
      String(value || "")
        .toLowerCase()
        .replace(/&amp;|&/g, "and")
        .replace(/[^a-z0-9]+/g, "-")
        .replace(/^-+|-+$/g, "")
    );
  }

  function extractUrlFromCssBackground(backgroundValue) {
    if (!backgroundValue || backgroundValue === "none") return "";

    const matches = Array.from(
      String(backgroundValue).matchAll(/url\((["']?)(.*?)\1\)/g),
    );

    if (!matches.length) return "";

    const url = matches[matches.length - 1][2];

    if (!url || url.indexOf("data:") === 0) return "";

    return decodeHtmlEntities(url);
  }

  function findCardThumbnail(card) {
    if (!card) return "";

    const img = card.querySelector("img");
    if (img) {
      return img.currentSrc || img.src || "";
    }

    const candidates = [
      card,
      card.querySelector(".elementor-widget-wrap"),
      card.querySelector(".elementor-background-overlay"),
    ].filter(Boolean);

    card
      .querySelectorAll("[style], .elementor-widget-wrap, .elementor-element")
      .forEach(function (el) {
        candidates.push(el);
      });

    for (const el of candidates) {
      const bg = extractUrlFromCssBackground(
        window.getComputedStyle(el).backgroundImage,
      );
      if (bg) return bg;
    }

    return "";
  }

  function parsePopupIdFromElementorHref(href) {
    if (!href || href.indexOf("elementor-action") === -1) return null;

    try {
      const decodedHref = decodeURIComponent(href);
      const match = decodedHref.match(/settings=([^&]+)/);

      if (!match || !match[1]) return null;

      const settings = JSON.parse(window.atob(match[1]));
      const id = Number(settings.id);

      return Number.isFinite(id) ? id : null;
    } catch (error) {
      log("Could not decode Elementor popup href:", href, error);
      return null;
    }
  }

  function makeElementorPopupHref(popupId) {
    const settings = window.btoa(
      JSON.stringify({
        id: String(popupId),
        toggle: false,
      }),
    );

    return (
      "#elementor-action%3Aaction%3Dpopup%3Aopen%26settings%3D" +
      encodeURIComponent(settings)
    );
  }

  function getHashFromCard(card, popupId, title) {
    if (card.dataset.popupHash) {
      return card.dataset.popupHash.trim();
    }

    if (POPUP_HASH_MAP[popupId]) {
      return POPUP_HASH_MAP[popupId];
    }

    const buttonWidget = card.querySelector('[id^="click-"]');
    if (buttonWidget && buttonWidget.id) {
      return "#" + buttonWidget.id.replace(/^click-/, "");
    }

    return slugify(title);
  }

  function getTitleFromCard(card) {
    const explicit = card.dataset.videoTitle || card.dataset.title;
    if (explicit) return explicit.trim();

    const h5 = card.querySelector("h5.elementor-heading-title, h5");
    if (h5 && h5.textContent.trim()) return h5.textContent.trim();

    const heading = card.querySelector(".elementor-heading-title");
    if (heading && heading.textContent.trim())
      return heading.textContent.trim();

    return "";
  }

  function getPopupIdFromCard(card) {
    if (card.dataset.popupId) {
      const explicitId = Number(card.dataset.popupId);
      if (Number.isFinite(explicitId)) return explicitId;
    }

    const actionLink = card.querySelector(
      'a[href*="elementor-action"], a[href*="popup%3Aopen"], a[href*="popup:open"]',
    );
    if (!actionLink) return null;

    return parsePopupIdFromElementorHref(actionLink.getAttribute("href"));
  }

  function collectPageVideos() {
    const source = document.querySelector(CONFIG.featuredSourceSelector);

    if (!source) {
      log("No featured source section found.");
      return [];
    }

    const rawCards = Array.from(
      source.querySelectorAll(CONFIG.featuredCardSelector),
    ).slice(0, CONFIG.maxFeaturedVideos);

    const seenPopupIds = new Set();

    const videos = rawCards
      .map(function (card, index) {
        const popupId = getPopupIdFromCard(card);
        const title = getTitleFromCard(card);

        if (!popupId || !title) {
          log("Skipping card because popup ID or title was not found.", card);
          return null;
        }

        if (seenPopupIds.has(popupId)) {
          log("Skipping duplicate popup ID:", popupId);
          return null;
        }

        seenPopupIds.add(popupId);

        const hash = getHashFromCard(card, popupId, title);
        const thumb =
          card.dataset.videoThumb ||
          card.dataset.thumbnail ||
          findCardThumbnail(card);
        const href = makeElementorPopupHref(popupId);

        if (hash) {
          POPUP_HASH_MAP[popupId] = hash;
          HASH_POPUP_MAP[hash] = popupId;
        }

        return {
          order: index + 1,
          popupId: popupId,
          hash: hash,
          title: title,
          thumb: thumb,
          href: href,
        };
      })
      .filter(Boolean);

    log("Collected page videos:", videos);

    return videos;
  }

  function getRelatedVideos(currentId) {
    const related = [];

    for (const video of pageVideos) {
      if (Number(video.popupId) === Number(currentId)) continue;

      related.push(video);

      if (related.length >= CONFIG.relatedCount) break;
    }

    return related;
  }

  function getPopupRoot(popupId) {
    return document.querySelector(
      '[data-elementor-type="popup"][data-elementor-id="' +
        Number(popupId) +
        '"]',
    );
  }

  function getOpenPopupRoot() {
    const roots = Array.from(
      document.querySelectorAll(
        '[data-elementor-type="popup"][data-elementor-id]',
      ),
    );

    return (
      roots.find(function (root) {
        const style = window.getComputedStyle(root);
        return (
          style.display !== "none" &&
          root.offsetWidth > 0 &&
          root.offsetHeight > 0
        );
      }) || null
    );
  }

  function updateImageWidget(imageWidget, video) {
    if (!imageWidget || !video) return;

    const img = imageWidget.querySelector("img");
    const caption = imageWidget.querySelector("figcaption");

    if (img && video.thumb) {
      img.removeAttribute("srcset");
      img.removeAttribute("sizes");
      img.src = video.thumb;
      img.alt = video.title || "";
    }

    /**
     * Prevent old <source type="image/webp"> from overriding the new image.
     */
    imageWidget.querySelectorAll("picture source").forEach(function (source) {
      source.removeAttribute("srcset");
      source.removeAttribute("sizes");
    });

    if (caption) {
      caption.textContent = video.title || "";
    }

    imageWidget.dataset.ommPopupId = String(video.popupId);
    imageWidget.dataset.ommPopupHash = video.hash || "";
    imageWidget.style.cursor = "pointer";
  }

  function updateButtonWidget(buttonWidget, video) {
    if (!buttonWidget || !video) return;

    const link = buttonWidget.querySelector("a");

    if (link) {
      link.href = video.href;
      link.dataset.ommPopupId = String(video.popupId);
      link.dataset.ommPopupHash = video.hash || "";
      link.setAttribute("aria-label", "Watch " + video.title);
    }

    buttonWidget.dataset.ommPopupId = String(video.popupId);
    buttonWidget.dataset.ommPopupHash = video.hash || "";
  }

  function updateRelatedCard(card, video) {
    if (!card) return;

    if (!video) {
      card.style.display = "none";
      return;
    }

    card.style.display = "";

    card.dataset.ommPopupId = String(video.popupId);
    card.dataset.ommPopupHash = video.hash || "";
    card.dataset.ommPopupTitle = video.title || "";

    const imageWidget =
      card.querySelector(CONFIG.popupRelatedImageSelector) ||
      card.querySelector(".elementor-widget-image");

    const buttonWidget =
      card.querySelector(CONFIG.popupRelatedButtonSelector) ||
      card.querySelector(".elementor-widget-button");

    updateImageWidget(imageWidget, video);
    updateButtonWidget(buttonWidget, video);
  }

  function populatePopupRelatedVideos(popupId) {
    if (!pageVideos.length) {
      pageVideos = collectPageVideos();
    }

    if (!pageVideos.length) {
      log("No page videos available; related popup cards not changed.");
      return;
    }

    const root = getPopupRoot(popupId);

    if (!root) {
      log("Popup root not found yet for ID:", popupId);
      return;
    }

    const lists = Array.from(
      root.querySelectorAll(CONFIG.popupRelatedListSelector),
    );

    if (!lists.length) {
      log("Popup has no dynamic related list classes:", popupId);
      return;
    }

    const related = getRelatedVideos(popupId);

    lists.forEach(function (list) {
      const cards = Array.from(
        list.querySelectorAll(CONFIG.popupRelatedCardSelector),
      ).slice(0, CONFIG.relatedCount);

      cards.forEach(function (card, index) {
        updateRelatedCard(card, related[index]);
      });
    });

    log("Updated related videos for popup:", popupId, related);
  }

  function isElementorPopupReady() {
    return !!(
      window.elementorProFrontend &&
      window.elementorProFrontend.modules &&
      window.elementorProFrontend.modules.popup
    );
  }

  function waitForElementorPopup(callback, attemptsLeft) {
    attemptsLeft = typeof attemptsLeft === "number" ? attemptsLeft : 40;

    if (isElementorPopupReady()) {
      callback();
      return;
    }

    if (attemptsLeft <= 0) {
      log("Elementor popup frontend module was not available.");
      return;
    }

    window.setTimeout(function () {
      waitForElementorPopup(callback, attemptsLeft - 1);
    }, 100);
  }

  function openPopupByVideo(video, event) {
    if (!video || !video.popupId) return;

    rememberScrollPosition();

    waitForElementorPopup(function () {
      const targetPopupId = Number(video.popupId);

      if (Number(currentPopupId) === targetPopupId) {
        populatePopupRelatedVideos(targetPopupId);
        return;
      }

      isInternalPopupNavigation = true;

      try {
        window.elementorProFrontend.modules.popup.closePopup({}, event || null);
      } catch (error) {
        log("closePopup warning:", error);
      }

      window.setTimeout(function () {
        try {
          window.elementorProFrontend.modules.popup.showPopup({
            id: targetPopupId,
          });

          currentPopupId = targetPopupId;

          /**
           * Do NOT manually set URL hash here during pilot.
           * The old global footer popupHashMap script will handle hash update
           * on elementor/popup/show.
           */

          window.setTimeout(function () {
            populatePopupRelatedVideos(targetPopupId);
          }, 150);
        } catch (error) {
          log("showPopup failed:", error);
        }

        window.setTimeout(function () {
          isInternalPopupNavigation = false;
        }, 500);
      }, 140);
    });
  }

  function findVideoByPopupId(popupId) {
    return (
      pageVideos.find(function (video) {
        return Number(video.popupId) === Number(popupId);
      }) || null
    );
  }

  function findVideoFromRelatedClickTarget(target) {
    const card = target.closest(CONFIG.popupRelatedCardSelector);
    const widget = target.closest(
      CONFIG.popupRelatedButtonSelector +
        ", " +
        CONFIG.popupRelatedImageSelector,
    );
    const link = target.closest("a[data-omm-popup-id]");

    const source = link || widget || card;

    if (!source) return null;

    const popupId = Number(
      source.dataset.ommPopupId || (card && card.dataset.ommPopupId),
    );

    if (!Number.isFinite(popupId)) return null;

    return {
      popupId: popupId,
      hash:
        source.dataset.ommPopupHash ||
        (card && card.dataset.ommPopupHash) ||
        POPUP_HASH_MAP[popupId] ||
        "",
      title:
        source.dataset.ommPopupTitle ||
        (card && card.dataset.ommPopupTitle) ||
        "",
      href: makeElementorPopupHref(popupId),
    };
  }

  function bindPopupOpenScrollMemory() {
    /**
     * Store scroll position before any normal Elementor popup open link is clicked.
     * This covers both first-section and second-section video cards.
     */
    document.addEventListener(
      "click",
      function (event) {
        const popupOpenLink = event.target.closest(
          'a[href*="elementor-action"], a[href*="popup%3Aopen"], a[href*="popup:open"]',
        );

        if (!popupOpenLink) return;

        rememberScrollPosition();
      },
      true,
    );
  }

  function bindRelatedClickInterceptor() {
    /**
     * Capture phase is intentional.
     * It prevents the old per-popup click-a/click-b/click-c scripts and .popup-f close handler
     * from overriding the new dynamic logic during the pilot.
     */
    document.addEventListener(
      "click",
      function (event) {
        const insideRelated =
          event.target.closest(CONFIG.popupRelatedButtonSelector) ||
          event.target.closest(CONFIG.popupRelatedImageSelector) ||
          event.target.closest(CONFIG.popupRelatedCardSelector);

        if (!insideRelated) return;

        const popupRoot = event.target.closest('[data-elementor-type="popup"]');
        if (!popupRoot) return;

        const video = findVideoFromRelatedClickTarget(event.target);

        if (!video || !video.popupId) return;

        event.preventDefault();
        event.stopPropagation();
        event.stopImmediatePropagation();

        openPopupByVideo(video, event);
      },
      true,
    );
  }

  function bindElementorPopupEvents() {
    if (!window.jQuery) {
      log("jQuery not found; Elementor popup events not bound.");
      return;
    }

    window
      .jQuery(document)
      .on("elementor/popup/show", function (event, popupId) {
        const id = Number(popupId);

        if (!Number.isFinite(id)) return;

        currentPopupId = id;

        /**
         * Do NOT set/push/replace URL hash here during the pilot.
         * The old global Scripts Inserter popupHashMap script is still responsible
         * for popup hash routing and hash updates.
         */

        window.setTimeout(function () {
          populatePopupRelatedVideos(id);
        }, 80);

        window.setTimeout(function () {
          populatePopupRelatedVideos(id);
        }, 350);
      });

    window.jQuery(document).on("elementor/popup/hide", function () {
      if (isInternalPopupNavigation) return;

      currentPopupId = null;

      /**
       * Do NOT clear URL hash here during the pilot.
       * The old global footer script already clears the hash.
       */

      restoreScrollPosition();
    });
  }

  function openInitialHashPopupIfNeeded() {
    const hash = window.location.hash;

    if (!hash) return;

    const popupId = HASH_POPUP_MAP[hash];

    if (!popupId) return;

    waitForElementorPopup(function () {
      window.setTimeout(function () {
        const openRoot = getOpenPopupRoot();

        if (openRoot) {
          const openId = Number(openRoot.dataset.elementorId);
          currentPopupId = openId;
          populatePopupRelatedVideos(openId);
          return;
        }

        try {
          window.elementorProFrontend.modules.popup.showPopup({
            id: popupId,
          });

          currentPopupId = popupId;

          window.setTimeout(function () {
            populatePopupRelatedVideos(popupId);
          }, 200);
        } catch (error) {
          log("Initial hash popup open failed:", error);
        }
      }, 900);
    });
  }

  function exposeDebugHelpers() {
    window.OMMVideoPopupPilot = {
      get videos() {
        return pageVideos;
      },
      recollect: function () {
        pageVideos = collectPageVideos();
        return pageVideos;
      },
      populate: populatePopupRelatedVideos,
      open: function (popupId) {
        const video = findVideoByPopupId(Number(popupId)) || {
          popupId: Number(popupId),
          hash: POPUP_HASH_MAP[Number(popupId)] || "",
          href: makeElementorPopupHref(Number(popupId)),
        };

        openPopupByVideo(video);
      },
      rememberScroll: rememberScrollPosition,
      restoreScroll: restoreScrollPosition,
    };
  }

  function init() {
    if (hasInitialized) return;
    hasInitialized = true;

    if (!isEnabledPage()) {
      return;
    }

    pageVideos = collectPageVideos();

    if (!pageVideos.length) {
      log("Pilot script loaded, but no featured videos were collected.");
    }

    bindPopupOpenScrollMemory();
    bindRelatedClickInterceptor();
    bindElementorPopupEvents();

    /**
     * Keep this commented while the old global footer hash script is active.
     */
    // openInitialHashPopupIfNeeded();

    exposeDebugHelpers();

    log("Initialized.");
  }

  if (document.readyState === "complete") {
    init();
  } else {
    window.addEventListener("load", init);
  }
})();
