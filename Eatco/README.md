# Eatco wholesale storefront

## Site structure

```text
index.html                 Home
categories.html            Categories
products.html              Product catalog
deals.html                 Bulk deals
how-it-works.html          Ordering steps
partners.html              Partners and testimonials
about.html                 Story, mission, community, certifications
contact.html               Contact, quote form, FAQ
faq.html                   FAQ
shipping.html              Shipping information
careers.html               Careers
help.html                  Help center
privacy.html               Privacy policy
terms.html                 Terms of service
accessibility.html         Accessibility
404.html                   Custom not-found page
api/index.php              Shared PHP page renderer and quote handler
includes/data.php          Shared content and product data
scripts/build-pages.php    Generates every static route, sitemap, and robots.txt
scripts/build-static.ps1   Windows build command
assets/style.css            Shared styles and responsive layout
assets/script.js            Shared interactions and page transitions
vercel.json                 Clean URLs and PHP function configuration
```

Vercel serves the generated HTML routes as static pages with clean URLs, such as `/products` and `/about`. The same PHP template supplies the shared header, mobile drawer, footer, product data, modal, and controls on each page.

## Page-to-section map

| Route | Content |
|---|---|
| `/` | Hero, animated trust counters, category preview, Eatco Difference, about teaser, partner marquee, newsletter |
| `/categories` | All eight category tiles |
| `/products` | Full 16-product catalog and filters |
| `/deals` | Featured offer and bulk savings grid |
| `/how-it-works` | Benefit cards and four-step timeline |
| `/partners` | Partner marquee and testimonials |
| `/about` | Story, mission, vision, counters, community, certifications |
| `/contact` | Contact details, map placeholder, quote form, FAQ |
| `/faq`, `/shipping`, `/careers`, `/help`, `/privacy`, `/terms`, `/accessibility` | Support and legal pages |

## Build and preview

From the project folder, regenerate the pages after content or template edits:

```powershell
pwsh -File scripts/build-static.ps1
```

To preview static files locally, start PHP’s built-in server:

```powershell
php -S localhost:8000
```

Open `http://localhost:8000/` for Home. For inner pages locally, use their `.html` filenames, such as `http://localhost:8000/products.html`; Vercel removes those extensions in production.

## Add a page

1. Add the page title, description, route, and label to `$pages` in `scripts/build-pages.php`.
2. Add its section selection to `$routes`, or add copy to `$supportContent` for a simple informational page.
3. Add it to the main or mobile navigation if it belongs there.
4. Run `pwsh -File scripts/build-static.ps1`; the builder also refreshes `sitemap.xml` and `robots.txt`.

## Edit content

Edit `includes/data.php` for brand text, contact details, categories, products, pricing tiers, stock, suppliers, FAQs, partners, testimonials, certifications, and images. Then rebuild the pages. Edit shared layout and page sections in `api/index.php`, shared behavior in `assets/script.js`, and design in `assets/style.css`.

The theme, currency choice, saved products, and quote cart persist in `localStorage` across routes. Product searches use `/products?q=...&category=...`.

## Deploy to Vercel

After rebuilding, deploy the current folder from the Vercel CLI:

```powershell
vercel deploy --prod
```

Quote-form requests use the PHP function. Vercel’s function filesystem is not durable, so the form does not claim submissions were received until a persistent storage service is connected. Local PHP runs can save requests under `storage/`.
