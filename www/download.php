<?
include("config.php");
include("auth.php");
include("automate.php");

$build_id = $_GET['build'];
if (isset($_GET['source'])) {
    $source_file = $_GET['source'];
    $filepath = $builds_path."/".$build_id."/source/".$source_file;
}
else {
    $build_file = $_GET['file'];
    $filepath = $builds_path."/".$build_id."/result/".$build_file;
}
$extension = end(explode(".", $filepath));

if (file_exists($filepath)) {
    header("Content-Description: File Transfer");
    if (($extension == "dsc") || ($extension == "changes"))
        header("Content-Type: text/plain");
    else {
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"".basename($filepath)."\"");
    }
    //header("Content-Transfer-Encoding: binary");
    //header("Expires: 0");
    //header("Cache-Control: must-revalidate");
    //header("Pragma: public");
    header("Content-Length: " . filesize($filepath));
    ob_clean();
    flush();
    readfile($filepath);
    exit;
}

?>
