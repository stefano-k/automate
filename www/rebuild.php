<?
if (!isset($user))
    die();

include 'functions.php';
cleanParams();


$build_id = $_GET['build'];
$dist = $_GET['dist'];
$arch = $_GET['arch'];
$build_file = $builds_path."/".$build_id."/build.json";
$build = json_decode(file_get_contents($build_file), true);

$res_file = $builds_path."/".$build_id."/log/".$dist."_".$arch.".ret";

if (file_exists($res_file)) {
    $res = intval(file_get_contents($res_file));

    if ($res != 0) {
        $queue = array(
            'build_id' => $build['build_id'],
            'package' => $build['package'],
            'version' => $build['version'],
            'maintainer' => $build['maintainer'],
            'changed_by' => $build['changed_by'],
            'source_dir' => $build['source_dir'],
            'dist' => $dist,
            'arch' => $arch
        );

        $queue_filename = $queue_path."/".$queue['build_id']."_".$queue['package']."_".$queue['version']."_".$queue['dist']."_".$queue['arch'].".json";

        echo "<h2>rebuild</h2>";
        if (file_put_contents($queue_filename, json_encode($queue))) {

            echo "build #".$build_id." (".$build['package'].".".$build['version'].") for ".$dist."/".$arch." restarted";

        }
        else {

            echo "error, cannot write $queue_filename";

        }

     }

}
?>
