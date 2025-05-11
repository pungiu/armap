<?php
session_start();

require_once 'db.php';

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = $_POST["geslo"];

    if (!empty($_POST["email"])) {
        $email = mysqli_real_escape_string($conn, $_POST["email"]);

        if (empty($username) || empty($password) || empty($email)) {
            $_SESSION['error_message'] = "Vsa polja so obvezna!";
                header("Location: register-page.php");
                exit();
        } else {
            $check_query = "SELECT * FROM usr WHERE username = '$username' OR mail = '$email'";
            $result = mysqli_query($conn, $check_query);

            if (mysqli_num_rows($result) > 0) {
                $_SESSION['register_error_message'] = "Uporabniško ime ali geslo že obstajata!";
                header("Location: register-page.php");
                exit();
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $insert_query = "INSERT INTO usr (username, password, mail)
                                VALUES ('$username', '$hashed_password', '$email')";

                if (mysqli_query($conn, $insert_query)) {
                    $_SESSION['success_message'] = "Registracija uspešna! Sedaj se lahko prijavite.";
                    header("Location: login-page.php");
                    exit();
                } else {
                    $_SESSION['register_error_message'] = "Napaka: " . mysqli_error($conn);
                    header("Location: register-page.php");
                    exit();
                }
            }
        }
    } else {
        if (empty($username) || empty($password)) {
            echo "Prosim vnesite uporabniško ime in geslo!";
        } else {
            $login_query = "SELECT * FROM usr WHERE username = '$username' OR mail = '$username'";
            $result = mysqli_query($conn, $login_query);

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);

                if (password_verify($password, $user["password"])) {
                    $_SESSION["loggedin"] = true;
                    $_SESSION["id"] = $user["id"];
                    $_SESSION["username"] = $user["username"];

                    header("location: https://pungi.org/");
                    exit;
                } else {
                    $_SESSION['error_message'] = "Napačno geslo!";
                    header("Location: login-page.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "Uporabnik ne obstaja!";
                header("Location: login-page.php");
                exit();

            }
        }
    }
}

mysqli_close($conn);
?>
