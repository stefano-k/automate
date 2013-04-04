<?
require_once("functions.php");
cleanParams();


include("config.php");
include("auth.php");
include("automate.php");

$build_id = (int) $_GET['build'];

if (isset($_GET['source'])) {
    $source_file = basename($_GET['source']);
    $filepath = $builds_path . "/" . $build_id . "/source/" . $source_file;
}
else {
    $build_file = basename($_GET['dist'])."/".basename($_GET['arch'])."/".basename($_GET['file']);
    $filepath = $builds_path . "/" . $build_id . "/result/" . $build_file;
}
$pathinfo = pathinfo($filepath);
$extension = $pathinfo['extension'];

if (file_exists($filepath)) {

    // open text files in browser instead of download them
    if (in_array($extension, array("dsc", "changes"))) {
        header("Content-Type: text/plain");
        header("Content-Disposition: filename=\"".basename($filepath)."\"");
    }
    else {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $filepath);
        finfo_close($finfo);
        header("Content-Description: File Transfer");
        header("Content-Type: ".$mime); //application/octet-stream
        header("Content-Disposition: attachment; filename=\"".basename($filepath)."\"");
    }
    header("Content-Length: " . filesize($filepath));
    ob_clean();
    flush();
    readfile($filepath);
    exit;
}

?>
