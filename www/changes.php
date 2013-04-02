<?
if (!isset($user))
    die();

require_once("functions.php");
cleanParams();


$build_file = $builds_path."/".$_GET['build']."/build.json";
$build = json_decode(file_get_contents($build_file), true);

if (isset($_GET['dsc'])) {
    $changes_file = glob($builds_path."/".$_GET['build']."/source/*.dsc");
}
else {
    $changes_file = glob($builds_path."/".$_GET['build']."/source/*.changes");
}
$changes_file = $changes_file[0];

if (file_exists($changes_file)) {

    echo "<h4>".basename($changes_file)."</h4>";
    flush();

    $rows = file($changes_file);

    echo "<pre class='pre-scrollable'>";
    foreach ($rows as $row) {
        echo $row;
        flush();
    }
    echo "</pre>";

}

?>
