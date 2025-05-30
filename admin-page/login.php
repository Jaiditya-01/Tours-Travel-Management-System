<?php
session_start();

$host = "localhost";
$username = "root";
$password = "qwepoi"; // Replace with your actual DB password
$database = "TourTravelDB";

$conn = new mysqli($host, $username, $password, $database);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $plain_password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($plain_password)) {
        $error_message = "Please fill in all fields.";
    } else {
        $sql = "SELECT Customer_ID, Name, Password, User_Type FROM Customer WHERE Email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            // Case-insensitive User_Type check
            if (strtolower($row['User_Type'] ?? '') !== 'admin') {
                $error_message = "This page is for admin access only. User_Type: " . ($row['User_Type'] ?? 'NULL');
            } else {
                // Check password: SHA-1 truncated to 16 characters
                $stored_hash = $row['Password'];
                $input_hash = substr(sha1($plain_password), 0, 16);

                if ($input_hash === $stored_hash) {
                    $_SESSION['customer_id'] = $row['Customer_ID'];
                    $_SESSION['name'] = $row['Name'];
                    header("Location: index.php");
                    exit();
                } else {
                    $error_message = "Incorrect password. Please try again.";
                }
            }
        } else {
            $error_message = "User does not exist. Please check your email or sign up.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Bootstrap Material Admin by Bootstrapious.com</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="vendor/bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome CSS-->
    <link rel="stylesheet" href="vendor/font-awesome/css/font-awesome.min.css">
    <!-- Fontastic Custom icon font-->
    <link rel="stylesheet" href="css/fontastic.css">
    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">
    <!-- theme stylesheet-->
    <link rel="stylesheet" href="css/style.default.css" id="theme-stylesheet">
    <!-- Custom stylesheet - for your changes-->
    <link rel="stylesheet" href="css/custom.css">
    <!-- Favicon-->
    <link rel="shortcut icon" href="img/favicon.ico">
    <!-- Tweaks for older IEs-->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->
  </head>
  <body>
    <div class="page login-page">
      <div class="container d-flex align-items-center">
        <div class="form-holder has-shadow">
          <div class="row">
            <!-- Logo & Information Panel-->
            <div class="col-lg-6">
              <div class="info d-flex align-items-center">
                <div class="content">
                  <div class="logo">
                    <h1>Admin Dashboard</h1>
                  </div>
                  <p>Access the TourTravel Admin Panel.</p>
                </div>
              </div>
            </div>
            <!-- Form Panel -->
            <div class="col-lg-6 bg-white">
              <div class="form d-flex align-items-center">
                <div class="content">
                  <?php if ($error_message): ?>
                    <div class="alert alert-danger" role="alert">
                      <?php echo htmlspecialchars($error_message); ?>
                    </div>
                  <?php endif; ?>
                  <form id="login-form" method="post" action="login.php">
                    <div class="form-group">
                      <input id="login-email" type="email" name="email" required class="input-material">
                      <label for="login-email" class="label-material">Email Address</label>
                    </div>
                    <div class="form-group">
                      <input id="login-password" type="password" name="password" required class="input-material">
                      <label for="login-password" class="label-material">Password</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Login</button>
                  </form>
                  <a href="#" class="forgot-pass">Forgot Password?</a><br>
                  <small>Do not have an account? </small><a href="register.php" class="signup">Signup</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="copyrights text-center">
        <p>Design by <a href="https://bootstrapious.com/admin-templates" class="external">Bootstrapious</a></p>
      </div>
    </div>
    <!-- Javascript files-->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="vendor/popper.js/umd/popper.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="vendor/jquery.cookie/jquery.cookie.js"></script>
    <script src="vendor/chart.js/Chart.min.js"></script>
    <script src="vendor/jquery-validation/jquery.validate.min.js"></script>
    <!-- Main File-->
    <script src="js/front.js"></script>
  </body>
</html>