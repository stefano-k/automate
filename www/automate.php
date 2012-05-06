<?
if (isset($_GET['instance']))
    $instance = $_GET['instance'];
else
    $instance = $instances[0];

$instance_path = $automate_path."/instances/".$instance;
if (!file_exists($instance_path."/automate.conf")) {
    die();
}

$builds_path = $instance_path."/builds";
$upload_path = $instance_path."/upload";
$queue_path = $instance_path."/queue";
?>
