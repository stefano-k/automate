<?
include ("../common.php");
include ("../config.php");
include ("../functions.php");

cleanParams();

function create_user($username, $password, $type) {
    global $salt;
    $user_file = $username.".json";
    $user = array();
    $user['username'] = $username;
    $user['password'] = sha1($salt.$password);
    $user['type'] = $type;
    file_put_contents($user_file, json_encode($user));
}

// uncomment the following row to create an user
// user types: USER_ADMIN, USER_DEVELOPER, USER_GUEST

// create_user("guest", "guest", USER_GUEST);

?>
