document.addEventListener("DOMContentLoaded", function () {
  // Variable to track the status of the basket load
  let cartLoaded = false;
  
  // Store cart items data globally to allow for comparison
  let currentCartItems = [];
  let currentCartTotal = 0;

  // Processing "Add to Cart" button clicks
  document.querySelectorAll(".addproductCar").forEach((button) => {
    button.addEventListener("click", async function () {
      const productId = this.dataset.productId;
      const spinner = this.querySelector(".spinner") || createSpinner(this);

      try {
        spinner.style.display = "inline-block";
        this.disabled = true;

        const response = await fetch("api/add_to_cart.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            product_id: productId,
            quantity: 1,
          }),
        });

        const data = await response.json();

        if (data.status === "success") {
          updateCartCount(data.cart_count);
          // Always load cart items after adding to cart
          await loadCartItems();
          showTempAlert("Product added to cart!", "success");
        } else {
          showTempAlert("The product is already in the cart", "error");
        }
      } catch (error) {
        showTempAlert("Network error. Please try again.", "error");
      } finally {
        spinner.style.display = "none";
        this.disabled = false;
      }
    });
  });

  // Helper function to create spinner download
  function createSpinner(button) {
    const spinner = document.createElement("span");
    spinner.className = "spinner";
    spinner.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
      <path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2z"/>
      <path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466"/>
    </svg>
    `;
    spinner.style.display = "none";
    spinner.style.marginLeft = "5px";
    button.appendChild(spinner);
    return spinner;
  }

  // Function to update the basket counter
  function updateCartCount(count) {
    document.querySelectorAll(".cart-count").forEach((el) => {
      el.textContent = count;
      el.classList.add("pulse");
      setTimeout(() => el.classList.remove("pulse"), 500);
    });
  }

  // Function to display alerts
  function showTempAlert(message, type) {
    const alert = document.createElement("div");
    alert.className = `cart-alert ${type}`;
    alert.textContent = message;
    document.body.appendChild(alert);

    setTimeout(() => {
      alert.style.opacity = "0";
      setTimeout(() => alert.remove(), 500);
    }, 3000);
  }

  // Get basket count when page loads
  async function loadCartCount() {
    try {
      const response = await fetch("api/get_cart_count.php");
      const data = await response.json();

      if (data.status === "success") {
        updateCartCount(data.count);
        await loadCartItems();
      }
    } catch (error) {
      console.error("Error loading cart count:", error);
    }
  }

  // Function to retrieve and display the contents of the basket
  async function loadCartItems() {
    try {
      const response = await fetch("api/get_cart_items.php");
      const data = await response.json();

      if (data.status === "success") {
        // Check if cart items have actually changed before re-rendering
        if (hasCartChanged(data.items, data.total)) {
          currentCartItems = data.items;
          currentCartTotal = data.total;
          renderCartItems(data.items, data.total);
        }
      }
    } catch (error) {
      console.error("Error loading cart items:", error);
    }
  }

  // Helper function to check if cart has changed
  function hasCartChanged(newItems, newTotal) {
    if (newItems.length !== currentCartItems.length) return true;
    if (newTotal !== currentCartTotal) return true;
    
    for (let i = 0; i < newItems.length; i++) {
      const newItem = newItems[i];
      const oldItem = currentCartItems[i];
      
      if (!oldItem || 
          newItem.id !== oldItem.id || 
          newItem.quantity !== oldItem.quantity ||
          newItem.subtotal !== oldItem.subtotal) {
        return true;
      }
    }
    
    return false;
  }

  // Function to display products in the cart
  function renderCartItems(items, total) {
    const cartContainer = document.getElementById("cart_product");
    const cartItemsWraperSm = document.querySelector(".cartItemsWraperSm");

    // Clear current content
    cartContainer.innerHTML = "";
    cartItemsWraperSm.innerHTML = "";

    if (items.length === 0) {
      const emptyMsg = createEmptyCartMessage();
      cartContainer.appendChild(emptyMsg);
      cartItemsWraperSm.appendChild(emptyMsg.cloneNode(true));
      return;
    }

    // Create basket items
    items.forEach((item) => {
      const cartItem = createCartItemElement(item);
      cartContainer.appendChild(cartItem);
      cartItemsWraperSm.appendChild(cartItem.cloneNode(true));
    });

    // Add the grand total
    const cartTotal = createCartTotalElement(total);
    cartContainer.appendChild(cartTotal);
    cartItemsWraperSm.appendChild(cartTotal.cloneNode(true));

    // Add deletion events
    addRemoveEventListeners();
  }

  // Helper function to create empty basket message
  function createEmptyCartMessage() {
    const emptyDiv = document.createElement("div");
    emptyDiv.className = "empty-cart";
    emptyDiv.textContent = "Your cart is empty";
    return emptyDiv;
  }

  // Helper function to create a product item in the cart
  function createCartItemElement(item) {
    const cartItem = document.createElement("div");
    cartItem.className = "cart-item";
    cartItem.dataset.id = item.id;

    // Product image
    const itemImage = document.createElement("div");
    itemImage.className = "cart-item-image";
    const img = document.createElement("img");
    img.src = `./assets/products_images/${item.image}`;
    img.alt = item.name;
    itemImage.appendChild(img);

    // Product details
    const itemDetails = document.createElement("div");
    itemDetails.className = "cart-item-details";

    const itemName = document.createElement("h4");
    itemName.textContent = item.name;
    itemName.setAttribute('class', 'text-ellipsis-1');

    const itemPrice = document.createElement("div");
    itemPrice.className = "cart-item-price";
    itemPrice.textContent = `EG ${item.price} x ${item.quantity}`;

    const itemSubtotal = document.createElement("div");
    itemSubtotal.className = "cart-item-subtotal";
    itemSubtotal.textContent = `EG ${item.subtotal.toFixed(0)}`;

    itemDetails.append(itemName, itemPrice, itemSubtotal);

    // Delete button
    const removeBtn = document.createElement("button");
    removeBtn.className = "remove-item";
    removeBtn.dataset.id = item.id;
    removeBtn.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
        </svg>
    `;

    cartItem.append(itemImage, itemDetails, removeBtn);
    return cartItem;
  }

  // Helper function to create a total item
  function createCartTotalElement(total) {
    const cartTotal = document.createElement("div");
    cartTotal.className = "cart-total";

    const totalLabel = document.createElement("span");
    totalLabel.textContent = "Total:";

    const totalValue = document.createElement("span");
    totalValue.textContent = `EG ${total.toFixed(0)}`;

    cartTotal.append(totalLabel, totalValue);
    return cartTotal;
  }

  // Function to add delete events
  function addRemoveEventListeners() {
    document.querySelectorAll(".remove-item").forEach((button) => {
      button.addEventListener("click", removeFromCart);
    });
  }

  // Delete product from cart function
  async function removeFromCart(event) {
    const itemId = event.currentTarget.dataset.id;

    try {
      const response = await fetch("api/remove_from_cart.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: itemId }),
      });

      const data = await response.json();

      if (data.status === "success") {
        await loadCartItems();
        updateCartCount(data.count);
      }
    } catch (error) {
      console.error("Error removing item:", error);
    }
  }

  // Function to periodically check for cart updates
  function startCartPolling() {
    setInterval(async () => {
      await loadCartItems();
    }, 3000); // Check every 3 seconds
  }

  // Initialize
  loadCartCount();
  startCartPolling();

  // Cart icon click event
  document
    .querySelector(".dropdown-toggle")
    .addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      const dropdown = document.querySelector(".cart-dropdown");
      dropdown.classList.toggle("active");

      if (dropdown.classList.contains("active") && !cartLoaded) {
        loadCartItems();
        cartLoaded = true;
      }
    });

  // Cart Icon Click Event (Mobile Mini Version)
  carSmToggle.addEventListener("click", () => {
    popupListCartItemsSm.classList.add("active");

    if (!cartLoaded) {
      loadCartItems();
      cartLoaded = true;
    }
  });

  // Close the large and small basket when clicking outside it
  document.addEventListener("click", function (e) {
    const dropdown = document.querySelector(".cart-dropdown");
    const dropdownToggle = document.querySelector(".dropdown-toggle");

    const popupCartSm = document.querySelector(".popupListCartItemsSm");
    const cartSmToggle = document.querySelector("#carSmToggle");
    const closePopupListCartItemsSm = document.querySelector(".closePopupListCartItemsSm");

    if (
      !e.target.closest(".dropdown") &&
      !e.target.closest(".dropdown-toggle")
    ) {
      dropdown.classList.remove("active");
    }

    if (
      !e.target.closest(".closePopupListCartItemsSm") &&
      !e.target.closest("#carSmToggle")
    ) {
      popupCartSm.classList.remove("active");
    }
  });
});

// popupListCartItemsSm
const popupListCartItemsSm = document.querySelector(".popupListCartItemsSm");
const closePopupListCartItemsSm = document.querySelector(
  ".closePopupListCartItemsSm"
);
const carSmToggle = document.querySelector("#carSmToggle");

closePopupListCartItemsSm.addEventListener("click", () => {
  popupListCartItemsSm.classList.remove("active");
});