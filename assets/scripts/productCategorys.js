document.addEventListener("DOMContentLoaded", () => {
  fetchProducts().then(() => {
    // Render All Products
    renderProductCategorys("all", "allProductItems", "allProductPagination");

    // Render Electronics Products
    renderProductCategorys("Electronics", "electronicProductItems", "electronicProductPagination");

    // Render Ladies Wears Products
    renderProductCategorys("Ladies Wears", "LadiesWearsProductItems", "LadiesWearsProductPagination");

    // Render Mens Wears Products
    renderProductCategorys("Mens Wear", "mensWearProductItems", "mensWearProductPagination");

    // Render Furnitures Products
    renderProductCategorys("Furnitures", "furnituresProductItems", "furnituresProductPagination");

    // Render Home Appliances Products
    renderProductCategorys("Home Appliances", "homeAppliancesProductItems", "homeAppliancesProductPagination");

    // Render Stationery Products
    renderProductCategorys("Stationery", "stationeryProductItems", "stationeryProductPagination");

    // Render Food Stuff Products
    renderProductCategorys("Food Stuff", "foodStuffProductItems", "foodStuffProductPagination");
  });
});

let allProducts = [];
const productsPerPage = 16;

async function fetchProducts() {
  try {
    const response = await fetch("./api/products_api.php");
    allProducts = await response.json();
  } catch (error) {
    console.error("Error fetching products:", error);
  }
}

async function renderProductCategorys(category, containerProducts, pagination) {
  // Filter products by category
  const filteredProducts =
    category === "all"
      ? [...allProducts].reverse()
      : allProducts.filter((product) => product.cat_title === category);

  // Store filtered products and current page in the container's dataset
  const container = document.getElementById(containerProducts);
  if (container) {
    container.dataset.filteredProducts = JSON.stringify(filteredProducts);
    container.dataset.currentPage = "1";
  }

  renderProducts(1, containerProducts);

  if (pagination) {
    setupPagination(pagination, containerProducts);
  }
}

function renderProducts(page, containerProducts) {
  const productItems = document.getElementById(containerProducts);
  if (!productItems) return;

  // Get filtered products and current page from the container's dataset
  const filteredProducts = JSON.parse(productItems.dataset.filteredProducts || "[]");
  productItems.dataset.currentPage = page.toString();

  productItems.innerHTML = "";

  const startIndex = (page - 1) * productsPerPage;
  const endIndex = Math.min(startIndex + productsPerPage, filteredProducts.length);
  const productsToShow = filteredProducts.slice(startIndex, endIndex);

  productsToShow.forEach((product) => {
    const productElement = document.createElement("div");
    productElement.className = "product-slide";

    const productLink = document.createElement("a");
    productLink.setAttribute("href", `product.php?p=${product.product_id}`);

    const wraperImg = document.createElement("div");
    wraperImg.setAttribute("class", "product-img");

    const productImg = document.createElement("img");
    productImg.setAttribute("src", `./assets/products_images/${product.product_image}`);
    productImg.setAttribute("alt", `${product.product_title}`);

    wraperImg.appendChild(productImg);
    productLink.appendChild(wraperImg);

    const productLable = document.createElement("div");
    productLable.setAttribute("class", "product-label");

    const addToCartButton = document.createElement("button");
    addToCartButton.setAttribute("class", "addproductCar");
    addToCartButton.setAttribute("data-product-id", `${product.product_id}`);

    const buttonSpan = document.createElement("span");
    buttonSpan.innerHTML = `
      <svg id='Layer_1' class='fa-shopping-cart' data-name='Layer 1' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 762.47 673.5'>
      <path d='M600.86,489.86a91.82,91.82,0,1,0,91.82,91.82A91.81,91.81,0,0,0,600.86,489.86Zm0,142.93a51.12,51.12,0,1,1,51.11-51.11A51.12,51.12,0,0,1,600.82,632.79Z'></path><path d='M303.75,489.86a91.82,91.82,0,1,0,91.82,91.82A91.82,91.82,0,0,0,303.75,489.86Zm-.05,142.93a51.12,51.12,0,1,1,51.12-51.11A51.11,51.11,0,0,1,303.7,632.79Z'></path><path d='M392.07,561.33h66.55a20.52,20.52,0,0,1,20.46,20.46h0a20.52,20.52,0,0,1-20.46,20.46H392.07'></path><path d='M698.19,451.14H205.93a23.11,23.11,0,0,1-23.09-22c0-.86-.09-1.72-.19-2.57l-1.82-16.36H723.51L721.3,428A23.11,23.11,0,0,1,698.19,451.14Z'></path><path d='M759.15,153.79H246.94l-3.32-24.38a17.25,17.25,0,0,1,17.25-17.26H745.21a17.26,17.26,0,0,1,17.26,17.26Z'></path><path d='M271.55,345.56l-31.16-208A20.53,20.53,0,0,1,257.13,114h0a20.53,20.53,0,0,1,23.6,16.74l31.16,208a20.52,20.52,0,0,1-16.74,23.59h0A20.52,20.52,0,0,1,271.55,345.56Z'></path><path d='M676,451.15l48.69-337.74,22.9.07a17.25,17.25,0,0,1,14.55,19.59l-42.1,303.16a17.24,17.24,0,0,1-19.59,14.54Z'></path><path d='M184.24,436.27,123.7.12l23.72,0a17.26,17.26,0,0,1,19.33,14.92l60.56,436.35-23.74-.25A17.25,17.25,0,0,1,184.24,436.27Z'></path><path d='M148.38,40.77H20.26A20.32,20.32,0,0,1,0,20.51H0A20.32,20.32,0,0,1,20.26.25H148.38'></path>
      </svg>
    `;

    addToCartButton.appendChild(buttonSpan);
    productLable.appendChild(addToCartButton);

    // Runn Add to cart script
    handleCart(addToCartButton);

    const productBody = document.createElement("div");
    productBody.setAttribute("class", "product-body");

    const productCategory = document.createElement("p");
    productCategory.setAttribute("class", "product-category text-2");
    productCategory.textContent = product.cat_title;

    const productName = document.createElement("h3");
    productName.setAttribute("class", "product-name text-3 text-ellipsis-2");
    productName.textContent = product.product_title;

    const productPrice = document.createElement("h4");
    productPrice.setAttribute("class", "product-price text-2");
    productPrice.textContent = `EG ${product.product_price}`;

    const del = document.createElement("del");
    del.setAttribute("class", "product-old-price");
    del.style.color = "red";
    del.style.marginLeft = ".5rem";
    del.style.fontSize = "12px";
    del.textContent = `EG ${Math.ceil(product.product_price * 1.2)}`;

    productPrice.appendChild(del);

    const productRating = document.createElement("div");
    productRating.setAttribute("class", "product-rating");
    productRating.innerHTML = `${renderRating(product.avg_rating)}`;

    productBody.append(
      productCategory,
      productName,
      productPrice,
      productRating
    );
    productElement.append(productLink, productLable, productBody);
    productItems.appendChild(productElement);
  });
}

function renderRating(avgRating) {
  if (!avgRating || avgRating <= 0) {
    return '<span class="no-rating text-1">No ratings yet</span>';
  }

  let stars = "";
  const roundedRating = Math.round(avgRating);

  for (let i = 1; i <= 5; i++) {
    stars += i <= roundedRating ? '<i class="fa fa-star starColor"></i>' : '<i class="fa fa-star-o starColor"></i>';
  }

  return stars;
}

function setupPagination(paginationId, containerId) {
  const pagination = document.getElementById(paginationId);
  if (!pagination) return;

  const container = document.getElementById(containerId);
  if (!container) return;

  const filteredProducts = JSON.parse(
    container.dataset.filteredProducts || "[]"
  );
  const totalPages = Math.ceil(filteredProducts.length / productsPerPage);

  pagination.innerHTML = "";

  // Previous Button
  const prevLink = document.createElement("a");
  prevLink.href = "#";
  prevLink.className = "prevLinkBtn";

  const prevSvg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
  prevSvg.setAttribute("viewBox", "0 0 320 512");
  prevSvg.setAttribute("aria-hidden", "true");
  prevSvg.classList.add("paginationPrevIcon");
  prevSvg.innerHTML = '<path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/>';

  const prevText = document.createElement("span");
  prevText.className = "pagination-text";
  prevText.textContent = "Previous";

  prevLink.appendChild(prevSvg);
  prevLink.appendChild(prevText);

  prevLink.addEventListener("click", (e) => {
    e.preventDefault();
    const currentPage = parseInt(container.dataset.currentPage || "1");
    if (currentPage > 1) {
      container.dataset.currentPage = (currentPage - 1).toString();
      renderProducts(currentPage - 1, containerId);
      updatePaginationUI(paginationId, containerId);
      scrollToSection();
    }
  });

  function updatePrevButtonLayout() {
    if (window.innerWidth <= 768) {
      prevText.style.display = "none";
      prevSvg.style.display = "block";
      prevLink.style.minWidth = "40px";
      prevLink.style.justifyContent = "center";
    } else {
      prevText.style.display = "inline-block";
      prevSvg.style.display = "none";
      prevLink.style.minWidth = "";
      prevLink.style.justifyContent = "";
    }
  }

  updatePrevButtonLayout();
  window.addEventListener("resize", updatePrevButtonLayout);

  pagination.appendChild(prevLink);

  const pageNumbersContainer = document.createElement("div");
  pageNumbersContainer.className = "page-numbers-container";
  pagination.appendChild(pageNumbersContainer);

  // Page Numbers
  function updateMorOptions() {
    pageNumbersContainer.innerHTML = "";

    const currentPage = parseInt(container.dataset.currentPage || "1");

    if (window.innerWidth <= 420) {
      // For mobile - show limited pages
      const startPage = Math.max(1, currentPage - 1);
      const endPage = Math.min(totalPages, currentPage + 1);

      for (let i = startPage; i <= endPage; i++) {
        const pageLink = document.createElement("a");
        pageLink.href = "#";
        pageLink.className = "pageLinkBtn";
        pageLink.textContent = i;
        if (i === currentPage) {
          pageLink.classList.add("active");
        }
        pageLink.addEventListener("click", (e) => {
          e.preventDefault();
          container.dataset.currentPage = i.toString();
          renderProducts(i, containerId);
          updatePaginationUI(paginationId, containerId);
          scrollToSection();
        });
        pageNumbersContainer.appendChild(pageLink);
      }

      if (endPage < totalPages) {
        let morOptions = document.createElement("span");
        morOptions.className = "morOptions";
        morOptions.textContent = "...";
        pageNumbersContainer.appendChild(morOptions);
      }
    } else {
      // For desktop - show all pages
      for (let i = 1; i <= totalPages; i++) {
        const pageLink = document.createElement("a");
        pageLink.href = "#";
        pageLink.className = "pageLinkBtn";
        pageLink.textContent = i;
        if (i === currentPage) {
          pageLink.classList.add("active");
        }
        pageLink.addEventListener("click", (e) => {
          e.preventDefault();
          container.dataset.currentPage = i.toString();
          renderProducts(i, containerId);
          updatePaginationUI(paginationId, containerId);
          scrollToSection();
        });
        pageNumbersContainer.appendChild(pageLink);
      }
    }
  }

  updateMorOptions();
  window.addEventListener("resize", updateMorOptions);

  // Next Button
  const nextLink = document.createElement("a");
  nextLink.href = "#";
  nextLink.className = "nextLinkBtn";

  const nextSvg = document.createElementNS("http://www.w3.org/2000/svg", "svg");
  nextSvg.setAttribute("viewBox", "0 0 320 512");
  nextSvg.setAttribute("aria-hidden", "true");
  nextSvg.classList.add("pagination-icon");
  nextSvg.innerHTML = '<path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/>';

  const nextText = document.createElement("span");
  nextText.className = "pagination-text";
  nextText.textContent = "Next";

  nextLink.appendChild(nextSvg);
  nextLink.appendChild(nextText);

  nextLink.addEventListener("click", (e) => {
    e.preventDefault();
    const currentPage = parseInt(container.dataset.currentPage || "1");
    if (currentPage < totalPages) {
      container.dataset.currentPage = (currentPage + 1).toString();
      renderProducts(currentPage + 1, containerId);
      updatePaginationUI(paginationId, containerId);
      scrollToSection();
    }
  });

  function updateNextButtonLayout() {
    if (window.innerWidth <= 768) {
      nextText.style.display = "none";
      nextSvg.style.display = "block";
      nextLink.style.minWidth = "40px";
      nextLink.style.justifyContent = "center";
    } else {
      nextText.style.display = "inline-block";
      nextSvg.style.display = "none";
      nextLink.style.minWidth = "";
      nextLink.style.justifyContent = "";
    }
  }

  updateNextButtonLayout();
  window.addEventListener("resize", updateNextButtonLayout);
  pagination.appendChild(nextLink);

  updatePaginationUI(paginationId, containerId);

  const scrollToSection = () => {
    setTimeout(() => {
      const productCategorys = document.getElementById("productCategorys");
      if (productCategorys) {
        const elementPosition = productCategorys.getBoundingClientRect().top;
        const offsetPosition = elementPosition + window.pageYOffset - 100;

        window.scrollTo({
          top: offsetPosition,
          behavior: "smooth",
        });
      }
    }, 50);
  };
}

function updatePaginationUI(paginationId, containerId) {
  const pagination = document.getElementById(paginationId);
  if (!pagination) return;

  const container = document.getElementById(containerId);
  if (!container) return;

  const currentPage = parseInt(container.dataset.currentPage || "1");
  const links = pagination.querySelectorAll("a.pageLinkBtn");

  links.forEach((link) => {
    link.classList.remove("active");
    if (parseInt(link.textContent) === currentPage) {
      link.classList.add("active");
    }
  });

  // Update prev/next buttons state
  const filteredProducts = JSON.parse(
    container.dataset.filteredProducts || "[]"
  );
  const totalPages = Math.ceil(filteredProducts.length / productsPerPage);

  const prevLink = pagination.querySelector(".prevLinkBtn");
  const nextLink = pagination.querySelector(".nextLinkBtn");

  if (prevLink) {
    prevLink.classList.toggle("disabled", currentPage === 1);
  }

  if (nextLink) {
    nextLink.classList.toggle(
      "disabled",
      currentPage === totalPages || totalPages === 0
    );
  }
}

function handleCart(btn) {
  btn.addEventListener("click", async function () {
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
        showTempAlert("Product added to cart!", "success");

        // If the basket is open, update the contents.
        if (
          document.querySelector(".cart-dropdown").classList.contains("active")
        ) {
          loadCartItems();
        }
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
        renderCartItems(data.items, data.total);
      }
    } catch (error) {
      console.error("Error loading cart items:", error);
    }
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

    const itemImage = document.createElement("div");
    itemImage.className = "cart-item-image";
    const img = document.createElement("img");
    img.src = `./assets/products_images/${item.image}`;
    img.alt = item.name;
    itemImage.appendChild(img);

    const itemDetails = document.createElement("div");
    itemDetails.className = "cart-item-details";

    const itemName = document.createElement("h4");
    itemName.textContent = item.name;

    const itemPrice = document.createElement("div");
    itemPrice.className = "cart-item-price";
    itemPrice.textContent = `$${item.price} x ${item.quantity}`;

    const itemSubtotal = document.createElement("div");
    itemSubtotal.className = "cart-item-subtotal";
    itemSubtotal.textContent = `EG ${item.subtotal.toFixed(0)}`;

    itemDetails.append(itemName, itemPrice, itemSubtotal);

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

  function addRemoveEventListeners() {
    document.querySelectorAll(".remove-item").forEach((button) => {
      button.addEventListener("click", removeFromCart);
    });
  }

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
        loadCartItems();
        updateCartCount(data.count);
      }
    } catch (error) {
      console.error("Error removing item:", error);
    }
  }

  // Load basket number at page start
  loadCartCount();
}