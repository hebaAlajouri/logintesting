<?php
include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

// Ensure $message is always an array
$message = []; 

if (isset($_POST['submit'])) {
    $email = trim($_POST['email']);
    $pass = trim($_POST['pass']);

    // Validate Email
    if (empty($email)) {
        $message[] = '<p style="color: red;">Email is required!</p>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = '<p style="color: red;">Invalid email format!</p>';
    }

    // Validate Password
    if (empty($pass)) {
        $message[] = '<p style="color: red;">Password is required!</p>';
    } elseif (strlen($pass) < 8) {
        $message[] = '<p style="color: red;">Password must be at least 8 characters!</p>';
    }

    // Only proceed if there are no validation errors
    if (empty($message)) {
        // Check if the user exists in the database
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
        $select_user->execute([$email]);
        $row = $select_user->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            // Verify password
            if (password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                header('location:home.php');
                exit();
            } else {
                $message[] = '<p style="color: red;">Incorrect password!</p>';
            }
        } else {
            $message[] = '<p style="color: red;">No account found with this email!</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        /* Global selection style */
        ::selection {
            background-color: #3399ff; /* Blue background */
            color: white; /* White text */
        }

        /* Specific selection style for paragraphs */
        p::selection {
            background-color: #ffcc00; /* Yellow background */
            color: black; /* Black text */
        }
    </style>
</head>
<body>

    <?php include 'components/user_header.php'; ?>

    <section class="form-container">
        <form action="" method="post">
            <h3>Login Now</h3>

            <?php
            // Ensure that $message is always an array and print out the errors
            if (is_array($message) && !empty($message)) {
                foreach ($message as $msg) {
                    echo $msg; // This will output each error message as a red-colored paragraph
                }
            }
            ?>

            <input type="email" name="email" required placeholder="Enter your email" maxlength="50" class="box" value="<?= htmlspecialchars($email ?? '') ?>" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="password" name="pass" required placeholder="Enter your password" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
            <input type="submit" value="Login" class="btn" style="color:#fff;" name="submit">

            <p>Don't have an account?</p>
            <a href="user_register.php" class="option-btn" style="color:#fff;">Register Now</a>
        </form>
    </section>

    <?php include 'components/footer.php'; ?>
    <script src="js/script.js"></script>

</body>
</html>