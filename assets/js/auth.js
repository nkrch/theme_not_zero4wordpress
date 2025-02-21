let option = "enter";
const popUpAuth = `<div class="container">
    <div class="switcher">
    <div class="switch-inner">
    
     <button class="btn-switcher" id="enter-btn" onclick="formSwitcher('enter')">
            Вход
        </button>
        <span id='span-switcher'>
        </span>
        <button class="btn-switcher" onclick="formSwitcher('register')">
            Регистрация
        </button>
</div>
       
    </div>
    
</div>
`;
let span = document.getElementById("span-switcher");

const formAuth = `<form class="formAuth">  
<input name="email" placeholder="email" type="email"/>
<input name="password" placeholder="password" type="password"/>
<button class="submit-btn" onclick="enterFunc(event)">Войти</button>
</form>
`;

const formRegister = `<form class="formRegister">            
<input name='name' placeholder='name' type="text"/>
<input name="email" placeholder="email" type="email"/>
<input name="password" placeholder="password" type="password"/>
<input name="passwordrepeat" placeholder="repeat password" type="password"/>
<button class="submit-btn" onclick="registerFunc(event)">Зарегистрироваться</button>
</form>
`;

function startAuth() {
  console.log("###startAuth");
  const popUpAuthForm = document.createElement("div");

  popUpAuthForm.innerHTML = popUpAuth;
  popUpAuthForm.className = "pop-up-form";
  popUpAuthForm.style.width = "500px";
  popUpAuthForm.style.position = "fixed";
  popUpAuthForm.style.top = "50%";
  popUpAuthForm.style.left = "50%";
  popUpAuthForm.style.transform = "translate(-50%, -50%)";
  popUpAuthForm.style.display = "flex";

  popUpAuthForm.style.borderRadius = "10px";
  popUpAuthForm.style.zIndex = "1";
  document.body.append(popUpAuthForm);
  document.getElementsByClassName("switcher")[0].innerHTML += formAuth;
  animationsSwitch("enter");
}

function registerFunc() {}

function formSwitcher(optionTo) {
  if (optionTo != option) {
    if (optionTo == "register") {
      // Switching to register
      console.log("Switching to register");

      // Apply left position for register (move 50%)
      option = "register"; // Update the option
      console.log(option);
      animationsSwitch("regist");
      // Remove and append form elements (e.g., form switching)
      document.getElementsByClassName("formAuth")[0].remove();
      document.getElementsByClassName("switcher")[0].innerHTML += formRegister;
    } else if (optionTo == "enter") {
      // Switching to enter
      console.log("Switching to enter");

      // Apply left position for enter (move -50%)
      option = "enter"; // Update the option
      console.log(option);
      animationsSwitch("enter");
      // Remove and append form elements (e.g., form switching)
      document.getElementsByClassName("formRegister")[0].remove();
      document.getElementsByClassName("switcher")[0].innerHTML += formAuth;
    }
  }
}

function animationsSwitch(option) {
  switch (option) {
    case "enter":
      console.log("enter-anima");
      span = document.getElementById("span-switcher");
      // Ensure the span element is not in any animation state
      span.style.width =
        document.getElementsByClassName("btn-switcher")[0].clientWidth + "px";

      span.style.animation = "none"; // Clear any existing animation

      // Force a reflow to ensure the animation is reset
      span.offsetHeight; // Trigger reflow

      // Apply the "toenter" animation
      span.style.animation = "toenter 0.3s ease forwards";
      span.style.animationIterationCount = 1;
      // Add an animationend listener for additional handling if needed

      break;

    case "regist":
      console.log("regist-anima");
      span = document.getElementById("span-switcher");

      // Ensure the span element is not in any animation state
      span.style.width =
        document.getElementsByClassName("btn-switcher")[0].clientWidth + "px";
      span.style.animation = "none"; // Clear any existing animation

      // Force a reflow to ensure the animation is reset
      span.offsetHeight; // Trigger reflow

      // Apply the "toregister" animation
      span.style.animation = "toregister 0.3s ease forwards";
      span.style.animationIterationCount = 1;

      // Add an animationend listener for additional handling if needed

      break;

    default:
      break;
  }
}

function enterFunc(event) {
  event.preventDefault();
  const data = getData();
  enterUser(data, siteUrl);
}

function registerFunc(event) {
  event.preventDefault();
  console.log("###registerFunc");
  console.log(siteUrl);
  const data = getData();
  console.log(data.password);
  console.log(data.password != data.passwordrepeat);
  if (option != "enter" && data.password != data.passwordrepeat) {
    alert("Пароли не совпадают");
  } else {
    regUser(data, siteUrl);
  }
}

function getData() {
  let form = document.getElementsByClassName("formRegister")[0];
  if (form == null) {
    form = document.getElementsByClassName("formAuth")[0];
  }
  console.log(form);

  const formData = new FormData(form);
  const data = Object.fromEntries(formData.entries());
  data.role = "Пользователь";
  console.log(data);
  return data;
}

function regUser(data, siteUrl) {
  const dbUrl = siteUrl + "wp-admin/admin-ajax.php";

  const formData = new URLSearchParams();
  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      formData.append(key, data[key]);
    }
  }

  // Corrected action name
  formData.append("action", "user_register");

  fetch(dbUrl, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log(data);
        toastifier(
          "Регистрация прошла успешно",
          1000,
          true,
          "top",
          "center",
          "#0b6e4f",
        );
      } else {
        console.error("Error:", data.data.message);
      }
    })
    .catch((error) => console.error("Error:", error));
}

function enterUser(data, siteUrl) {
  const dbUrl = siteUrl + "wp-admin/admin-ajax.php";
  console.log(dbUrl);
  const formData = new URLSearchParams();
  for (const key in data) {
    if (data.hasOwnProperty(key)) {
      formData.append(key, data[key]);
    }
  }

  // Corrected action name
  formData.append("action", "user_enter");

  fetch(dbUrl, {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        console.log(data);
        toastifier("Успешно", 1000, true, "top", "center", "#0b6e4f");
        //make save
        setTimeout(() => location.reload(), 600);
      } else {
        toastifier(data.data.message, 1000, true, "top", "center", "#0b6e4f");
      }
    })
    .catch((error) => console.error("Ошибка:", error));
}
