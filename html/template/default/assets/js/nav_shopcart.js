/* ==========================================================================
   Shopcart Navigation & UI Interactive Behaviors
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  // 1. Mobile Drawer Toggle
  var mobileToggle = document.getElementById('shopcartMobileNavToggle');
  var mobileDrawer = document.getElementById('shopcartMobileDrawer');
  var drawerOverlay = document.getElementById('shopcartDrawerOverlay');
  var drawerClose = document.getElementById('shopcartDrawerClose');

  function openDrawer() {
    if (mobileDrawer) {
      mobileDrawer.classList.add('is-open');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeDrawer() {
    if (mobileDrawer) {
      mobileDrawer.classList.remove('is-open');
      document.body.style.overflow = '';
    }
  }

  if (mobileToggle) {
    mobileToggle.addEventListener('click', openDrawer);
  }
  if (drawerOverlay) {
    drawerOverlay.addEventListener('click', closeDrawer);
  }
  if (drawerClose) {
    drawerClose.addEventListener('click', closeDrawer);
  }

  // 2. Mobile Accordion for Categories
  var accordionBtns = document.querySelectorAll('.shopcart-mobile-accordion__btn');
  accordionBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var parent = btn.closest('.shopcart-mobile-accordion');
      if (parent) {
        parent.classList.toggle('is-open');
      }
    });
  });

  // 3. Location Selector Item Click
  var locationItems = document.querySelectorAll('.shopcart-loc-item');
  var currentLocationText = document.getElementById('shopcartCurrentLocation');
  
  // Load saved location if present
  var savedLoc = localStorage.getItem('shopcart_selected_location');
  if (savedLoc && currentLocationText) {
    currentLocationText.textContent = savedLoc;
  }

  locationItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var loc = item.getAttribute('data-location');
      if (loc && currentLocationText) {
        currentLocationText.textContent = loc;
        localStorage.setItem('shopcart_selected_location', loc);
      }
      // Close dropdown if open
      var dropdown = item.closest('.shopcart-dropdown');
      if (dropdown) {
        dropdown.classList.remove('is-open');
      }
    });
  });

  // 4. Click Toggle on Mobile / Touch for Dropdowns
  var dropdownToggles = document.querySelectorAll('.shopcart-dropdown__toggle');
  dropdownToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
      if (window.innerWidth < 992 || toggle.getAttribute('type') === 'button') {
        var dropdown = toggle.closest('.shopcart-dropdown');
        if (dropdown) {
          // Close other open dropdowns
          document.querySelectorAll('.shopcart-dropdown.is-open').forEach(function (openDd) {
            if (openDd !== dropdown) {
              openDd.classList.remove('is-open');
            }
          });
          dropdown.classList.toggle('is-open');
        }
      }
    });
  });

  // Close dropdowns on outside click
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.shopcart-dropdown')) {
      document.querySelectorAll('.shopcart-dropdown.is-open').forEach(function (dropdown) {
        dropdown.classList.remove('is-open');
      });
    }
  });
});
