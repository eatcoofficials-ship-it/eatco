/* EDIT HERE — Storefront interactions and motion. All product data comes from data.php. */
(() => {

  // Mobile navigation: bind independently and use delegated events for reliable touch support.
  const menuButton = document.querySelector('.menu-toggle');
  const mobileDrawer = document.getElementById('mobile-navigation');
  const mobileBackdrop = document.querySelector('.mobile-backdrop');
  const drawerClose = mobileDrawer?.querySelector('.drawer-close');
  let menuWasOpen = false;
  function openMenu() {
    if (!menuButton || !mobileDrawer) return;
    menuWasOpen = true;
    mobileDrawer.classList.add('open');
    mobileBackdrop?.classList.add('open');
    menuButton.setAttribute('aria-expanded', 'true');
    menuButton.setAttribute('aria-label', 'Close menu');
    mobileDrawer.setAttribute('aria-hidden', 'false');
    document.body.classList.add('drawer-open');
    drawerClose?.focus({ preventScroll: true });
  }
  function closeMenu() {
    if (!menuButton || !mobileDrawer) return;
    menuWasOpen = false;
    mobileDrawer.classList.remove('open');
    mobileBackdrop?.classList.remove('open');
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Open menu');
    mobileDrawer.setAttribute('aria-hidden', 'true');
    document.body.classList.remove('drawer-open');
    if (document.activeElement === drawerClose) menuButton.focus({ preventScroll: true });
  }
  menuButton?.addEventListener('click', () => {
    if (menuButton.getAttribute('aria-expanded') === 'true') closeMenu();
    else openMenu();
  });
  drawerClose?.addEventListener('click', closeMenu);
  mobileBackdrop?.addEventListener('click', closeMenu);
  mobileDrawer?.addEventListener('click', event => {
    const link = event.target.closest('a[href]');
    if (link) closeMenu();
  });
  document.addEventListener('keydown', event => {
    if (event.key === 'Escape' && menuWasOpen) closeMenu();
    if (event.key === 'Tab' && menuWasOpen && mobileDrawer) {
      const focusable = [...mobileDrawer.querySelectorAll('a[href],button:not([disabled])')];
      if (!focusable.length) return;
      const first = focusable[0], last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) { event.preventDefault(); last.focus(); }
      else if (!event.shiftKey && document.activeElement === last) { event.preventDefault(); first.focus(); }
    }
  });
  mobileDrawer?.setAttribute('aria-hidden', 'true');


  const products = window.EATCO_PRODUCTS || [];
  const images = window.EATCO_IMAGES || {};
  const grid = document.getElementById('product-grid');
  const searchInput = document.getElementById('product-search');
  const globalSearch = document.getElementById('global-search');
  const categorySearch = document.getElementById('search-category');
  const supplierFilter = document.getElementById('supplier-filter');
  const sortSelect = document.getElementById('sort-select');
  const priceFilter = document.getElementById('price-filter');
  const moqFilter = document.getElementById('moq-filter');
  const cards = [...document.querySelectorAll('.product-card')];
  let activeCategory = '';
  let quoteItems = (() => { try { return JSON.parse(localStorage.getItem('eatco-quote') || '[]'); } catch (error) { return []; } })();
  let savedItems = (() => { try { return JSON.parse(localStorage.getItem('eatco-saved') || '[]'); } catch (error) { return []; } })();
  const toast = document.querySelector('.toast');
  let toastTimer;

  // The theme switch uses the same centered flip-and-return motion as the currency control.
  const themeButton = document.getElementById('theme-toggle');
  const themeIcon = document.getElementById('theme-icon');
  const themeLabel = document.getElementById('theme-label');
  const themeScene = document.getElementById('theme-scene');
  let currentTheme = document.documentElement.dataset.theme === 'dark' ? 'dark' : 'light';
  let themeAnimating = false;
  function syncThemeControl() {
    const dark = currentTheme === 'dark';
    if (themeIcon) { themeIcon.textContent = dark ? '☼' : '☾'; themeIcon.classList.remove('theme-icon-spin'); void themeIcon.offsetWidth; themeIcon.classList.add('theme-icon-spin'); }
    if (themeLabel) themeLabel.textContent = dark ? 'Light' : 'Dark';
    if (themeButton) {
      themeButton.setAttribute('aria-label', `Switch to ${dark ? 'light' : 'dark'} theme`);
      themeButton.setAttribute('aria-pressed', String(dark));
      themeButton.dataset.theme = currentTheme;
    }
    document.querySelector('meta[name="theme-color"]')?.setAttribute('content', dark ? '#0B0B0B' : '#FFFFFF');
  }
  syncThemeControl();
  function setTheme(nextTheme) {
    currentTheme = nextTheme;
    document.documentElement.dataset.theme = currentTheme;
    try { localStorage.setItem('eatco-theme', currentTheme); } catch (error) {}
    syncThemeControl();
  }
  const waitForTheme = ms => new Promise(resolve => setTimeout(resolve, ms));
  async function switchTheme() {
    if (!themeButton || themeAnimating || themeButton.disabled) return;
    const from = currentTheme, to = from === 'light' ? 'dark' : 'light';
    if (matchMedia('(prefers-reduced-motion: reduce)').matches || !themeScene) { setTheme(to); return; }

    themeAnimating = true;
    themeButton.disabled = true;
    themeScene.classList.add('is-active');
    themeScene.setAttribute('aria-hidden', 'false');
    document.documentElement.classList.add('theme-changing');

    const rect = themeButton.getBoundingClientRect();
    const startX = rect.left + rect.width / 2, startY = rect.top + rect.height / 2;
    const centerX = window.innerWidth / 2, centerY = window.innerHeight / 2 - 24;
    const coin = document.createElement('div');
    coin.className = 'currency-coin theme-coin';
    const coinSymbol = document.createElement('span');
    coinSymbol.className = 'currency-coin-symbol';
    coinSymbol.textContent = from === 'light' ? '☾' : '☼';
    const coinCode = document.createElement('span');
    coinCode.className = 'currency-coin-code';
    coinCode.textContent = to === 'dark' ? 'DARK MODE' : 'LIGHT MODE';
    coin.append(coinSymbol, coinCode);
    document.body.appendChild(coin);
    Object.assign(coin.style, {left:`${startX}px`,top:`${startY}px`});

    try {
      const launch = coin.animate([
        {left:`${startX}px`,top:`${startY}px`,width:'36px',height:'36px',transform:'translate(-50%,-50%) scale(.35) rotateY(0deg) rotateZ(0deg)',opacity:.35},
        {left:`${centerX}px`,top:`${centerY}px`,width:'184px',height:'184px',transform:'translate(-50%,-50%) scale(1) rotateY(540deg) rotateZ(12deg)',opacity:1}
      ], {duration:720,easing:'cubic-bezier(.22,1,.36,1)',fill:'forwards'});
      await launch.finished;
      launch.cancel();
      Object.assign(coin.style,{left:`${centerX}px`,top:`${centerY}px`,width:'184px',height:'184px',transform:'translate(-50%,-50%) scale(1)'});
      coin.classList.add('coin-arrived');
      themeScene.classList.add('is-burst');
      const targetSymbol = to === 'dark' ? '☼' : '☾';
      const flip = coinSymbol.animate([
        {transform:'rotateY(0deg) scale(1)',opacity:1,filter:'blur(0)'},
        {transform:'rotateY(90deg) scale(.72)',opacity:0,filter:'blur(5px)',offset:.46},
        {transform:'rotateY(90deg) scale(.72)',opacity:0,filter:'blur(5px)',offset:.5},
        {transform:'rotateY(0deg) scale(1.08)',opacity:1,filter:'blur(0)',offset:.72},
        {transform:'rotateY(0deg) scale(1)',opacity:1,filter:'blur(0)'}
      ],{duration:660,easing:'cubic-bezier(.22,1,.36,1)'});
      await waitForTheme(320);
      coinSymbol.textContent = targetSymbol;
      coinCode.textContent = to === 'dark' ? 'DARK MODE' : 'LIGHT MODE';
      setTheme(to);
      await waitForTheme(260);
      await flip.finished;
      const homeRect = themeButton.getBoundingClientRect();
      const homeX = homeRect.left + homeRect.width / 2, homeY = homeRect.top + homeRect.height / 2;
      const returnFlight = coin.animate([
        {left:`${centerX}px`,top:`${centerY}px`,width:'184px',height:'184px',transform:'translate(-50%,-50%) scale(1) rotateY(0deg)',opacity:1},
        {left:`${homeX}px`,top:`${homeY}px`,width:'36px',height:'36px',transform:'translate(-50%,-50%) scale(.25) rotateY(-540deg) rotateZ(-12deg)',opacity:.1}
      ],{duration:680,easing:'cubic-bezier(.22,1,.36,1)',fill:'forwards'});
      await returnFlight.finished;
    } catch (error) {
      setTheme(to);
    } finally {
      coin.remove();
      themeScene.classList.remove('is-active','is-burst');
      themeScene.setAttribute('aria-hidden','true');
      document.documentElement.classList.remove('theme-changing');
      themeButton.disabled = false;
      themeAnimating = false;
    }
  }
  themeButton?.addEventListener('click', switchTheme);

  // Keep every storefront image visible when an external photo URL is unavailable.
  function guardImage(image) {
    const useFallback = () => {
      if (image.dataset.fallbackUsed) return;
      image.dataset.fallbackUsed = 'true';
      image.src = '/assets/images/food-fallback.svg';
      image.classList.add('image-fallback');
    };
    image.addEventListener('error', useFallback, { once:true });
    if (image.complete && image.naturalWidth === 0) useFallback();
  }
  document.querySelectorAll('img').forEach(guardImage);

  // Currency conversion uses the editable reference rate from includes/data.php.
  const exchangeRate = Number(window.EATCO_CURRENCY?.pkr_per_usd || 278.35);
  const currencyButton = document.getElementById('currency-toggle');
  const currencySymbol = document.getElementById('currency-symbol');
  const currencyCode = document.getElementById('currency-code');
  const currencyScene = document.getElementById('currency-scene');
  let currencyAnimating = false;
  let displayCurrency = localStorage.getItem('eatco-currency') || window.EATCO_CURRENCY?.default || 'USD';
  if (!['USD','PKR'].includes(displayCurrency)) displayCurrency = 'USD';
  function formatCurrency(usd, decimals = 2) {
    const amount = Number(usd) || 0;
    if (displayCurrency === 'PKR') return `₨ ${new Intl.NumberFormat('en-PK', { maximumFractionDigits:0 }).format(Math.round(amount * exchangeRate))}`;
    return new Intl.NumberFormat('en-US', { style:'currency', currency:'USD', minimumFractionDigits:decimals, maximumFractionDigits:decimals }).format(amount);
  }
  function updateCurrencyDisplay(animatePrices = false) {
    document.querySelectorAll('.currency-value[data-usd]').forEach((el, index) => {
      const decimals = Number(el.dataset.decimals ?? (Number.isInteger(Number(el.dataset.usd)) ? 0 : 2));
      el.classList.remove('price-refresh'); void el.offsetWidth;
      el.textContent = formatCurrency(el.dataset.usd, decimals);
      if (animatePrices) {
        el.style.animationDelay = `${Math.min(index * 13, 390)}ms`;
        el.classList.add('price-refresh');
        el.addEventListener('animationend', () => { el.style.animationDelay = ''; }, { once:true });
      }
    });
    if (currencyCode) currencyCode.textContent = displayCurrency;
    if (currencySymbol) currencySymbol.textContent = displayCurrency === 'PKR' ? '₨' : '$';
    if (currencyButton) {
      const next = displayCurrency === 'USD' ? 'Pakistani rupees' : 'US dollars';
      currencyButton.setAttribute('aria-label', `Switch display currency to ${next}`);
      currencyButton.dataset.currency = displayCurrency;
    }
  }
  updateCurrencyDisplay();
  const pause = ms => new Promise(resolve => setTimeout(resolve, ms));
  async function switchCurrency() {
    if (currencyAnimating) return;
    const from = displayCurrency, to = from === 'USD' ? 'PKR' : 'USD';
    if (matchMedia('(prefers-reduced-motion: reduce)').matches || !currencyScene || !currencyButton) {
      displayCurrency = to; localStorage.setItem('eatco-currency', to); updateCurrencyDisplay(true); return;
    }
    currencyAnimating = true;
    currencyButton.disabled = true;
    const rateLabel = document.getElementById('scene-rate');
    document.getElementById('scene-from').textContent = from;
    document.getElementById('scene-to').textContent = to;
    rateLabel.textContent = from === 'USD'
      ? `1 USD = ₨ ${new Intl.NumberFormat('en-PK').format(exchangeRate)}`
      : `₨ ${new Intl.NumberFormat('en-PK').format(exchangeRate)} = 1 USD`;
    currencyScene.classList.add('is-active');
    currencyScene.setAttribute('aria-hidden','false');
    document.body.classList.add('currency-shifting');

    const rect = currencyButton.getBoundingClientRect();
    const startX = rect.left + rect.width / 2, startY = rect.top + rect.height / 2;
    const centerX = window.innerWidth / 2, centerY = window.innerHeight / 2 - 25;
    const coin = document.createElement('div'); coin.className = 'currency-coin';
    const coinSymbol = document.createElement('span'); coinSymbol.className = 'currency-coin-symbol'; coinSymbol.textContent = from === 'USD' ? '$' : '₨';
    const coinCode = document.createElement('span'); coinCode.className = 'currency-coin-code'; coinCode.textContent = from;
    coin.append(coinSymbol, coinCode); document.body.appendChild(coin);
    Object.assign(coin.style, { left:`${startX}px`, top:`${startY}px` });

    try {
      const launch = coin.animate([
        { left:`${startX}px`, top:`${startY}px`, width:'36px', height:'36px', transform:'translate(-50%,-50%) scale(.35) rotateY(0deg) rotateZ(0deg)', opacity:.35 },
        { left:`${centerX}px`, top:`${centerY}px`, width:'184px', height:'184px', transform:'translate(-50%,-50%) scale(1) rotateY(540deg) rotateZ(12deg)', opacity:1 }
      ], { duration:720, easing:'cubic-bezier(.22,1,.36,1)', fill:'forwards' });
      await launch.finished;
      launch.cancel();
      Object.assign(coin.style, { left:`${centerX}px`, top:`${centerY}px`, width:'184px', height:'184px', transform:'translate(-50%,-50%) scale(1) rotateY(0deg)' });
      coin.classList.add('coin-arrived');
      currencyScene.classList.add('is-burst');
      coinSymbol.animate([
        { transform:'rotateY(0deg) scale(1)', opacity:1, filter:'blur(0)' },
        { transform:'rotateY(90deg) scale(.72)', opacity:0, filter:'blur(5px)', offset:.46 },
        { transform:'rotateY(90deg) scale(.72)', opacity:0, filter:'blur(5px)', offset:.5 },
        { transform:'rotateY(0deg) scale(1.08)', opacity:1, filter:'blur(0)', offset:.72 },
        { transform:'rotateY(0deg) scale(1)', opacity:1, filter:'blur(0)' }
      ], { duration:660, easing:'cubic-bezier(.22,1,.36,1)' });
      await pause(320);
      coinSymbol.textContent = to === 'PKR' ? '₨' : '$';
      coinCode.textContent = to;
      displayCurrency = to;
      localStorage.setItem('eatco-currency', to);
      updateCurrencyDisplay(true);
      await pause(260);
      coin.classList.add('coin-returning');
      const homeRect = currencyButton.getBoundingClientRect();
      const homeX = homeRect.left + homeRect.width / 2, homeY = homeRect.top + homeRect.height / 2;
      const returnFlight = coin.animate([
        { left:`${centerX}px`, top:`${centerY}px`, width:'184px', height:'184px', transform:'translate(-50%,-50%) scale(1) rotateY(0deg)', opacity:1 },
        { left:`${homeX}px`, top:`${homeY}px`, width:'36px', height:'36px', transform:'translate(-50%,-50%) scale(.25) rotateY(-540deg) rotateZ(-12deg)', opacity:.1 }
      ], { duration:680, easing:'cubic-bezier(.22,1,.36,1)', fill:'forwards' });
      await returnFlight.finished;
    } catch (error) {
      // If the tab changes or animation is interrupted, finish the conversion cleanly.
      displayCurrency = to; localStorage.setItem('eatco-currency', to); updateCurrencyDisplay();
    } finally {
      coin.remove();
      currencyScene.classList.remove('is-active','is-burst');
      currencyScene.setAttribute('aria-hidden','true');
      document.body.classList.remove('currency-shifting');
      currencyButton.disabled = false;
      currencyAnimating = false;
    }
  }
  currencyButton?.addEventListener('click', switchCurrency);

  function showToast(message) {
    if (!toast) return;
    toast.textContent = message;
    toast.classList.add('show');
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove('show'), 2600);
  }
  function productById(id) { return products.find(product => product.id === id); }

  // Scroll reveal and the yellow timeline line.
  const revealObserver = new IntersectionObserver(entries => {
    entries.forEach(entry => {
      if (!entry.isIntersecting) return;
      entry.target.classList.add('is-visible');
      revealObserver.unobserve(entry.target);
    });
  }, { threshold: 0.12 });
  document.querySelectorAll('.reveal, .timeline').forEach(el => {
    revealObserver.observe(el);
  });

  // Sticky header, shrink state, and reading progress.
  const header = document.querySelector('.site-header');
  const progress = document.querySelector('.scroll-progress');
  let maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
  let scrollFrame = 0;
  const measureScrollRange = () => {
    maxScroll = Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
  };
  const updateScroll = () => {
    if (scrollFrame) return;
    scrollFrame = requestAnimationFrame(() => {
      scrollFrame = 0;
      const y = window.scrollY;
      header?.classList.toggle('scrolled', y > 30);
      if (progress) progress.style.transform = `scaleX(${maxScroll > 0 ? Math.min(y / maxScroll, 1) : 0})`;
    });
  };
  window.addEventListener('scroll', updateScroll, { passive: true });
  window.addEventListener('resize', () => { measureScrollRange(); updateScroll(); }, { passive: true });
  window.addEventListener('load', measureScrollRange, { once: true });
  if ('ResizeObserver' in window) new ResizeObserver(measureScrollRange).observe(document.body);
  updateScroll();

  // Animate counters only when visible.
  const counterObserver = new IntersectionObserver(entries => entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    const el = entry.target, target = Number(el.dataset.counter || 0), suffix = el.dataset.suffix || '';
    const start = performance.now(), duration = 1400;
    const tick = now => {
      const t = Math.min((now - start) / duration, 1), eased = 1 - Math.pow(1 - t, 4);
      el.textContent = Math.round(target * eased).toLocaleString() + suffix;
      if (t < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick); counterObserver.unobserve(el);
  }), { threshold: .65 });
  document.querySelectorAll('[data-counter]').forEach(el => counterObserver.observe(el));

  // Catalog search, category, supplier, price, MOQ, and sorting.
  function applyFilters() {
    const query = (searchInput?.value || '').trim().toLowerCase();
    const supplier = supplierFilter?.value || '';
    const maxPrice = Number(priceFilter?.value || 15);
    const maxMoq = Number(moqFilter?.value || 0);
    let visible = 0;
    cards.forEach(card => {
      const categoryMatches = !activeCategory || (activeCategory === 'Deals' ? card.dataset.deal === 'true' : card.dataset.category === activeCategory);
      const matches = categoryMatches
        && (!query || card.dataset.name.includes(query) || card.dataset.category.toLowerCase().includes(query) || card.dataset.store.toLowerCase().includes(query))
        && (!supplier || card.dataset.store === supplier)
        && Number(card.dataset.price) <= maxPrice
        && (!maxMoq || Number(card.dataset.moq) <= maxMoq);
      card.hidden = !matches;
      if (matches) { visible++; card.classList.remove('is-visible'); requestAnimationFrame(() => card.classList.add('is-visible')); }
    });
    const count = document.getElementById('result-count');
    if (count) count.textContent = `${visible} ${visible === 1 ? 'product' : 'products'}`;
    const empty = document.getElementById('no-results'); if (empty) empty.hidden = visible > 0;
  }
  document.querySelectorAll('.filter-chip').forEach(chip => chip.addEventListener('click', () => {
    document.querySelector('.filter-chip.active')?.classList.remove('active'); chip.classList.add('active'); activeCategory = chip.dataset.filter || ''; applyFilters();
  }));
  document.querySelectorAll('[data-category-link], [data-nav-category]').forEach(link => link.addEventListener('click', () => {
    const category = link.dataset.categoryLink || link.dataset.navCategory || '';
    const chip = [...document.querySelectorAll('.filter-chip')].find(item => item.dataset.filter === category);
    if (chip) { document.querySelector('.filter-chip.active')?.classList.remove('active'); chip.classList.add('active'); activeCategory = category; applyFilters(); }
  }));
  searchInput?.addEventListener('input', applyFilters);
  if (searchInput) {
    const params = new URLSearchParams(window.location.search);
    const initialQuery = params.get('q') || '';
    activeCategory = params.get('category') || '';
    searchInput.value = initialQuery;
    if (globalSearch) globalSearch.value = initialQuery;
    if (categorySearch) categorySearch.value = activeCategory;
    const initialChip = [...document.querySelectorAll('.filter-chip')].find(chip => (chip.dataset.filter || '') === activeCategory);
    if (initialChip) {
      document.querySelector('.filter-chip.active')?.classList.remove('active');
      initialChip.classList.add('active');
    }
    applyFilters();
  }
  const activeQueryCategory = new URLSearchParams(window.location.search).get('category') || '';
  document.querySelectorAll('.category-nav [data-nav-category]').forEach(link => {
    if (link.dataset.navCategory === activeQueryCategory) link.setAttribute('aria-current', 'page');
  });
  const categoryToggle = document.getElementById('category-toggle');
  const categoryPanel = document.getElementById('category-menu-panel');
  const categoryMenu = categoryToggle?.closest('.category-menu');
  if (categoryToggle && categoryPanel && categoryMenu) {
    const closeCategoryMenu = () => {
      categoryToggle.setAttribute('aria-expanded', 'false');
      categoryPanel.hidden = true;
    };
    categoryToggle.addEventListener('click', () => {
      const open = categoryToggle.getAttribute('aria-expanded') !== 'true';
      categoryToggle.setAttribute('aria-expanded', String(open));
      categoryPanel.hidden = !open;
    });
    document.addEventListener('click', event => {
      if (!categoryMenu.contains(event.target)) closeCategoryMenu();
    });
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && categoryToggle.getAttribute('aria-expanded') === 'true') {
        closeCategoryMenu();
        categoryToggle.focus();
      }
    });
    categoryPanel.addEventListener('click', event => {
      if (event.target.closest('a')) closeCategoryMenu();
    });
  }
  supplierFilter?.addEventListener('change', applyFilters);
  moqFilter?.addEventListener('change', applyFilters);
  priceFilter?.addEventListener('input', () => { document.getElementById('price-value').dataset.usd = priceFilter.value; updateCurrencyDisplay(); applyFilters(); });
  sortSelect?.addEventListener('change', () => {
    const sort = sortSelect.value;
    const sorted = [...cards].sort((a, b) => sort === 'price-low' ? +a.dataset.price - +b.dataset.price : sort === 'price-high' ? +b.dataset.price - +a.dataset.price : sort === 'name' ? a.dataset.name.localeCompare(b.dataset.name) : +b.dataset.popularity - +a.dataset.popularity);
    sorted.forEach(card => grid.appendChild(card));
  });
  globalSearch?.addEventListener('input', () => { if (searchInput) { searchInput.value = globalSearch.value; applyFilters(); } });
  categorySearch?.addEventListener('change', () => {
    if (!searchInput) return;
    const category = categorySearch.value;
    const chip = [...document.querySelectorAll('.filter-chip')].find(item => item.dataset.filter === category);
    if (chip) { document.querySelector('.filter-chip.active')?.classList.remove('active'); chip.classList.add('active'); activeCategory = category; }
    else { activeCategory = ''; document.querySelector('.filter-chip.active')?.classList.remove('active'); document.querySelector('.filter-chip[data-filter=""]')?.classList.add('active'); }
    searchInput.value = globalSearch.value; applyFilters();
  });

  // Grid/list display.
  document.querySelectorAll('.view-btn').forEach(button => button.addEventListener('click', () => {
    document.querySelector('.view-btn.active')?.classList.remove('active'); button.classList.add('active');
    grid.classList.toggle('list-view', button.dataset.view === 'list');
  }));

  // Quote list persisted in this browser. A request still goes through the PHP form.
  function renderQuoteDrawer() {
    let drawer = document.querySelector('.quote-drawer');
    if (!drawer) {
      drawer = document.createElement('aside'); drawer.className = 'quote-drawer'; drawer.setAttribute('aria-label', 'Quote list');
      drawer.innerHTML = '<button class="drawer-close" aria-label="Close quote list">×</button><p class="eyebrow">YOUR SELECTION</p><h2>Quote list</h2><div class="quote-items"></div><a class="button button-yellow" href="/contact">Request this quote <span>↗</span></a>';
      document.body.appendChild(drawer);
      drawer.querySelector('.drawer-close').addEventListener('click', () => drawer.classList.remove('open'));
      drawer.querySelector('a').addEventListener('click', () => drawer.classList.remove('open'));
    }
    const list = drawer.querySelector('.quote-items');
    list.replaceChildren();
    if (!quoteItems.length) {
      const empty = document.createElement('p'); empty.className = 'quote-empty'; empty.textContent = 'Your quote list is ready for a few good things.'; list.appendChild(empty);
    } else quoteItems.forEach(item => {
      const product = productById(item.id); if (!product) return;
      const row = document.createElement('div'); row.className = 'quote-item';
      const name = document.createElement('strong'); name.textContent = product.name;
      const detail = document.createElement('small'); detail.textContent = `${item.quantity} units · MOQ ${product.moq}`;
      const remove = document.createElement('button'); remove.type = 'button'; remove.textContent = '×'; remove.setAttribute('aria-label', `Remove ${product.name}`);
      remove.addEventListener('click', () => { quoteItems = quoteItems.filter(q => q.id !== item.id); persistQuote(); });
      row.append(name, detail, remove); list.appendChild(row);
    });
    document.getElementById('quote-count').textContent = quoteItems.reduce((sum, item) => sum + item.quantity, 0);
  }
  function persistQuote() { localStorage.setItem('eatco-quote', JSON.stringify(quoteItems)); renderQuoteDrawer(); }
  function addToQuote(id, quantity) {
    const product = productById(id); if (!product) return;
    const units = Math.max(product.moq, Number(quantity) || product.moq);
    const existing = quoteItems.find(item => item.id === id);
    if (existing) existing.quantity += units; else quoteItems.push({ id, quantity: units });
    persistQuote();
    const icon = document.querySelector('.quote-link');
    const source = document.querySelector(`[data-product-id="${CSS.escape(id)}"] .product-image-wrap img`);
    if (icon && source && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
      const from = source.getBoundingClientRect(), to = icon.getBoundingClientRect(), flyer = source.cloneNode();
      Object.assign(flyer.style, { position:'fixed', zIndex:120, left:`${from.left}px`, top:`${from.top}px`, width:`${Math.min(from.width,100)}px`, height:`${Math.min(from.height,100)}px`, objectFit:'cover', borderRadius:'50%', pointerEvents:'none', boxShadow:'0 10px 30px rgba(0,0,0,.25)' });
      document.body.appendChild(flyer);
      flyer.animate([{ transform:'scale(1)', opacity:1 }, { transform:`translate(${to.left-from.left}px,${to.top-from.top}px) scale(.12)`, opacity:.35 }], { duration:650, easing:'cubic-bezier(.22,1,.36,1)' }).finished.finally(() => flyer.remove());
    }
    icon?.classList.remove('bounce','flight'); void icon?.offsetWidth; icon?.classList.add('bounce','flight');
    showToast(`${product.name} added to your quote list`);
  }
  renderQuoteDrawer();
  document.querySelector('.quote-link')?.addEventListener('click', () => document.querySelector('.quote-drawer')?.classList.toggle('open'));
  document.querySelectorAll('.add-quote').forEach(button => button.addEventListener('click', () => addToQuote(button.dataset.id)));

  // Product details are composed from the safely encoded data payload with DOM APIs.
  const modal = document.getElementById('product-modal'), modalContent = document.getElementById('modal-content');
  function openProduct(id) {
    const product = productById(id); if (!product) return;
    modalContent.replaceChildren();
    const layout = document.createElement('div'); layout.className = 'modal-layout';
    const photo = document.createElement('div'); photo.className = 'modal-photo';
    const image = document.createElement('img'); image.alt = product.name; guardImage(image); image.src = product.image || ''; photo.appendChild(image);
    const info = document.createElement('div'); info.className = 'modal-info';
    const supplier = document.createElement('div'); supplier.className = 'product-supplier'; supplier.textContent = `SOLD BY: ${product.store}`;
    const category = document.createElement('div'); category.className = 'product-supplier'; category.style.marginTop = '10px'; category.textContent = product.category;
    const title = document.createElement('h2'); title.id = 'modal-title'; title.textContent = product.name;
    const description = document.createElement('p'); description.textContent = product.details + ' Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
    const price = document.createElement('div'); price.className = 'modal-price'; price.textContent = `${formatCurrency(product.price)} `;
    const unit = document.createElement('small'); unit.textContent = '/ unit'; price.appendChild(unit);
    const specs = document.createElement('table'); specs.className = 'spec-table';
    const specsCaption = document.createElement('caption'); specsCaption.textContent = 'Product details'; specs.appendChild(specsCaption);
    Object.entries(product.specs || {}).forEach(([key, value]) => { const row = specs.insertRow(); row.insertCell().textContent = key; row.insertCell().textContent = value; });
    // Product details intentionally omit bulk-pricing tiers and quote-order controls.
    info.append(category, supplier, title, description, price, specs); layout.append(photo, info); modalContent.appendChild(layout);
    modal.classList.add('open'); modal.setAttribute('aria-hidden','false'); document.body.classList.add('modal-open'); modal.querySelector('.modal-close').focus();
  }
  document.querySelectorAll('.view-product').forEach(button => button.addEventListener('click', () => openProduct(button.dataset.id)));
  function closeModal() { modal.classList.remove('open'); modal.setAttribute('aria-hidden','true'); document.body.classList.remove('modal-open'); }
  modal.querySelector('.modal-close').addEventListener('click', closeModal);
  modal.addEventListener('click', event => { if (event.target === modal) closeModal(); });
  document.addEventListener('keydown', event => { if (event.key === 'Escape') { closeModal(); document.querySelector('.quote-drawer')?.classList.remove('open'); closeMenu(); } });

  // Favorite/save micro-interaction.
  document.querySelectorAll('.save-product').forEach(button => {
    const card = button.closest('.product-card'), id = card.dataset.productId;
    if (savedItems.includes(id)) { button.classList.add('saved'); button.textContent = '♥'; }
    button.addEventListener('click', () => {
      if (savedItems.includes(id)) { savedItems = savedItems.filter(saved => saved !== id); button.classList.remove('saved'); button.textContent = '♡'; showToast('Removed from saved products'); }
      else { savedItems.push(id); button.classList.add('saved'); button.textContent = '♥'; showToast('Saved for later'); }
      localStorage.setItem('eatco-saved', JSON.stringify(savedItems));
    });
  });

  // Subtle 3D tilt on pointer devices.
  if (matchMedia('(hover:hover) and (pointer:fine)').matches && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    cards.forEach(card => {
      card.addEventListener('pointermove', event => {
        if (card.hidden) return;
        const rect = card.getBoundingClientRect(), x = (event.clientX - rect.left) / rect.width - .5, y = (event.clientY - rect.top) / rect.height - .5;
        card.style.transform = `perspective(900px) rotateY(${x * 3}deg) rotateX(${-y * 3}deg) translateY(-2px)`;
      });
      card.addEventListener('pointerleave', () => { card.style.transform = ''; });
    });
  }


  // Buttons get a small pointer pull and click ripple.
  if (matchMedia('(hover:hover) and (pointer:fine)').matches && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('.button-yellow').forEach(button => {
      button.addEventListener('pointermove', event => {
        const rect = button.getBoundingClientRect();
        button.style.translate = `${(event.clientX - rect.left - rect.width / 2) * .035}px ${(event.clientY - rect.top - rect.height / 2) * .08}px`;
      });
      button.addEventListener('pointerleave', () => { button.style.translate = ''; });
    });
  }
  document.addEventListener('click', event => {
    const button = event.target.closest('.button'); if (!button || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const rect = button.getBoundingClientRect(), ripple = document.createElement('span'), size = Math.max(rect.width, rect.height) * 1.8;
    ripple.className = 'ripple-dot'; Object.assign(ripple.style, { width:`${size}px`, height:`${size}px`, left:`${event.clientX-rect.left-size/2}px`, top:`${event.clientY-rect.top-size/2}px` });
    button.appendChild(ripple); ripple.addEventListener('animationend', () => ripple.remove(), { once:true });
  });

  // Lightweight offer countdown (hours/minutes/seconds).
  let seconds = 51, minutes = 34, hours = 12;
  setInterval(() => {
    seconds--; if (seconds < 0) { seconds = 59; minutes--; }
    if (minutes < 0) { minutes = 59; hours--; }
    if (hours < 0) { hours = 12; minutes = 34; seconds = 59; }
    const nodes = [...document.querySelectorAll('[data-time]')];
    [hours, minutes, seconds].forEach((value, i) => { if (nodes[i]) nodes[i].textContent = String(value).padStart(2,'0'); });
  }, 1000);

  // Add a short shared wipe between static pages, unless reduced motion is preferred.
  const transitionOverlay = document.querySelector('.page-transition');
  const prefersReducedMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (transitionOverlay && !prefersReducedMotion) {
    try {
      if (sessionStorage.getItem('eatco-page-transition') === '1') {
        sessionStorage.removeItem('eatco-page-transition');
        document.documentElement.classList.add('page-entering');
      }
    } catch (error) {}
    document.addEventListener('click', event => {
      const link = event.target.closest?.('a[href]');
      if (!link || event.defaultPrevented || link.target || link.hasAttribute('download') || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
      const destination = new URL(link.href, window.location.href);
      if (destination.origin !== window.location.origin) return;
      if (destination.pathname === window.location.pathname && destination.search === window.location.search) return;
      event.preventDefault();
      try { sessionStorage.setItem('eatco-page-transition', '1'); } catch (error) {}
      document.documentElement.classList.add('page-leaving');
      window.setTimeout(() => window.location.assign(destination.href), 180);
    });
  }
})();
