<?php
    include 'config.php';

    // Function to validate names (should be 3-25 characters long, no numbers allowed)
    function validateName($name) {
        return preg_match("/^[a-zA-Z]{3,25}$/", $name);
    }

    // Function to validate password (should be at least 6 characters long, with uppercase, lowercase, number, and symbol)
    function validatePassword($password) {
        return preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{6,}$/", $password);
    }

    if (isset($_POST['submit'])) {
        // Retrieve user inputs
        $name = mysqli_real_escape_string($conn, $_POST['Name']);
        $Sname = mysqli_real_escape_string($conn, $_POST['Sname']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $cpassword = mysqli_real_escape_string($conn, $_POST['cpassword']);
        $user_type = $_POST['user_type'];

        // Validation errors array
        $message = [];

        // Check if name and surname are valid
        if (!validateName($name)) {
            $message[] = 'Name must be between 3 to 25 characters and contain only letters.';
        }

        if (!validateName($Sname)) {
            $message[] = 'Surname must be between 3 to 25 characters and contain only letters.';
        }

        // Check if the email already exists in the database
        $select_users = $conn->query("SELECT * FROM users_info WHERE email = '$email'") or die('Query failed');
        if (mysqli_num_rows($select_users) != 0) {
            $message[] = 'User already exists!';
        }

        // Check if passwords match
        if ($password != $cpassword) {
            $message[] = 'Passwords do not match!';
        }

        // Validate the password strength
        if (!validatePassword($password)) {
            $message[] = 'Password must be at least 6 characters long, with at least one uppercase letter, one lowercase letter, one number, and one special character.';
        }

        // If no validation errors, proceed with registration
        if (empty($message)) {
            // Inserting in db
            $insert_user = "INSERT INTO users_info(`name`, `surname`, `email`, `password`, `user_type`) 
                            VALUES('$name', '$Sname', '$email', '$password', '$user_type')";
            if (mysqli_query($conn, $insert_user)) {
                $message[] = 'Registration successful!';
            } else {
                $message[] = 'An error occurred while registering. Please try again later.';
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="css/register.css" />
    <title>Register</title>
    <style>
      .container2 {
        display: flex;
        justify-content: center;
        background-image: linear-gradient(45deg, rgba(0, 0, 3, 0.1), rgba(0, 0, 0, 0.5)), url(../bgimg/2.jpg);
        background-repeat: no-repeat;
        background-position: center;
        background-size: cover;
        height: 98vh;
      }

      .container form .link {
        text-decoration: none;
        color: white;
        border-radius: 17px;
        padding: 8px 18px;
        margin: 0px 10px;
        background: rgb(0, 0, 0);
        font-size: 20px;
      }

      .container form .link:hover {
        background: rgb(0, 167, 245);
      }

      .message {
        background-color: #f8d7da;
        color: #721c24;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
      }

      .message span {
        font-weight: bold;
      }
    </style>
  </head>

  <body>
    <?php
      if (isset($message)) {
        foreach ($message as $msg) {
          echo '<div class="message" id="messages"><span>' . $msg . '</span></div>';
        }
      }
    ?>

    <div class="container">
      <form action="" method="post">
        <h3 style="color:white">Register</h3>
        <input type="text" name="Name" placeholder="Enter Name" required class="text_field">
        <input type="text" name="Sname" placeholder="Enter Surname" required class="text_field">
        <input type="email" name="email" placeholder="Enter Email Id" required class="text_field">
        <input type="password" name="password" placeholder="Enter password" required class="text_field">
        <input type="password" name="cpassword" placeholder="Confirm password" required class="text_field">
        <select name="user_type" required class="text_field">
          <option value="User">User</option>
        </select>
        <input type="submit" value="Register" name="submit" class="btn text_field">
        <p>Already have an Account? <br> <a class="link" href="login.php">Login</a><a class="link" href="index.php">Back</a></p>
      </form>
    </div>

    <script>
      // Hide messages after 8 seconds
      setTimeout(() => {
        const box = document.getElementById('messages');
        if (box) {
          box.style.display = 'none';
        }
      }, 5000);
    </script>
  </body>
</html>
