//ADMIN SCRIPTS
let id_order = 0,
  prev = {},
  now;
function makeSelectF(products, checked) {
  let select = "";

  for (let i = 0; i < products.length; i++) {
    if (!checked || products[i].title !== checked) {
      select += `<option value="${products[i].title}" >${products[i].title}</option>`;
    } else {
      select += `<option value="${products[i].title}" selected >${products[i].title}</option>`;
      prev = products[i];
      console.log(prev);
    }
  }

  return select;
}

async function selectFunc() {
  //send ajax to get product fields PRICE
  let exactpost = document.getElementById("name").value;
  //we got ID, then we send ajax to get fields
  const products = await getProd();
  const found = products.filter((item) => item.title == exactpost)[0];

  document.getElementById("price").value = found.acf.price;
}

let isPopup = false;
async function func_ADMIN_addProductToCart(url, id) {
  console.log(id);
  try {
    const getProducts = await getProd(url); // Await the Promise

    const makeSelect = makeSelectF(getProducts);

    let inner = `
      <form id="popupForm" style="display: grid; grid-template-columns: 30% 300px;">
        <label for="name">Имя:</label>
        <select onChange="selectFunc()" id="name" name="name"> <option value="" disabled selected>Выберите продукт</option> ${makeSelect}</select>

        <label for="quantity">Количество:</label>
        <input type="number" id="quantity" name="quantity" >

        <label for="price">Цена:</label>
        <input type="number" id="price" name="price" >
      </form>
      <input type="submit" value="Сохранить" onClick="save_add_product(event, '${url}', '${id}');">
      <button onClick="func_ADMIN_closePopup(event);">Закрыть</button>`;

    if (!isPopup) {
      isPopup = true;
      popup(inner);
    }
  } catch (err) {
    console.error("Error fetching products:", err);
  }
}

function func_ADMIN_clearCart() {}

async function func_ADMIN_changeOrder(
  title,
  quantity,
  id,
  price,
  postid,
  url,
  id
) {
  id_order = id;
  try {
    const getProducts = await getProd(url); // Await the Promise

    const checked = title;

    const makeSelect = makeSelectF(getProducts, checked);

    let inner = `
      <form id="popupForm" style="display: grid; grid-template-columns: 30% 300px;">
        <label for="name">Имя:</label>
        <select onChange="selectFunc()" id="name" name="name"> ${makeSelect}</select>

        <label for="quantity">Количество:</label>
        <input type="number" id="quantity" name="quantity" value="${quantity}">

        <label for="price">Цена:</label>
        <input type="number" id="price" name="price" value="${price}">
      </form>
      <input type="submit" value="Сохранить" onClick="func_ADMIN_savePopupData(event);">
      <button onClick="func_ADMIN_closePopup(event);">Закрыть</button>`;

    if (!isPopup) {
      isPopup = true;
      popup(inner);
    }
  } catch (err) {
    console.error("Error fetching products:", err);
  }
}

async function save_add_product(event, url, id) {
  console.log("save popup data");
  id_order = id;
  console.log(prev);
  //SAVE
  const form = document.getElementById("popupForm");
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());
  console.log("DATA FROM FORM", data);

  //получить заказы
  const orders = await getOrders();
  console.log(orders);
  const ordSearch = orders.filter((item) => item.id == id_order)[0];
  console.log(ordSearch);
  const products = await getProd();
  console.log("PRODUCTS DB", products);

  //normal form of order item

  const product = products.filter((item) => item.title == data.name)[0];
  console.log("SEARCHED PRODUCT", product);
  if (data.quantity == "" || data.quantity == "") {
    data.quantity = 1;
  }
  const newItemOfOrder = {
    title: product.title,
    quantity: data.quantity,
    price: data.price,
    description: product.acf.description,
    image: product.acf.image,
    id: product.id,
  };
  console.log(newItemOfOrder);

  //search for exact order
  const order = orders.filter((item) => item.id == id_order)[0].cart;
  console.log("ORDER", order);
  order.push(newItemOfOrder);
  console.log("ORDER", order);
  await post_formatting(order);
  console.log("ORDER", order);
}

function func_ADMIN_removeFromCart(item, id) {
  console.log(item);
  console.log(id);
}

function getCart_admin_ajax() {}

async function func_ADMIN_savePopupData() {
  console.log("save popup data");
  console.log(id_order);
  console.log(prev);
  //SAVE
  const form = document.getElementById("popupForm");
  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());
  console.log("DATA FROM FORM", data);

  //получить заказы
  const orders = await getOrders();
  //найти конкретный

  console.log(orders);
  //find with id_order

  const order = orders.filter((item) => item.id == id_order)[0].cart;
  console.log("ORDER", order);

  const products_db = await getProd();
  console.log("PRODUCTS DB", products_db);

  //normal form of order item

  const product = products_db.filter((item) => item.title == data.name)[0];
  console.log("SEARCHED PRODUCT", product);

  const newItemOfOrder = {
    title: product.title,
    quantity: data.quantity,
    price: data.price,
    description: product.acf.description,
    image: product.acf.image,
    id: product.id,
  };
  console.log("NEW ITEM OF ORDER", newItemOfOrder);

  console.log(order);
  //find and replace
  const newOrder = order.map((item) => {
    if (item.title == prev.title) {
      console.log("ITEM", item);
      return newItemOfOrder;
    } else {
      return item;
    }
  });
  console.log("NEW ORDER", newOrder);
  post_formatting(newOrder);
}

async function post_formatting(order) {
  //check if order has repeating orders
  const newOrd = mergeDuplicates(order);
  console.log("NEW ORDER", newOrd);

  //FINALLY SAVE
  let arrayOfAllOrders = await getOrders();

  console.log(arrayOfAllOrders);

  arrayOfAllOrders = arrayOfAllOrders.map((item) => {
    if (item.id == id_order) {
      item.cart = newOrd;
    }
    return item;
  });
  console.log(arrayOfAllOrders);

  //сюда впихнуть сэйв
  save_orders(arrayOfAllOrders);
}

function mergeDuplicates(items) {
  const merged = {};

  items.forEach((item) => {
    if (merged[item.title]) {
      merged[item.title].quantity += parseInt(item.quantity, 10);
    } else {
      merged[item.title] = { ...item, quantity: parseInt(item.quantity, 10) };
    }
  });

  return Object.values(merged);
}

function save_orders(orders) {
  const url = `${ajax_object.ajax_url}?action=resave_orders`;
  console.log(orders);
  // Сохраняем в файл (или в БД)
  let formData = new FormData();
  formData.append("action", "resave_orders"); // WordPress ожидает поле "action"
  formData.append("orders", JSON.stringify(orders)); // Передаем сериализованные данные
  console.log(formData);
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
    })
    .catch((err) => console.error("Error:", err));
}

function popup(innerContent) {
  let popup = document.createElement("div");
  popup.classList.add("popup");
  popup.style.width = "500px";
  popup.style.height = "430px";
  popup.style.position = "fixed";
  popup.style.top = "50%";
  popup.style.left = "50%";
  popup.style.transform = "translate(-50%, -50%)";
  popup.style.backgroundColor = "grey";
  popup.style.display = "flex";
  popup.style.justifyContent = "center";
  popup.style.alignItems = "center";
  popup.style.borderRadius = "10px";
  popup.style.zIndex = "1";
  popup.innerHTML = `<div class="popup-content">
  ${innerContent}
  </div>
</div>`;
  document.body.append(popup);
}

function func_ADMIN_closePopup(event) {
  if (isPopup) {
    document.querySelector(".popup").remove();
    isPopup = false;
  }
}

async function getProd() {
  try {
    const url = `${ajax_object.ajax_url}?action=get_products_for_select`;

    const res = await fetch(url, { method: "GET" });

    if (!res.ok) {
      throw new Error(`HTTP error! Status: ${res.status}`);
    }

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.data.message || "Unknown error");
    }

    return data.data.products; // This is an array, but inside an async function
  } catch (err) {
    console.error("Error:", err);
    return [];
  }
}

async function getOrders() {
  try {
    const params = new URLSearchParams({ action: "get_orders" });
    const url = `${ajax_object.ajax_url}?${params.toString()}`;

    const res = await fetch(url, { method: "GET", credentials: "same-origin" });

    if (!res.ok) {
      throw new Error(`HTTP error! Status: ${res.status}`);
    }

    const data = await res.json();

    if (!data.success) {
      throw new Error(data.data.message || "Unknown error");
    }

    return JSON.parse(data.data); // Fixed: Returning correct orders array
  } catch (err) {
    console.error("Error:", err);
    return [];
  }
}
