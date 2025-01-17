//Создайте файл cart.js в вашей теме.
//Реализуйте функции для добавления товаров в корзину,
function submitFunc(product, price, description, image) {
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
      cart.find((item) => item.title === product).quantity + 1
    );
  } else {
    console.log("###new cart");
    cart.push({ title: product, quantity: 1, price, description, image });
    saveCart(cart);
  }

  console.log(cart);
  updateCartCount();
}

// Получение корзины из cookies
function getCart() {
  const cart = Cookies.get("cart");

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

function ajaxAddToCart(product) {
  jQuery.post(ajaxurl, { action: "add_to_cart", product }, function (response) {
    if (response.success) {
      updateCartCount();
    }
  });
}

function calculateTotal() {
  const cart = getCart();
  return cart.reduce((total, item) => total + item.price * item.quantity, 0);
}
