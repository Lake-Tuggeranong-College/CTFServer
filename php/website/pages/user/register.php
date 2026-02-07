<?php 
    include "../../includes/template.php";
    /** @var $conn */

    // Security Check
    if (!authorisedAccess(true, true, true)) {
        header("Location:/");
        exit;
    }

    $errors = [];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = sanitise_data($_POST['username']);
        $password = sanitise_data($_POST['password']);
        $email = sanitise_data($_POST['email']);
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $accessLevel = 1;

        // check username in database using prepared statement for security
        $stmt = $conn->prepare("SELECT COUNT(*) FROM Users WHERE Username = ?");
        $stmt->execute([$username]);
        $numberOfUsers = (int)$stmt->fetchColumn();

        if ($numberOfUsers > 0) {
            $errors[] = "This username has already been taken.";
        } else {
            $sql = "INSERT INTO Users (Username, user_email, HashedPassword, AccessLevel, Enabled) VALUES (:newUsername, :newEmail, :newPassword, :newAccessLevel, 1)";
            $stmt = $conn->prepare($sql);
            $stmt->bindValue(':newUsername', $username);
            $stmt->bindValue(':newEmail', $email);
            $stmt->bindValue(':newPassword', $hashed_password);
            $stmt->bindValue(':newAccessLevel', $accessLevel);
            $stmt->execute();

            // Get the newly created ID
            $UserID = $conn->lastInsertId();

            $_SESSION["username"] = $username;
            $_SESSION["email"] = $email;
            $_SESSION['access_level'] = 1;
            $_SESSION['user_id'] = $UserID;

            $_SESSION["flash"] = ['type' => 'success', 'text' => 'Account Created! Welcome aboard.'];
            header("Location:../../index.php");
            exit;
        }
    }
?>

<style>
    body { background-color: #f8f9fa; }
    .register-container {
        min-height: 85vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 0;
    }
    .card-register {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 450px;
    }
    .btn-register {
        padding: 0.6rem;
        font-weight: 600;
        border-radius: 0.5rem;
    }
</style>

<div class="container register-container">
    <div class="card card-register">
        <div class="card-header text-center bg-transparent border-0 pt-4">
            <div class="mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-plus-fill text-success" viewBox="0 0 16 16">
                    <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                    <path fill-rule="evenodd" d="M13.5 5a.5.5 0 0 1 .5.5V7h1.5a.5.5 0 0 1 0 1H14v1.5a.5.5 0 0 1-1 0V8h-1.5a.5.5 0 0 1 0-1H13V5.5a.5.5 0 0 1 .5-.5z"/>
                </svg>
            </div>
            <h2 class="fw-bold">Create Account</h2>
            <p class="text-muted">Join us today!</p>
        </div>

        <div class="card-body px-4 pb-4">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger small py-2" role="alert">
                    <ul class="mb-0">
                        <?php foreach($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-floating mb-3">
                    <input type="text" name="username" class="form-control" id="regUsername" placeholder="Username" 
                           value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    <label for="regUsername">Username</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="email" name="email" class="form-control" id="regEmail" placeholder="name@example.com" 
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    <label for="regEmail">Email Address</label>
                </div>

                <div class="mb-3">
                    <div class="input-group">
                        <div class="form-floating flex-grow-1">
                            <input type="password" name="password" class="form-control" id="password" placeholder="Password" required>
                            <label for="password">Password</label>
                        </div>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" id="eyeIcon" viewBox="0 0 16 16">
                                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" name="formSubmit" class="btn btn-success btn-register">Register Now</button>
                </div>
            </form>
        </div>

        <div class="card-footer text-center py-3 bg-light border-0" style="border-radius: 0 0 1rem 1rem;">
            <small class="text-muted">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></small>
        </div>
    </div>
</div>

<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');

    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.classList.toggle('btn-secondary');
        this.classList.toggle('btn-outline-secondary');
    });
</script>