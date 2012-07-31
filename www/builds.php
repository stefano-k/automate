<?
if (!isset($user))
    die();

echo "<h2>builds</h2>\n";

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $max_results = 50;
}
else {
    $search = "";
    $max_results = 20;
}

echo "<form method='get' action='index.php'>";
echo "<input type='hidden' name='page' value='builds'/>";
echo "<input type='text' name='search' value='".$search."'/> ";
echo "<button type='submit'>search</button>";
echo "</form><br/>";

echo "<table style='width:100%;'>";

$i = 0;
$builds_folders = glob($builds_path."/*");
$builds = array();
foreach($builds_folders as $build_folder)
    $builds[] = basename($build_folder);
natsort($builds);
$builds = array_reverse($builds);

foreach ($builds as $build_id) {
    
    $build_file = $builds_path."/".$build_id."/build.json";
    $build = json_decode(file_get_contents($build_file), true);
    
    if ($search != "") {
        if (preg_match("/".$search."/", $build['package']) == 0) {
            continue;
        } 
    }
    
    $import_log_file = $builds_path."/".$build_id."/import.log";
    $import_request_file = $builds_path."/".$build_id."/import.request";
    $import_ignore_file = $builds_path."/".$build_id."/import.ignore";
    $import_error_file = $builds_path."/".$build_id."/import.error";
    
    $all_ok = true;
    foreach($build['dists'] as $dist) {
        foreach($build['archs'] as $arch) {
            $result = false;
            $res_file = $builds_path."/".$build_id."/log/".$dist."_".$arch.".ret";
            $log_file = $builds_path."/".$build_id."/log/".$dist."_".$arch.".log";
            if (file_exists($res_file)) {
                $res = intval(file_get_contents($res_file));
                if ($res == 0) {
                    $result = true;
                }
                $all_ok &= $result;
            }
        }
    }
    
    echo "<tr>";
    
    echo "<td style='width:20px;'><img src='img/system-run.png'/></td>";
    echo "<td style='width:60px;'>#".sprintf("%03s", $build['build_id'])."</td>";
    if (file_exists($import_request_file)) {
        $img = "aptdaemon-wait";
        $img_title = "import requested";
    }
    elseif (file_exists($import_ignore_file)) {
        $img = "aptdaemon-cleanup";
        $img_title = "ignored";
    }
    elseif (file_exists($import_log_file)) {
        $img = "aptdaemon-add";
        $img_title = "imported";
    }
    elseif (file_exists($import_error_file)) {
        $img = "aptdaemon-delete";
        $img_title = "import error";
    }
    elseif (!$all_ok) {
        $img = "aptdaemon-delete";
        $img_title = "build error";
    }
    else {
        $img = "aptdaemon-working";
        $img_title = "running";
    }
        
    echo "<td style='width:330px;'><img src='img/$img.png' title='$img_title'/> <a href='index.php?instance=$instance&page=build&build=".$build['build_id']."'>".$build['package']."-".$build['version']."</a></td>";
    echo "<td style='width:150px;'>".$build['timestamp']."</td>";
    //echo "<td>".htmlspecialchars($build['changed_by'])."</td>";
    
    echo "<td>";
    $all_ok = true;
    foreach($build['dists'] as $dist) {
        foreach($build['archs'] as $arch) {
            $result = false;
            $res_file = $builds_path."/".$build_id."/log/".$dist."_".$arch.".ret";
            $log_file = $builds_path."/".$build_id."/log/".$dist."_".$arch.".log";
            $update_file = $log_file.".update";
            if (!file_exists($res_file)) {
                if (!file_exists($log_file) && !file_exists($update_file)) {
                    $img = "visualization.png";
                }
                else {
                    $img = "load.gif";
                }
            }
            else {
                $res = intval(file_get_contents($res_file));
                if ($res == 0) {
                    $img = "gtk-yes.png";
                    $result = true;
                }
                else {
                    $img = "gtk-no.png";
                }
            }
            $all_ok &= $result;
            if (file_exists($log_file) || file_exists($update_file)) {
                $log_link = "index.php?instance=$instance&page=log&amp;build=$build_id&amp;dist=$dist&amp;arch=$arch";
                echo "<img src='img/$img'/> <a href='$log_link'>$dist/$arch</a> ";
            }
            else
                echo "<img src='img/$img'/> $dist/$arch ";
        }
    }
    echo "</td>";
    
    echo "<td>";
    if ($all_ok) {
        $import_log_file = $builds_path."/".$build_id."/import.log";
        $import_request_file = $builds_path."/".$build_id."/import.request";
        $import_ignore_file = $builds_path."/".$build_id."/import.ignore";
        $import_error_file = $builds_path."/".$build_id."/import.error";
        if (!file_exists($import_request_file) && !file_exists($import_ignore_file) 
                && !file_exists($import_log_file) && !file_exists($import_error_file)) {
            echo "<img src='img/system-run.png'/>";
        }
    }
    echo "</td>";
    
    echo "</tr>";
    
    flush();
    
    $i += 1;
    
    if (!isset($_GET['all'])) {
        if ($i >= $max_results) {
            break;
        }
    }
}
echo "</table>";

if (!isset($_GET['all'])) {
    if ($i >= $max_results) {
        echo "<ul><li><a href='index.php?instance=$instance&page=builds&amp;search=$search&amp;all'>show all</a></li></ul>";
    }
}

echo "<br/><br/>";
?>
