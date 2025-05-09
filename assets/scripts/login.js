document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();

  const errorElement = document.getElementById("errorMsg");
  errorElement.style.display = "none";

  if (!email || !password) {
    showError("Please fill out all fields");
    return;
  }

  fetch("./api/login.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({
      email: email,
      password: password,
    }),
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok");
      }
      return response.json();
    })
    .then((data) => {
      if (data.status === "success") {
        window.location.href = "index.php";
      } else {
        showError(
          data.message || "Login failed. Please try again."
        );
      }
    })
    .catch((error) => {
      showError("There was an error connecting to the server. Please try again later.");
    });
});

function showError(message) {
  const errorElement = document.getElementById("errorMsg");
  document.getElementById("e_msg").textContent = message;
  errorElement.style.cssText = `display: flex; justify-content: center; align-items: center;`;
}
