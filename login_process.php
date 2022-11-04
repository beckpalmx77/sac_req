<?php
session_start();
error_reporting(0);
include('config/connect_db.php');
include('config/lang.php');


if ($_SESSION['alogin'] != '') {
    $_SESSION['alogin'] = '';
}

//$my_file = fopen("Login.txt", "w") or die("Unable to open file!");
//fwrite($my_file, "Login " . " = " . $_POST['username']);
//fclose($my_file);


$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$remember = $_POST['remember'];
$sql = "SELECT *,mp.dashboard_page as dashboard_page FROM muser_account  
        left join mpermission mp on mp.permission_id = muser_account.account_type WHERE user_id=:username ";

$query = $conn->prepare($sql);
$query->bindParam(':username', $username, PDO::PARAM_STR);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

if ($query->rowCount() == 1) {
    foreach ($results as $result) {
        if (password_verify($_POST['password'], $result->user_password)) {
            $_SESSION['alogin'] = $result->user_id;
            $_SESSION['login_id'] = $result->id;
            $_SESSION['username'] = $result->user_id;
            $_SESSION['first_name'] = $result->first_name;
            $_SESSION['last_name'] = $result->last_name;
            $_SESSION['email'] = $result->email_address;
            $_SESSION['account_type'] = $result->account_type;
            $_SESSION['user_picture'] = $result->picture;
            $_SESSION['lang'] = $result->lang;
            $_SESSION['dashboard_page'] = $result->dashboard_page;

            //$my_file = fopen("D-Login-To.txt", "w") or die("Unable to open file!");
            //fwrite($my_file, "Data " . " = " . $result->user_id . " | " . $result->first_name . " | "
            //. $result->user_password . " | " . $result->account_type . " | " . $result->email_address . " | " . $result->lang);
            //fclose($my_file);

            if ($remember == "on") { // ถ้าติ๊กถูก Login ตลอดไป ให้ทำการสร้าง cookie
                setcookie("username", $_POST["username"], time() + (86400 * 30), "/");
                setcookie("password", $_POST["password"], time() + (86400 * 30), "/");
                setcookie("remember_chk", "check", time() + (86400 * 30), "/");
            } else {
                setcookie("username", "");
                setcookie("password", "");
                setcookie("remember_chk", "");
            }
            //echo $result->dashboard_page . ".php";
            echo $result->dashboard_page;

        } else {
            echo 0;
        }
    }
}