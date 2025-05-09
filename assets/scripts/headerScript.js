// Search functionality
let closeSearchSm = document.getElementById("closeSearchSm");
let openSearchSm = document.getElementById("SearchSmToggle");
let boxSearch = document.getElementById("boxSearch");

closeSearchSm.addEventListener("click", () => {
  boxSearch.classList.remove("active");
});

openSearchSm.addEventListener("click", () => {
  boxSearch.classList.toggle("active");
});

// Navigation items
const navItems = [
  { id: "index.php", label: "Home" },
  { id: "electronic.php", label: "Electronics" },
  { id: "ladiesWears.php", label: "Ladies Wears" },
  { id: "mens_wear.php", label: "Mens Wear" },
  { id: "furnitures.php", label: "Furnitures" },
  { id: "home_appliances.php", label: "Home Appliances" },
  { id: "stationery.php", label: "Stationery" },
  { id: "food_stuff.php", label: "Food Stuff" },
];

const navList = document.getElementById("navList");
const smNavList = document.getElementById("wraperSmNav");

const currentPage = window.location.pathname.split("/").pop();

navItems.forEach(({ id, label }) => {
  const link1 = document.createElement("a");
  link1.href = id;

  const link2 = document.createElement("a");
  link2.href = id;

  const li1 = document.createElement("li");
  li1.textContent = label;

  const li2 = document.createElement("li");
  li2.textContent = label;

  if (id === currentPage) {
    li1.classList.add("active");
    li2.classList.add("active");
  }

  link1.appendChild(li1);
  link2.appendChild(li2);

  navList.appendChild(link1);
  smNavList.appendChild(link2);
});

// Sm Nav
document.addEventListener("DOMContentLoaded", () => {
  const smToggleNav = document.querySelector(".smToggleMenu");
  const smNav = document.querySelector(".smNav");

  smToggleNav.addEventListener("click", () => {
    let menuIcon = smToggleNav.querySelector(".bi-list");
    let closeIcon = smToggleNav.querySelector(".bi-x");
    if (menuIcon && closeIcon) {
      menuIcon.style.display =
        menuIcon.style.display === "none" ? "block" : "none";
      closeIcon.style.display =
        closeIcon.style.display === "none" ? "block" : "none";
    }

    smNav.classList.toggle("active");

    smToggleNav.classList.toggle("is-active");
  });

  const navLinks = smNav.querySelectorAll("a");
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      smNav.classList.remove("active");
      smToggleNav.classList.remove("is-active");

      const [menuIcon, closeIcon] = smToggleNav.querySelectorAll("svg, i");
      if (menuIcon && closeIcon) {
        menuIcon.style.display = "block";
        closeIcon.style.display = "none";
      }
    });
  });

  document.addEventListener("click", (e) => {
    if (!smNav.contains(e.target) && !smToggleNav.contains(e.target)) {
      smNav.classList.remove("active");
      smToggleNav.classList.remove("is-active");
    }
  });
});

// Scroll To Up
let scrollToUp = document.querySelector(".scrollToUp");

window.addEventListener("scroll", function () {
  if (window.scrollY >= 200) {
    scrollToUp.classList.add("active");
  } else {
    scrollToUp.classList.remove("active");
  }
});

scrollToUp.addEventListener("click", () => {
  window.scrollTo({
    top: 0,
    left: 0,
    behavior: "smooth",
  });
});
