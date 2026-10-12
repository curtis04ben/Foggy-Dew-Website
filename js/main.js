/* Foggy Dew Farm: draft prototype interactions (no dependencies) */
(function () {
  "use strict";

  var header = document.querySelector(".site-header");
  var nav = document.getElementById("site-nav");
  var toggle = document.querySelector(".nav-toggle");
  var reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  /* ---------- Image fallback: swap broken images for a labelled placeholder ---------- */
  function markMissing(img) {
    var holder = img.closest(".media");
    if (holder) holder.classList.add("is-missing");
  }
  document.querySelectorAll(".media img").forEach(function (img) {
    if (img.complete && img.naturalWidth === 0) markMissing(img);
    img.addEventListener("error", function () { markMissing(img); });
  });

  /* ---------- Header state on scroll ---------- */
  function onScroll() {
    header.classList.toggle("is-scrolled", window.scrollY > 40);
  }
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  /* ---------- Mobile navigation ---------- */
  function setNav(open) {
    toggle.setAttribute("aria-expanded", String(open));
    nav.classList.toggle("is-open", open);
    document.body.classList.toggle("nav-open", open);
  }
  toggle.addEventListener("click", function () {
    setNav(toggle.getAttribute("aria-expanded") !== "true");
  });
  nav.addEventListener("click", function (e) {
    if (e.target.closest("a")) setNav(false);
  });
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape" && nav.classList.contains("is-open")) {
      setNav(false);
      toggle.focus();
    }
  });
  window.matchMedia("(min-width: 901px)").addEventListener("change", function (mq) {
    if (mq.matches) setNav(false);
  });

  /* ---------- Scroll-spy: highlight the current section in the nav ---------- */
  var navLinks = Array.prototype.slice.call(nav.querySelectorAll("a[href^='#']"));
  var spyTargets = navLinks
    .map(function (a) { return document.querySelector(a.getAttribute("href")); })
    .filter(Boolean);
  // Camping sits inside "Experiences" for nav purposes
  var spyAlias = { camping: "experiences" };
  var camping = document.getElementById("camping");
  if (camping) spyTargets.push(camping);

  if ("IntersectionObserver" in window) {
    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var id = spyAlias[entry.target.id] || entry.target.id;
        navLinks.forEach(function (a) {
          if (a.getAttribute("href") === "#" + id) a.setAttribute("aria-current", "true");
          else a.removeAttribute("aria-current");
        });
      });
    }, { rootMargin: "-45% 0px -50% 0px" });
    spyTargets.forEach(function (el) { spy.observe(el); });
  }

  /* ---------- Reveal-on-scroll ---------- */
  var reveals = document.querySelectorAll(".reveal");
  if (reduceMotion || !("IntersectionObserver" in window)) {
    reveals.forEach(function (el) { el.classList.add("is-visible"); });
  } else {
    var revealer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add("is-visible");
          revealer.unobserve(entry.target);
        }
      });
    }, { rootMargin: "0px 0px -8% 0px", threshold: 0.08 });
    reveals.forEach(function (el, i) {
      // Small stagger for siblings in grids
      el.style.transitionDelay = (i % 4) * 80 + "ms";
      revealer.observe(el);
    });
  }

  /* ---------- CTA buttons pre-select the enquiry topic ---------- */
  var interest = document.getElementById("f-interest");
  if (interest) document.querySelectorAll("[data-interest]").forEach(function (link) {
    link.addEventListener("click", function () {
      var value = link.getAttribute("data-interest");
      for (var i = 0; i < interest.options.length; i++) {
        if (interest.options[i].text === value) { interest.selectedIndex = i; break; }
      }
    });
  });

  /* ---------- Enquiry form (prototype: validates, but sends nothing) ---------- */
  var form = document.getElementById("enquiry-form");
  var status = document.getElementById("form-status");

  if (form) form.addEventListener("submit", function (e) {
    e.preventDefault();
    var firstInvalid = null;
    form.querySelectorAll("[required]").forEach(function (field) {
      var ok = field.checkValidity() && field.value.trim() !== "";
      field.setAttribute("aria-invalid", ok ? "false" : "true");
      if (!ok && !firstInvalid) firstInvalid = field;
    });

    if (firstInvalid) {
      status.className = "form-status is-error";
      status.textContent = "Please fill in your name, a valid email address and a message.";
      firstInvalid.focus();
      return;
    }

    var name = form.elements.name.value.trim().split(" ")[0];
    status.className = "form-status is-ok";
    status.textContent = "Thank you, " + name + ". This is a prototype, so your enquiry has not been sent. " +
      "Once the site is live, this form will be connected to the farm's inbox.";
  });

  /* ---------- Footer year ---------- */
  var year = document.getElementById("year");
  if (year) year.textContent = new Date().getFullYear();
})();
