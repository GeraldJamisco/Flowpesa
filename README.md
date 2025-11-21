# 💸 Flowpesa — Save. Send. Grow. Together.

**Flowpesa** is an Africa-first digital wallet designed for everyday people, small communities, and SACCO groups.  
It enables users to **send, receive, save, and manage money seamlessly** through mobile money, bank, card, and agent channels starting in Uganda :uganda:

---

## 🚀 Key Features

- 🌍 **Multiple deposit channels** — mobile money, bank transfer, card, or agent  
- 💸 **P2P transfers** — send to contacts, Flow tags, or bank accounts  
- 🏦 **SACCO saving groups** — shared pots and personal saving goals  
- 🔐 **Tiered KYC system** — increase limits with verified ID  
- 🧾 **Transaction receipts** — every action tracked and timestamped  
- 💬 **In-app support** — helpdesk and ticket history

---

## 🧱 Project Overview

Flowpesa is built with a simple, scalable structure:

Flowpesa/
│
├── api/ → Backend PHP endpoints
│ ├── auth/ → Registration, login, tokens
│ ├── wallet/ → Balance, transfers, transactions
│ ├── kyc/ → ID uploads & review status
│ ├── payment/ → Flutterwave payment init & webhooks
│ └── helpers/ → Shared utilities
│
├── public/ → Web UI (HTML/CSS/JS)
│ ├── login.php
│ ├── register.php
│ └── dashboard.php
│
├── assets/ → CSS, JS, images
│
└── docs/ → Flows, architecture, API notes


> Sensitive configuration lives in `.env` or `config.local.php` (not included in the repository).

---

## 🌐 High-Level Architecture

- **Client:** HTML/CSS/JS  
- **Backend:** Modular PHP API + MySQL  
- **Payments:** Flutterwave (collections + webhook validation)  
- **KYC:** Step-based ID verification tiers  
- **Security:** Token authentication, hashed passcodes, server-side validation  

Core flows (registration, KYC, payments) are documented in the `/docs/` folder.

---

## ⚙️ Tech Stack

| Layer | Technology |
|-------|-----------|
| **Frontend** | HTML5, CSS3 (Inter / Montserrat) |
| **Backend** | PHP API + MySQL |
| **Payments** | Flutterwave |
| **Version Control** | Git & GitHub |
| **Deployment** | Test server → `test.flowpesa.com` |

---

## 🧩 UI/UX Principles

- **Simple > Fancy** — complete actions in 3 taps  
- **Trust First** — transparent balances & fees  
- **Lightweight** — optimized for African networks  
- **Consistent** — spacing, colors, typography  
- **Mobile-priority** — built for small screens  

---

## 📌 Roadmap (2025)

- [x] Onboarding & authentication UI  
- [ ] API for registration & login  
- [ ] Flutterwave integration  
- [ ] Tiered KYC (Tier 0 → Tier 2)  
- [ ] Admin dashboard  
- [ ] PWA support + offline mode  
- [ ] SACCO groups engine  

---

## 🖼️ Screenshots

Screenshots are available in:

/screenshots


(Preloader, dashboard, and UI previews.)

---

## 🚀 Getting Started (Local Development)

1. Clone the repository  
   ```bash
   git clone https://github.com/GeraldJamisco/Flowpesa

🤝 Contributing

Contributions are welcome!
Open a pull request with clear details on UI, backend, API, or integration improvements.

🛡️ License

Released under the MIT License.

📇 Maintainer

Flowpesa Development Team
📧 support@flowpesa.com

Flowpesa — Built for Africa. Made for you.







