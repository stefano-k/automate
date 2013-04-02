<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?
	include 'functions.php';
	cleanParams();

    include("common.php");
    include("config.php");
    include("functions.php");
    include("auth.php");
    include("automate.php");


    // page
    if (isset($_GET['page']))
        $page = $_GET['page'];
    else
        $page = 'builds';
?>
<head>
    <title>AutoMate [<? echo $page; ?>]</title>
    <link href="lib/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link rel="stylesheet" href="style.css" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico"/>
    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js'></script>
    <script src="lib/bootstrap/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <a class="brand" href="index.php">automate</a>
            <?
            if (isset($user)) {
                ?>
                <ul class="nav">
                    <li><a><? echo $user['username']; ?></a></li>
                    <li><a href="index.php?logout">Logout</a></li>
                </ul>
                <?
            }
            ?>
        </div>
    </div>
    <div class="btn-group">
    <?
    $items = array("builds", "incoming", "repository");
    if (in_array($page, $items))
        $mpage = $page;
    else
        $mpage = "builds";
    foreach($instances as $minstance) {
        $btnclasses = array("btn");
        if ($minstance == $instance) {
            $btnclasses[] = "btn-primary";
        }
        echo "<button onclick=\"location.href='index.php?instance=".$minstance."&amp;page=".$mpage."';\" class='".implode(" ", $btnclasses)."'>".$minstance."</button>\n";
    }
    ?>
    </div>
    <div class="btn-group">
    <?

    foreach($items as $item) {
        $btnclasses = array("btn");
        if ($item == $page)
            $btnclasses[] = "btn-primary";
        echo "<button onclick=\"location.href='index.php?instance=".$instance."&amp;page=".$item."';\" class='".implode(" ", $btnclasses)."'>".$item."</button>\n";
    }
    echo "</div>";
    echo "<br/>";

    if (!isset($user)) {
        ?>
        <div class="well">
        <div class="alert alert-info">
        Login required to use <strong>automate</strong>!
        </div>
        <form method="post" action="index.php">
        username<br/>
        <input type="text" style="width:150px;" name="username"><br/>
        password<br/>
        <input type="password" style="width:150px;" name="password"><br/>
        <br/>
        <button class="btn btn-info" type="submit" style="width:160px;">login</button>
        </form>
        <?
        if (isset($loginerror))
            if ($loginerror)
                echo "<br/><span class='error'>username and/or password wrong!</span>";
        ?>
        </div>
        <?
    }
    else {
        // breadcrumbs
        include("breadcrumbs.php");
        // page
        if (file_exists($page.".php"))
            include($page.".php");
        else
            echo "<h2>404 Error</h2>Page not found";
    }
    ?>
</body>
