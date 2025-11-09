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

- `create-account.html` → `verify-phone.html` → `verify-email.html` → `set-passcode.html` → `confirm-passcode.html` → `verify-id-citizenship.html` → `verify-id-consent.html` → `verify-id-type.html` → `upload-…`

---

## ⚙️ Tech Stack

| Layer | Technology |
|-------|-------------|
| **Frontend** | HTML5, CSS3 (Inter & Poppins fonts) |
| **Styling** | Responsive Flex/Grid, Dark-mode design |
| **Backend (planned)** | PHP + MySQL + Flutterwave API |
| **Version Control** | Git & GitHub |
| **Deployment** | Test server → `test.flowpesa.com` |


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

## 🔒 Passcode Setup Flow

- Create passcode (`set-passcode.html`, `Js/set-passcode.js`)
  - User enters 6 digits.
  - When complete, store `sessionStorage.fp_first_passcode` and route to `confirm-passcode.html`.

- Confirm passcode (`confirm-passcode.html`, `Js/confirm-passcode.js`)
  - Compare input with `sessionStorage.fp_first_passcode`.
  - If mismatch: show red message + shake animation; let user try again.
  - If match: clear `fp_first_passcode` and continue to `verify-id-citizenship.html`.

Notes
- `verify-id-citizenship.html` is the intended next step and can be added later.
- Store only salted+hashed passcodes server‑side; client storage is temporary UX only.

### API Wiring (on Confirm success)

- Endpoint: `POST /api/auth/passcode/set`
- Request (JSON):
  - `phone`: `string` (e.g., `+256700000000`)
  - `passcode`: `string` (6 digits)
  - `client`: `"web" | "android" | "ios"`
  - `device_fingerprint` (optional): `string`
- Response (200 JSON):
  - `token`: `string` (JWT or opaque)
  - `next`: `string` (e.g., `"verify-id-citizenship.html"`)
- Errors: `400` invalid payload, `401` unauthorized/expired session, `409` passcode already set

Optional hardening later
- Challenge/response: server issues `{ salt_id, salt }`; client submits `{ phone, passcode_hash = sha256(passcode+salt), salt_id }`.

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

screenshots
Logo first show up as the preloader
<img width="686" height="754" alt="image" src="https://github.com/user-attachments/assets/cf3072a9-a409-4c72-93ab-1bfaca0d65eb" />


dashboard
<img width="341" height="752" alt="image" src="https://github.com/user-attachments/assets/b23379c1-ea1c-4e33-82b1-029f855b7df8" />



<img width="1888" height="867" alt="image" src="https://github.com/user-attachments/assets/26f3e0e8-3ebf-48ca-87ef-60a88cce4c2d" />












### 👑 Author
**Gerald Jamisco**  
Fintech Developer & Founder — Flowpesa  
📧 [gjamisco@flowpesa.com](mailto:gjamisco@flowpesa.com)

---

> “Built for Africa, made for you.”
