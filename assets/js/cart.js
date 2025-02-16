function submitFunc(product, price, description, image, id) {
  console.log(product);
  const cart = getCart();
  console.log("###cart");
  console.log(cart);
  console.log(cart.find((item) => item.title === product));
  if (cart.find((item) => item.title === product)) {
    console.log("###need to update");
    console.log(cart.find((item) => item.title === product).quantity + 1);
    console.log(cart.find((item) => item.title === product));
    updateQuantity(
      product,
      cart.find((item) => item.title === product).quantity + 1,
    );
  } else {
    console.log("###new cart");
    cart.push({ title: product, quantity: 1, price, description, image, id });
    saveCart(cart);
  }

  console.log(cart);
  updateCartCount();
}

// Получение корзины из cookies
function getCart() {
  const cart = Cookies.get("cart");
  console.log(cart);
  return cart ? JSON.parse(cart) : [];
}

function updateQuantity(title, newQuantity) {
  const cart = getCart();
  const product = cart.find((item) => item.title === title);
  console.log("###before updates");
  console.log(product);
  if (product) {
    product.quantity = newQuantity;
    console.log("###updates");
    console.log(product);
    if (newQuantity <= 0) {
      removeFromCart(title);
    }
    saveCart(cart);
  }
}

// Сохранение корзины в cookies
function saveCart(cart) {
  Cookies.set("cart", JSON.stringify(cart), { expires: 7 });
  ajaxAddToCart(); // Обновляем сервер после удаления
}

// Обновление счетчика товаров в корзине
function updateCartCount() {
  const cart = getCart();
  const count = cart.reduce((total, item) => total + item.quantity, 0);
  console.log(count);
}

//  изменения количества,
// удаления товаров
// расчета общей стоимости.

//Используйте cookies для хранения данных о товарах.
//Добавить количество элементов корзины рядом с корзиной

function removeFromCart(title) {
  let cart = getCart();
  cart = cart.filter((item) => item.title !== title);
  saveCart(cart);
  updateCartCount();

  updateCartDisplay();
}

function ajaxAddToCart() {
  location.reload();
}

function calculateTotal() {
  const cart = getCart();
  return cart.reduce((total, item) => total + item.price * item.quantity, 0);
}

function updateFromCartPage(product, newQuantity) {
  const cart = getCart();
  const productIndex = cart.findIndex((item) => item.title === product);
  cart[productIndex].quantity = newQuantity;
  if (newQuantity <= 0) {
    removeFromCart(product);
  }
  saveCart(cart);
}

function orderFunc(event, siteUrl) {
  event.preventDefault();
  const dbUrl = siteUrl + "/wp-admin/admin-ajax.php";
  const orderData = {
    name: document.querySelector("input[placeholder='Имя']").value,
    email: document.querySelector("input[placeholder='Email']").value,

    cart: getCart(),
  };

  sendAjax(orderData, dbUrl);
}

//use local domain
//create local domain
//in settings of wordpress in generals rewrite rules
//

function sendAjax(data, url) {
  console.log(data);
  console.log(url);

  // Преобразуем данные в формат, ожидаемый WordPress (FormData)
  const formData = new FormData();
  formData.append("action", "create_order"); // WordPress ожидает поле "action"
  formData.append("data", JSON.stringify(data)); // Передаем сериализованные данные
  fetch(url, {
    method: "POST",
    body: formData,
  })
    .then((res) => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.text();
    })
    .then((data) => {
      console.log("Response:", JSON.parse(data));
      document.querySelector("form").reset();
      deleteCart();
    })
    .catch((err) => console.error("Error:", err));
}

function deleteCart() {
  Cookies.remove("cart");
  location.reload();
}

function ajaxUserCartSave(data, url, option) {
  switch (option) {
    case "update-existing":
      break;
    case "delete-existing":
      break;
    case "create-new":
      createAjaxCartSave(data, url);
      break;

    default:
      break;
  }
}

function createAjaxCartSave(data, url) {}
