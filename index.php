<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Materialize CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css" rel="stylesheet" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Café Iyah</title>
    <link rel="stylesheet" href="css/style.css" />
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
            <img src="images/coffee-shop-1.jpg" alt="Coffee Shop Interior" />
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
            <img src="images/coffee-shop-2.jpg" alt="Coffee Counter" />
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
        <p>There's always a room for coffee, it's not just coffee, it’s an experience, life is better with coffee.</p>

        <div class="coffee-items-carousel-wrapper">
            <div class="coffee-items-carousel">

                <div class="coffee-card">
                    <img src="images/mocha-java-chip.jpg" alt="Mocha Java Chip" />
                    <h3>MOCHA JAVA CHIP</h3>
                    <div class="rating">⭐ 4.5</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">(The price will appear)</div>
                    <button>+</button>
                </div>

                <div class="coffee-card">
                    <img src="images/white-mocha-latte.jpg" alt="White Mocha Latte" />
                    <h3>WHITE MOCHA LATTE</h3>
                    <div class="rating">⭐ 4.7</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">(The price will appear)</div>
                    <button>+</button>
                </div>

                <div class="coffee-card">
                    <img src="images/cappuccino.jpg" alt="Cappuccino" />
                    <h3>CAPPUCCINO</h3>
                    <div class="rating">⭐ 4.5</div>
                    <select>
                        <option value="">SIZE</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                    <div class="price">(The price will appear)</div>
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
        <p>There's always a room for coffee, it's not just coffee, it’s an experience, life is better with coffee.</p>

        <div class="rice-item">
            <div class="rice-desc">
                <h3>CREAMY CHICKEN ALA KING</h3>
                <p>Crispy chicken fillet with creamy ala king sauce, rice and buttered corn.</p>
                <div class="rating">⭐ 4.5</div>
                <div class="price-tag">₱65.00</div>
                <button>+</button>
            </div>
            <img src="images/creamy-chicken-ala-king.jpg" alt="Creamy Chicken Ala King" />
        </div>

        <div class="rice-item">
            <img src="images/chicken-katsu.jpg" alt="Chicken Katsu" />
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
            <img src="images/shawarma.jpg" alt="Shawarma" />
        </div>

        <div class="rice-item">
            <img src="images/chaofan-siomai.jpg" alt="Chaofan with Siomai" />
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
  const gap = 5; // Match this with your CSS gap
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

  // Optional: Keep your existing prev/next button event listeners below if you want manual controls
});
</script>
<!-- Materialize JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
<script src="js/app.js"></script>

</body>
</body>
</html>