<?
if (!isset($user))
    die();

$build_file = $builds_path."/".$_GET['build']."/build.json";
$build = json_decode(file_get_contents($build_file), true);

$log_file = $builds_path."/".$_GET['build']."/log/".$_GET['dist']."_".$_GET['arch'].".log";
$update_file = $builds_path."/".$_GET['build']."/log/".$_GET['dist']."_".$_GET['arch'].".log.update";

if (file_exists($log_file) || file_exists($update_file)) {
    
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
    
}

?>
