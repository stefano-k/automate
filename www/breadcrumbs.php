<ul class="breadcrumb" style="margin:0 0 5px;">
<li><a href="index.php">home</a> <span class="divider">/</span></li>
<?
	require_once("functions.php");
	cleanParams();

    echo "<li><a href='index.php?instance=$instance'>$instance</a> <span class='divider'>/</span></li> ";
    if (in_array($page, $items))
        echo "<li class='active'>$page</li>";
    elseif (in_array ($page, array("build", "log", "changes", "lintian"))) {
        echo "<li><a href='index.php?instance=$instance&amp;page=builds'>builds</a> <span class='divider'>/</span></li> ";
        $build_id = $_GET['build'];
        $build_file = $builds_path."/".$build_id."/build.json";
        $build = json_decode(file_get_contents($build_file), true);
        if ($page == "build")
            echo "<li class='active'>#".$build_id." - ".$build['package']." ".$build['version']."</li>";
        elseif (in_array($page, array("log", "changes", "lintian"))) {
            echo "<li><a href='index.php?instance=$instance&amp;page=build&amp;build=".$build_id."'>#".$build_id." - ".$build['package']." ".$build['version']."</a> <span class='divider'>/</span></li> ";
            if ($page == "log")
                echo "<li class='active'>".$_GET['dist']."/".$_GET['arch']." log</li>";
            elseif ($page == "changes") {
                if (isset($_GET['dsc']))
                    echo "<li class='active'>dsc</li>";
                else
                    echo "<li class='active'>changes</li>";
            }
            elseif ($page == "lintian")
                echo "<li class='active'>".$_GET['dist']."/".$_GET['arch']." lintian check</li>";
        }
    }
?>
</ul>
