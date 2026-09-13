/* ==========================================================================
   Shopcart Navigation & UI Interactive Behaviors
   ========================================================================== */

document.addEventListener('DOMContentLoaded', function () {
  /* ========================================================================
     1. Mobile Drawer Toggle
     ======================================================================== */
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

  /* ========================================================================
     2. Mobile Accordion for Categories
     ======================================================================== */
  var accordionBtns = document.querySelectorAll('.shopcart-mobile-accordion__btn');
  accordionBtns.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var parent = btn.closest('.shopcart-mobile-accordion');
      if (parent) {
        parent.classList.toggle('is-open');
      }
    });
  });

  /* ========================================================================
     3. Location Selector Item Click
     ======================================================================== */
  var locationItems = document.querySelectorAll('.shopcart-loc-item');
  var currentLocationText = document.getElementById('shopcartCurrentLocation');
  
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
      var dropdown = item.closest('.shopcart-dropdown');
      if (dropdown) {
        dropdown.classList.remove('is-open');
      }
    });
  });

  /* ========================================================================
     4. Header Dropdowns (Categories / Location)
     ======================================================================== */
  var headerDropdownToggles = document.querySelectorAll('.shopcart-dropdown__toggle');
  headerDropdownToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var dropdown = toggle.closest('.shopcart-dropdown');
      if (dropdown) {
        // Close other open header dropdowns
        document.querySelectorAll('.shopcart-dropdown.is-open').forEach(function (openDd) {
          if (openDd !== dropdown) {
            openDd.classList.remove('is-open');
          }
        });
        // Close filter dropdowns
        document.querySelectorAll('.shopcart-filter-dropdown.is-open').forEach(function (fDd) {
          fDd.classList.remove('is-open');
        });
        dropdown.classList.toggle('is-open');
      }
    });
  });

  /* ========================================================================
     5. Product Filter Pills Dropdown Toggles
     ======================================================================== */
  var filterToggles = document.querySelectorAll('.shopcart-filter-toggle');
  
  filterToggles.forEach(function (toggle) {
    toggle.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var parentDropdown = toggle.closest('.shopcart-filter-dropdown');
      if (!parentDropdown) return;

      var wasOpen = parentDropdown.classList.contains('is-open');

      // Close all other filter dropdowns
      document.querySelectorAll('.shopcart-filter-dropdown.is-open').forEach(function (dd) {
        dd.classList.remove('is-open');
      });

      // Close header dropdowns
      document.querySelectorAll('.shopcart-dropdown.is-open').forEach(function (dd) {
        dd.classList.remove('is-open');
      });

      // Toggle current dropdown
      if (!wasOpen) {
        parentDropdown.classList.add('is-open');
      }
    });
  });

  // Prevent clicks inside the dropdown menu from closing it immediately unless clicking an item
  var filterMenus = document.querySelectorAll('.shopcart-filter-menu');
  filterMenus.forEach(function (menu) {
    menu.addEventListener('click', function (e) {
      e.stopPropagation();
    });
  });

  // Global Outside Click to Close All Dropdowns
  document.addEventListener('click', function (e) {
    if (!e.target.closest('.shopcart-dropdown')) {
      document.querySelectorAll('.shopcart-dropdown.is-open').forEach(function (dropdown) {
        dropdown.classList.remove('is-open');
      });
    }
    if (!e.target.closest('.shopcart-filter-dropdown')) {
      document.querySelectorAll('.shopcart-filter-dropdown.is-open').forEach(function (fDropdown) {
        fDropdown.classList.remove('is-open');
      });
    }
  });

  // Escape key closes open dropdowns
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' || e.keyCode === 27) {
      document.querySelectorAll('.shopcart-dropdown.is-open, .shopcart-filter-dropdown.is-open').forEach(function (dd) {
        dd.classList.remove('is-open');
      });
    }
  });

  /* ========================================================================
     6. Live Product Filtering & Sorting System
     ======================================================================== */
  var productGrid = document.getElementById('shopcartProductGrid');
  if (!productGrid) return;

  var productCards = Array.from(productGrid.querySelectorAll('.shopcart-product-card'));
  if (productCards.length === 0) return;

  // Store initial index for default "featured" sort
  productCards.forEach(function (card, idx) {
    card.setAttribute('data-default-order', idx);
  });

  // Active filter state
  var filterState = {
    type: 'all',
    priceMin: 0,
    priceMax: 99999,
    rating: 0,
    color: 'all',
    material: 'all',
    offer: 'all',
    sort: 'featured'
  };

  // Helper to update pill button label & styling
  function updatePillLabel(groupName, text, isActive) {
    var groupEl = document.querySelector('.shopcart-filter-dropdown[data-filter-group="' + groupName + '"]');
    if (!groupEl) return;
    var labelEl = groupEl.querySelector('.shopcart-filter-label');
    var pillBtn = groupEl.querySelector('.shopcart-filter-pill');
    if (labelEl) {
      labelEl.textContent = text;
    }
    if (pillBtn) {
      if (isActive) {
        pillBtn.classList.add('is-filtered');
      } else {
        pillBtn.classList.remove('is-filtered');
      }
    }
  }

  // Core Filtering & Sorting Execution
  function applyFiltersAndSort() {
    var visibleCards = [];

    productCards.forEach(function (card) {
      var cType = (card.getAttribute('data-type') || '').toLowerCase();
      var cPrice = parseFloat(card.getAttribute('data-price') || '0');
      var cRating = parseFloat(card.getAttribute('data-rating') || '0');
      var cColor = (card.getAttribute('data-color') || '').toLowerCase();
      var cMaterial = (card.getAttribute('data-material') || '').toLowerCase();
      var cOffer = (card.getAttribute('data-offer') || '').toLowerCase();

      // Check Type
      var matchType = (filterState.type === 'all') || (cType === filterState.type.toLowerCase());

      // Check Price
      var matchPrice = (cPrice >= filterState.priceMin) && (cPrice <= filterState.priceMax);

      // Check Rating
      var matchRating = (cRating >= filterState.rating);

      // Check Color
      var matchColor = (filterState.color === 'all') || (cColor === filterState.color.toLowerCase());

      // Check Material
      var matchMaterial = (filterState.material === 'all') || (cMaterial === filterState.material.toLowerCase());

      // Check Offer
      var matchOffer = true;
      if (filterState.offer !== 'all') {
        if (filterState.offer === '50') {
          matchOffer = parseFloat(cOffer) >= 50;
        } else if (filterState.offer === '30') {
          matchOffer = parseFloat(cOffer) >= 30;
        } else if (filterState.offer === 'free-shipping') {
          matchOffer = cOffer === 'free-shipping' || cPrice >= 50;
        }
      }

      if (matchType && matchPrice && matchRating && matchColor && matchMaterial && matchOffer) {
        card.style.display = '';
        visibleCards.push(card);
      } else {
        card.style.display = 'none';
      }
    });

    // Sorting
    visibleCards.sort(function (a, b) {
      var priceA = parseFloat(a.getAttribute('data-price') || '0');
      var priceB = parseFloat(b.getAttribute('data-price') || '0');
      var ratingA = parseFloat(a.getAttribute('data-rating') || '0');
      var ratingB = parseFloat(b.getAttribute('data-rating') || '0');
      var dateA = new Date(a.getAttribute('data-date') || '2026-01-01').getTime();
      var dateB = new Date(b.getAttribute('data-date') || '2026-01-01').getTime();
      var orderA = parseInt(a.getAttribute('data-default-order') || '0', 10);
      var orderB = parseInt(b.getAttribute('data-default-order') || '0', 10);

      switch (filterState.sort) {
        case 'price-asc':
          return priceA - priceB;
        case 'price-desc':
          return priceB - priceA;
        case 'rating':
          return ratingB - ratingA;
        case 'newest':
          return dateB - dateA;
        case 'featured':
        default:
          return orderA - orderB;
      }
    });

    // Re-append sorted visible cards to grid
    visibleCards.forEach(function (card) {
      productGrid.appendChild(card);
    });

    // Handle No Results State
    var emptyState = document.getElementById('shopcartEmptyState');
    if (visibleCards.length === 0) {
      if (!emptyState) {
        emptyState = document.createElement('div');
        emptyState.id = 'shopcartEmptyState';
        emptyState.className = 'shopcart-empty-state';
        emptyState.innerHTML = '<div class="shopcart-empty-icon"><i class="fas fa-search"></i></div>' +
          '<h3>No matching headphones found</h3>' +
          '<p>Try adjusting your filters to find what you are looking for.</p>' +
          '<button type="button" class="shopcart-reset-btn" id="shopcartEmptyResetBtn">Reset All Filters</button>';
        productGrid.parentNode.appendChild(emptyState);

        var emptyResetBtn = document.getElementById('shopcartEmptyResetBtn');
        if (emptyResetBtn) {
          emptyResetBtn.addEventListener('click', resetAllFilters);
        }
      }
      emptyState.style.display = 'block';
    } else if (emptyState) {
      emptyState.style.display = 'none';
    }
  }

  // 6.1 Headphone Type Filter Items
  var typeItems = document.querySelectorAll('.shopcart-filter-dropdown[data-filter-group="type"] .shopcart-filter-item');
  typeItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var val = item.getAttribute('data-value') || 'all';
      typeItems.forEach(function (el) { el.classList.remove('is-active'); });
      item.classList.add('is-active');

      filterState.type = val;
      if (val === 'all') {
        updatePillLabel('type', 'Headphone Type', false);
      } else {
        updatePillLabel('type', item.textContent.trim(), true);
      }

      var dd = item.closest('.shopcart-filter-dropdown');
      if (dd) dd.classList.remove('is-open');
      applyFiltersAndSort();
    });
  });

  // 6.2 Price Filter Items
  var priceItems = document.querySelectorAll('.shopcart-filter-dropdown[data-filter-group="price"] .shopcart-filter-item');
  priceItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var min = parseFloat(item.getAttribute('data-min') || '0');
      var max = parseFloat(item.getAttribute('data-max') || '99999');
      priceItems.forEach(function (el) { el.classList.remove('is-active'); });
      item.classList.add('is-active');

      filterState.priceMin = min;
      filterState.priceMax = max;
      if (min === 0 && max >= 99999) {
        updatePillLabel('price', 'Price', false);
      } else {
        updatePillLabel('price', item.textContent.trim(), true);
      }

      var dd = item.closest('.shopcart-filter-dropdown');
      if (dd) dd.classList.remove('is-open');
      applyFiltersAndSort();
    });
  });

  // 6.3 Review Filter Items
  var reviewItems = document.querySelectorAll('.shopcart-filter-dropdown[data-filter-group="review"] .shopcart-filter-item');
  reviewItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var rating = parseFloat(item.getAttribute('data-rating') || '0');
      reviewItems.forEach(function (el) { el.classList.remove('is-active'); });
      item.classList.add('is-active');

      filterState.rating = rating;
      if (rating === 0) {
        updatePillLabel('review', 'Review', false);
      } else {
        updatePillLabel('review', '★ ' + rating + ' & up', true);
      }

      var dd = item.closest('.shopcart-filter-dropdown');
      if (dd) dd.classList.remove('is-open');
      applyFiltersAndSort();
    });
  });

  // 6.4 Color Filter Items
  var colorItems = document.querySelectorAll('.shopcart-filter-dropdown[data-filter-group="color"] .shopcart-color-item');
  colorItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var color = item.getAttribute('data-color') || 'all';
      colorItems.forEach(function (el) { el.classList.remove('is-active'); });
      item.classList.add('is-active');

      filterState.color = color;
      if (color === 'all') {
        updatePillLabel('color', 'Color', false);
      } else {
        var labelText = item.textContent.trim();
        updatePillLabel('color', labelText, true);
      }

      var dd = item.closest('.shopcart-filter-dropdown');
      if (dd) dd.classList.remove('is-open');
      applyFiltersAndSort();
    });
  });

  // 6.5 Material Filter Items
  var materialItems = document.querySelectorAll('.shopcart-filter-dropdown[data-filter-group="material"] .shopcart-filter-item');
  materialItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var mat = item.getAttribute('data-material') || 'all';
      materialItems.forEach(function (el) { el.classList.remove('is-active'); });
      item.classList.add('is-active');

      filterState.material = mat;
      if (mat === 'all') {
        updatePillLabel('material', 'Material', false);
      } else {
        updatePillLabel('material', item.textContent.trim(), true);
      }

      var dd = item.closest('.shopcart-filter-dropdown');
      if (dd) dd.classList.remove('is-open');
      applyFiltersAndSort();
    });
  });

  // 6.6 Offer Filter Items
  var offerItems = document.querySelectorAll('.shopcart-filter-dropdown[data-filter-group="offer"] .shopcart-filter-item');
  offerItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var offer = item.getAttribute('data-offer') || 'all';
      offerItems.forEach(function (el) { el.classList.remove('is-active'); });
      item.classList.add('is-active');

      filterState.offer = offer;
      if (offer === 'all') {
        updatePillLabel('offer', 'Offer', false);
      } else {
        updatePillLabel('offer', item.textContent.trim(), true);
      }

      var dd = item.closest('.shopcart-filter-dropdown');
      if (dd) dd.classList.remove('is-open');
      applyFiltersAndSort();
    });
  });

  // 6.7 Sort By Items
  var sortItems = document.querySelectorAll('.shopcart-sort-dropdown .shopcart-sort-item');
  var sortLabel = document.getElementById('currentSortLabel');
  sortItems.forEach(function (item) {
    item.addEventListener('click', function (e) {
      e.preventDefault();
      var sort = item.getAttribute('data-sort') || 'featured';
      sortItems.forEach(function (el) { el.classList.remove('is-active'); });
      item.classList.add('is-active');

      filterState.sort = sort;
      if (sortLabel) {
        sortLabel.textContent = item.textContent.trim();
      }

      var dd = item.closest('.shopcart-filter-dropdown');
      if (dd) dd.classList.remove('is-open');
      applyFiltersAndSort();
    });
  });

  // 6.8 Reset All Filters Button
  function resetAllFilters() {
    filterState.type = 'all';
    filterState.priceMin = 0;
    filterState.priceMax = 99999;
    filterState.rating = 0;
    filterState.color = 'all';
    filterState.material = 'all';
    filterState.offer = 'all';
    filterState.sort = 'featured';

    // Reset labels
    updatePillLabel('type', 'Headphone Type', false);
    updatePillLabel('price', 'Price', false);
    updatePillLabel('review', 'Review', false);
    updatePillLabel('color', 'Color', false);
    updatePillLabel('material', 'Material', false);
    updatePillLabel('offer', 'Offer', false);
    if (sortLabel) sortLabel.textContent = 'Featured';

    // Reset active items in all dropdown menus
    document.querySelectorAll('.shopcart-filter-dropdown .shopcart-filter-item, .shopcart-filter-dropdown .shopcart-color-item, .shopcart-sort-item').forEach(function (el) {
      el.classList.remove('is-active');
    });

    // Set defaults as active
    document.querySelectorAll('.shopcart-filter-menu').forEach(function (menu) {
      var first = menu.querySelector('.shopcart-filter-item, .shopcart-color-item, .shopcart-sort-item');
      if (first) first.classList.add('is-active');
    });

    // Close all open dropdowns
    document.querySelectorAll('.shopcart-filter-dropdown.is-open').forEach(function (dd) {
      dd.classList.remove('is-open');
    });

    applyFiltersAndSort();
  }

  var resetBtn = document.getElementById('shopcartResetFiltersBtn');
  if (resetBtn) {
    resetBtn.addEventListener('click', function (e) {
      e.preventDefault();
      resetAllFilters();
    });
  }

  /* ========================================================================
     7. Interactive Wishlist & Add-to-Cart Actions
     ======================================================================== */
  var wishlistBtns = document.querySelectorAll('.shopcart-wishlist-btn');
  wishlistBtns.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      var icon = btn.querySelector('i');
      if (icon) {
        if (icon.classList.contains('far')) {
          icon.classList.remove('far');
          icon.classList.add('fas');
          icon.style.color = '#ef4444';
        } else {
          icon.classList.remove('fas');
          icon.classList.add('far');
          icon.style.color = '';
        }
      }
    });
  });

  var addToCartBtns = document.querySelectorAll('.shopcart-add-cart-btn');
  addToCartBtns.forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      // If it's a link to cart, let's provide visual feedback
      var originalText = btn.textContent;
      btn.textContent = '✓ Added!';
      btn.style.background = '#003d29';
      btn.style.color = '#ffffff';
      btn.style.borderColor = '#003d29';

      setTimeout(function () {
        btn.textContent = originalText;
        btn.style.background = '';
        btn.style.color = '';
        btn.style.borderColor = '';
      }, 1400);
    });
  });
});
