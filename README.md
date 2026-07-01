<div align="center">
Выберите язык / Choose prefered language
    
[Русский](README.ru.md) | <b>English</b>

</div>
<hr>

# Stevens Shop - Online Store with Telegram Bot

Educational project of an online store built with pure PHP, MySQL, and Telegram Bot integration.

## Technologies

- Backend: PHP 7.4+
- Database: MySQL
- Extension: MySQLi
- Bot: Python 3.14, pyTelegramBotAPI
- Frontend: HTML5, CSS3, JavaScript
- Security: Prepared Statements, sessions, XSS protection

## Architectural Solutions
- Modular structure - separation into reusable components (header, footer)
- MySQLi with Prepared Statements - protection against SQL injections
- PHP Sessions - user state and cart management
- Database normalization - separate tables for products, categories, brands, cart
- htmlspecialchars() - XSS protection for user input
- Telegram Bot API - integration with external service for product queries

<hr>

## Implemented Features
### Authentication System
- User registration with validation
- Login/Logout functionality
- Session-based authentication
- Protected cart functionality for authenticated users only

### Product Catalog
- Dynamic catalog generation from database
- Full-text search by product name
- Filter by categories
- Filter by brands
- Product availability indicator (in stock / on order)
- Product images display

### Product Page
- Detailed product information (name, description, price, image)
- Product specifications table
- Brand and category information
- Stock quantity display
- Add to cart functionality

### Shopping Cart
- Add items to cart
- View cart with all items
- Remove items from cart
- Cart persistence in database (per user)
- Real-time total calculation
- Order placement with success modal

### Telegram Bot Integration
- View all products (/products)
- Search products by name (/search)
- Get random product (/random)
- Check product availability (/check)
- Interactive keyboard navigation
- Direct MySQL database connection from bot
- Real-time product data retrieval

### Database Structure
- Users table with authentication
- Products table with pricing and stock
- Categories for product organization
- Brands for product classification
- Cart table linking users to products with quantities
