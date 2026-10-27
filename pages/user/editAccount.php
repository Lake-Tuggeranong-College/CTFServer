<?php
ob_start();
include_once "../../includes/template.php";

/** @var PDO $conn */
if (!authorisedAccess(false, true, true)) {
    header("Location:../../index.php");
    exit;
}

//Avatar directory
$avatarDirectory = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/assets/img/profilePictures/';
$avatarUrlDirectory = '/assets/img/profilePictures/';
$allowedMimeTypes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp',
];
$maxAvatarBytes = 5 * 1024 * 1024; // 5 MB
$errorMessage = '';

// Fetch the user's current data from the database
$userId = $_SESSION['user_id'];
$query = "SELECT Username, user_email FROM Users WHERE ID = :userId";
$stmt = $conn->prepare($query);
$stmt->bindParam(':userId', $userId, PDO::PARAM_INT);   
$stmt->execute();
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitise the input data
    $username = sanitise_data($_POST['username']);
    $email = sanitise_data($_POST['email']);

// Validate and save the optional profile picture.
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload = $_FILES['avatar'];

        if ($upload['error'] !== UPLOAD_ERR_OK) {
            $errorMessage = 'The profile picture could not be uploaded. Please try again.';
        } elseif ($upload['size'] > $maxAvatarBytes) {
            $errorMessage = 'The profile picture must be 5 MB or smaller.';
        } elseif (!is_uploaded_file($upload['tmp_name'])) {
            $errorMessage = 'Invalid upload. Please choose the image again.';
        } else {
            $imageInfo = @getimagesize($upload['tmp_name']);
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($upload['tmp_name']);

            if ($imageInfo === false || !isset($allowedMimeTypes[$mimeType])) {
                $errorMessage = 'Please upload a valid JPG, PNG, or WebP image.';
            } else {
                if (!is_dir($avatarDirectory) && !mkdir($avatarDirectory, 0755, true) && !is_dir($avatarDirectory)) {
                    $errorMessage = 'The profile picture upload directory could not be created.';
                } elseif (!is_writable($avatarDirectory)) {
                    $errorMessage = 'The server cannot write to the profile picture directory.';
                } else {
                    $extension = $allowedMimeTypes[$mimeType];
                    $newAvatarPath = $avatarDirectory . $userId . '.' . $extension;

                    if (!move_uploaded_file($upload['tmp_name'], $newAvatarPath)) {
                        $errorMessage = 'The profile picture could not be saved. Please try again.';
                        $newAvatarPath = null;
                    } else {
                        // Remove any previous avatar with another supported extension.
                        foreach (array_values($allowedMimeTypes) as $oldExtension) {
                            $oldPath = $avatarDirectory . $userId . '.' . $oldExtension;
                            if ($oldPath !== $newAvatarPath && is_file($oldPath)) {
                                @unlink($oldPath);
                            }
                        }
                    }
                }
            }
        }
    }
    if ($errorMessage === '') {
	// Update the user's data in the database
        $query = "UPDATE Users SET Username = :newUsername, user_email = :newEmail WHERE ID = :userId";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':newUsername', $username);
        $stmt->bindParam(':newEmail', $email);
        $stmt->bindParam(':userId', $userId);
	    $stmt->execute();
	    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

	// Update the session data with new changes
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;

    // Flash message to indicate that the changes are done successfully
        $_SESSION["flash_message"] = "<div class='bg-success'>Account Updated!</div>";

    // Redirect to this page
	    header("Location:../../index.php");
	    exit(); // clear one-time usage resource as best practice
    }

        // Keep the submitted values on screen if the upload fails.
    $userData['Username'] = $username;
    $userData['user_email'] = $email;
}

// Find the current avatar.
$avatarUrl = null;

foreach (array_values($allowedMimeTypes) as $extension) {
    $candidatePath = $avatarDirectory . $userId . '.' . $extension;

    if (is_file($candidatePath)) {
        $avatarUrl = $avatarUrlDirectory . $userId . '.' . $extension . '?v=' . filemtime($candidatePath);
        break;
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row g-4">
        <!-- Profile Section -->
        <div class="col-md-4 text-center">
            <div class="card p-4">
                <?php if ($avatarUrl !== null): ?>
                    <img src="<?= htmlspecialchars($avatarUrl, ENT_QUOTES, 'UTF-8') ?>"
                         class="rounded-circle mx-auto" width="150" height="150"
                         style="object-fit: cover;" alt="Profile Picture">
                <?php else: ?>
                    <div class="rounded-circle mx-auto d-flex align-items-center justify-content-center bg-light"
                         style="width:150px;height:150px;" aria-label="No profile picture uploaded">
                        <i class="bi bi-person-circle" style="font-size:5rem;"></i>
                    </div>
                <?php endif; ?>

                <h5 class="mt-3"><?= htmlspecialchars($userData['Username'], ENT_QUOTES, 'UTF-8') ?></h5>
                <p><?= htmlspecialchars($userData['user_email'], ENT_QUOTES, 'UTF-8') ?></p>
            </div>
        </div>

        <!-- Edit Form Section -->
         <div class="col-md-8">
            <div class="card p-4">
                <h4 class="mb-4 text-center">Edit Account</h4>

                <?php if ($errorMessage !== ''): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form action="editAccount.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control"
                               value="<?= htmlspecialchars($userData['Username'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control"
                               value="<?= htmlspecialchars($userData['user_email'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="avatar" class="form-label">Profile picture</label>
                        <input type="file" id="avatar" name="avatar" class="form-control"
                               accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
                        <div class="form-text">Optional. JPG, PNG, or WebP; maximum 5 MB.</div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Flush the output buffer and turn off buffering
ob_end_flush();
?>