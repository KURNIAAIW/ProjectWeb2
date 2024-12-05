<?php
include "../auth/mw_user.php";
include '../config/db_connect.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Keranjang Belanja</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 800px;
      margin: auto;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

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

    .discount-code {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }

    .discount-code input {
      flex: 1;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      margin-right: 10px;
    }

    .discount-code button {
      padding: 10px 15px;
      background-color: #F59E0B;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .discount-code button:hover {
      background-color: #e6b800;
    }

    .proceed-to-pay {
      margin-top: 20px;
      text-align: center;
    }

    .proceed-to-pay button {
      background-color: #F59E0B;
      color: #fff;
      border: none;
      padding: 15px 20px;
      border-radius: 5px;
      font-size: 18px;
      cursor: pointer;
    }

    .proceed-to-pay button:hover {
      background-color: #e6b800;
    }

    .total-price {
      text-align: right;
      font-size: 18px;
      font-weight: bold;
      margin-top: 15px;
    }

    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
      max-width: 400px;
      width: 90%;
      text-align: center;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
      position: relative;
    }

    .modal-content h3 {
      margin-bottom: 15px;
      font-size: 18px;
    }

    .modal-content .bank-details {
      text-align: left;
      margin-bottom: 20px;
    }

    .modal-content .bank-details span {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
    }

    .modal-content .bank-details .copy-btn {
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
      font-size: 12px;
    }

    .modal-content input[type="file"] {
      display: block;
      margin: 15px 0;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      width: 100%;
    }

    .modal-content button {
      background-color: #007bff;
      color: #fff;
      border: none;
      padding: 10px 15px;
      border-radius: 5px;
      cursor: pointer;
    }

    .modal-content button:hover {
      background-color: #0056b3;
    }

    .modal-content .close-btn {
      background-color: transparent;
      border: none;
      font-size: 20px;
      position: absolute;
      top: 10px;
      right: 10px;
      cursor: pointer;
    }
  </style>
</head>

<body>
  <div class="container">
    <h1>Keranjang Belanja</h1>

    <!-- Cart Items -->
    <div id="cart-items-container"></div>

    <!-- Total Price -->
    <div class="total-price" id="total-price">Total: Rp 0.00</div>


    <!-- Proceed to Pay -->
    <div class="proceed-to-pay">
      <button id="pay-button">Bayar</button>
    </div>
  </div>

  <div class="modal" id="payment-modal">
    <div class="modal-content">
      <button class="close-btn" onclick="closeModal()">&times;</button>
      <h3>Upload Bukti</h3>
      <div class="bank-details">
        <span><strong>BCA</strong>: A/n Admin Jajanan Nusantara</span>
        <span>Account Number: <strong>1234567890</strong></span>
        <button class="copy-btn" onclick="copyToClipboard('1234567890')">Salin</button>
      </div>
      <label for="payment-proof">Upload Pembayaran (Max: 2MB)</label>
      <input type="file" id="payment-proof" accept="image/*">
      <button onclick="submitPaymentProof()">Kirim</button>
    </div>
  </div>

  <script>
    // Function to open the modal
    function openModal() {
      document.getElementById("payment-modal").style.display = "flex";
    }

    // Function to close the modal
    function closeModal() {
      document.getElementById("payment-modal").style.display = "none";
    }

    function copyToClipboard(text) {
      navigator.clipboard.writeText(text).then(() => {
        alert("Account number copied to clipboard!");
      });
    }

    function submitPaymentProof() {
      const fileInput = document.getElementById("payment-proof");
      const file = fileInput.files[0];

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
            closeModal(); // Close the modal
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

    // Event listener for the pay button
    document.getElementById("pay-button").addEventListener("click", openModal);
    // Function to get cookie value by name
    function getCookie(name) {
      const cookies = document.cookie.split("; ");
      for (let cookie of cookies) {
        const [key, value] = cookie.split("=");
        if (key === name) {
          return JSON.parse(decodeURIComponent(value));
        }
      }
      return [];
    }

    // Function to set cookie
    function setCookie(name, value, days) {
      const date = new Date();
      date.setTime(date.getTime() + days * 24 * 60 * 60 * 1000);
      document.cookie = `${name}=${encodeURIComponent(JSON.stringify(value))};expires=${date.toUTCString()};path=/`;
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
</body>

</html>