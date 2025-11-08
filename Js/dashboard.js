// Mask / unmask balance
const balText = document.getElementById('balText');
const maskBtn  = document.getElementById('maskBtn');
let masked = false;
const actualBalance = balText.textContent;

function renderMask(){
  if (masked){
    const dots = '••••••••';
    balText.textContent = dots;
    maskBtn.textContent = 'Show balance';
    maskBtn.setAttribute('aria-pressed','true');
  } else {
    balText.textContent = actualBalance;
    maskBtn.textContent = 'Hide balance';
    maskBtn.setAttribute('aria-pressed','false');
  }
}
maskBtn.addEventListener('click', () => { masked = !masked; renderMask(); });
renderMask();

// Quick Send — replace avatar src with your local assets
const quickSend = [
  { name:'M. Jennifer', img:'assets/avatars/jennifer.png' },
  { name:'A. Smith',    img:'assets/avatars/smith.png' },
  { name:'P. Taylor',   img:'assets/avatars/taylor.png' },
  { name:'L. Brown',    img:'assets/avatars/brown.png' },
  { name:'M. Jennifer', img:'assets/avatars/jennifer2.png' },
];

const qsList = document.getElementById('qsList');
qsList.innerHTML = quickSend.map(p => `
  <button class="avatar" role="listitem" aria-label="Send to ${p.name}">
    <span class="pic"><img src="${p.img}" alt=""></span>
    <small>${p.name}</small>
  </button>
`).join('');

// Transactions — demo data
const txns = [
  { title:'Airtime - MTN', sub:'Today • 10:42', amount:-5000 },
  { title:'Salary', sub:'Yesterday • ACH', amount:+1450000 },
  { title:'UMEME Power', sub:'Sep 12 • Token', amount:-78000 },
  { title:'Wallet Top-up', sub:'Sep 10 • Flutterwave', amount:+250000 },
];

const txnList = document.getElementById('txnList');
txnList.innerHTML = txns.map(t => `
  <li class="txn">
    <span class="badge">${t.amount < 0 ? '−' : '+'}</span>
    <div class="meta">
      <p class="title">${t.title}</p>
      <div class="sub">${t.sub}</div>
    </div>
    <div class="${t.amount < 0 ? 'amount-neg' : 'amount-pos'}">
      ${t.amount < 0 ? '-' : '+'} UGX ${Math.abs(t.amount).toLocaleString()}
    </div>
  </li>
`).join('');

// Example navigation hooks
document.getElementById('goTransfer').addEventListener('click', () => {
  window.location.href = 'transfer.html';
});
