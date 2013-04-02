<?
if (!isset($user))
    die();

include 'functions.php';
cleanParams();


if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $max_results = 50;
}
else {
    $search = "";
    $max_results = 12;
}

echo "<p>";
echo "<form method='get' action='index.php' style='display:inline;'>";
echo "<input type='hidden' name='instance' value='".$instance."'/>";
echo "<input type='hidden' name='page' value='builds'/>";
echo "<input class='input-medium search-query' type='text' name='search' value='".$search."' style='width:300px;'/> ";
echo "<button class='btn' type='submit'>search</button>&nbsp;";
echo "</form>\n";
if (!isset($_GET['all'])) {
    echo "<button class='btn' onclick=\"location.href='index.php?instance=$instance&page=builds&amp;search=$search&amp;all'\">show all builds</button>";
}
echo "</p>";

echo "<table style='width:100%;' class='table table-condensed'>";

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

    echo "<tr id='tr-build-".$build_id."'>";

    echo "<td style='width:43px;'><span class='badge badge-inverse'>".sprintf("%03s", $build['build_id'])."</span></td>";
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

    echo "<td style='width:350px;'>";
        echo "<img src='img/$img.png' title='$img_title'/>&nbsp;";
        echo "<a title='".htmlspecialchars($build['changed_by'])."' href='index.php?instance=$instance&page=build&build=".
            $build['build_id']."'>".$build['package']."-".$build['version']."</a>";
    echo "</td>";
    echo "<td style='width:90px;'><span style='font-size:0.6em;'>".$build['timestamp']."</span></td>";

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
                    $label = "info";
                }
                else {
                    $label = "warning";
                }
            }
            else {
                $res = intval(file_get_contents($res_file));
                if ($res == 0) {
                    $label = "success";
                    $result = true;
                }
                else {
                    $label = "important";
                }
            }
            $all_ok &= $result;
            if (file_exists($log_file) || file_exists($update_file)) {
                $log_link = "index.php?instance=$instance&page=log&amp;build=$build_id&amp;dist=$dist&amp;arch=$arch";
                echo "<span class='label label-$label'
                            onclick=\"window.open('$log_link');\"
                            style='cursor:pointer;'
                            title='open build log'>$dist/$arch</span> ";
            }
            else
                echo "<span class='label label-$label'>$dist/$arch</span> ";
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
            echo "<script>$('#tr-build-".$build_id."').addClass('success');</script>";
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

?>
<p></p>
<div class="well well-small">
    <strong>Legend:</strong>
    <span class="label label-success">Success</span>
    <span class="label label-important">Error</span>
    <span class="label label-warning">Building</span>
    <span class="label label-info">Queued</span>
</div>
