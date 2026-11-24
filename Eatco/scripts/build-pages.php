<?php
declare(strict_types=1);

// Render one shared PHP template, then publish each route as static HTML.
putenv('VERCEL=1');
$_SERVER['REQUEST_METHOD'] = 'GET';
$_GET = [];
ob_start();
include dirname(__DIR__) . '/api/index.php';
$source = (string)ob_get_clean();

function queryNodes(DOMXPath $xpath, string $query, ?DOMNode $context = null): array
{
    $list = $xpath->query($query, $context);
    return $list ? iterator_to_array($list) : [];
}

function byClass(DOMXPath $xpath, string $class, ?DOMNode $context = null): ?DOMElement
{
    $nodes = queryNodes($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')]", $context);
    return isset($nodes[0]) && $nodes[0] instanceof DOMElement ? $nodes[0] : null;
}

function byId(DOMXPath $xpath, string $id, ?DOMNode $context = null): ?DOMElement
{
    $nodes = queryNodes($xpath, ".//*[@id='{$id}']", $context);
    return isset($nodes[0]) && $nodes[0] instanceof DOMElement ? $nodes[0] : null;
}

function element(DOMDocument $dom, string $tag, string $class = '', string $text = ''): DOMElement
{
    $node = $dom->createElement($tag);
    if ($class !== '') $node->setAttribute('class', $class);
    if ($text !== '') $node->appendChild($dom->createTextNode($text));
    return $node;
}

function pageBanner(DOMDocument $dom, string $title, string $eyebrow, string $description, string $route): DOMElement
{
    $section = element($dom, 'section', 'page-banner');
    $wrap = element($dom, 'div', 'wrap page-banner-inner');
    $crumbs = element($dom, 'nav', 'breadcrumbs');
    $crumbs->setAttribute('aria-label', 'Breadcrumb');
    $home = element($dom, 'a', '', 'Home');
    $home->setAttribute('href', '/');
    $crumbs->appendChild($home);
    $crumbs->appendChild(element($dom, 'span', 'breadcrumb-separator', ' / '));
    $current = element($dom, 'span', '', $title);
    $current->setAttribute('aria-current', 'page');
    $crumbs->appendChild($current);
    $wrap->appendChild($crumbs);
    $wrap->appendChild(element($dom, 'p', 'eyebrow', $eyebrow));
    $h1 = element($dom, 'h1', 'page-title reveal', $title);
    $wrap->appendChild($h1);
    $wrap->appendChild(element($dom, 'p', 'page-description reveal', $description));
    $section->appendChild($wrap);
    $section->setAttribute('data-page', $route);
    return $section;
}

function setHref(DOMElement $node, string $href): void
{
    $node->setAttribute('href', $href);
}

function allByClass(DOMXPath $xpath, string $class, ?DOMNode $context = null): array
{
    return queryNodes($xpath, ".//*[contains(concat(' ', normalize-space(@class), ' '), ' {$class} ')]", $context);
}

function makeDocument(string $source): array
{
    $dom = new DOMDocument('1.0', 'UTF-8');
    $dom->preserveWhiteSpace = false;
    libxml_use_internal_errors(true);
    $dom->loadHTML($source, LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();
    return [$dom, new DOMXPath($dom)];
}

$pages = [
    'home' => ['path'=>'/', 'title'=>'Eatco — Wholesale food, made simpler', 'description'=>'A thoughtful wholesale marketplace for the people who feed their communities.', 'banner'=>false, 'label'=>'Home'],
    'categories' => ['path'=>'/categories', 'title'=>'Categories | Eatco', 'description'=>'Explore all eight Eatco wholesale food and beverage categories.', 'banner'=>true, 'label'=>'Categories'],
    'products' => ['path'=>'/products', 'title'=>'Products | Eatco', 'description'=>'Browse wholesale food and beverage products, suppliers, and bulk pricing.', 'banner'=>true, 'label'=>'Products'],
    'deals' => ['path'=>'/deals', 'title'=>'Bulk Deals | Eatco', 'description'=>'Explore Eatco bulk offers and savings across the wholesale catalog.', 'banner'=>true, 'label'=>'Deals'],
    'how-it-works' => ['path'=>'/how-it-works', 'title'=>'How It Works | Eatco', 'description'=>'Learn how Eatco helps businesses browse, request quotes, confirm, and receive orders.', 'banner'=>true, 'label'=>'How It Works'],
    'partners' => ['path'=>'/partners', 'title'=>'Partners | Eatco', 'description'=>'Meet the partners and businesses growing together with Eatco.', 'banner'=>true, 'label'=>'Partners'],
    'about' => ['path'=>'/about', 'title'=>'About Eatco', 'description'=>'Learn about Eatco’s story, mission, community, and standards.', 'banner'=>true, 'label'=>'About'],
    'contact' => ['path'=>'/contact', 'title'=>'Contact Eatco', 'description'=>'Get in touch with Eatco and request a wholesale quote.', 'banner'=>true, 'label'=>'Contact'],
    'faq' => ['path'=>'/faq', 'title'=>'Frequently Asked Questions | Eatco', 'description'=>'Answers to common questions about wholesale pricing, orders, and delivery.', 'banner'=>true, 'label'=>'FAQ'],
    'shipping' => ['path'=>'/shipping', 'title'=>'Shipping Information | Eatco', 'description'=>'Shipping information for Eatco wholesale orders.', 'banner'=>true, 'label'=>'Shipping'],
    'careers' => ['path'=>'/careers', 'title'=>'Careers | Eatco', 'description'=>'Explore opportunities to work with Eatco.', 'banner'=>true, 'label'=>'Careers'],
    'help' => ['path'=>'/help', 'title'=>'Help Center | Eatco', 'description'=>'Find help with Eatco wholesale products, quotes, and orders.', 'banner'=>true, 'label'=>'Help Center'],
    'accessibility' => ['path'=>'/accessibility', 'title'=>'Accessibility | Eatco', 'description'=>'Read about accessibility at Eatco.', 'banner'=>true, 'label'=>'Accessibility'],
];

$supportContent = [
    'shipping'=>['Shipping information','Shipping timing, availability, and delivery details are confirmed with each wholesale quote.'],
    'careers'=>['Careers','We welcome people who care about good food, reliable service, and the communities we serve.'],
    'help'=>['Help center','Browse the FAQ or contact our team for help with products, quotes, and orders.'],
    'accessibility'=>['Accessibility','Eatco aims to make its website usable across devices and assistive technologies.'],
];

$routes = [
    'home'=>['hero','home-categories','benefits','home-about','home-partners','newsletter'],
    'categories'=>['categories'],
    'products'=>['products'],
    'deals'=>['deals','bulk-products'],
    'how-it-works'=>['benefits','steps'],
    'partners'=>['partners'],
    'about'=>['about','community','certifications'],
    'contact'=>['contact','faq'],
    'faq'=>['faq'],
];

$navigation = [
    ['Home','/'],['Products','/products'],['Deals','/deals'],['How It Works','/how-it-works'],
    ['Partners','/partners'],['About','/about'],['Contact','/contact'],
];

function buildNav(DOMDocument $dom, array $navigation, string $route): DOMElement
{
    $nav = element($dom, 'nav', 'primary-nav');
    $nav->setAttribute('aria-label', 'Main navigation');
    foreach ($navigation as [$label, $href]) {
        $link = element($dom, 'a', '', $label);
        $link->setAttribute('href', $href);
        if (($route === 'home' && $href === '/') || ($route !== 'home' && $href === '/' . $route)) {
            $link->setAttribute('aria-current', 'page');
        }
        $nav->appendChild($link);
    }
    return $nav;
}

function makeMobileDrawer(DOMDocument $dom, array $navigation, array $categories, string $route): DOMElement
{
    $drawer = element($dom, 'aside', 'mobile-drawer');
    $drawer->setAttribute('aria-label', 'Mobile navigation');
    $close = element($dom, 'button', 'drawer-close', '×');
    $close->setAttribute('type', 'button');
    $close->setAttribute('aria-label', 'Close menu');
    $drawer->appendChild($close);
    $drawer->appendChild(element($dom, 'div', 'drawer-brand', 'Eatco'));
    foreach ($navigation as [$label, $href]) {
        $link = element($dom, 'a', '', $label);
        $link->setAttribute('href', $href);
        if (($route === 'home' && $href === '/') || ($route !== 'home' && $href === '/' . $route)) $link->setAttribute('aria-current', 'page');
        $drawer->appendChild($link);
    }
    $extraPages = [
        ['Categories','/categories'],['FAQ','/faq'],['Shipping','/shipping'],['Careers','/careers'],
        ['Help Center','/help'],['Accessibility','/accessibility'],
    ];
    foreach ($extraPages as [$label, $href]) {
        $link = element($dom, 'a', '', $label);
        $link->setAttribute('href', $href);
        if ($href === '/' . $route) $link->setAttribute('aria-current', 'page');
        $drawer->appendChild($link);
    }
    $drawer->appendChild(element($dom, 'p', 'drawer-category-title', 'Shop categories'));
    foreach ($categories as $category) {
        $link = element($dom, 'a', '', $category['name']);
        $link->setAttribute('href', '/products?category=' . rawurlencode($category['name']));
        $drawer->appendChild($link);
    }
    $quote = element($dom, 'a', 'button button-yellow', 'Request a Quote ↗');
    $quote->setAttribute('href', '/contact');
    $drawer->appendChild($quote);
    return $drawer;
}

function pageSections(string $route, DOMDocument $dom, DOMXPath $xpath, DOMElement $main, array $products, array $categories, array $supportContent): array
{
    $getId = fn(string $id) => byId($xpath, $id, $main);
    $getClass = fn(string $class) => byClass($xpath, $class, $main);
    $clone = static fn(?DOMElement $node) => $node ? $node->cloneNode(true) : null;
    $section = match ($route) {
        'hero' => $clone($getClass('hero')),
        'home-categories' => $clone($getId('categories')),
        'categories' => $clone($getId('categories')),
        'products' => $clone($getId('products')),
        'deals' => $clone($getId('deals')),
        'benefits' => $clone($getClass('benefits-section')),
        'steps' => $clone($getClass('steps-section')),
        'partners' => $clone($getClass('partners-section')),
        'home-partners' => $clone($getClass('partners-section')),
        'about' => $clone($getId('about')),
        'home-about' => $clone($getId('about')),
        'community' => $clone($getClass('community-strip')),
        'certifications' => $clone($getClass('cert-section')),
        'contact' => $clone($getId('contact')),
        'faq' => $clone($getClass('faq-section')),
        'newsletter' => $clone($getClass('newsletter')),
        default => null,
    };
    if ($route === 'home-about' && $section) {
        if ($node = byClass($xpath, 'mission-vision', $section)) $node->parentNode?->removeChild($node);
        if ($node = byClass($xpath, 'about-stat-row', $section)) $node->parentNode?->removeChild($node);
        return [$section];
    }
    if ($route === 'home-partners' && $section) {
        return [$section];
    }
    if ($route === 'home-categories' && $section) {
        foreach (array_slice(allByClass($xpath, 'category-tile', $section), 4) as $tile) $tile->parentNode?->removeChild($tile);
    }
    if ($route === 'products' && $section) {
        foreach (allByClass($xpath, 'product-card', $section) as $card) $card->setAttribute('data-deal', 'true');
    }
    if ($route === 'partners' && $section) return [$section];
    if ($route === 'bulk-products') {
        $sourceSection = $getId('products');
        $cards = $sourceSection ? allByClass($xpath, 'product-card', $sourceSection) : [];
        $bulk = element($dom, 'section', 'section product-section deal-product-section');
        $bulk->setAttribute('id', 'bulk-offers');
        $wrap = element($dom, 'div', 'wrap');
        $heading = element($dom, 'div', 'section-heading reveal');
        $heading->appendChild(element($dom, 'div', '', ''));
        $headingInner = $heading->firstChild;
        $headingInner->appendChild(element($dom, 'p', 'eyebrow', 'BULK PRICING'));
        $headingInner->appendChild(element($dom, 'h2', '', 'Save more by the case.'));
        $heading->appendChild(element($dom, 'p', 'section-intro', 'Compare unit prices at the largest order tiers.'));
        $wrap->appendChild($heading);
        $grid = element($dom, 'div', 'product-grid');
        $grid->setAttribute('id', 'product-grid');
        foreach ($cards as $card) {
            $productId = $card->getAttribute('data-product-id');
            $product = null;
            foreach ($products as $candidate) if ($candidate['id'] === $productId) { $product = $candidate; break; }
            if (!$product || empty($product['tiers'])) continue;
            $lastTier = $product['tiers'][count($product['tiers']) - 1];
            $saving = $product['price'] > 0 ? (int)round((1 - ($lastTier[1] / $product['price'])) * 100) : 0;
            $copy = $card->cloneNode(true);
            $copy->setAttribute('data-deal', 'true');
            if ($photo = byClass($xpath, 'product-image-wrap', $copy)) {
                $badge = element($dom, 'span', 'deal-savings', 'SAVE ' . $saving . '%');
                $photo->appendChild($badge);
            }
            $grid->appendChild($copy);
        }
        $wrap->appendChild($grid);
        $bulk->appendChild($wrap);
        return [$bulk];
    }
    if (isset($supportContent[$route])) {
        [$heading, $copy] = $supportContent[$route];
        $section = element($dom, 'section', 'section support-content');
        $wrap = element($dom, 'div', 'wrap support-card glass-panel reveal');
        $wrap->appendChild(element($dom, 'p', 'eyebrow', 'EATCO · SUPPORT'));
        $wrap->appendChild(element($dom, 'h2', '', $heading));
        $wrap->appendChild(element($dom, 'p', '', $copy));
        $contact = element($dom, 'a', 'button button-yellow', 'Contact our team ↗');
        $contact->setAttribute('href', '/contact');
        $wrap->appendChild($contact);
        $section->appendChild($wrap);
        return [$section];
    }
    return $section ? [$section] : [];
}

function pageSpecificMarkup(DOMXPath $xpath, DOMDocument $dom, string $route, string $label): void
{
    $main = queryNodes($xpath, '//main')[0] ?? null;
    if (!$main instanceof DOMElement) return;
    $map = [
        'hero'=>'hero', 'categories'=>'categories', 'products'=>'products', 'deals'=>'deals',
        'how-it-works'=>'how-it-works', 'partners'=>'partners', 'about'=>'about', 'contact'=>'contact',
        'faq'=>'faq', 'shipping'=>'shipping', 'careers'=>'careers', 'help'=>'help',
        'accessibility'=>'accessibility', 'top'=>'home',
    ];
    $hashMap = ['#top'=>'/', '#categories'=>'/categories', '#products'=>'/products', '#deals'=>'/deals', '#about'=>'/about', '#contact'=>'/contact', '#faq'=>'/faq'];
    foreach (queryNodes($xpath, '//a[@href]') as $anchor) {
        if (!$anchor instanceof DOMElement) continue;
        $href = $anchor->getAttribute('href');
        if (isset($hashMap[$href])) $anchor->setAttribute('href', $hashMap[$href]);
        elseif ($href === '#') {
            $name = strtolower($anchor->getAttribute('aria-label'));
            $anchor->setAttribute('href', match ($name) {
                'instagram'=>'https://www.instagram.com/', 'linkedin'=>'https://www.linkedin.com/', 'facebook'=>'https://www.facebook.com/',
                default=> '/',
            });
        }
        $text = trim($anchor->textContent);
        if (in_array($text, ['About Eatco','Our story'], true)) $anchor->setAttribute('href', '/about');
        if ($text === 'Become a partner') $anchor->setAttribute('href', '/contact');
        if ($text === 'Careers') $anchor->setAttribute('href', '/careers');
        if ($text === 'Contact us') $anchor->setAttribute('href', '/contact');
        if ($text === 'FAQs') $anchor->setAttribute('href', '/faq');
        if ($text === 'Shipping information') $anchor->setAttribute('href', '/shipping');
        if ($text === 'Help center') $anchor->setAttribute('href', '/help');
        if ($text === 'Accessibility') $anchor->setAttribute('href', '/accessibility');
        $activeFooterRoutes = [
            'Contact us'=>'contact','FAQs'=>'faq','Shipping information'=>'shipping','Careers'=>'careers',
            'Help center'=>'help','Accessibility'=>'accessibility',
        ];
        if (($activeFooterRoutes[$text] ?? null) === $route) $anchor->setAttribute('aria-current', 'page');
        if ($text === 'View all products') $anchor->setAttribute('href', '/products');
        if (str_starts_with($text, 'Get to know us')) $anchor->setAttribute('href', '/about');
        if (str_starts_with($text, 'Shop this week’s deals') || str_starts_with($text, 'See the offer')) $anchor->setAttribute('href', '/deals');
        if (str_starts_with($text, 'Browse catalog')) $anchor->setAttribute('href', '/products');
        if ($text === 'Request a Quote') $anchor->setAttribute('href', '/contact');
        if (str_contains($anchor->getAttribute('class'), 'all-categories')) {
            if ($route === 'categories') $anchor->setAttribute('aria-current', 'page');
            else $anchor->removeAttribute('aria-current');
        }
        if ($anchor->hasAttribute('data-category-link')) {
            $category = $anchor->getAttribute('data-category-link');
            $anchor->setAttribute('href', '/products?category=' . rawurlencode($category));
        }
        if ($anchor->hasAttribute('data-nav-category')) {
            $category = $anchor->getAttribute('data-nav-category');
            $anchor->setAttribute('href', '/products?category=' . rawurlencode($category));
        }
    }
    foreach (queryNodes($xpath, '//form[@class="search"]') as $form) {
        $form->setAttribute('action', '/products');
        $form->setAttribute('method', 'get');
        $form->removeAttribute('onsubmit');
    }
    if ($select = byId($xpath, 'search-category')) $select->setAttribute('name', 'category');
    if ($input = byId($xpath, 'global-search')) $input->setAttribute('name', 'q');
    foreach (queryNodes($xpath, '//form[contains(@class,"newsletter-form")]') as $form) $form->setAttribute('action', '/contact');
    if ($aside = byClass($xpath, 'mobile-drawer')) {
        $newDrawer = makeMobileDrawer($dom, $GLOBALS['navigation'], $GLOBALS['categories'], $route);
        $aside->parentNode?->replaceChild($newDrawer, $aside);
    }
    if ($header = byClass($xpath, 'site-header')) {
        $headerMain = byClass($xpath, 'header-main', $header);
        $categoryNav = byClass($xpath, 'category-nav', $header);
        if ($headerMain && $categoryNav) $header->insertBefore(buildNav($dom, $GLOBALS['navigation'], $route), $categoryNav);
        if ($brand = byClass($xpath, 'brand', $header)) $brand->setAttribute('href', '/');
        foreach (queryNodes($xpath, './/a[@href="#contact"]', $header) as $anchor) $anchor->setAttribute('href', '/contact');
        foreach (queryNodes($xpath, './/a[@href="#categories"]', $header) as $anchor) {
            $anchor->setAttribute('href', '/categories');
            if (str_contains($anchor->getAttribute('class'), 'all-categories')) {
                if ($route === 'categories') $anchor->setAttribute('aria-current', 'page');
                else $anchor->removeAttribute('aria-current');
            }
        }
        foreach (queryNodes($xpath, './/a[@href="#products"]', $header) as $anchor) {
            $category = $anchor->getAttribute('data-nav-category');
            $anchor->setAttribute('href', $category !== '' ? '/products?category=' . rawurlencode($category) : '/products');
        }
        foreach (queryNodes($xpath, './/a[@href="#deals"]', $header) as $anchor) $anchor->setAttribute('href', '/deals');
        if ($quote = byClass($xpath, 'header-quote', $header)) $quote->setAttribute('href', '/contact');
        if ($account = byClass($xpath, 'account-link', $header)) $account->setAttribute('href', '/contact');
        if ($searchButton = queryNodes($xpath, './/form[contains(@class,"search")]//button', $header)[0] ?? null) $searchButton->setAttribute('type', 'submit');
    }
    if ($drawer = byClass($xpath, 'quote-drawer')) {
        foreach (queryNodes($xpath, './/a', $drawer) as $anchor) $anchor->setAttribute('href', '/contact');
    }
    foreach (queryNodes($xpath, '//link[starts-with(@href,"assets/style.css")]') as $asset) $asset->setAttribute('href', '/assets/style.css?v=20261124-logo-loader-v2');
    foreach (queryNodes($xpath, '//script[starts-with(@src,"assets/script.js")]') as $script) $script->setAttribute('src', '/assets/script.js?v=20261124-logo-loader-v2');
    if ($body = queryNodes($xpath, '//body')[0] ?? null) {
        $transition = element($dom, 'div', 'page-transition');
        $transition->setAttribute('aria-hidden', 'true');
        $body->appendChild($transition);
    }
    if ($main = queryNodes($xpath, '//main')[0] ?? null) {
        $GLOBALS['sourceMain'] = $main->cloneNode(true);
        while ($main->firstChild) $main->removeChild($main->firstChild);
        if ($route !== 'home' && $route !== 'not-found') $main->appendChild(pageBanner($dom, $label, 'WHOLESALE, WITH A HUMAN TOUCH', $GLOBALS['pages'][$route]['description'] ?? '', $route));
        foreach ($GLOBALS['routes'][$route] ?? [$route] as $sectionRoute) {
            foreach (pageSections($sectionRoute, $dom, $xpath, $GLOBALS['sourceMain'], $GLOBALS['products'], $GLOBALS['categories'], $GLOBALS['supportContent']) as $section) $main->appendChild($section);
        }
        if ($route === 'not-found') {
            $notFound = element($dom, 'section', 'section not-found-section');
            $wrap = element($dom, 'div', 'wrap not-found-card glass-panel reveal');
            $wrap->appendChild(element($dom, 'p', 'eyebrow', '404 · PAGE NOT FOUND'));
            $wrap->appendChild(element($dom, 'h1', 'page-title', 'This page has wandered off.'));
            $wrap->appendChild(element($dom, 'p', '', 'The page you’re looking for isn’t here. Let’s get you back to the marketplace.'));
            $home = element($dom, 'a', 'button button-yellow', 'Back to Home ↗');
            $home->setAttribute('href', '/');
            $wrap->appendChild($home);
            $notFound->appendChild($wrap);
            $main->appendChild($notFound);
        }
    }
}

function renderPage(string $source, string $route, array $meta, array $pages, array $navigation, array $products, array $categories, array $supportContent): string
{
    [$dom, $xpath] = makeDocument($source);
    $GLOBALS['pages'] = $pages;
    $GLOBALS['navigation'] = $navigation;
    $GLOBALS['products'] = $products;
    $GLOBALS['categories'] = $categories;
    $GLOBALS['supportContent'] = $supportContent;
    $GLOBALS['source'] = $source;
    $GLOBALS['sourceMain'] = queryNodes($xpath, '//main')[0];
    pageSpecificMarkup($xpath, $dom, $route, $meta['label']);
    $title = queryNodes($xpath, '//title')[0] ?? null;
    if ($title) $title->textContent = $meta['title'];
    foreach (queryNodes($xpath, '//meta[@name="description"]') as $node) $node->setAttribute('content', $meta['description']);
    foreach (queryNodes($xpath, '//meta[@property="og:title"]') as $node) $node->setAttribute('content', $meta['title']);
    foreach (queryNodes($xpath, '//meta[@property="og:description"]') as $node) $node->setAttribute('content', $meta['description']);
    $canonical = queryNodes($xpath, '//link[@rel="canonical"]')[0] ?? null;
    if (!$canonical) {
        $canonical = $dom->createElement('link');
        $canonical->setAttribute('rel', 'canonical');
        $head = queryNodes($xpath, '//head')[0];
        $head->appendChild($canonical);
    }
    $canonical->setAttribute('href', 'https://eatco-roan.vercel.app' . $meta['path']);
    return $dom->saveHTML();
}

[$templateDom, $templateXPath] = makeDocument($source);
$templateMain = queryNodes($templateXPath, '//main')[0] ?? null;
if (!$templateMain instanceof DOMElement) throw new RuntimeException('The shared storefront template has no main element.');
$GLOBALS['routes'] = $routes;
$GLOBALS['sourceMain'] = $templateMain;

foreach ($pages as $route => $meta) {
    $html = renderPage($source, $route, $meta, $pages, $navigation, $products, $categories, $supportContent);
    $file = $route === 'home' ? 'index.html' : $route . '.html';
    file_put_contents(dirname(__DIR__) . '/' . $file, $html);
}
$notFoundMeta = ['path'=>'/404', 'title'=>'Page Not Found | Eatco', 'description'=>'The page could not be found.', 'label'=>'Not Found'];
$notFoundHtml = renderPage($source, 'not-found', $notFoundMeta, $pages, $navigation, $products, $categories, $supportContent);
file_put_contents(dirname(__DIR__) . '/404.html', $notFoundHtml);

$sitemap = new DOMDocument('1.0', 'UTF-8');
$urlset = $sitemap->createElement('urlset');
$urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
foreach ($pages as $meta) {
    $url = $sitemap->createElement('url');
    $loc = $sitemap->createElement('loc', htmlspecialchars('https://eatco-roan.vercel.app' . $meta['path'], ENT_XML1 | ENT_COMPAT, 'UTF-8'));
    $url->appendChild($loc);
    $urlset->appendChild($url);
}
$sitemap->appendChild($urlset);
$sitemap->formatOutput = true;
$sitemap->save(dirname(__DIR__) . '/sitemap.xml');
file_put_contents(dirname(__DIR__) . '/robots.txt', "User-agent: *\nAllow: /\nSitemap: https://eatco-roan.vercel.app/sitemap.xml\n");
