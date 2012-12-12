<?
if (!isset($user))
    die();

$files = glob($upload_path."/*");
if (count($files) > 0) {
    echo "<div class='well well-small'><ul class='unstyled'>\n";
    foreach($files as $file) {
        echo "<li>";
        echo basename($file)." <span class='label'>".size_formatted($file)."</span>";
        echo "</li>\n";
    }
    echo "</ul></div>\n";
}
else {
    echo "<div class='well well-small muted'>directory is empty</div>";
}
?>
