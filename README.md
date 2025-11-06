# 💸 Flowpesa — Save. Send. Grow. Together.

**Flowpesa** is a modern Africa-first fintech wallet built for everyday people and small communities (SACCOs, traders, and mobile workers).  
It enables users to **send, receive, save, and grow** their money seamlessly through **mobile money, bank, card, and agent channels** — starting with Uganda 🇺🇬.

---

## 🚀 Features

- 🌍 **Multi-channel deposits** — via bank, mobile money, agent, or card  
- 💸 **Peer-to-peer transfers** — send to contacts, @Flow tags, or bank accounts  
- 🏦 **SACCO saving groups** — community pots and personal goals  
- 🧠 **KYC tiers** — unlock higher limits with identity verification  
- 🧾 **Transaction receipts** — every action is timestamped and traceable  
- 💬 **In-app support** — chat and ticket history for transparency  

---

## 🧱 Project Structure

```bash
flowpesa/
├── index.html                 # Onboarding slides
├── create-account.html        # Phone number signup
<<<<<<< HEAD
├── verify-phone.html          # 6-digit verification screen
=======
├── verify-otp.html            # 6-digit verification screen
>>>>>>> e63a2dba45972b2976932632279679ed61f12aa1
├── css/
│   ├── style.css              # Unified UI/UX styling
│   └── vars.css               # Color & font variables
├── assets/                    # Images, icons, logos
└── README.md
```

## 🔐 Registration Flow

- `create-account.html` → `verify-phone.html` (OTP) → `verify-location.html` (country + street) → `verify-email.html` → `verify-id.html`

---

## ⚙️ Tech Stack

| Layer | Technology |
|-------|-------------|
| **Frontend** | HTML5, CSS3 (Inter & Poppins fonts) |
| **Styling** | Responsive Flex/Grid, Dark-mode design |
| **Backend (planned)** | PHP + MySQL + Flutterwave API |
| **Version Control** | Git & GitHub |
| **Deployment** | Test server → `test.flowpesa.com` |

---

<<<<<<< HEAD
## 🧑‍💻 Getting Started (Local Preview)

```bash
# 1. Clone the repo
git clone https://github.com/<your-username>/flowpesa.git

# 2. Open the folder
cd flowpesa

# 3. Run locally (any static server)
npx serve    # or "python -m http.server"

# 4. Open in browser
http://localhost:3000
```

---

=======
>>>>>>> e63a2dba45972b2976932632279679ed61f12aa1
## 🪄 UI/UX Principles

- **Simple > Fancy:** 3 taps to finish any action  
- **Trust by default:** visible balances, fees, receipts  
- **Offline-friendly:** lightweight pages, low data use  
- **Local-first:** tailored for African mobile networks  
- **Consistency:** unified spacing, color tokens, typography  

---

## 🌐 Integration Roadmap

- [x] Onboarding + Auth screens (HTML/CSS)
- [ ] Flutterwave payment integration  
- [ ] API endpoint for signup/login  
- [ ] KYC tier flow (Tier 0 → Tier 2)  
- [ ] Admin dashboard  

---

## 🧩 Branding

- **Primary color:** `#1682F9` (Teal-blue)  
- **Accent:** Dark gray `#111111`, white text  
- **Fonts:** [Inter](https://fonts.google.com/specimen/Inter) / [Montserrat](https://fonts.google.com/specimen/Montserrat)  
- **Tagline:** *Save. Send. Grow — Together.*

---

## 🤝 Contributing

Pull requests are welcome!  
If you’d like to contribute, fork the repository and open a PR with a clear description of what you’ve improved (UI/UX, integration, bug fix, etc).

---

## 🛡️ License

This project is under the **MIT License** — feel free to adapt for learning or personal use.

---

### 👑 Author
**Gerald Jamisco**  
Fintech Developer & Founder — Flowpesa  
📧 [gjamisco@flowpesa.com](mailto:gjamisco@flowpesa.com)

---

> “Built for Africa, made for you.”
