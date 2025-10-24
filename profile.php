<?php require __DIR__.'/require_login.php'; ?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Flowpesa — Profile</title>

  
   <link rel="icon" href="assets/flowpesa-icon.png">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
  <script src="js/profile.js" defer></script>
</head>
<body>
  <div class="stage">
    <div class="title">Profile & Settings</div>

    <div class="phone">
      <!-- Header -->
      <div class="hero">
        <div class="hello">Account</div>
        <div class="hero-row">
          <div class="d-flex align-items-center gap-3">
            <label class="avatar" for="avatarInput">
              <img id="avatarImg" alt="Avatar" />
              <span class="avatar-edit"><i class="bi bi-camera"></i></span>
            </label>
            <div>
              <div class="name" id="pName">—</div>
              <div class="text-secondary small" id="pEmail">—</div>
            </div>
          </div>
          <div class="text-end">
            <div class="badge bg-dark rounded-pill px-3 py-2" id="tierBadge">Tier 0</div>
          </div>
        </div>
      </div>

      <!-- Body -->
      <div class="body">


        <!-- Edit Profile Modal -->
<div class="modal-sheet" id="editModal" hidden>
  <div class="modal-backdrop" data-close></div>
  <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="editTitle">
    <div class="modal-head">
      <b id="editTitle">Edit Profile</b>
      <button class="modal-x" data-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
    </div>
    <form id="editForm" class="modal-body">
      <label class="label">Full name</label>
      <input id="fName" class="input" placeholder="Your name" required />

      <label class="label mt-2">Email</label>
      <input id="fEmail" type="email" class="input" placeholder="you@example.com" required />

      <label class="label mt-2">Phone</label>
      <input id="fPhone" class="input" placeholder="+256 700 000000" required />

      <label class="label mt-2">Country</label>
      <select id="fCountry" class="select" required>
        <option value="Uganda">Uganda</option>
        <option value="Kenya">Kenya</option>
        <option value="Tanzania">Tanzania</option>
        <option value="Rwanda">Rwanda</option>
      </select>

      <div class="modal-foot">
        <button type="button" class="btn btn-outline-dark" data-close>Cancel</button>
        <button type="submit" class="btn btn-dark">Save</button>
      </div>
    </form>
  </div>
</div>

        <!-- KYC progress -->
        <div class="section-card">
          <div class="section-title"><b>KYC Progress</b><a href="#" class="text-secondary small" id="continueKyc">Continue</a></div>
          <div class="progress" style="height:10px" aria-label="KYC progress">
            <div id="kycBar" class="progress-bar bg-dark" style="width:0%"></div>
          </div>
          <div class="small text-secondary mt-1" id="kycNote">Complete verification to unlock higher limits.</div>
        </div>

        <!-- Account details -->
        <div class="section-card">
          <div class="section-title"><b>Account Details</b><a href="#" class="text-secondary small" id="editProfile">Edit</a></div>
          <div class="list">
            <div class="rowi"><span><i class="bi bi-person"></i> Full name</span><b id="dName">—</b></div>
            <div class="rowi"><span><i class="bi bi-envelope"></i> Email</span><b id="dEmail">—</b></div>
            <div class="rowi"><span><i class="bi bi-telephone"></i> Phone</span><b id="dPhone">—</b></div>
            <div class="rowi"><span><i class="bi bi-geo-alt"></i> Country</span><b id="dCountry">—</b></div>
          </div>
        </div>

        <!-- Security -->
        <div class="section-card">
          <div class="section-title"><b>Security</b></div>
          <div class="list">
            <button class="rowi btn-linkish" id="changePin"><span><i class="bi bi-key"></i> Change PIN</span><i class="bi bi-chevron-right"></i></button>
            <button class="rowi btn-linkish" id="resetPwd"><span><i class="bi bi-lock"></i> Reset Password</span><i class="bi bi-chevron-right"></i></button>
          </div>
        </div>

        <!-- Preferences -->
        <div class="section-card">
          <div class="section-title"><b>Preferences</b></div>
          <div class="list">
            <label class="rowi">
              <span><i class="bi bi-moon"></i> Dark mode</span>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" id="prefDark">
              </div>
            </label>
            <label class="rowi">
              <span><i class="bi bi-bell"></i> Notifications</span>
              <div class="form-check form-switch m-0">
                <input class="form-check-input" type="checkbox" id="prefNotify" checked>
              </div>
            </label>
          </div>
        </div>

        <!-- Danger / Logout -->
        <div class="section-card">
          <button class="btn btn-dark w-100" id="logout"><i class="bi bi-box-arrow-right"></i> Log out</button>
        </div>
      </div>

      <!-- Bottom nav -->
      <nav class="nav">
        <a href="dashboard.php"><i class="bi bi-house"></i>Home</a>
        <a href="#"><i class="bi bi-receipt"></i>Bills</a>
        <a href="wallet.php"><i class="bi bi-wallet2"></i>Wallet</a>
        <a href="profile.php" class="active"><i class="bi bi-person-fill"></i>Profile</a>
      </nav>
    </div>
  </div>

  <!-- hidden file input for avatar -->
  <input id="avatarInput" type="file" accept="image/*" style="display:none"/>
</body>
</html>
