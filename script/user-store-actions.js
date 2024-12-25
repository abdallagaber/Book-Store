document.addEventListener("DOMContentLoaded", function () {
  // Wishlist button functionality
  document.querySelectorAll(".add-to-wishlist").forEach(function (button) {
    button.addEventListener("click", function () {
      const bookId = this.getAttribute("data-book-id");

      fetch("store.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "action=add_to_wishlist&book_id=" + bookId,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "error") {
            alert(data.message || "Please login first");
            return;
          }

          // Toggle active class on the button
          if (data.status === "added") {
            this.classList.add("active");
          } else if (data.status === "removed") {
            this.classList.remove("active");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred. Please try again.");
        });
    });
  });

  // Add to Cart functionality (login check)
  document.querySelectorAll(".add-to-cart").forEach(function (button) {
    button.addEventListener("click", function () {
      const bookId = this.getAttribute("data-book-id");

      fetch("store.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "action=add_to_cart&book_id=" + bookId,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "error") {
            alert(data.message || "Please login first");
            return;
          }

          // Show success message
          if (data.status === "added" || data.status === "updated") {
            alert("Item added to cart successfully!");
          } else {
            alert("An error occurred. Please try again.");
          }
        })
        .catch((error) => {
          console.error("Error:", error);
          alert("An error occurred. Please try again.");
        });
    });
  });
});
