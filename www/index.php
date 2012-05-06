<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?
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
    <link rel="stylesheet" href="style.css" type="text/css"/>
    <link rel="shortcut icon" href="favicon.ico"/>
    <script type='text/javascript' src='http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js'></script>
</head>
<body>
    <h1>AutoMate</h1>
    <i>instances:</i>
    <?
    foreach($instances as $minstance) {
        if ($minstance == $instance) {
            echo "<strong>$minstance</strong> &nbsp;";
        }
        else {
            echo "<a href='index.php?instance=$minstance'>$minstance</a> &nbsp; ";
        }
    }
    ?>
    <p class="menu">
        <span style="float:right;">
        <?
        if (isset($user)) {
            echo "<img src='img/unknown-channel.png'/> ".$user['username']." &nbsp; ";
            echo "<a href='index.php?logout'>logout</a>";
        ?>
        </span>
        <a href='index.php?instance=<? echo $instance; ?>&page=builds'>builds</a> &nbsp;
        <a href='index.php?instance=<? echo $instance; ?>&page=incoming'>incoming</a> &nbsp;
        <a href='index.php?instance=<? echo $instance; ?>&page=repository'>repository</a> &nbsp;
        
        <?
    }
    else {
        ?></span>&nbsp;<?
    }
    ?>
    </p>
    
    <?
    if (!isset($user)) {
        ?>
        <div style="padding:5px; border:1px solid #4D4D4D;">
        <form method="post" action="index.php">
        username<br/>
        <input type="text" style="width:150px;" name="username"><br/>
        password<br/>
        <input type="password" style="width:150px;" name="password"><br/>
        <br/>
        <button type="submit" style="width:150px;">login</button>
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

        // page
        if (file_exists($page.".php"))
            include($page.".php");
        else
            echo "<h2>404 Error</h2>Page not found";
    }
    ?>
</body>
