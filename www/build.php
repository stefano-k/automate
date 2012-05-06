<?
if (!isset($user))
    die();

$build_file = $builds_path."/".$_GET['build']."/build.json";
$build_id = $_GET['build'];
$build = json_decode(file_get_contents($build_file), true);

echo "<h2>#".sprintf("%03s", $build['build_id'])." - ".$build['package']."-".$build['version']."</h2>";

echo "<table>";
echo "<tr><td style='font-weight:bolder; width:130px;'>Date:</td><td>".$build['timestamp']."</td></tr>";
echo "<tr><td style='font-weight:bolder;'>Package:</td><td>".$build['package']."</td></tr>";
echo "<tr><td style='font-weight:bolder;'>Version:</td><td>".$build['version']."</td></tr>";
echo "<tr><td style='font-weight:bolder;'>Uploader:</td><td>".htmlspecialchars($build['changed_by'])."</td></tr>";
echo "<tr><td style='font-weight:bolder;'>Dists:</td><td>".join(", ", $build['dists'])."</td></tr>";
echo "<tr><td style='font-weight:bolder;'>Archs:</td><td>".join(", ", $build['archs'])."</td></tr>";
$files_links = array(
    "<a href='index.php?instance=$instance&page=changes&build=".$build['build_id']."'>changes</a>",
    "<a href='index.php?instance=$instance&page=changes&build=".$build['build_id']."&dsc'>dsc</a>"
);
echo "<tr><td style='font-weight:bolder;'>Files:</td><td>".join(", ", $files_links)."</td></tr>";

// import status
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
        }
        $all_ok &= $result;
    }
}
echo "<tr><td style='font-weight:bolder; vertical-align:top;'>Import status:</td><td>";
if ($all_ok) {
    $import_log_file = $builds_path."/".$build_id."/import.log";
    $import_request_file = $builds_path."/".$build_id."/import.request";
    $import_ignore_file = $builds_path."/".$build_id."/import.ignore";
    $import_error_file = $builds_path."/".$build_id."/import.error";
    
    if ($user['type'] <= USER_DEVELOPER) {
        if (isset($_GET['import'])) {
            if (!file_exists($import_log_file) && !file_exists($import_request_file) && !file_exists($import_ignore_file) && !file_exists($import_error_file)) {
                if (file_put_contents($import_request_file, time()))
                    echo "(requested inclusion for package ".$build['package']."-".$build['version'].")<br/>";
                else
                    echo "(error requesting inclusion)<br/>";
            }
        }
        elseif (isset($_GET['ignore'])) {
            if (!file_exists($import_log_file) && !file_exists($import_request_file) && !file_exists($import_ignore_file) && !file_exists($import_error_file)) {
                if (file_put_contents($import_ignore_file, time()))
                    echo "(ignored inclusion for package ".$build['package']."-".$build['version'].")<br/>";
                else
                    echo "(error requesting ignore)<br/>";
            }
        }
    }
    
    if (file_exists($import_log_file)) {
        echo "Imported";
    }
    elseif (file_exists($import_request_file)) {
        echo "Requested";
    }
    elseif (file_exists($import_ignore_file)) {
        echo "Ignored (development build)";
    }
    elseif (file_exists($import_error_file)) {
        echo "Error";
    }
    else {
        if ($user['type'] <= USER_DEVELOPER) {
            $import_link = "index.php?instance=$instance&page=build&amp;build=$build_id&amp;import";
            $ignore_link = "index.php?instance=$instance&page=build&amp;build=$build_id&amp;ignore";
            echo "- <a href='$import_link'>import to repository</a><br/>";
            echo "- <a href='$ignore_link'>ignore this build</a><br/>";
        }
    }
}
else {
    echo "-";
}
    
echo "</td></tr>";

echo "</table>";

// SOURCE
echo "<br/>";
echo "<h2>source</h2>";
echo "<ul>";
$source_files = glob($builds_path."/".$_GET['build']."/source/*");
foreach($source_files as $source_file) {
    echo "<li>";
    echo "<a href='download.php?instance=$instance&build=".$_GET['build']."&amp;source=".
        str_replace($builds_path."/".$_GET['build']."/source/", "", $source_file)."'>";
    echo basename($source_file)."</a> <span class='label'>(".size_formatted($source_file).")</span>";
    echo "</li>";
}
echo "</ul>";

// BUILD RESULTS
foreach($build['dists'] as $dist) {
    foreach($build['archs'] as $arch) {
        
        echo "<h2>$dist/$arch</h2>";
        
        echo "<ul>";
        
        $log_file = $builds_path."/".$build['build_id']."/log/".$dist."_".$arch.".log";
        $update_file = $log_file.".update";
        
        $res_file = $builds_path."/".$build_id."/log/".$dist."_".$arch.".ret";
        if (file_exists($res_file)) {
            $res = intval(file_get_contents($res_file));
            if ($res != 0) {
                echo "<li><a href='index.php?instance=$instance&page=rebuild&amp;build=".$build['build_id'].
                "&amp;dist=$dist&amp;arch=$arch'>rebuild</a></li>";
            }
        }
        
        if (file_exists($log_file) || file_exists($update_file))
            echo "<li><a href='index.php?instance=$instance&page=log&amp;build=".$build['build_id'].
                "&amp;dist=$dist&amp;arch=$arch'>build log</a></li>";
        
        $result_files = glob($builds_path."/".$_GET['build']."/result/$dist/$arch/*");
        
        if (count($result_files) > 0) {
            echo "<li><a href='index.php?instance=$instance&page=lintian&amp;build=".$build['build_id'].
                "&amp;dist=$dist&amp;arch=$arch'>lintian check</a></li>";
        }
        
        foreach($result_files as $result_file) {
            echo "<li>";
            echo "<a href='download.php?instance=$instance&build=".$_GET['build']."&amp;file=".
                urlencode(str_replace($builds_path."/".$_GET['build']."/result/", "", $result_file))."'>";
            echo basename($result_file)."</a> <span class='label'>(".size_formatted($result_file).")</span>";
            echo "</li>";
        }
        echo "</ul>";
        
    }
}

$import_log_file = $builds_path."/".$build_id."/import.log";
if (file_exists($import_log_file)) {
    echo "<h2>import log</h2>";
    $rows = file($import_log_file);
    echo "<pre>";
    foreach ($rows as $row) {
        echo $row;
        flush();
    }
    echo "</pre>";
}

?>
