document.addEventListener("DOMContentLoaded", () => {
    const cartCount = document.querySelector(".cart-count");
    const addToCartButtons = document.querySelectorAll(".add-to-cart");
    let itemCount = parseInt(cartCount.textContent);
  
    addToCartButtons.forEach(button => {
      button.addEventListener("click", () => {
        itemCount++;
        cartCount.textContent = itemCount;
      });
    });
  });
  