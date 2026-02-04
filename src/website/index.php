<?php include "includes/template.php";
/** @var $conn */ ?>

<title>Cyber City | Home</title>

<div class="container-fluid bg-body-tertiary py-5 mb-4 border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4 fw-bold text-uppercase">
                    Rebels: We Need Your Help
                </h1>
                
                <?php if (isset($_SESSION["username"])): ?>
                    <div class="badge rounded-pill text-bg-success mb-3 p-2 px-3">
                        <i class="bi bi-person-check-fill"></i> ACTIVE CONNECTION: <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    </div>
                    <p class="lead">The mainframe is open. Your contribution to the resistance is vital.</p>
                    <a href="pages/challenges/challengesList.php?projectID=2025" class="btn btn-primary btn-lg mt-2">Enter the City</a>
                <?php else: ?>
                    <div class="badge rounded-pill text-bg-warning mb-3 p-2 px-3">
                        <i class="bi bi-shield-lock-fill"></i> ENCRYPTED ACCESS ONLY
                    </div>
                    <p class="lead">Unauthorized identity detected. Please authenticate to join the cause.</p>
                    <div class="d-flex gap-2 mt-3">
                        <a href="login.php" class="btn btn-primary btn-lg px-4 shadow-sm">Log In</a>
                        <a href="register.php" class="btn btn-outline-secondary btn-lg px-4">Register</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4 text-start">
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0 bg-body-secondary">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">1</div>
                        <h2 class="card-title h4 mb-0 fw-bold">Beginnings</h2>
                    </div>
                    <p class="card-text opacity-75">
                        In 1850, a rural town was created, referred to as <strong>Latafa</strong>. This logging town brought great riches to its controllers before the 1898 Red Tuesday bushfires consumed it. Rebuilt in isolation, it eventually vanished from modern maps—lost to time and history.
                    </p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0 bg-body-secondary">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">2</div>
                        <h2 class="card-title h4 mb-0 fw-bold">Currently</h2>
                    </div>
                    <p class="card-text opacity-75">
                        <strong>Oak-Crack</strong> is the remains of the town, hidden by a natural forest barrier. The <strong>LTC</strong> has discovered that the <strong>TBW</strong> is cultivating a super-virus in a French Bio-Lab known as <em>Lab 404</em>, deep underground.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>