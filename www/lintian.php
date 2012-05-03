<?
$build_id = $_GET['build'];
$dist = $_GET['dist'];
$arch = $_GET['arch'];

$files = glob($builds_path."/".$build_id."/result/".$dist."/".$arch."/*.d??");

echo "<div style='font-family:monospace; font-size:1.1em;'>";
foreach($files as $file) {
    $output = array();
    echo "<strong>".basename($file)."</strong><br/>";
    exec("lintian -I -E -i --pedantic --show-overrides $file", $output);
    foreach($output as $row) {
        $row = str_replace(array("  ", "<p>", "</p>"), array("&nbsp;&nbsp;", "", ""), $row);
        
        if ($row[0] == "E")
            echo "<span style='background-color:#FFA4A3;'>$row</span>";
        elseif ($row[0] == "W")
            echo "<span style='background-color:#FFFF86;'>$row</span>";
        elseif ($row[0] == "I")
            echo "<span style='background-color:#9BFF86;'>$row</span>";
        elseif ($row[0] == "P")
            echo "<span style='background-color:#B4D3FF;'>$row</span>";
        elseif ($row[0] == "X")
            echo "<span style='background-color:#CDCDCD;'>$row</span>";
        else
            echo $row;
        
        echo "<br/>";
    }
    echo "<br/><br/>";
    flush();
}
echo "</div>";

?>
