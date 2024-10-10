$(document).ready(function() {
    const productAPI = "https://3sb655pz3a.execute-api.ap-southeast-2.amazonaws.com/live/product";  // Product API endpoint
    let cart = [];

    // Fetch product data from the API using jQuery's $.ajax method
    $.ajax({
        url: productAPI,
        method: 'GET',
        dataType: 'json',
        success: function(product) {
            // Populate product details dynamically in the HTML
            $('#title').text(product.title);  // Set the product title
            $('#description').text(product.description);  // Set the product description
            $('#price').text(`$${product.price.toFixed(2)}`);  // Set the product price
            $('#image').attr('src', product.imageURL);  // Set the product image URL

            // Populate size options in the dropdown
            const sizeSelect = $('#size');
            product.sizeOptions.forEach(size => {
                sizeSelect.append(`<option value="${size.label}">${size.label}</option>`);  // Add size options dynamically
            });
        },
        error: function(xhr, status, error) {
            console.error('Error fetching product data:', error);
        }
    });

    // Event listener for "Add to Cart" button click
    $('#add-to-cart').on('click', function() {
        const size = $('#size').val();  // Get selected size
        const title = $('#title').text();  // Get product title
        const price = parseFloat($('#price').text().replace('$', ''));  // Get product price
        const imageUrl = $('#image').attr('src');  // Get product image
        const quantity = 1;

        if (!size) {
            alert("Please select a size.");
            return;
        }

        // Send cart data to the backend using AJAX
        $.ajax({
            url: 'http://localhost:8080/server/cart.php',
            method: 'POST',
            data: {
                title: title,
                size: size,
                price: price,
                image: imageUrl,
                quantity: quantity
            },
            success: function(response) {
                console.log(response.message);
                updateCart();  // After adding to cart, reload cart items
            },
            error: function(xhr, status, error) {
                console.error('Error adding item to cart:', error);
            }
        });
    });

    // Toggle mini cart display
    $('#cart-toggle').on('click', function() {
        $('#mini-cart').toggle();
    });

    // Update cart display and item count
    function updateCart() {
        $.ajax({
            url: 'http://localhost:8080/server/cart.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                const cartItemsList = $('#cart-items');
                cartItemsList.empty();  // Clear the current cart items
                let totalQuantity = 0;
                let final_data = response.data;
                // Loop through the cart and display items
                final_data.forEach(item => {
                    const price = parseFloat(item.price);
                    const displayPrice = isNaN(price) ? 'N/A' : price.toFixed(2);
                    cartItemsList.append(`
                        <li>
                            <img src="${item.image}" alt="${item.title}">
                            <div>
                                <p>${item.title}</p>
                                <p>${item.quantity} x $${displayPrice}</p>
                                <p>Size: ${item.size}</p>
                            </div>
                        </li>
                    `);
                    totalQuantity += item.quantity;
                });

                // Update cart count in the header
                $('#cart-count').text(totalQuantity);
            },
            error: function(xhr, status, error) {
                console.error('Error loading cart items:', error);
            }
        });
    }

    // Load cart items when the page loads
    updateCart();
});
