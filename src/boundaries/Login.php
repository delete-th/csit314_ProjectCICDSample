<!-- boundaries/Login.php -->
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Show logout success popup
$showLogoutPopup = false;
if (!empty($_SESSION['logout_success'])) {
    $showLogoutPopup = true;
    unset($_SESSION['logout_success']); // clear after showing
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        /* Simple modal styles */
        #logoutModal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        #logoutModalContent {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            width: 300px;
            text-align: center;
            border-radius: 5px;
        }
        #logoutModal button {
            padding: 5px 15px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once __DIR__ . '/../controllers/LoginController.php';

    $errorMessage = '';



    // Handle form submission
    if (isset($_POST['Login'])) {
        if (!empty(trim($_POST['name'])) && !empty(trim($_POST['pwd']))
            && !empty(trim($_POST['login_as']))) {
            $name = trim($_POST['name']);
            $password = trim($_POST['pwd']);
            $loginAs = trim($_POST['login_as']);

            $loginController = new LoginController();
            $result = $loginController->loginUser($name, $password, $loginAs);
			
			$action = $result[0];
			$userID = $result[1]; //user ID
			$_SESSION['userID'] = $userID;

            // If action is a redirection action, redirect
            if (strpos($action, 'Menu') !== false) {
                header("Location: index.php?action={$action}");
                exit();
            } 
            else {
                // Otherwise, treat it as an error message
                $errorMessage = $action;
            }

        } else {
            $errorMessage = "Please enter your username and password.";
        }
    }
?>
    <h1>Login</h1>

    <?php if (!empty($errorMessage)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($errorMessage); ?></p>
    <?php endif; ?>

    <form method="post" action="index.php?action=Login">
        <label for="name">Name:</label><br>
        <input type="text" name="name" required><br><br>

        <label for="pwd">Password:</label><br>
        <input type="password" name="pwd" required><br><br>

        <label>Login As:</label>
            <select name="login_as" required>
                <option value="User Admin">User Admin</option>
                <option value="PIN">PIN</option>
                <option value="CSR Rep">CSR Rep</option>
                <option value="Platform Manager">Platform Manager</option>
            </select><br><br>

        <input type="submit" name="Login" value="Login">
    </form>

	<!-- logout pop up -->
	<?php if ($showLogoutPopup): ?>
	<div id="logoutModal">
		<div id="logoutModalContent">
			<p>Logged out successfully</p>
			<button onclick="document.getElementById('logoutModal').style.display='none';">OK</button>
		</div>
	</div>
	<script>
		// Show the modal
		document.getElementById('logoutModal').style.display = 'block';
	</script>
	<?php endif; ?>
</body>
</html>
