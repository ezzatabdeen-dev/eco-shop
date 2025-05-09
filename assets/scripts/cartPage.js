// Update quantity
document.querySelectorAll(".quantity-input").forEach((input) => {
  input.addEventListener("change", function () {
    const cartItem = this.closest(".cart-items-section");
    const cartId = this.dataset.cartId;
    const quantity = this.value;

    this.disabled = true;
    const originalValue = this.value;

    fetch("./api/update_cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        cart_id: cartId,
        quantity: quantity,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.status === "success") {
          // Update subtotal
          const priceText = cartItem.querySelector(
            ".product-price .price.text-1"
          )?.textContent;
          const price = parseFloat(priceText.replace("EG", "").trim());
      
          const newSubtotal = price * quantity;
          const subtotalEl = cartItem.querySelector(".product-subtotal");
          if (subtotalEl) {
            subtotalEl.textContent = "EG " + newSubtotal.toFixed(2);
          }
      
          // Update totals and counters
          updateGrandTotal();
          updateCartCounters();
          showTempAlert("Quantity updated successfully", "success");
        } else {
          throw new Error(data.message || "Failed to update quantity");
        }
      })
      .catch((error) => {
        showTempAlert(error.message, "error");
        this.value = originalValue;
      })
      .finally(() => {
        this.disabled = false;
      });
  });
});

// Remove product from cart
document.querySelectorAll(".remove-btn").forEach((button) => {
  button.addEventListener("click", function () {
    const cartItem = this.closest(".cart-items-section");
    const cartId = this.dataset.cartId;

    // Create HTML confirmation dialog instead of browser alert
    const confirmDialog = document.createElement("div");
    confirmDialog.className = "custom-confirm-dialog";
    confirmDialog.innerHTML = `
      <div class="confirm-content">
        <p>Are you sure you want to remove this item from your cart?</p>
        <div class="confirm-buttons">
          <button class="confirm-yes">Yes</button>
          <button class="confirm-no">No</button>
        </div>
      </div>
    `;
    
    document.body.appendChild(confirmDialog);
    
    // Handle confirmation
    confirmDialog.querySelector(".confirm-yes").addEventListener("click", () => {
      confirmDialog.remove();
      proceedWithRemoval();
    });
    
    confirmDialog.querySelector(".confirm-no").addEventListener("click", () => {
      confirmDialog.remove();
    });

    function proceedWithRemoval() {
      this.disabled = true;

      fetch("api/remove_from_cart.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          id: cartId,
        }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            cartItem.remove();
            updateGrandTotal();
            updateCartCounters();
            showTempAlert("Product removed successfully", "success");
            
            if (document.querySelectorAll(".cart-items-section").length === 0) {
              document.querySelector(".cart-wraper").innerHTML = `
              <div class="empty-cart">
                  <p>Your cart is empty</p>
                  <a href="index.php" class="continue-shopping">Continue Shopping</a>
              </div>
              `;
            }
          } else {
            throw new Error(data.message || "Failed to remove item");
          }
        })
        .catch((error) => {
          showTempAlert(error.message, "error");
        })
        .finally(() => {
          this.disabled = false;
        });
    }
  });
});

// Update grand total
function updateGrandTotal() {
  let total = 0;
  document.querySelectorAll(".product-subtotal").forEach((el) => {
    total += parseFloat(el.textContent.replace("EG", "").trim());
  });

  const totalEl = document.querySelector(".total-price");
  if (totalEl) {
    totalEl.innerHTML = `<strong class="text-3">Total:</strong> EG ${total.toFixed(2)}`;
  }
}

// Update cart counters
function updateCartCounters() {
  fetch("api/get_cart_count.php")
    .then(response => response.json())
    .then(data => {
      if (data.status === "success") {
        // Update counters in header
        const cartCounters = document.querySelectorAll(".cart-count");
        cartCounters.forEach(counter => {
          counter.textContent = data.count;
        });

        // Update counter in cart page if present
        const cartItemCount = document.querySelector(".cart-item-count");
        if (cartItemCount) {
          cartItemCount.textContent = data.count === 0 ? "Empty cart" : `${data.count} item${data.count !== 1 ? 's' : ''}`;
        }
      }
    })
    .catch(error => console.error("Error updating cart counters:", error));
}

// Show temporary alert (HTML-based)
function showTempAlert(message, type) {
  const alert = document.createElement("div");
  alert.className = `custom-alert ${type}`;
  alert.innerHTML = `
    <div class="alert-content">
      <span class="alert-message">${message}</span>
      <span class="alert-close">&times;</span>
    </div>
  `;
  
  document.body.appendChild(alert);
  
  // Close button functionality
  alert.querySelector(".alert-close").addEventListener("click", () => {
    alert.style.animation = "fadeOut 0.3s";
    setTimeout(() => {
      alert.remove();
    }, 300);
  });

  // Auto-close after 3 seconds
  setTimeout(() => {
    alert.style.animation = "fadeOut 0.3s";
    setTimeout(() => {
      alert.remove();
    }, 300);
  }, 3000);
}

// Add styles for alerts and confirm dialog
const style = document.createElement("style");
style.textContent = `
  /* Alert styles */
  .custom-alert {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    min-width: 250px;
    border-radius: 4px;
    padding: 15px;
    color: white;
    z-index: 1000;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    animation: fadeIn 0.3s;
    overflow: hidden;
  }
  
  .custom-alert.success {
    background-color: #4CAF50;
  }
  
  .custom-alert.error {
    background-color: #f44336;
  }
  
  .alert-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  
  .alert-close {
    cursor: pointer;
    font-size: 20px;
    margin-left: 15px;
  }
  
  /* Confirm dialog styles */
  .custom-confirm-dialog {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    z-index: 1001;
    display: flex;
    justify-content: center;
    align-items: center;
  }
  
  .confirm-content {
    background-color: white;
    padding: 20px;
    border-radius: 5px;
    max-width: 400px;
    width: 90%;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
  }
  
  .confirm-content p {
    margin-bottom: 20px;
    color: #333;
  }
  
  .confirm-buttons {
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }
  
  .confirm-buttons button {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }
  
  .confirm-yes {
    background-color: #f44336;
    color: white;
  }
  
  .confirm-no {
    background-color: #e0e0e0;
    color: #333;
  }
  
  /* Animations */
  @keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
  @keyframes fadeOut {
    from { opacity: 1; }
    to { opacity: 0; }
  }
`;
document.head.appendChild(style);