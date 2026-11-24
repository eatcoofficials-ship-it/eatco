<?php
declare(strict_types=1);

// EDIT HERE — Brand, contact, SEO, quote handling, and utility-bar content.
$site = [
    'name' => 'Eatco', 'tagline' => 'Quality food. Dependable supply. Better business.',
    'phone' => '0300 8110441', 'email' => 'eatco.officials@gmail.com', 'address' => 'Plot # 33 B, Sector 2, Near Cheema Plaza, Khayaban-e-Sirsyed, Rawalpindi, Pakistan',
    'shipping_threshold' => 500, 'currency' => 'USD', 'language' => 'English', 'since' => 2008,
    'title' => 'Eatco — Wholesale food, made simpler',
    'description' => 'Eatco helps businesses source wholesale food and beverages with dependable service and practical bulk options.',
    'social' => ['Instagram' => '#', 'LinkedIn' => '#', 'Facebook' => '#'],
    'mail_to' => 'eatco.officials@gmail.com', 'send_mail' => false,
];

// EDIT HERE — Reference conversion for display only. Refresh this rate as your business requires.
$currency = ['default'=>'USD','pkr_per_usd'=>278.35,'rate_date'=>'2026-11-19'];

// EDIT HERE — Keep one original metrics row in the homepage hero.
$stats = [['value'=>18,'suffix'=>'+','label'=>'Years in business'],['value'=>2400,'suffix'=>'+','label'=>'Clients served'],['value'=>24,'suffix'=>'','label'=>'Countries reached'],['value'=>860,'suffix'=>'+','label'=>'Products available']];

// EDIT HERE — All marketing copy for the main page.
$content = [
    'hero_eyebrow' => 'WHOLESALE, WITH A HUMAN TOUCH',
    'hero_title' => "Good food moves\nbusiness forward.",
    'hero_text' => 'A dependable wholesale partner for the businesses and communities that bring good food to the table.',
    'about' => 'Eatco helps businesses bring dependable food and beverage products to the tables, shelves, and kitchens that matter. We make wholesale sourcing straightforward, with responsive service and practical options.',
    'mission' => 'We help businesses source quality food and beverage products with dependable service. We connect good products with the people who make every meal possible.',
    'vision' => 'We help businesses source quality food and beverage products with dependable service. A more connected food supply chain, built around lasting relationships.',
    'community' => 'Feeding communities, supporting local farmers.',
    'story' => 'Trusted by families and businesses since',
    'contact_heading' => 'Let’s build your next order.',
    'contact_text' => 'Tell us what your business needs. Contact our team by phone or email, or use the directions below to find us.',
    'newsletter_heading' => 'A little more good in your inbox.',
    'newsletter_text' => 'We help businesses source quality food and beverage products with dependable service. Subscribe for offers, new arrivals, and useful updates.',
    'footer_text' => 'Quality food, dependable supply, and responsive service for wholesale buyers.',
    'quote_success' => 'Thank you. Your request is on its way to our team.',
];

// EDIT HERE — Navigation, section headings, buttons, and form labels used across the storefront.
$labels = [
    'browse'=>'Browse catalog','partner'=>'Become a partner','quote'=>'Request a Quote','all_categories'=>'All Categories',
    'shop_category'=>'Shop by category','explore'=>'Explore all products','deals_title'=>'Bulk offers worth making room for.',
    'shop_deals'=>'Shop this week’s deals','featured'=>'Find your next favorite.','all_products'=>'All products',
    'supplier'=>'Supplier','sort'=>'Sort by','popular'=>'Popular','price_low'=>'Price: low to high','price_high'=>'Price: high to low','name_sort'=>'Name: A to Z',
    'max_price'=>'Max price','moq'=>'MOQ','any_quantity'=>'Any quantity','no_results'=>'No products found','why_title'=>'Good business starts with good people.',
    'steps_title'=>'Four steps. Good things ahead.','partners_title'=>'Good things grow together.','about_title'=>'Good food has a way of bringing us closer.',
    'cert_title'=>'Standards that mean something.','company'=>'Company name','person'=>'Contact person','email'=>'Email address','phone'=>'Phone number',
    'interest'=>'Product interest','quantity'=>'Estimated quantity','message'=>'Anything else we should know?','send_request'=>'Send quote request',
    'company_col'=>'Company','categories_col'=>'Categories','support_col'=>'Support','legal_col'=>'Good to know',
];

// EDIT HERE — Category labels, icons and descriptions. Keep icons as inline-safe emoji or short text.
$categories = [
    ['name'=>'Canned Foods','icon'=>'🥫','description'=>'Carefully selected for wholesale buyers.'], ['name'=>'Bottled Drinks','icon'=>'🥤','description'=>'Carefully selected for wholesale buyers.'],
    ['name'=>'Snacks','icon'=>'🥨','description'=>'Carefully selected for wholesale buyers.'], ['name'=>'Dairy','icon'=>'🧀','description'=>'Carefully selected for wholesale buyers.'],
    ['name'=>'Grains and Rice','icon'=>'🌾','description'=>'Carefully selected for wholesale buyers.'], ['name'=>'Oils and Sauces','icon'=>'🫒','description'=>'Carefully selected for wholesale buyers.'],
    ['name'=>'Frozen','icon'=>'❄️','description'=>'Carefully selected for wholesale buyers.'], ['name'=>'Deals','icon'=>'✦','description'=>'Carefully selected for wholesale buyers.'],
];


// EDIT HERE — Why businesses choose Eatco.
$benefits = [
    ['icon'=>'✳','title'=>'Quality you can trust','text'=>'We help businesses source quality food and beverage products with dependable service.'],
    ['icon'=>'↗','title'=>'Better bulk pricing','text'=>'We help businesses source quality food and beverage products with dependable service.'],
    ['icon'=>'◷','title'=>'Reliable logistics','text'=>'We help businesses source quality food and beverage products with dependable service.'],
    ['icon'=>'♡','title'=>'A dedicated partner','text'=>'We help businesses source quality food and beverage products with dependable service.'],
    ['icon'=>'◇','title'=>'Flexible terms','text'=>'We help businesses source quality food and beverage products with dependable service.'],
    ['icon'=>'❄','title'=>'Careful cold chain','text'=>'We help businesses source quality food and beverage products with dependable service.'],
];

// EDIT HERE — Ordering journey steps.
$steps = [['number'=>'01','title'=>'Browse','text'=>'We help businesses source quality food and beverage products with dependable service.'],['number'=>'02','title'=>'Request a quote','text'=>'We help businesses source quality food and beverage products with dependable service.'],['number'=>'03','title'=>'Confirm','text'=>'We help businesses source quality food and beverage products with dependable service.'],['number'=>'04','title'=>'Delivery','text'=>'We help businesses source quality food and beverage products with dependable service.']];
\r\n// Partner names and quality certifications.\r\n$partners = ['Partner One','Partner Two','Partner Three','Partner Four','Partner Five','Partner Six'];
$certifications = [['name'=>'ISO','detail'=>'Quality management'],['name'=>'HACCP','detail'=>'Food safety'],['name'=>'HALAL','detail'=>'Certified products'],['name'=>'GMP','detail'=>'Good practices']];

// EDIT HERE — Frequently asked questions shown near the quote form.
$faqs = [
    ['question'=>'How does wholesale pricing work?','answer'=>'We help businesses source quality food and beverage products with dependable service. Bulk tiers are shown on each product card and in its details.'],
    ['question'=>'What is the minimum order?','answer'=>'We help businesses source quality food and beverage products with dependable service. Each product listing includes its minimum order quantity.'],
    ['question'=>'How quickly can my order arrive?','answer'=>'We help businesses source quality food and beverage products with dependable service. Our team confirms timing and delivery details with every quote.'],
];

// EDIT HERE — Product catalog. Each item includes: id, name, store, category, price, MOQ, stock, details, specs, tiers, image.
$products = [
 ['id'=>'p01','name'=>'San Marzano Style Tomatoes','store'=>'Eatco Store 1','category'=>'Canned Foods','price'=>1.20,'moq'=>120,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 28 oz can · case of 12 · 24 month shelf life.','specs'=>['Pack size'=>'28 oz · case of 12','Shelf life'=>'24 months','Origin'=>'Lorem ipsum','Packaging'=>'Recyclable tin'],'tiers'=>[[120,1.20],[500,0.95],[1000,0.82]],'image'=>'tomatoes'],
 ['id'=>'p02','name'=>'Organic Chickpeas','store'=>'Eatco Store 2','category'=>'Canned Foods','price'=>0.88,'moq'=>144,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 15 oz can · case of 12 · pantry staple.','specs'=>['Pack size'=>'15 oz · case of 12','Shelf life'=>'30 months','Origin'=>'Lorem ipsum','Packaging'=>'Recyclable tin'],'tiers'=>[[144,.88],[500,.74],[1000,.62]],'image'=>'chickpeas'],
 ['id'=>'p03','name'=>'Sweet Golden Corn','store'=>'Eatco Store 1','category'=>'Canned Foods','price'=>0.76,'moq'=>120,'stock'=>'Low Stock','details'=>'Carefully selected for wholesale buyers. 15 oz can · case of 12 · naturally sweet.','specs'=>['Pack size'=>'15 oz · case of 12','Shelf life'=>'24 months','Origin'=>'Lorem ipsum','Packaging'=>'Recyclable tin'],'tiers'=>[[120,.76],[500,.64],[1000,.55]],'image'=>'corn'],
 ['id'=>'p04','name'=>'Sparkling Citrus Water','store'=>'Eatco Store 3','category'=>'Bottled Drinks','price'=>0.92,'moq'=>96,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 12 fl oz glass bottle · case of 24.','specs'=>['Pack size'=>'12 fl oz · case of 24','Shelf life'=>'18 months','Origin'=>'Lorem ipsum','Packaging'=>'Glass bottle'],'tiers'=>[[96,.92],[500,.78],[1000,.68]],'image'=>'citrus'],
 ['id'=>'p05','name'=>'Cold Brew Coffee','store'=>'Eatco Store 2','category'=>'Bottled Drinks','price'=>1.85,'moq'=>72,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 10 fl oz bottle · case of 12.','specs'=>['Pack size'=>'10 fl oz · case of 12','Shelf life'=>'12 months','Origin'=>'Lorem ipsum','Packaging'=>'Glass bottle'],'tiers'=>[[72,1.85],[300,1.58],[600,1.39]],'image'=>'coffee'],
 ['id'=>'p06','name'=>'Still Spring Water','store'=>'Eatco Store 4','category'=>'Bottled Drinks','price'=>0.42,'moq'=>240,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 16.9 fl oz bottle · case of 24.','specs'=>['Pack size'=>'16.9 fl oz · case of 24','Shelf life'=>'24 months','Origin'=>'Lorem ipsum','Packaging'=>'Recyclable PET'],'tiers'=>[[240,.42],[1000,.35],[2500,.29]],'image'=>'water'],
 ['id'=>'p07','name'=>'Sea Salt Pretzel Twists','store'=>'Eatco Store 1','category'=>'Snacks','price'=>1.10,'moq'=>100,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 8 oz bag · case of 12 · baked crunch.','specs'=>['Pack size'=>'8 oz · case of 12','Shelf life'=>'9 months','Origin'=>'Lorem ipsum','Packaging'=>'Flexible pouch'],'tiers'=>[[100,1.10],[500,.92],[1000,.79]],'image'=>'pretzels'],
 ['id'=>'p08','name'=>'Roasted Almond Mix','store'=>'Eatco Store 3','category'=>'Snacks','price'=>2.40,'moq'=>60,'stock'=>'Low Stock','details'=>'Carefully selected for wholesale buyers. 12 oz pouch · case of 10.','specs'=>['Pack size'=>'12 oz · case of 10','Shelf life'=>'12 months','Origin'=>'Lorem ipsum','Packaging'=>'Resealable pouch'],'tiers'=>[[60,2.40],[300,2.06],[600,1.82]],'image'=>'almonds'],
 ['id'=>'p09','name'=>'Cultured Greek Yogurt','store'=>'Eatco Store 2','category'=>'Dairy','price'=>3.15,'moq'=>48,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 32 oz tub · case of 6 · chilled.','specs'=>['Pack size'=>'32 oz · case of 6','Shelf life'=>'45 days','Origin'=>'Lorem ipsum','Packaging'=>'Chilled tub'],'tiers'=>[[48,3.15],[200,2.82],[400,2.55]],'image'=>'yogurt'],
 ['id'=>'p10','name'=>'Creamy Salted Butter','store'=>'Eatco Store 4','category'=>'Dairy','price'=>4.80,'moq'=>40,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 1 lb pack · case of 12 · chilled.','specs'=>['Pack size'=>'1 lb · case of 12','Shelf life'=>'90 days','Origin'=>'Lorem ipsum','Packaging'=>'Chilled carton'],'tiers'=>[[40,4.80],[200,4.28],[400,3.96]],'image'=>'butter'],
 ['id'=>'p11','name'=>'Long Grain Rice','store'=>'Eatco Store 1','category'=>'Grains and Rice','price'=>8.50,'moq'=>20,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 10 lb bag · case of 2 · versatile grain.','specs'=>['Pack size'=>'10 lb · case of 2','Shelf life'=>'24 months','Origin'=>'Lorem ipsum','Packaging'=>'Woven bag'],'tiers'=>[[20,8.50],[100,7.60],[250,6.95]],'image'=>'rice'],
 ['id'=>'p12','name'=>'Rolled Oats','store'=>'Eatco Store 3','category'=>'Grains and Rice','price'=>5.20,'moq'=>24,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 5 lb bag · case of 4 · whole grain.','specs'=>['Pack size'=>'5 lb · case of 4','Shelf life'=>'18 months','Origin'=>'Lorem ipsum','Packaging'=>'Paper pouch'],'tiers'=>[[24,5.20],[120,4.60],[240,4.15]],'image'=>'oats'],
 ['id'=>'p13','name'=>'Extra Virgin Olive Oil','store'=>'Eatco Store 2','category'=>'Oils and Sauces','price'=>9.90,'moq'=>24,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 1 L bottle · case of 6 · first press.','specs'=>['Pack size'=>'1 L · case of 6','Shelf life'=>'24 months','Origin'=>'Lorem ipsum','Packaging'=>'Dark glass'],'tiers'=>[[24,9.90],[120,8.80],[240,7.95]],'image'=>'olive'],
 ['id'=>'p14','name'=>'Classic Tomato Sauce','store'=>'Eatco Store 4','category'=>'Oils and Sauces','price'=>1.45,'moq'=>96,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 24 oz jar · case of 12 · rich and ready.','specs'=>['Pack size'=>'24 oz · case of 12','Shelf life'=>'18 months','Origin'=>'Lorem ipsum','Packaging'=>'Glass jar'],'tiers'=>[[96,1.45],[480,1.22],[960,1.08]],'image'=>'sauce'],
 ['id'=>'p15','name'=>'Garden Vegetable Blend','store'=>'Eatco Store 1','category'=>'Frozen','price'=>2.10,'moq'=>60,'stock'=>'Low Stock','details'=>'Carefully selected for wholesale buyers. 2 lb bag · case of 6 · flash frozen.','specs'=>['Pack size'=>'2 lb · case of 6','Shelf life'=>'18 months frozen','Origin'=>'Lorem ipsum','Packaging'=>'Freezer pouch'],'tiers'=>[[60,2.10],[300,1.82],[600,1.60]],'image'=>'veggies'],
 ['id'=>'p16','name'=>'Wild Blueberries','store'=>'Eatco Store 3','category'=>'Frozen','price'=>4.25,'moq'=>40,'stock'=>'In Stock','details'=>'Carefully selected for wholesale buyers. 2 lb bag · case of 6 · picked at peak.','specs'=>['Pack size'=>'2 lb · case of 6','Shelf life'=>'24 months frozen','Origin'=>'Lorem ipsum','Packaging'=>'Freezer pouch'],'tiers'=>[[40,4.25],[200,3.78],[400,3.38]],'image'=>'berries'],
];

// EDIT HERE — Product photo URLs. Replace these Unsplash URLs with your own local image paths when ready.
$productImages = [
 'tomatoes'=>'https://images.unsplash.com/photo-1547592180-85f173990554?auto=format&fit=crop&w=900&q=82','chickpeas'=>'https://images.unsplash.com/photo-1515543904379-3d757afe72e4?auto=format&fit=crop&w=900&q=82','corn'=>'https://images.unsplash.com/photo-1551754655-cd27e38d2076?auto=format&fit=crop&w=900&q=82','citrus'=>'https://images.unsplash.com/photo-1647939572092-0010eb63f964?auto=format&fit=crop&w=900&q=82','coffee'=>'https://images.unsplash.com/photo-1517701604599-bb29b565090c?auto=format&fit=crop&w=900&q=82','water'=>'https://images.unsplash.com/photo-1523362628745-0c100150b504?auto=format&fit=crop&w=900&q=82','pretzels'=>'https://images.unsplash.com/photo-1599599810694-b5ac4dddb3f3?auto=format&fit=crop&w=900&q=82','almonds'=>'https://images.unsplash.com/photo-1508061253366-f7da158b6d46?auto=format&fit=crop&w=900&q=82','yogurt'=>'https://images.unsplash.com/photo-1488477181946-6428a0291777?auto=format&fit=crop&w=900&q=82','butter'=>'https://images.unsplash.com/photo-1589985270826-4b7bb135bc9d?auto=format&fit=crop&w=900&q=82','rice'=>'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=900&q=82','oats'=>'https://images.unsplash.com/photo-1517673132405-a56a62b18caf?auto=format&fit=crop&w=900&q=82','olive'=>'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=900&q=82','sauce'=>'https://images.unsplash.com/photo-1472476443507-c7a5948772fc?auto=format&fit=crop&w=900&q=82','veggies'=>'https://images.unsplash.com/photo-1597362925123-77861d3fbac7?auto=format&fit=crop&w=900&q=82','berries'=>'https://images.unsplash.com/photo-1498557850523-fd3d118b962e?auto=format&fit=crop&w=900&q=82'
];

// Resolve each catalog image key to its editable image path for rendering and product details.
foreach ($products as &$product) {
    $product['image'] = $productImages[$product['image']] ?? '';
}
unset($product);
