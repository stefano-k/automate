<?
if (!isset($user))
    die();

include 'functions.php';
cleanParams();


$build_file = $builds_path."/".$_GET['build']."/build.json";
$build = json_decode(file_get_contents($build_file), true);

$log_file = $builds_path."/".$_GET['build']."/log/".$_GET['dist']."_".$_GET['arch'].".log";
$res_file = $builds_path."/".$_GET['build']."/log/".$_GET['dist']."_".$_GET['arch'].".ret";
$update_file = $log_file.".update";

if (file_exists($log_file) || file_exists($update_file)) {

    function nice_row($row) {
        $row = htmlspecialchars ($row);
        $replaces = array(
            "error:" => "<span class='label label-important'>ERROR:</span>",
            "warning:" => "<span class='label label-warning'>WARNING:</span>",
            "*** WARNING ***" => "<span class='label label-warning'>*** WARNING ***</span>"
        );
        foreach($replaces as $search => $replace)
            $row = str_ireplace($search, $replace, $row);
        return $row;
    }

    $is_building = !file_exists($res_file);
    ?>
    <script type="text/javascript">
        function log_scroll(where) {
            if (where == "end") {
                $("#pre-log").scrollTop(
                    $("#pre-log")[0].scrollHeight - $("#pre-log").height()
                );
            }
            else {
                $("#pre-log").scrollTop(
                    0
                );
            }
        }
    </script>
    <?
    echo "<p><button onclick='log_scroll(\"start\");' class='btn btn-mini'>jump to start</button> ";
    echo "<button onclick='log_scroll(\"end\");' class='btn btn-mini'>jump to end</button></p>";
    flush();

    echo "<pre id='pre-log' class='pre-scrollable'>";
    if (file_exists($update_file)) {
        $rows = file($update_file);
        foreach ($rows as $row) {
            echo nice_row($row);
            flush();
        }
    }

    if (file_exists($log_file)) {
        $rows = file($log_file);
        foreach ($rows as $row) {
            echo nice_row($row);
            flush();
        }
    }
    echo "</pre>";
    if ($is_building) {
        echo "<div class='well well-small'><img src='img/load.gif'/> running build, automatic refresh enabled</div>";
    }

    if ($is_building) {
        $log_link = "index.php?instance=$instance&page=log&build=".$_GET['build']."&dist=".$_GET['dist']."&arch=".$_GET['arch'];
        ?>
        <script type="text/javascript">
            $().ready(function () {
                log_scroll("end");
                setTimeout(function(){ location.href = "<? echo $log_link ?>"; }, 5000);
            });
        </script>
        <?
    }
}
?>
