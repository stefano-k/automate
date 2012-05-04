<?
unset($user);

function login($username, $password, $hash) {
    global $user;
    $file = "users/$username.json";
    if(file_exists($file)) {
        $userfile = json_decode(file_get_contents($file), true);
        if ($hash) {
            $password = sha1($salt.$password);
        }
        if ($password == $userfile['password']) {
            $expires = time() + (60 * 60 * 24 * 60); // 60 days (2 months)
            setcookie("username", $username, $expires);
            setcookie("password", $password, $expires);
            $user = $userfile;
            return true;
        }
    }
    return false;
}

if (isset($_GET['logout'])) {
    $expires = time() - 1;
    setcookie("username", "", $expires);
    setcookie("password", "", $expires);
    unset($_COOKIE);
}
elseif (isset($_POST['username'])) {
    if (!login($_POST['username'], $_POST['password'], true)) {
        $loginerror = true;
    }
}
elseif (isset($_COOKIE['username'])) {
    login($_COOKIE['username'], $_COOKIE['password'], false);
}

?>
