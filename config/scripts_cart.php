<div class="modal" id="modal">
  <div class="modal-content" style="max-width: 500px;">
    <button class="close-btn">&times;</button>
    <img style="margin: 0 auto; margin-top: -100px;" id="modal-image" src="" alt="Product Image">
    <h3 id="modal-title">Title</h3>
    <p id="modal-type">Type</p>
    <p>Amount</p>
    <div class="d-flex align-items-center gap-1 mx-auto">
      <button class="btn btn-primary" onclick="decreaseAmount()">-</button>
      <input class="form-control" type="text" id="modal-amount" value="1" min="1" readonly>
      <button class="btn btn-primary" onclick="increaseAmount()">+</button>
    </div>
    <p id="modal-price">Price</p>
    <button class="add-to-cart text-white">Add to cart</button>
  </div>
</div>


<script>
  let selectedProduct = null;

  function increaseAmount() {
    const amountInput = document.getElementById('modal-amount');
    let currentValue = parseInt(amountInput.value);
    amountInput.value = currentValue + 1; // Increase value by 1
  }

  // Function to decrease the amount
  function decreaseAmount() {
    const amountInput = document.getElementById('modal-amount');
    let currentValue = parseInt(amountInput.value);
    if (currentValue > 1) { // Prevent value from going below 1
      amountInput.value = currentValue - 1;
    }
  }


  function updateCartCount() {
    const cartItems = getCookie("cart"); // Get cart items from cookie
    let totalCount = 0;

    // Calculate total quantity of items in the cart
    cartItems.forEach(item => {
      totalCount += parseInt(item.quantity, 10);
    });

    // Update the cart count in the HTML
    document.querySelector(".cart-count").textContent = totalCount;
  }

  document.addEventListener("DOMContentLoaded", () => {
    updateCartCount()
    const modal = document.getElementById("modal");
    const closeModalButton = document.querySelector(".close-btn");
    const viewDetailsButtons = document.querySelectorAll(".view-details");
    const modalImage = document.getElementById("modal-image");
    const modalTitle = document.getElementById("modal-title");
    const modalType = document.getElementById("modal-type");
    const modalPrice = document.getElementById("modal-price");

    // Show Modal
    viewDetailsButtons.forEach(button => {
      button.addEventListener("click", (event) => {
        const item = button.getAttribute("data-item");
        const type = button.getAttribute("data-type");
        const price = button.getAttribute("data-price");
        const image = button.getAttribute("data-image");

        modalImage.src = image;
        modalTitle.textContent = item;
        modalType.textContent = type;
        modalPrice.textContent = price;
        selectedProduct = button.getAttribute("data-id");
        modal.style.display = "flex";
      });
    });


    // Close Modal
    closeModalButton.addEventListener("click", () => {
      modal.style.display = "none";
      selectedProduct = null;
    });

    // Close Modal on outside click
    window.addEventListener("click", (event) => {
      if (event.target === modal) {
        modal.style.display = "none";
      }
    });

    // Event listener for "Add to Cart" button
    document.querySelector(".add-to-cart").addEventListener("click", () => {
      // Get modal details
      const item = modalTitle.textContent;
      const type = modalType.textContent;
      const price = modalPrice.textContent;
      const image = modalImage.src;
      const quantity = document.getElementById("modal-amount").value;

      // Create a cart item object
      const cartItem = {
        id: selectedProduct,
        item,
        type,
        price,
        image,
        quantity
      };

      // Get existing cart items from the cookie
      let cartItems = getCookie("cart");

      // Check if the item already exists in the cart
      const existingItemIndex = cartItems.findIndex(cart => cart.id === selectedProduct);
      if (existingItemIndex > -1) {
        // Update quantity if item already exists
        cartItems[existingItemIndex].quantity = parseInt(cartItems[existingItemIndex].quantity) + parseInt(quantity);
      } else {
        // Add new item to the cart
        cartItems.push(cartItem);
      }

      // Store updated cart items in the cookie
      setCookie("cart", cartItems, 7);

      // Close the modal
      modal.style.display = "none";
      updateCartCount()
      alert("Item added to cart!");
      window.location.reload();
    });
  });
</script>