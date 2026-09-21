// Demo auth: simple localStorage-backed users for UI testing only.
// Replace with real API calls (fetch POST) in production.

const $ = sel => document.querySelector(sel);
const $$ = sel => document.querySelectorAll(sel);

const btnLogin = $('#btnLogin');
const btnRegister = $('#btnRegister');
const loginForm = $('#loginForm');
const registerForm = $('#registerForm');
const message = $('#message');
const welcome = $('#welcome');
const welcomeText = $('#welcomeText');
const logoutBtn = $('#logout');
const demoFill = $('#demoFill');

function showMsg(text, type='') {
  message.textContent = text;
  message.className = 'message' + (type ? ' ' + type : '');
  setTimeout(() => {
    if (message.textContent === text) message.textContent = '';
  }, 4200);
}

function toggleForm(showRegister) {
  if (showRegister) {
    btnRegister.classList.add('active'); btnRegister.setAttribute('aria-selected','true');
    btnLogin.classList.remove('active'); btnLogin.setAttribute('aria-selected','false');
    loginForm.classList.add('hidden'); loginForm.setAttribute('aria-hidden','true');
    registerForm.classList.remove('hidden'); registerForm.setAttribute('aria-hidden','false');
  } else {
    btnLogin.classList.add('active'); btnLogin.setAttribute('aria-selected','true');
    btnRegister.classList.remove('active'); btnRegister.setAttribute('aria-selected','false');
    registerForm.classList.add('hidden'); registerForm.setAttribute('aria-hidden','true');
    loginForm.classList.remove('hidden'); loginForm.setAttribute('aria-hidden','false');
  }
}

// user store helpers
function loadUsers() {
  try { return JSON.parse(localStorage.getItem('demoUsers')||'{}'); } catch(e){return {}}
}
function saveUsers(u){ localStorage.setItem('demoUsers', JSON.stringify(u)); }

function registerUser(name, email, pass) {
  const users = loadUsers();
  const key = email.toLowerCase();
  if (users[key]) return {ok:false, msg:'Email already registered'};
  users[key] = {name, pass};
  saveUsers(users);
  return {ok:true};
}
function loginUser(email, pass) {
  const users = loadUsers();
  const key = email.toLowerCase();
  if (!users[key]) return {ok:false, msg:'No account found for that email'};
  if (users[key].pass !== pass) return {ok:false, msg:'Incorrect password'};
  return {ok:true, name: users[key].name};
}

// UI wiring
btnLogin.addEventListener('click', () => toggleForm(false));
btnRegister.addEventListener('click', () => toggleForm(true));

registerForm.addEventListener('submit', (ev) => {
  ev.preventDefault();
  const name = $('#regName').value.trim();
  const email = $('#regEmail').value.trim();
  const pass = $('#regPass').value;
  const pass2 = $('#regPass2').value;
  if (!name || !email || !pass) return showMsg('Please complete all fields', 'err');
  if (pass.length < 6) return showMsg('Password must be at least 6 characters', 'err');
  if (pass !== pass2) return showMsg('Passwords do not match', 'err');
  const res = registerUser(name, email, pass);
  if (!res.ok) return showMsg(res.msg, 'err');
  showMsg('Account created — you can sign in now', 'ok');
  // auto-switch to login
  toggleForm(false);
  $('#loginEmail').value = email;
});

loginForm.addEventListener('submit', (ev) => {
  ev.preventDefault();
  const email = $('#loginEmail').value.trim();
  const pass = $('#loginPass').value;
  if (!email || !pass) return showMsg('Enter email and password', 'err');
  const res = loginUser(email, pass);
  if (!res.ok) return showMsg(res.msg, 'err');
  // success
  showWelcome(res.name || 'Friend');
  showMsg('Signed in!', 'ok');
});

function showWelcome(name) {
  welcome.classList.remove('hidden');
  welcomeText.textContent = `Welcome, ${name}!`;
  // hide auth forms/card parts
  $('.forms').style.display = 'none';
  $('.switcher').style.display = 'none';
  message.textContent = '';
}

logoutBtn.addEventListener('click', () => {
  welcome.classList.add('hidden');
  $('.forms').style.display = '';
  $('.switcher').style.display = '';
  // clear inputs
  $$('#loginForm input, #registerForm input').forEach(i => i.value = '');
  showMsg('Logged out');
});

// Demo account button: creates/fills a demo user
demoFill.addEventListener('click', () => {
  const demo = {name:'MikuFan', email:'miku@example.test', pass:'secret123'};
  registerUser(demo.name, demo.email, demo.pass);
  $('#loginEmail').value = demo.email;
  $('#loginPass').value = demo.pass;
  showMsg('Demo account ready — press Sign in', 'ok');
});

// On load: if any user exists, show small hint
if (Object.keys(loadUsers()).length === 0) {
  // create small sample user for better demo UX (optional)
  registerUser('Sample', 'sample@example.test', 'password');
}