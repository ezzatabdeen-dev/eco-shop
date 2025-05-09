document.addEventListener("DOMContentLoaded", function () {
  const quantityInput = document.getElementById("inputNumber");
  const plusBtn = document.querySelector(".quantity-up");
  const minusBtn = document.querySelector(".quantity-down");
  const addToCartBtn = document.querySelector(".addproductCar");

  plusBtn.addEventListener("click", function () {
    let currentValue = parseInt(quantityInput.value);
    quantityInput.value = currentValue + 1;
  });

  minusBtn.addEventListener("click", function () {
    let currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
      quantityInput.value = currentValue - 1;
    }
  });

  addToCartBtn.addEventListener("click", function (e) {
    e.preventDefault();

    const productId = this.getAttribute("data-product-id");
    const quantity = parseInt(quantityInput.value);

    addToCart(productId, quantity);
  });

  function addToCart(productId, quantity) {
    fetch("./api/add_to_cart.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        product_id: productId,
        quantity: --quantity,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        updateCartCount(data.cart_count);

        if (data.status === "exists") {
          showAlert(data.message, "info");
        } else if (data.status === "success") {
          showAlert(data.message, "success");
        } else if (data.status === "error") {
          showAlert(data.message, "error");
        }

        setTimeout(() => location.reload(), 3000);
      })
      .catch((error) => {
        console.error("Network Error:", error);
        showAlert("Network error. Please try again.", "error");
      });
  }

  function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll(".cart-count");
    cartCountElements.forEach((el) => {
      el.textContent = count;
    });
  }
});
