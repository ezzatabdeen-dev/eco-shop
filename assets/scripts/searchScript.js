document.addEventListener("DOMContentLoaded", function () {
  const inputSearchs = document.querySelectorAll(".InputSearch");
  const searchButtons = document.querySelectorAll(".searchButtonAction");
  const searchResults = document.querySelectorAll(".searchResultAction");
  const closeSearchSm = document.getElementById("closeSearchSm");
  const largScreenSearchResult = document.querySelector(
    ".Larg-screen-searchResult"
  );

  let products = [];

  async function fetchProducts() {
    try {
      const response = await fetch("./api/products_api.php");
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      products = await response.json();
    } catch (error) {
      console.error("Error fetching products:", error);
      searchResults.forEach((result) => {
        result.innerHTML =
          '<code class="error">An error occurred while fetching data. Please try again later.</code>';
      });
    }
  }

  function filterProducts(searchTerm) {
    if (!searchTerm.trim()) {
      return [];
    }

    const lowerCaseSearchTerm = searchTerm.toLowerCase();

    return products.filter((product) => {
      return (
        String(product.product_id)
          .toLowerCase()
          .includes(lowerCaseSearchTerm) ||
        product.product_title.toLowerCase().includes(lowerCaseSearchTerm) ||
        product.cat_title.toLowerCase().includes(lowerCaseSearchTerm) ||
        String(product.product_price)
          .toLowerCase()
          .includes(lowerCaseSearchTerm)
      );
    });
  }

  function displayResults(filteredProducts) {
    searchResults.forEach((result) => {
      result.innerHTML = "";

      if (filteredProducts.length === 0) {
        result.innerHTML =
          '<code class="no-results text-1">There are no results matching your search.</code>';
        return;
      }

      filteredProducts.forEach((product) => {
        const productItem = document.createElement("li");
        productItem.className = "product-row";

        const productLink = document.createElement("a");
        productLink.href = `product.php?p=${product.product_id}`;
        productLink.className = "product-result";

        const wraperImg = document.createElement("div");
        wraperImg.className = "product-img";

        const productImg = document.createElement("img");
        productImg.src = `./assets/products_images/${product.product_image}`;
        productImg.alt = product.product_title;
        productImg.className = "product-image";

        wraperImg.appendChild(productImg);
        productLink.appendChild(wraperImg);

        const productBody = document.createElement("div");
        productBody.className = "product-body";

        const productCategory = document.createElement("code");
        productCategory.className = "product-category text-1";
        productCategory.textContent = product.cat_title;

        const productName = document.createElement("h3");
        productName.className = "product-name text-3 text-ellipsis-1";
        productName.textContent = product.product_title;

        const linkIcon = document.createElement("span");
        linkIcon.setAttribute("class", "linkIcon");
        linkIcon.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-up-right" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M14 2.5a.5.5 0 0 0-.5-.5h-6a.5.5 0 0 0 0 1h4.793L2.146 13.146a.5.5 0 0 0 .708.708L13 3.707V8.5a.5.5 0 0 0 1 0z"/>
            </svg>
          `;

        productBody.append(productName, productCategory);
        productLink.append(wraperImg, productBody, linkIcon);
        productItem.appendChild(productLink);
        result.appendChild(productItem);
      });
    });
  }

  function handleSearch() {
    let searchTerm = "";

    inputSearchs.forEach((input) => {
      if (input.value.trim() && !searchTerm) {
        searchTerm = input.value;
      }
    });

    const filteredProducts = filterProducts(searchTerm);
    displayResults(filteredProducts);
  }

  inputSearchs[0].addEventListener("input", function () {
    if (this.value.trim()) {
      largScreenSearchResult.classList.add("active");
    } else {
      largScreenSearchResult.classList.remove("active");
    }

    clearTimeout(this.timer);
    this.timer = setTimeout(handleSearch, 300);
  });

  for (let i = 1; i < inputSearchs.length; i++) {
    inputSearchs[i].addEventListener("input", function () {
      if (!this.value.trim()) {
        searchResults.forEach((result) => {
          result.innerHTML = "";
        });
        return;
      }

      clearTimeout(this.timer);
      this.timer = setTimeout(handleSearch, 300);
    });
  }

  inputSearchs.forEach((input) => {
    input.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        handleSearch();
      }
    });
  });

  searchButtons.forEach((button) => {
    button.addEventListener("click", handleSearch);
  });

  if (closeSearchSm) {
    closeSearchSm.addEventListener("click", function () {
      document.getElementById("boxSearch").classList.add("hideElementSm");
    });
  }

  fetchProducts();
});
