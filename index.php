<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Materialize CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Café Iyah</title>
    <style>
        :root {
            --primary-orange: #FF6B35;
            --light-orange: #FF9A3D;
            --dark-orange: #E55A2B;
            --cream: #FFF5EB;
            --light-gray: #F5F5F5;
            --dark-gray: #333333;
            --text-on-orange: #FFFFFF;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--primary-orange);
            color: var(--text-on-orange);
            line-height: 1.6;
        }
        
        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: var(--primary-orange);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--cream);
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        
        .nav {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .nav a {
            text-decoration: none;
            color: var(--cream);
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .nav a:hover {
            color: var(--light-orange);
        }
        
        .order-btn {
            background-color: var(--cream);
            color: var(--primary-orange);
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .order-btn:hover {
            background-color: var(--light-orange);
            color: white;
        }
        
        /* Hero Section */
        .hero-section {
            display: flex;
            height: 80vh;
            background-color: var(--primary-orange);
            overflow: hidden;
            position: relative;
        }
        
        .hero-left, .hero-right {
            flex: 1;
            overflow: hidden;
        }
        
        .hero-left img, .hero-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .hero-middle {
            flex: 1.5;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            text-align: center;
        }
        
        .hero-middle h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            line-height: 1.2;
            color: var(--cream);
        }
        
        .hero-middle h1 span {
            color: var(--light-orange);
        }
        
        .hero-middle p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            max-width: 500px;
            color: var(--cream);
        }
        
        .search-form {
            display: flex;
            width: 100%;
            max-width: 400px;
        }
        
        .search-form input {
            flex: 1;
            padding: 0.8rem 1rem;
            border: 1px solid var(--cream);
            border-radius: 25px 0 0 25px;
            outline: none;
            background-color: rgba(255, 255, 255, 0.9);
        }
        
        .search-form button {
            background-color: var(--cream);
            color: var(--primary-orange);
            border: none;
            padding: 0 1.5rem;
            border-radius: 0 25px 25px 0;
            cursor: pointer;
            transition: background-color 0.3s;
            font-weight: 500;
        }
        
        .search-form button:hover {
            background-color: var(--light-orange);
            color: white;
        }
        
        /* Coffee Types Bar */
        .coffee-types-bar, .rice-meals-bar {
            display: flex;
            justify-content: space-around;
            padding: 1rem 0;
            background-color: var(--light-orange);
            color: white;
            font-weight: 500;
            text-align: center;
        }
        
        .rice-meals-bar {
            background-color: var(--dark-orange);
        }
        
        /* Content Sections */
        .our-coffee, .our-rice-meals {
            padding: 4rem 2rem;
            text-align: center;
            background-color: var(--primary-orange);
            color: var(--text-on-orange);
        }
        
        .our-coffee h2, .our-rice-meals h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: var(--cream);
        }
        
        .our-coffee h2::after, .our-rice-meals h2::after {
            content: "";
            display: block;
            width: 80px;
            height: 3px;
            background-color: var(--cream);
            margin: 0.5rem auto;
        }
        
        .our-coffee p, .our-rice-meals p {
            max-width: 600px;
            margin: 0 auto 3rem;
            color: var(--cream);
        }
        
        /* Coffee Carousel */
        .coffee-items-carousel-wrapper {
            overflow: hidden;
            position: relative;
        }
        
        .coffee-items-carousel {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            padding: 1rem 0;
            scrollbar-width: none; /* Firefox */
        }
        
        .coffee-items-carousel::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Edge */
        }
        
        .coffee-card {
            flex: 0 0 280px;
            background-color: var(--cream);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .coffee-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .coffee-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }
        
        .coffee-card h3 {
            padding: 1rem 1rem 0.5rem;
            color: var(--dark-gray);
        }
        
        .rating {
            padding: 0 1rem;
            color: #FFC107;
        }
        
        .coffee-card select {
            width: 90%;
            margin: 0.5rem auto;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
        }
        
        .coffee-card .price {
            padding: 0.5rem 1rem;
            color: var(--primary-orange);
            font-weight: 500;
        }
        
        .coffee-card button {
            width: 90%;
            margin: 0.5rem auto 1rem;
            padding: 0.7rem;
            background-color: var(--primary-orange);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .coffee-card button:hover {
            background-color: var(--dark-orange);
        }
        
        /* Rice Meals Section */
        .rice-item {
            display: flex;
            align-items: center;
            max-width: 1000px;
            margin: 0 auto 3rem;
            background-color: #3a322b;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .rice-item:nth-child(even) {
            flex-direction: row-reverse;
        }
        
        .rice-item img {
            width: 40%;
            height: 250px;
            object-fit: cover;
        }
        
        .rice-desc {
            flex: 1;
            padding: 2rem;
            text-align: left;
        }
        
        .rice-desc h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #ece7e0;
        }
        
        .rice-desc p {
            margin-bottom: 1rem;
            color: #ece7e0;
        }
        
        .price-tag {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-orange);
            margin: 1rem 0;
        }
        
        .rice-desc button {
            background-color: var(--primary-orange);
            color: white;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .rice-desc button:hover {
            background-color: var(--dark-orange);
        }
        
        /* Footer */
        footer {
            background-color: var(--dark-orange);
            color: white;
            padding: 3rem 2rem 1rem;
        }
        
        .footer-info {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-info div {
            flex: 1;
            min-width: 200px;
        }
        
        .footer-info strong {
            display: block;
            margin-bottom: 1rem;
            color: var(--cream);
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--cream);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-section {
                flex-direction: column;
                height: auto;
            }
            
            .hero-left, .hero-right {
                flex: none;
                height: 300px;
            }
            
            .rice-item, .rice-item:nth-child(even) {
                flex-direction: column;
            }
            
            .rice-item img {
                width: 100%;
                height: 200px;
            }
        }
        
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .coffee-types-bar, .rice-meals-bar {
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            
            .footer-info {
                flex-direction: column;
                gap: 2rem;
            }
            
            .hero-middle h1 {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">café iyah</div>
        <nav class="nav">
            <a href="menu.php">Menu</a>
            <a href="location.php">Location</a>
            <a href="cart.php"><button class="order-btn">Order Now</button></a>
        </nav>
    </header>

    <section class="hero-section">
        <div class="hero-left">
            <img src="https://images.unsplash.com/photo-1554118811-1e0d58224f24?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Coffee Shop Interior" />
        </div>
        <div class="hero-middle">
            <h1><span>EXPRESSO</span><br />YOURSELF</h1>
            <p>Pure coffee, pure community, pure experience, because you can never have too much coffee in your life.</p>
            <form class="search-form">
                <input type="text" placeholder="Search Here" />
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="hero-right">
            <img src="https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" alt="Coffee Counter" />
        </div>
    </section>

    <div class="coffee-types-bar">
        <div>ESPRESSO</div>
        <div>SPANISH LATTE</div>
        <div>CAPPUCCINO</div>
        <div>WHITE MOCHA LATTE</div>
        <div>MOCHA JAVA CHIP</div>
    </div>

    <!-- OUR COFFEE Section with Carousel -->
    <section class="our-coffee">
        <h2>OUR COFFEE</h2>
        <p>There's always a room for coffee, it's not just coffee, it's an experience, life is better with coffee.</p>

        <div class="coffee-items-carousel-wrapper">
            <div class="coffee-items-carousel">

                <div class="coffee-card">
                    <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Mocha Java Chip" />
                    <h3>MOCHA JAVA CHIP</h3>
                    <div class="rating">⭐ 4.5</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">₱120.00</div>
                    <button>+</button>
                </div>

                <div class="coffee-card">
                    <img src="https://images.unsplash.com/photo-1561047029-3000c68339ca?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="White Mocha Latte" />
                    <h3>WHITE MOCHA LATTE</h3>
                    <div class="rating">⭐ 4.7</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">₱135.00</div>
                    <button>+</button>
                </div>

                <div class="coffee-card">
                    <img src="https://images.unsplash.com/photo-1572442388796-11668a67e53d?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Cappuccino" />
                    <h3>CAPPUCCINO</h3>
                    <div class="rating">⭐ 4.5</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">₱110.00</div>
                    <button>+</button>
                </div>

                <div class="coffee-card">
                    <img src="https://images.unsplash.com/photo-1511537190424-bbbab87ac5eb?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Espresso" />
                    <h3>ESPRESSO</h3>
                    <div class="rating">⭐ 4.6</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">₱95.00</div>
                    <button>+</button>
                </div>

                <div class="coffee-card">
                    <img src="https://images.unsplash.com/photo-1561047029-3000c68339ca?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Spanish Latte" />
                    <h3>SPANISH LATTE</h3>
                    <div class="rating">⭐ 4.8</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">₱125.00</div>
                    <button>+</button>
                </div>

            </div>
        </div>
    </section>

    <div class="rice-meals-bar">
        <div>CHICKEN KATSU</div>
        <div>SHAWARMA</div>
        <div>CREAMY CHICKEN ALA KING</div>
    </div>

    <section class="our-rice-meals">
        <h2>OUR RICE MEALS</h2>
        <p>There's always a room for coffee, it's not just coffee, it's an experience, life is better with coffee.</p>

        <div class="rice-item">
            <div class="rice-desc">
                <h3>CREAMY CHICKEN ALA KING</h3>
                <p>Crispy chicken fillet with creamy ala king sauce, rice and buttered corn.</p>
                <div class="rating">⭐ 4.5</div>
                <div class="price-tag">₱65.00</div>
                <button>+</button>
            </div>
            <img src="https://images.unsplash.com/photo-1606755962773-d324e74534a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Creamy Chicken Ala King" />
        </div>

        <div class="rice-item">
            <img src="https://images.unsplash.com/photo-1606755962773-d324e74534a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Chicken Katsu" />
            <div class="rice-desc">
                <h3>CHICKEN KATSU</h3>
                <p>Golden-fried chicken cutlet with katsu sauce, rice and shredded cabbage.</p>
                <div class="rating">⭐ 4.5</div>
                <div class="price-tag">₱65.00</div>
                <button>+</button>
            </div>
        </div>

        <div class="rice-item">
            <div class="rice-desc">
                <h3>SHAWARMA</h3>
                <p>Beef shawarma on turmeric rice with fresh veggies.</p>
                <div class="rating">⭐ 4.5</div>
                <div class="price-tag">₱65.00</div>
                <button>+</button>
            </div>
            <img src="https://images.unsplash.com/photo-1606755962773-d324e74534a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Shawarma" />
        </div>

        <div class="rice-item">
            <img src="https://images.unsplash.com/photo-1606755962773-d324e74534a2?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=80" alt="Chaofan with Siomai" />
            <div class="rice-desc">
                <h3>CHAO FAN WITH SIOMAI</h3>
                <p>ALL-NEW Meaty-Busog. Wok-Sarap fried rice with meat, egg, and kangkong leaves! Served with 4pcs of Siomai.</p>
                <div class="price-tag">₱65.00</div>
                <button>+</button>
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-info">
            <div>
                <strong>ENGINEER'S CAFE</strong><br />
                Engineer's Cafe<br />
                Marigman Rd. Brgy.<br />
                San Roque, Antipolo City, Rizal, Philippines
            </div>
            <div>
                <strong>OPENING HOURS</strong><br />
                Monday to Thursday<br />
                11:00am - 4:00pm
            </div>
            <div>
                <strong>COFFEE</strong><br />
                Home<br />
                About Us<br />
                Menu<br />
                Events<br />
                Contact Us
            </div>
            <div>
                <strong>ABOUT US</strong><br />
                Our Story<br />
                Values<br />
                Team<br />
                Suppliers<br />
                Community
            </div>
            <div>
                <strong>HOME</strong><br />
                Coffee<br />
                Rice Meal<br />
                List Menu<br />
                Order<br />
                Blogs
            </div>
        </div>
        <div class="copyright">
            2025 CPE 2 PROJECT ALL RIGHTS RESERVED
        </div>
    </footer>

    <!-- Place this script near the end of the body, after all content -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
          const carousel = document.querySelector('.coffee-items-carousel');
          const card = document.querySelector('.coffee-card');
          const gap = 24; // Match this with your CSS gap
          const scrollAmount = (card ? card.offsetWidth : 280) + gap;
          const scrollIntervalTime = 3000; // 3 seconds
          let maxScrollLeft;

          function updateMaxScroll() {
            maxScrollLeft = carousel.scrollWidth - carousel.clientWidth;
          }

          window.addEventListener('resize', updateMaxScroll);
          updateMaxScroll();

          // Auto scroll function
          function autoScroll() {
            if (carousel.scrollLeft + scrollAmount >= maxScrollLeft) {
              carousel.scrollTo({ left: 0, behavior: 'smooth' });
            } else {
              carousel.scrollBy({ left: scrollAmount, behavior: 'smooth' });
            }
          }

          let interval = setInterval(autoScroll, scrollIntervalTime);

          // Pause on hover
          carousel.addEventListener('mouseenter', () => clearInterval(interval));
          carousel.addEventListener('mouseleave', () => interval = setInterval(autoScroll, scrollIntervalTime));
        });
    </script>
    <!-- Materialize JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
</body>
</html>