<?php
/**
 * Template Name: Cabinet
 *
 * This page displays a personal cabinet or dashboard for a logged-in user.
 */

get_header();

// If the user is not logged in, redirect them to the login page.
if (!is_user_logged_in()) {
    echo '<script type="text/javascript">
           window.location = `' . site_url() . '`
      </script>';
    exit();
}

// Get current user data.
$current_user = wp_get_current_user();
?>

<div class="container">
    <h1>Welcome to Your Dashboard, <?php echo esc_html($current_user->display_name); ?>!</h1>

    <p><strong>User Login:</strong> <?php echo esc_html($current_user->user_login); ?></p>
    <p><strong>Email:</strong> <?php echo esc_html($current_user->user_email); ?></p>
    <p><strong>Role:</strong> <?php echo esc_html(implode(', ', $current_user->roles)); ?></p>

    <div class="user-actions">
        <h2>Your Actions</h2>
        <ul>
            <li><a href="<?php echo esc_url(wp_logout_url(get_permalink())); ?>">Log Out</a></li>
        </ul>
    </div>

    <!-- Make sure this div exists before JavaScript runs -->
    <div id="orders-container"><p>Loading orders...</p></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetchFromApi();
        });

        async function fetchFromApi() {
            const email = `<?php echo($current_user->user_email); ?>`;
            const apiUrl = new URL(window.location.origin + '/wordpress/wp-json/myplugin/v1/orders');
            apiUrl.searchParams.append('email', email);

            let gotten = [];

            try {
                const response = await fetch(apiUrl);
                const data = await response.json();

                console.log(data);
                data.forEach((el) => {
                    if (typeof el.cart === 'object') {
                        gotten.push(el);
                    }
                });

                console.log(gotten);
                displayData(gotten);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function displayData(items) {
            const container = document.createElement("div"); // Create a wrapper div for the table

            if (items.length === 0) {
                container.innerHTML = "<p>No orders found.</p>";
            } else {
                // Create table element
                const table = document.createElement("table");
                table.style.width = "100%";
                table.style.borderCollapse = "collapse";

                // Create table header
                const thead = document.createElement("thead");
                const headerRow = document.createElement("tr");

                ["Order ID", "Title", "Date", "Cart Items", "Status", "Description"].forEach(text => {
                    const th = document.createElement("th");
                    th.textContent = text;
                    th.style.border = "1px solid black";
                    th.style.padding = "8px";
                    th.style.backgroundColor = "#f2f2f2";
                    headerRow.appendChild(th);
                });

                thead.appendChild(headerRow);
                table.appendChild(thead);

                // Create table body
                const tbody = document.createElement("tbody");

                items.forEach(item => {
                    const row = document.createElement("tr");

                    // Format cart items
                    let cartContent = "N/A";
                    if (Array.isArray(item.cart)) {
                        cartContent = item.cart.map(cartItem => {
                            return `
                        <div style="border: 1px solid #ddd; padding: 8px; margin-bottom: 5px;">
                            <strong>${cartItem.title}</strong><br>
                            <img src="${cartItem.image}" alt="${cartItem.title}" style="width: 50px; height: 50px;"><br>
                            Quantity: ${cartItem.quantity} | Price: $${cartItem.price}
                        </div>
                    `;
                        }).join(""); // Join all items in case there are multiple products in the cart
                    }

                    // Append table cells
                    [
                        item.order_id || "N/A",
                        item.title || "N/A",
                        item.date || "N/A",
                        cartContent,
                        item.status || "N/A",
                        item.description || "N/A"
                    ].forEach((value, index) => {
                        const td = document.createElement("td");
                        td.style.border = "1px solid black";
                        td.style.padding = "8px";

                        // Render cart content as HTML (for images)
                        if (index === 3) {
                            td.innerHTML = value; // Allow HTML for cart items
                        } else {
                            td.textContent = value;
                        }

                        row.appendChild(td);
                    });

                    tbody.appendChild(row);
                });

                table.appendChild(tbody);
                container.appendChild(table);
            }

            // Find the first element with the class 'user-actions'
            const userActions = document.getElementsByClassName("user-actions")[0];

            if (userActions) {
                userActions.parentNode.insertBefore(container, userActions);
            } else {
                console.error("Error: No element with class 'user-actions' found.");
            }
        }


    </script>

</div>

<?php get_footer(); ?>
