function toastifier(
  text = "window",
  duration = "1000",
  closeBool = true,
  gravity = "top",
  position = "center",
  bckColor = "black",
) {
  Toastify({
    text: text,
    duration: duration,
    close: closeBool,
    gravity: gravity,
    position: position,
    backgroundColor: bckColor,
  }).showToast();
}
