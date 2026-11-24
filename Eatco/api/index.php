<?php
declare(strict_types=1);
session_start();
require dirname(__DIR__) . '/includes/data.php';

// Shared helpers keep every value safe before it reaches the page.
function e(mixed $value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function money(float $amount): string { return '<span class="currency-value" data-usd="' . number_format($amount, 2, '.', '') . '">$' . number_format($amount, 2) . '</span>'; }

if (empty($_SESSION['csrf'])) { $_SESSION['csrf'] = bin2hex(random_bytes(32)); }
$errors = [];
$sent = false;
$old = [];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_type'] ?? '') === 'quote') {
    $old = array_map(static fn($v) => is_string($v) ? trim($v) : '', $_POST);
    if (!getenv('VERCEL') && !hash_equals($_SESSION['csrf'], (string)($_POST['csrf'] ?? ''))) { $errors[] = 'Your session expired. Please refresh and try again.'; }
    if (!empty($_POST['website'] ?? '')) { $errors[] = 'We could not process this request.'; }
    $required = ['company'=>'Company name','person'=>'Contact person','email'=>'Email','phone'=>'Phone','quantity'=>'Estimated quantity'];
    foreach ($required as $key=>$label) if (trim((string)($_POST[$key] ?? '')) === '') $errors[] = $label . ' is required.';
    $email = filter_var(trim((string)($_POST['email'] ?? '')), FILTER_VALIDATE_EMAIL);
    if (!$email && !in_array('Email is required.', $errors, true)) $errors[] = 'Enter a valid email address.';
    $quantity = filter_var($_POST['quantity'] ?? '', FILTER_VALIDATE_INT, ['options'=>['min_range'=>1]]);
    if ($quantity === false && !in_array('Estimated quantity is required.', $errors, true)) $errors[] = 'Enter a whole number greater than zero for quantity.';
    if (!$errors) {
        if (getenv('VERCEL')) {
            // Vercel Functions have an ephemeral, read-only filesystem. Do not
            // claim a request was received until durable storage is configured.
            $errors[] = 'Quote requests are not configured on this hosted site yet. Please contact us directly.';
        } else {
            $request = ['received_at'=>gmdate(DATE_ATOM),'company'=>trim((string)$_POST['company']),'person'=>trim((string)$_POST['person']),'email'=>$email,'phone'=>trim((string)$_POST['phone']),'interest'=>trim((string)($_POST['interest'] ?? '')),'quantity'=>(int)$quantity,'message'=>trim((string)($_POST['message'] ?? ''))];
            $store = __DIR__ . '/storage';
            if (!is_dir($store)) { mkdir($store, 0750, true); }
            $requestFile = $store . '/quote-requests.php';
            if (!is_file($requestFile)) { file_put_contents($requestFile, "<?php http_response_code(404); exit; __halt_compiler();\n", LOCK_EX); }
            $saved = file_put_contents($requestFile, json_encode($request, JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE) . PHP_EOL, FILE_APPEND | LOCK_EX);
            if ($saved === false) {
                $errors[] = 'We could not save your quote request. Please try again later.';
            } else {
                if (!empty($site['send_mail']) && filter_var($site['mail_to'], FILTER_VALIDATE_EMAIL)) {
                    $body = "Company: {$request['company']}\nContact: {$request['person']}\nEmail: {$request['email']}\nPhone: {$request['phone']}\nProduct interest: {$request['interest']}\nQuantity: {$request['quantity']}\n\n{$request['message']}";
                    @mail($site['mail_to'], 'New Eatco quote request', $body, 'From: ' . $site['mail_to']);
                }
                $sent = true; $_SESSION['csrf'] = bin2hex(random_bytes(32)); $old = [];
            }
        }
    }
}
$productJson = json_encode($products, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
if (getenv('VERCEL') && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['form_type'] ?? '') === 'quote') {
    $contactPage = @file_get_contents(dirname(__DIR__) . '/contact.html');
    if ($contactPage !== false) {
        $items = $errors ?: ['Quote requests are not configured on this hosted site yet. Please contact us directly.'];
        $message = '<div class="form-errors" role="alert"><strong>Please review your request:</strong><ul>';
        foreach ($items as $error) $message .= '<li>' . e($error) . '</li>';
        $message .= '</ul></div>';
        echo str_replace('<div class="quote-form-card">', '<div class="quote-form-card">' . $message, $contactPage);
        exit;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($site['title']) ?></title><meta name="description" content="<?= e($site['description']) ?>">
  <meta property="og:title" content="<?= e($site['title']) ?>"><meta property="og:description" content="<?= e($site['description']) ?>"><meta property="og:type" content="website">
  <meta name="theme-color" content="#0B0B0B">
  <script>try{document.documentElement.dataset.theme=localStorage.getItem('eatco-theme')||'light'}catch(e){document.documentElement.dataset.theme='light'}</script>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Newsreader:opsz,wght@6..72,400;6..72,500;6..72,600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css?v=20261122-theme-icon-v1">
  <!-- Vercel Web Analytics -->
  <script>window.va = window.va || function () { (window.vaq = window.vaq || []).push(arguments); };</script>
  <script defer src="/_vercel/insights/script.js"></script>
  <link rel="icon" type="image/png" href="/assets/eatco-mark.png">
  <link rel="shortcut icon" href="/assets/eatco-mark.png" type="image/png">
  <link rel="apple-touch-icon" href="/assets/eatco-mark.png">
</head>
<body>
<div class="scroll-progress" aria-hidden="true"></div>
</div>
<header class="site-header" id="top">
  <div class="wrap header-main">
    <a class="brand" href="#top" aria-label="Eatco home"><img class="brand-logo" src="/assets/eatco-mark.png" alt="Eatco logo"><span><?= e($site['name']) ?><small>WHOLESALE MARKET</small></span></a>
    <form class="search" role="search" onsubmit="return false"><label class="sr-only" for="global-search">Search products</label><select id="search-category" aria-label="Search category"><option value="">All categories</option><?php foreach ($categories as $category): ?><option><?= e($category['name']) ?></option><?php endforeach; ?></select><input id="global-search" type="search" placeholder="Search products, brands, and more"><button type="button" aria-label="Search">⌕</button></form>
    <div class="header-actions"><button class="currency-toggle" id="currency-toggle" type="button" aria-label="Switch display currency to Pakistani rupees" title="Reference rate: 1 USD = PKR <?= number_format((float)$currency['pkr_per_usd'],2) ?> (<?= e($currency['rate_date']) ?>)"><span class="currency-symbol" id="currency-symbol">$</span><span id="currency-code">USD</span><span class="currency-arrows" aria-hidden="true">⇄</span></button><button class="theme-toggle" id="theme-toggle" type="button" aria-label="Switch to dark theme" aria-pressed="false"><span class="theme-icon" id="theme-icon" aria-hidden="true">☾</span><span class="theme-label" id="theme-label">Dark</span></button><a class="button button-yellow header-quote" href="#contact"><?= e($labels['quote']) ?> <span>↗</span></a><a class="icon-action account-link" href="#contact" aria-label="Account">♙</a><button class="icon-action quote-link" type="button" aria-label="Open quote list">♧ <b id="quote-count">0</b></button><button class="menu-toggle" aria-label="Open menu" aria-expanded="false">☰</button></div>
  </div>
  <nav class="category-nav" aria-label="Product categories">
  <div class="wrap nav-inner">
    <div class="category-menu">
      <button type="button" class="all-categories" id="category-toggle" aria-expanded="false" aria-controls="category-menu-panel">
        <span class="category-menu-icon" aria-hidden="true">☷</span><span>All Categories</span><span class="category-chevron" aria-hidden="true">⌄</span>
      </button>
      <div class="category-menu-panel" id="category-menu-panel" hidden>
        <div class="category-menu-heading"><div><span class="eyebrow">EXPLORE EATCO</span><strong>Browse categories</strong></div><a href="/categories" class="category-view-all">View all <span aria-hidden="true">↗</span></a></div>
        <div class="category-menu-grid"><?php foreach (array_slice($categories,0,7) as $category): ?><a href="#products" class="category-menu-link" data-nav-category="<?= e($category['name']) ?>"><span><?= e($category['name']) ?></span><span aria-hidden="true">↗</span></a><?php endforeach; ?></div>
      </div>
    </div>
  </div>
</nav>
</header>
<div class="mobile-backdrop"></div><aside class="mobile-drawer" aria-label="Mobile navigation"><button class="drawer-close" aria-label="Close menu">×</button><div class="drawer-brand"><?= e($site['name']) ?></div><a href="#categories"><?= e($labels['all_categories']) ?></a><?php foreach ($categories as $category): ?><a href="#products" data-nav-category="<?= e($category['name']) ?>"><?= e($category['name']) ?></a><?php endforeach; ?><a href="#contact" class="button button-yellow"><?= e($labels['quote']) ?></a></aside>

<main>
<section class="hero dark-section"><div class="hero-glow glow-one"></div><div class="hero-glow glow-two"></div><div class="wrap hero-layout"><div class="hero-copy"><p class="eyebrow reveal"><?= e($content['hero_eyebrow']) ?></p><h1 class="reveal"><?= nl2br(e($content['hero_title'])) ?></h1><p class="hero-text reveal"><?= e($content['hero_text']) ?></p><div class="hero-actions reveal"><a href="#products" class="button button-yellow"><?= e($labels['browse']) ?> <span>↗</span></a><a href="#contact" class="button button-outline-light"><?= e($labels['partner']) ?> <span>→</span></a></div><div class="hero-proof reveal"><div class="avatar-stack"><i>J</i><i>M</i><i>A</i><i>+</i></div><span><strong><?= e($content['story']) ?> <?= (int)$site['since'] ?></strong><br><?= e($site['tagline']) ?></span></div></div>
<div class="hero-art" aria-label="Featured food products"><div class="hero-orbit orbit-a"></div><div class="hero-orbit orbit-b"></div><div class="hero-product hero-product-main"><img src="<?= e($productImages['tomatoes']) ?>" alt="Fresh tomato product" fetchpriority="high"><span>Pantry essentials</span><strong>Better by the case.</strong></div><div class="floating-card float-card-one"><span class="float-icon">✳</span><div><small>Bulk favorite</small><b>Olive oil</b></div><strong>−18%</strong></div><div class="floating-card float-card-two"><span class="float-check">✓</span><div><small>Trusted supply</small><b>Always in season</b></div></div><div class="hero-stamp"><span>FOOD<br>FOR ALL</span><i>✳</i></div></div></div>
<div class="wrap hero-stats"><?php foreach ($stats as $stat): ?><div class="hero-stat"><strong data-counter="<?= (int)$stat['value'] ?>" data-suffix="<?= e($stat['suffix']) ?>">0</strong><span><?= e($stat['label']) ?></span></div><?php endforeach; ?></div>
</section>

<section class="section categories-section" id="categories"><div class="wrap"><div class="section-heading reveal"><div><p class="eyebrow">A WELL-STOCKED WORLD</p><h2><?= e($labels['shop_category']) ?></h2></div><a href="#products" class="text-link"><?= e($labels['explore']) ?> <span>↗</span></a></div><div class="category-grid"><?php foreach ($categories as $i=>$category): ?><a class="category-tile reveal" href="#products" data-category-link="<?= e($category['name']) ?>"><span class="category-illustration icon-<?= $i ?>"><?= e($category['icon']) ?></span><strong><?= e($category['name']) ?></strong><small><?= e($category['description']) ?></small><span class="tile-arrow">↗</span></a><?php endforeach; ?></div></div></section>

<section class="deal-band" id="deals"><div class="wrap deal-inner"><div><p class="eyebrow">GOOD THINGS, BY THE CASE</p><h2>Bulk offers worth<br>making room for.</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p><a href="#products" class="button button-dark">Shop this week’s deals <span>↗</span></a></div><div class="deal-feature"><div class="deal-photo"><img src="<?= e($productImages['olive']) ?>" alt="Olive oil bottle" loading="lazy"><span class="save-badge">SAVE UP TO<br><b>18%</b></span></div><div class="deal-product"><span>LIMITED-TIME BULK OFFER</span><h3>Extra virgin olive oil</h3><p>Bulk pricing from <strong class="currency-value" data-usd="7.95">$7.95</strong> / unit</p><div class="countdown" aria-label="Offer countdown"><div><b data-time="12">12</b><small>HRS</small></div><i>:</i><div><b data-time="34">34</b><small>MIN</small></div><i>:</i><div><b data-time="51">51</b><small>SEC</small></div></div><a href="#products" class="deal-link">See the offer <span>→</span></a></div></div></div></section>

<section class="section product-section" id="products"><div class="wrap"><div class="section-heading reveal"><div><p class="eyebrow">THOUGHTFULLY SOURCED</p><h2>Find your next favorite.</h2><p class="section-intro">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p></div><div class="view-switch" aria-label="Product layout"><button class="view-btn active" data-view="grid" aria-label="Grid view">▦</button><button class="view-btn" data-view="list" aria-label="List view">☷</button></div></div>
<div class="catalog-tools reveal"><div class="filter-chips" id="filter-chips"><button class="filter-chip active" data-filter="">All products <span><?= count($products) ?></span></button><?php foreach ($categories as $category): ?><button class="filter-chip" data-filter="<?= e($category['name']) ?>"><?= e($category['name']) ?></button><?php endforeach; ?></div><div class="filter-controls"><label class="filter-search"><span>⌕</span><input id="product-search" type="search" placeholder="Find a product"></label><label class="select-label">Supplier<select id="supplier-filter"><option value="">All suppliers</option><?php foreach (array_unique(array_column($products,'store')) as $store): ?><option><?= e($store) ?></option><?php endforeach; ?></select></label><label class="select-label">Sort by<select id="sort-select"><option value="popular">Popular</option><option value="price-low">Price: low to high</option><option value="price-high">Price: high to low</option><option value="name">Name: A to Z</option></select></label><label class="range-label">Max price <span id="price-value" data-usd="15">$15</span><input id="price-filter" type="range" min="1" max="15" value="15"></label><label class="select-label">MOQ<select id="moq-filter"><option value="0">Any quantity</option><option value="50">Up to 50</option><option value="100">Up to 100</option><option value="250">Up to 250</option></select></label></div></div>
<div class="results-meta"><span id="result-count"><?= count($products) ?> products</span><span>Wholesale prices shown per unit</span></div><div class="product-grid" id="product-grid">
<?php foreach ($products as $index=>$product): $bestTier = $product['tiers'][count($product['tiers'])-1]; ?><article class="product-card reveal" data-product-id="<?= e($product['id']) ?>" data-name="<?= e(strtolower($product['name'])) ?>" data-category="<?= e($product['category']) ?>" data-store="<?= e($product['store']) ?>" data-price="<?= e((string)$product['price']) ?>" data-moq="<?= e((string)$product['moq']) ?>" data-popularity="<?= 16-$index ?>"><div class="product-image-wrap"><img src="<?= e($product['image']) ?>" alt="<?= e($product['name']) ?>" loading="lazy"><span class="product-category"><?= e($product['category']) ?></span><button class="save-product" aria-label="Save <?= e($product['name']) ?>" type="button">♡</button></div><div class="product-info"><div class="product-supplier">SOLD BY: <?= e($product['store']) ?></div><h3><?= e($product['name']) ?></h3><p class="product-details"><?= e($product['details']) ?></p><div class="product-meta"><span class="stock <?= $product['stock']==='Low Stock'?'stock-low':'' ?>"><i></i><?= e($product['stock']) ?></span><span>MOQ <?= (int)$product['moq'] ?></span></div><div class="product-actions"><button class="button button-yellow add-quote" type="button" data-id="<?= e($product['id']) ?>">Add to quote <span>＋</span></button><button class="button button-outline view-product" type="button" data-id="<?= e($product['id']) ?>">Details</button></div></div></article><?php endforeach; ?></div><div class="no-results" id="no-results" hidden><span>⌕</span><h3>No products found</h3><p>Try adjusting your search or filters.</p></div></div></section>

<section class="benefits-section dark-section"><div class="wrap"><div class="section-heading section-heading-light reveal"><div><p class="eyebrow">THE EATCO DIFFERENCE</p><h2>Good business starts<br>with good people.</h2></div><p class="section-intro">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer tincidunt.</p></div><div class="benefit-grid"><?php foreach ($benefits as $benefit): ?><article class="benefit-card reveal"><span><?= e($benefit['icon']) ?></span><h3><?= e($benefit['title']) ?></h3><p><?= e($benefit['text']) ?></p></article><?php endforeach; ?></div></div></section>

<section class="section steps-section"><div class="wrap"><div class="section-heading reveal"><div><p class="eyebrow">STRAIGHTFORWARD FROM START TO FINISH</p><h2>Four steps. Good things ahead.</h2></div></div><div class="timeline"><div class="timeline-progress"></div><?php foreach ($steps as $step): ?><article class="step-card reveal"><span class="step-number"><?= e($step['number']) ?></span><div class="step-icon"><?= ['⌕','✉','✓','↗'][array_search($step,$steps,true)] ?></div><h3><?= e($step['title']) ?></h3><p><?= e($step['text']) ?></p></article><?php endforeach; ?></div></div></section>

<section class="partners-section"><div class="wrap"><div class="section-heading reveal"><div><p class="eyebrow">IN GOOD COMPANY</p><h2>Good things grow together.</h2></div><p class="section-intro">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p></div></div><div class="marquee" aria-label="Our partners"><div class="marquee-track"><?php for($repeat=0;$repeat<2;$repeat++): foreach($partners as $partner): ?><span><?= e($partner) ?><i>✳</i></span><?php endforeach; endfor; ?></div></div></section>

<section class="about-section" id="about"><div class="about-image"><img src="https://images.unsplash.com/photo-1542838132-92c53300491e?auto=format&fit=crop&w=1300&q=85" alt="Fresh produce arranged at a market" loading="lazy"><span class="image-caption">GOOD FOOD, MOVING TOGETHER</span></div><div class="about-copy"><p class="eyebrow">A LITTLE ABOUT US</p><h2>Good food has a way of bringing us closer.</h2><p><?= e($content['about']) ?></p><div class="mission-vision"><article><span>OUR MISSION</span><p><?= e($content['mission']) ?></p></article><article><span>OUR VISION</span><p><?= e($content['vision']) ?></p></article></div><a href="#contact" class="text-link">Get to know us <span>↗</span></a></div></section>

<section class="community-strip"><div class="wrap community-inner"><span class="community-icon">✿</span><p><?= e($content['community']) ?></p><span class="community-icons">✳　❋　✿</span></div></section>
<section class="cert-section"><div class="wrap cert-inner"><div><p class="eyebrow">QUALITY, BUILT IN</p><h2>Standards that<br>mean something.</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p></div><div class="cert-grid"><?php foreach($certifications as $cert): ?><div class="cert-badge"><span>✳</span><strong><?= e($cert['name']) ?></strong><small><?= e($cert['detail']) ?></small></div><?php endforeach; ?></div></div></section>

<section class="contact-section" id="contact"><div class="wrap contact-layout"><div class="contact-copy"><p class="eyebrow">LET’S TALK WHOLESALE</p><h2><?= e($content['contact_heading']) ?></h2><p><?= e($content['contact_text']) ?></p><div class="contact-details"><a href="tel:<?= e(preg_replace('/[^+\d]/','',$site['phone'])) ?>"><span>☎</span><span><small>GIVE US A CALL</small><?= e($site['phone']) ?></span></a><a href="mailto:<?= e($site['email']) ?>"><span>✉</span><span><small>SEND AN EMAIL</small><?= e($site['email']) ?></span></a><div><span>⌖</span><span><small>COME SAY HELLO</small><?= e($site['address']) ?></span></div></div><div class="map-placeholder"><div class="map-grid"></div><span class="map-pin">●</span><b><?= e($site['name']) ?></b><small><?= e($site['address']) ?></small></div></div>
<div class="quote-form-card"><div class="form-heading"><span>01 <i>/ 01</i></span><h3>Tell us what you’re looking for.</h3><p>We’ll help you put together the right order.</p></div><?php if($sent): ?><div class="form-success" role="status"><span class="success-check">✓</span><h3>Thank you.</h3><p><?= e($content['quote_success']) ?></p><div class="success-sparkles">✦　✧　✦</div></div><?php else: ?><?php if($errors): ?><div class="form-errors" role="alert"><strong>Please review your request:</strong><ul><?php foreach($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?><form method="post" action="<?= getenv('VERCEL') ? '/api/index#contact' : '#contact' ?>" class="quote-form" novalidate><input type="hidden" name="form_type" value="quote"><input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>"><div class="honeypot" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div><div class="form-grid"><label>Company name <b>*</b><input name="company" required autocomplete="organization" value="<?= e($old['company']??'') ?>" placeholder="Your business"></label><label>Contact person <b>*</b><input name="person" required autocomplete="name" value="<?= e($old['person']??'') ?>" placeholder="Your name"></label><label>Email address <b>*</b><input name="email" type="email" required autocomplete="email" value="<?= e($old['email']??'') ?>" placeholder="you@company.com"></label><label>Phone number <b>*</b><input name="phone" type="tel" required autocomplete="tel" value="<?= e($old['phone']??'') ?>" placeholder="+1 (555) 000-0000"></label><label>Product interest<select name="interest"><option value="">Choose a category</option><?php foreach($categories as $category): ?><option <?= (($old['interest']??'')===$category['name']?'selected':'') ?>><?= e($category['name']) ?></option><?php endforeach; ?></select></label><label>Estimated quantity <b>*</b><input name="quantity" type="number" min="1" required value="<?= e($old['quantity']??'') ?>" placeholder="e.g. 500"></label><label class="form-wide">Anything else we should know?<textarea name="message" rows="3" placeholder="Share a few details about your order..."><?= e($old['message']??'') ?></textarea></label></div><button type="submit" class="button button-yellow submit-button">Send quote request <span>↗</span></button><small class="form-note">By submitting, you agree to be contacted about your request.</small></form><?php endif; ?></div></div></section>

<section class="faq-section section"><div class="wrap faq-layout"><div><p class="eyebrow">A FEW GOOD ANSWERS</p><h2>Questions, meet answers.</h2><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p></div><div class="faq-list"><?php foreach($faqs as $i=>$faq): ?><details class="faq-item reveal" <?= $i===0?'open':'' ?>><summary><?= e($faq['question']) ?><span>＋</span></summary><p><?= e($faq['answer']) ?></p></details><?php endforeach; ?></div></div></section>
<section class="newsletter"><div class="wrap newsletter-inner"><div><p class="eyebrow">A NOTE FROM EATCO</p><h2><?= e($content['newsletter_heading']) ?></h2><p><?= e($content['newsletter_text']) ?></p></div><form class="newsletter-form" action="#contact" onsubmit="event.preventDefault();this.innerHTML='<span class=\'newsletter-thanks\'>Thank you for subscribing. ✦</span>'"><label class="sr-only" for="newsletter-email">Email address</label><input id="newsletter-email" type="email" required placeholder="Your email address"><button type="submit" aria-label="Subscribe">→</button></form></div></section>
</main>
<footer class="site-footer"><div class="wrap"><div class="footer-top"><div class="footer-about"><a class="brand brand-light" href="#top"><img class="brand-logo" src="/assets/eatco-mark.png" alt="Eatco logo"><span><?= e($site['name']) ?><small>WHOLESALE MARKET</small></span></a><p><?= e($content['footer_text']) ?></p><div class="socials"><?php foreach($site['social'] as $name=>$url): ?><a href="<?= e($url) ?>" aria-label="<?= e($name) ?>"><?= e(substr($name,0,1)) ?></a><?php endforeach; ?></div></div><div class="footer-column"><h3>Company</h3><a href="#about">About Eatco</a><a href="#about">Our story</a><a href="#contact">Become a partner</a><a href="#contact">Careers</a></div><div class="footer-column"><h3>Categories</h3><?php foreach(array_slice($categories,0,4) as $category): ?><a href="#products" data-nav-category="<?= e($category['name']) ?>"><?= e($category['name']) ?></a><?php endforeach; ?><a href="#products">View all products</a></div><div class="footer-column"><h3>Support</h3><a href="#contact">Contact us</a><a href="#contact">FAQs</a><a href="#contact">Shipping information</a><a href="#contact">Help center</a></div><div class="footer-column"><h3>Good to know</h3><a href="#contact">Accessibility</a><div class="payment-icons"><span>VISA</span><span>MC</span><span>AMEX</span></div></div></div><div class="footer-bottom"><span>© <?= date('Y') ?> <?= e($site['name']) ?>. All rights reserved.</span><span>Made for the people who feed us. <i>✳</i></span><span>Shipping with care　♧　↗</span></div></div></footer>
<div class="modal-backdrop" id="product-modal" aria-hidden="true"><div class="product-modal glass-panel" role="dialog" aria-modal="true" aria-labelledby="modal-title"><button class="modal-close" aria-label="Close details">×</button><div class="modal-content" id="modal-content"></div></div></div>
<div class="theme-scene" id="theme-scene" aria-hidden="true"><div class="theme-scene-wash"></div><i class="theme-ring ring-one"></i><i class="theme-ring ring-two"></i></div><div class="currency-scene" id="currency-scene" aria-hidden="true"><div class="currency-scene-wash"></div><i class="currency-ring ring-one"></i><i class="currency-ring ring-two"></i><div class="currency-scene-copy"><span>LIVE PRICE CONVERSION</span><strong><b id="scene-from">USD</b><i>→</i><b id="scene-to">PKR</b></strong><small id="scene-rate">1 USD = PKR <?= number_format((float)$currency['pkr_per_usd'],2) ?></small></div></div>
<div class="toast" role="status" aria-live="polite"></div><script>window.EATCO_PRODUCTS = <?= $productJson ?: '[]' ?>; window.EATCO_IMAGES = <?= json_encode($productImages, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>; window.EATCO_CURRENCY = <?= json_encode($currency, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP) ?>;</script><script src="assets/script.js?v=20261122-theme-coin" defer></script>
</body></html>
