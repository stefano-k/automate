<?
if (!isset($user))
    die();

echo "<h2>incoming</h2>";

$files = glob($upload_path."/*");
if (count($files) > 0) {
    echo "<ul>\n";
    foreach($files as $file) {
        echo "<li>";
        echo basename($file)." <span class='label'>(".size_formatted($file).")</span>";
        echo "</li>\n";
    }
    echo "</ul>\n";
}
else {
    echo "nothing";
}
?>
