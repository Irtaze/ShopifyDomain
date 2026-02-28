<?php
/**
 * Storefront Template
 * This is what visitors see when they visit a custom domain.
 * The $store variable is passed from index.php.
 */
$storeName   = htmlspecialchars($store['store_name']);
$description = htmlspecialchars($store['description'] ?? 'Welcome to our store!');
$themeColor  = htmlspecialchars($store['theme_color'] ?? '#4F46E5');
$domain      = htmlspecialchars($store['custom_domain']);
$email       = htmlspecialchars($store['owner_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $storeName ?></title>
    <style>
        :root {
            --theme: <?= $themeColor ?>;
            --theme-light: <?= $themeColor ?>15;
            --theme-dark: <?= $themeColor ?>;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #FAFAFA;
            color: #1a1a1a;
            min-height: 100vh;
        }

        /* ─── Navbar ─── */
        .nav {
            background: white;
            border-bottom: 2px solid var(--theme);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        }

        .nav-brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--theme);
        }

        .nav-links a {
            text-decoration: none;
            color: #555;
            margin-left: 1.5rem;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--theme); }

        /* ─── Hero ─── */
        .hero {
            background: linear-gradient(135deg, var(--theme) 0%, #1a1a2e 100%);
            color: white;
            padding: 5rem 2rem;
            text-align: center;
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 0.8rem;
            font-weight: 800;
        }

        .hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto 2rem;
            line-height: 1.7;
        }

        .hero-btn {
            display: inline-block;
            background: white;
            color: var(--theme);
            padding: 0.8rem 2.2rem;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            font-size: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .hero-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }

        /* ─── Products Grid ─── */
        .section {
            max-width: 1100px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        .section-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--theme);
            border-radius: 2px;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .product-img {
            width: 100%;
            height: 200px;
            background: var(--theme-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
        }

        .product-info {
            padding: 1.2rem;
        }

        .product-info h3 {
            font-size: 1.05rem;
            margin-bottom: 0.3rem;
        }

        .product-info .price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--theme);
        }

        .product-info .desc {
            font-size: 0.85rem;
            color: #888;
            margin-top: 0.3rem;
        }

        .add-to-cart {
            display: block;
            width: 100%;
            padding: 0.7rem;
            background: var(--theme);
            color: white;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .add-to-cart:hover { opacity: 0.9; }

        /* ─── Features ─── */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .feature-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        }

        .feature-icon { font-size: 2.5rem; margin-bottom: 0.8rem; }
        .feature-card h3 { margin-bottom: 0.4rem; font-size: 1.05rem; }
        .feature-card p { font-size: 0.88rem; color: #666; line-height: 1.5; }

        /* ─── Footer ─── */
        .footer {
            background: #1a1a2e;
            color: #aaa;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
            font-size: 0.85rem;
        }

        .footer a { color: var(--theme); text-decoration: none; }

        @media (max-width: 768px) {
            .hero h1 { font-size: 2rem; }
            .hero p { font-size: 1rem; }
            .nav { padding: 0.8rem 1rem; }
            .nav-brand { font-size: 1.1rem; }
        }
    </style>
</head>
<body>

<!-- ─── Navigation ─── -->
<nav class="nav">
    <div class="nav-brand"><?= $storeName ?></div>
    <div class="nav-links">
        <a href="#">Home</a>
        <a href="#">Products</a>
        <a href="#">About</a>
        <a href="#">Contact</a>
    </div>
</nav>

<!-- ─── Hero Section ─── -->
<section class="hero">
    <h1>Welcome to <?= $storeName ?></h1>
    <p><?= $description ?></p>
    <a href="#products" class="hero-btn">Browse Products</a>
</section>

<!-- ─── Products Section ─── -->
<section class="section" id="products">
    <h2 class="section-title">Featured Products</h2>
    <div class="products-grid">
        <?php
        // Demo products — in a real app these would come from the database
        $demoProducts = [
            ['name' => 'Premium Widget',    'price' => '$49.99',  'emoji' => '⚡', 'desc' => 'High-quality premium widget'],
            ['name' => 'Smart Gadget',      'price' => '$79.99',  'emoji' => '🔧', 'desc' => 'Next-gen smart gadget'],
            ['name' => 'Pro Toolkit',       'price' => '$129.99', 'emoji' => '🧰', 'desc' => 'Complete professional toolkit'],
            ['name' => 'Essential Package',  'price' => '$29.99',  'emoji' => '📦', 'desc' => 'Everything you need to start'],
            ['name' => 'Deluxe Bundle',     'price' => '$199.99', 'emoji' => '🎁', 'desc' => 'Our best value bundle'],
            ['name' => 'Starter Kit',       'price' => '$19.99',  'emoji' => '🚀', 'desc' => 'Perfect for beginners'],
        ];
        foreach ($demoProducts as $product):
        ?>
            <div class="product-card">
                <div class="product-img"><?= $product['emoji'] ?></div>
                <div class="product-info">
                    <h3><?= $product['name'] ?></h3>
                    <div class="price"><?= $product['price'] ?></div>
                    <div class="desc"><?= $product['desc'] ?></div>
                </div>
                <button class="add-to-cart" onclick="alert('Demo: Added to cart!')">Add to Cart</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ─── Features ─── -->
<section class="section">
    <h2 class="section-title">Why Choose Us</h2>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">🚚</div>
            <h3>Free Shipping</h3>
            <p>Free delivery on all orders over $50. Fast and reliable shipping worldwide.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔒</div>
            <h3>Secure Payments</h3>
            <p>Your payment info is safe with our encrypted checkout process.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">↩️</div>
            <h3>Easy Returns</h3>
            <p>30-day hassle-free return policy. No questions asked.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💬</div>
            <h3>24/7 Support</h3>
            <p>Our customer support team is always here to help you.</p>
        </div>
    </div>
</section>

<!-- ─── Footer ─── -->
<footer class="footer">
    <p>&copy; <?= date('Y') ?> <?= $storeName ?>. All rights reserved.</p>
    <p style="margin-top: 0.3rem;">
        Powered by <a href="#">StoreHub</a> &bull; Domain: <?= $domain ?>
    </p>
</footer>

</body>
</html>
