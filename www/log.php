<?
if (!isset($user))
    die();

$build_file = $builds_path."/".$_GET['build']."/build.json";
$build = json_decode(file_get_contents($build_file), true);

$log_file = $builds_path."/".$_GET['build']."/log/".$_GET['dist']."_".$_GET['arch'].".log";
$res_file = $builds_path."/".$_GET['build']."/log/".$_GET['dist']."_".$_GET['arch'].".ret";
$update_file = $log_file.".update";

if (file_exists($log_file) || file_exists($update_file)) {
    
    $is_building = !file_exists($res_file);
    
    echo "<h2>".$build['package']." ".$build['version']." ".$_GET['dist']."/".$_GET['arch']."</h2>";
    flush();
    
    if (file_exists($update_file)) {
        $rows = file($update_file);
        echo "<pre>";
        foreach ($rows as $row) {
            echo htmlspecialchars($row);
            flush();
        }
        echo "</pre>";
    }
    
    if (file_exists($update_file)) {
        $rows = file($log_file);
        echo "<pre>";
        foreach ($rows as $row) {
            echo htmlspecialchars($row);
            flush();
        }
        echo "</pre>";
    }
    echo "<a id='end'>&nbsp;</a>\n";
    ?>
    <script type="text/javascript">
        $().ready(function () {
            $('html, body').animate({
                scrollTop: $("#end").offset().top
            }, 0);
            <?
            if ($is_building) {
                $log_link = "index.php?instance=$instance&page=log&build=".$_GET['build']."&dist=".$_GET['dist']."&arch=".$_GET['arch'];
                ?>
                setTimeout(function(){ location.href = "<? echo $log_link ?>"; }, 5000);
                <?
            }
            ?>
        });
    </script>
    <?
}

?>
