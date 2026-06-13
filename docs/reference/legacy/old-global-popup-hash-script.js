window.addEventListener("load", () => {
  const popupHashMap = {
    22840: "#nick-kyrgios",
    24155: "#valiant-national",
    24487: "#women-in-ai",
    24491: "#vinnies",
    24495: "#unsw-aviation",
    24499: "#trisearch",
    24503: "#the-kids-cancer-project",
    24507: "#the-voice-to-parliament",
    24511: "#the-missing-link",
    24514: "#oz-harvest",
    24517: "#nsw-health-animation",
    24520: "#the-langham-hotels",
    24523: "#ippudo",
    24526: "#edith-cowan-university",
    24529: "#1-minute-media-animation-showreel",
    24532: "#ai-media",
    24535: "#professor-justin-yurbery",
    24538: "#afr",
    24541: "#heinemann",
    24544: "#fleetpartners",
    26500: "#dove",
    26530: "#my-health-app",
    26552: "#hv-fintech",
    26884: "#POCruises",
    28002: "#routine-skin",
    28005: "#Pedl-Bikes",
    28008: "#AH-Beard",
    28011: "#IRoad",
    33621: "#SSKB",
    33737: "#Banqeta",
    33754: "#DigitalFrontDoor",
    33772: "#PrintLocker",
    33792: "#ProductivityPack",
    34033: "#Refilled",
    36165: "#Officeworks",
    36185: "#GandM",
    36199: "#Beamtree",
    36207: "#CFCH",
    36282: "#Whiskey&Wealth",
    36358: "#MadDogs&Englishmen",
    36380: "#AIMedia",
    36406: "#Medmate",
    36726: "#CairnsCouncil",
  };

  const hashPopupMap = {};
  for (const [id, hash] of Object.entries(popupHashMap)) {
    hashPopupMap[hash] = Number(id);
  }

  const currentHash = window.location.hash;
  if (currentHash && hashPopupMap[currentHash]) {
    setTimeout(() => {
      elementorProFrontend.modules.popup.showPopup({
        id: hashPopupMap[currentHash],
      });
    }, 900);
  }

  jQuery(document).on("elementor/popup/show", (event, id) => {
    const hash = popupHashMap[id];
    if (hash) {
      history.pushState(null, "", hash);
    }
  });

  jQuery(document).on("elementor/popup/hide", (event, id) => {
    history.pushState(null, "", window.location.pathname);
  });
});
