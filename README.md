## Ecommerce Cart App

This is a simple Laravel API e-commerce project to manage the cycle of merchants and guests

First For Merchant , 
-Merchant can register and login using APIs (api/register,api/login) by email and password and during register there is a flag called is_merchant is equal 1 in this case.
-Merchant can logout using logout API (api/logout)
-After Login merchant can create a store using API (api/stores) by adding params (name-is_vat_included-shipping_cost-vat_price-vat_percentage) to the apis 
assumed that when is_vat_included equal 0 (not included) one of (vat_price ,vat_percentage) will be equal value and the other will be 0.
-Merchant can create product to specific store using API (api/products) and send param store_id with the value of the store id the product will be added to it

Second for (Guests/Users)
-User can register and login using APIs (api/register,api/login) by email and password and during register there is a flag called is_merchant is equal 0 in this case.
-User can logout using logout API (api/logout).
-User/Guest can view all products list (api/products)
-User/Guest can view single product details (api/products/{1})
-User can add products to cart and this action will be implemented into 2 steps
1- create a cart for user by API(api/carts) and it will return cart key to this user as indentifer for his cart.
2-add product to cart by using API(api/carts/cart_id) and this will check if this product exist in the cart it will take the new quantity if not it will create new cart item for this product.

-User can show cart data including cart items (products) and the calculation of cart prices using API(api)
-User can destroy the cart by using Api(api/carts/cart_id)

## Setup steps
Clone the repository

    git clone git@github.com:MohamedElMansy/ecommerce-app.git

Switch to the repo folder

    cd ecommerce-app

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

Generate a new application key

    php artisan key:generate

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate

Start the local development server

    php artisan serve

