<!-- Header Section -->
<header>
  <div class="logo">
    <img width="75" src="../assets/logo.jpg" alt="Logo">
  </div>
  <nav>
    <a href="./index.php">Menu</a>
    <a href="./favorite.php">Favorite</a>
    <a href="./contact.php">Contact Us</a>
  </nav>
  <div style="display: flex;flex-direction:row;">
    <button class="btn block cart">
      <a href="../user/transactions.php">
        <span class="cart-icon">📃</span>
      </a>
    </button>
    <button type="button" class="btn block cart" data-bs-toggle="modal"
      data-bs-target="#exampleModalCenter">
      <span class="cart-icon">🛒</span>
      <span class="cart-count">0</span>
    </button>
    <a href="../auth/logout.php" class="btn text-danger" style="margin-left: 20px;">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 30px;height: 30px;">
        <path fill-rule=" evenodd" d="M16.5 3.75a1.5 1.5 0 0 1 1.5 1.5v13.5a1.5 1.5 0 0 1-1.5 1.5h-6a1.5 1.5 0 0 1-1.5-1.5V15a.75.75 0 0 0-1.5 0v3.75a3 3 0 0 0 3 3h6a3 3 0 0 0 3-3V5.25a3 3 0 0 0-3-3h-6a3 3 0 0 0-3 3V9A.75.75 0 1 0 9 9V5.25a1.5 1.5 0 0 1 1.5-1.5h6ZM5.78 8.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 0 0 0 1.06l3 3a.75.75 0 0 0 1.06-1.06l-1.72-1.72H15a.75.75 0 0 0 0-1.5H4.06l1.72-1.72a.75.75 0 0 0 0-1.06Z" clip-rule="evenodd" />
      </svg>

    </a>
  </div>
</header>

<?php include '../config/scripts_cookie.php' ?>

<style>
  .cart-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }

  .cart-item img {
    width: 80px;
    height: 80px;
    border-radius: 8px;
    margin-right: 15px;
  }

  .cart-item-details {
    flex: 1;
  }

  .cart-item-title {
    font-size: 16px;
    font-weight: bold;
    margin: 0;
  }

  .cart-item-meta {
    font-size: 14px;
    color: #666;
    margin: 5px 0;
  }

  .cart-item-quantity {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .cart-item-quantity button {
    background-color: transparent;
    border: 1px solid #ddd;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
  }

  .cart-item-quantity button:hover {
    background-color: #f5f5f5;
  }

  .cart-item-price {
    font-size: 16px;
    font-weight: bold;
    margin-left: 20px;
  }

  .cart-item-remove {
    color: #e74c3c;
    cursor: pointer;
    font-size: 20px;
  }

  .total-price {
    text-align: right;
    font-size: 18px;
    font-weight: bold;
    margin-top: 15px;
  }


  .bank-details {
    text-align: left;
    margin-bottom: 20px;
  }

  .bank-details span {
    display: block;
    margin-bottom: 5px;
    font-size: 14px;
  }

  .bank-details .copy-btn {
    background-color: #e74c3c;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 12px;
  }

  input[type="file"] {
    display: block;
    margin: 15px 0;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    width: 100%;
  }
</style>

<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog"
  aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-centered modal-dialog-scrollable"
    role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalCenterTitle">Keranjang Belanja
        </h5>
        <button type="button" class="close" data-bs-dismiss="modal"
          aria-label="Close">
          <i data-feather="x"></i>
        </button>
      </div>
      <div class="modal-body">
        <div id="cart-items-container" style="text-align: left;"></div>
        <div class="total-price" id="total-price">Total: Rp 0.00</div>

        <div class="mt-4" id="payment-section" style="text-align: left;">
          <!-- Address Input -->
          <div class="form-group mb-3">
            <h6>Masukkan alamat</h6>
            <input class="form-control" type="text" id="address" placeholder="Masukkan alamat lengkap" required>
          </div>

          <h6>Upload Bukti</h6>
          <div class="bank-details">
            <span><strong>BCA</strong>: A/n Admin Jajanan Nusantara</span>
            <span>Account Number: <strong>1234567890</strong></span>
            <button class="copy-btn btn btn-info" onclick="copyToClipboard('1234567890')">Salin</button>
          </div>

          <!-- File Upload -->
          <div>
            <label for="payment-proof">Upload Pembayaran (Max: 2MB)</label>
            <input type="file" id="payment-proof" accept="image/*" required>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light-secondary"
          data-bs-dismiss="modal">
          <i class="bx bx-x d-block d-sm-none"></i>
          <span class="d-none d-sm-block">Tutup</span>
        </button>
        <button type="button" class="btn btn-primary ms-1" data-bs-dismiss="modal" onclick="submitPaymentProof()">
          <i class="bx bx-check d-block d-sm-none"></i>
          <span class="d-none d-sm-block">Bayar</span>
        </button>
      </div>
    </div>
  </div>
</div>


<script>
  const paymentSection = document.getElementById("payment-section");
  document.addEventListener("DOMContentLoaded", () => {
    const cartItems = getCookie("cart");
    if (cartItems.length > 0) {
      paymentSection.style.display = "block";
    } else {
      paymentSection.style.display = "none";
    }
  });

  function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
      alert("Account number copied to clipboard!");
    });
  }

  function submitPaymentProof() {
    const fileInput = document.getElementById("payment-proof");
    const file = fileInput.files[0];
    const addressInput = document.getElementById("address");
    const address = addressInput.value.trim();

    if (!address) {
      alert("Please enter your address.");
      return;
    }

    if (!file) {
      alert("Please upload a payment proof.");
      return;
    }

    const cartItems = getCookie("cart");
    if (cartItems.length === 0) {
      alert("Keranjang kosong. Silakan tambahkan produk.");
      return;
    }

    const transactionId = Date.now(); // Unique transaction ID
    const formData = new FormData();
    formData.append("transaction_id", transactionId);
    formData.append("payment_proof", file);
    formData.append("address", address);
    formData.append("items", JSON.stringify(cartItems));

    fetch("save_transaction_with_image.php", {
        method: "POST",
        body: formData,
      })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Transaksi berhasil disimpan!");
          setCookie("cart", [], 7); // Clear cart
          renderCartItems(); // Re-render cart
          window.location.reload();
        } else {
          alert("Gagal menyimpan transaksi: " + data.message);
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Terjadi kesalahan. Silakan coba lagi.");
      });
  }

  // Function to render cart items
  function renderCartItems() {
    const cartItems = getCookie("cart");
    const container = document.getElementById("cart-items-container");
    const totalPriceElement = document.getElementById("total-price");
    container.innerHTML = "";
    let totalPrice = 0;

    if (cartItems.length === 0) {
      container.innerHTML = "<p>Keranjangnya kosong.</p>";
      totalPriceElement.textContent = "Total: Rp 0.00";
      return;
    }

    cartItems.forEach(item => {

      const itemPrice = parseFloat(item.price.replace("Rp ", "")) * item.quantity;
      totalPrice += itemPrice;

      const itemElement = document.createElement("div");
      itemElement.classList.add("cart-item");
      itemElement.innerHTML = `
          <img src="${item.image}" alt="Product Image">
          <div class="cart-item-details">
            <p class="cart-item-title">${item.item}</p>
            <div class="cart-item-quantity">
              <button class="quantity-decrease">-</button>
              <span class="quantity-value">${item.quantity}</span>
              <button class="quantity-increase">+</button>
            </div>
          </div>
          <p class="cart-item-price">Rp ${itemPrice.toFixed(2)}</p>
          <span class="cart-item-remove">&times;</span>
        `;

      // Decrease quantity
      itemElement.querySelector(".quantity-decrease").addEventListener("click", () => {
        if (item.quantity > 1) {
          item.quantity--;
          setCookie("cart", cartItems, 7);
          renderCartItems();
        }
      });

      // Increase quantity
      itemElement.querySelector(".quantity-increase").addEventListener("click", () => {
        item.quantity++;
        setCookie("cart", cartItems, 7);
        renderCartItems();
      });

      // Remove item
      itemElement.querySelector(".cart-item-remove").addEventListener("click", () => {
        const index = cartItems.findIndex(cartItem => cartItem.id === item.id);
        cartItems.splice(index, 1);
        setCookie("cart", cartItems, 7);
        renderCartItems();
      });

      container.appendChild(itemElement);
    });

    totalPriceElement.textContent = `Total: Rp ${totalPrice.toFixed(2)}`;
  }

  // Render cart items on page load
  document.addEventListener("DOMContentLoaded", renderCartItems);
</script>