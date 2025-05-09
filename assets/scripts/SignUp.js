document.getElementById("signup_form").addEventListener("submit", async function (e) {
    e.preventDefault();

    const errorElement = document.getElementById("errorMsg");
    errorElement.style.display = "none";

    if (!document.getElementById("ckb1").checked) {
      showError("You must agree to the terms");
      return;
    }

    const formData = {
      f_name: document.getElementById("f_name").value.trim(),
      l_name: document.getElementById("l_name").value.trim(),
      email: document.getElementById("email").value.trim(),
      mobile: document.getElementById("mobile").value.trim(),
      password: document.getElementById("password").value,
      address1: document.getElementById("address1").value.trim(),
      address2: document.getElementById("address2").value.trim(),
    };

    try {
      const response = await fetch("api/signup.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(formData),
      });

      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || "Network response was not ok");
      }

      if (data.status === "success") {
        window.location.href = "index.php";
      } else {
        showError(data.message || "Registration failed");
      }
    } catch (error) {
      showError(error.message || "An error occurred. Please try again.");
    }
  });

function showError(message) {
  const errorElement = document.getElementById("errorMsg");
  document.getElementById("e_msg").textContent = message;
  errorElement.style.cssText = `display: flex; justify-content: center; align-items: center;`;
  errorElement.scrollIntoView({ behavior: "smooth" });
}
