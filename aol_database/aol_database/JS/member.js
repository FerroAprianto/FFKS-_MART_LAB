
const $ = (sel) => document.querySelector(sel);
const show = (el, msg = '') => { el.classList.remove('hidden'); el.textContent = msg; };
const hide = (el) => { el.classList.add('hidden'); el.textContent = ''; };

const form = $('#memberForm');
const successBox = $('#msgSuccess');

const nameI = $('#name');
const emailI = $('#email');
const phoneI = $('#phone');
const addrI = $('#address');

const errName = $('#errName');
const errEmail = $('#errEmail');
const errPhone = $('#errPhone');
const errAddress = $('#errAddress');

const resetBtn = $('#resetBtn');


function validateName(v){ return v.trim().length >= 2; }
function validateEmail(v){ return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); }
function validatePhone(v){
  const digits = v.replace(/[^0-9]/g,'');
  return digits.length >= 9 && digits.length <= 15;
}
function validateAddress(v){ return v.trim().length >= 6; }

function clearErrors(){ [errName,errEmail,errPhone,errAddress].forEach(hide); }
function hideSuccess(){ successBox.classList.add('hidden'); successBox.textContent = ''; }
function showSuccess(msg){ successBox.classList.remove('hidden'); successBox.textContent = msg; }


form.addEventListener('submit', function(e){
  e.preventDefault();
  clearErrors();
  hideSuccess();

  const name = nameI.value.trim();
  const email = emailI.value.trim();
  const phone = phoneI.value.trim();
  const address = addrI.value.trim();

  let ok = true;

  if(!validateName(name)){ show(errName,'Nama minimal 2 karakter'); ok = false; }
  if(!validateEmail(email)){ show(errEmail,'Email tidak valid'); ok = false; }
  if(!validatePhone(phone)){ show(errPhone,'Nomor telepon 9–15 digit'); ok = false; }
  if(!validateAddress(address)){ show(errAddress,'Alamat terlalu pendek'); ok = false; }

  if(!ok) return;

  const payload = { name, email, phone, address, created_at: new Date().toISOString() };

  console.log("Payload:", payload);

  form.reset();
showPopup();
});


resetBtn.addEventListener('click', function(){
  form.reset();
  clearErrors();
  hideSuccess();
});



const popup = document.getElementById("popupSuccess");
const btnGoDiscount = document.getElementById("btnGoDiscount");
const btnClosePopup = document.getElementById("btnClosePopup");


function showPopup() {
  popup.classList.remove("hidden");
}


btnGoDiscount.addEventListener("click", () => {
  window.location.href = "../PHP/member-diskon.php"; 
});


btnClosePopup.addEventListener("click", () => {
  popup.classList.add("hidden");
});
