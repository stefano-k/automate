<?

require_once("functions.php");
cleanParams();


$build_id = $_GET['build'];
$dist = $_GET['dist'];
$arch = $_GET['arch'];

$files = glob($builds_path."/".$build_id."/result/".$dist."/".$arch."/*.d??");

echo "<pre class='pre-scrollable'>\n";
foreach($files as $file) {
    $output = array();
    echo "<strong>".basename($file)."</strong><br/>\n";
    exec("lintian -I -E -i --pedantic --show-overrides $file", $output);
    foreach($output as $row) {
        $row = str_replace(array("  ", "<p>", "</p>"), array("&nbsp;&nbsp;", "", ""), $row);

        if ($row[0] == "E")
            echo "<span class='label label-important'>$row</span>";
        elseif ($row[0] == "W")
            echo "<span class='label label-warning'>$row</span>";
        elseif ($row[0] == "I")
            echo "<span class='label label-info'>$row</span>";
        elseif ($row[0] == "P")
            echo "<span class='label label-default'>$row</span>";
        elseif ($row[0] == "X")
            echo "<span class='label label-inverse'>$row</span>";
        elseif ($row != "N:")
            echo str_replace("N:&nbsp;&nbsp;&nbsp;&nbsp;", "", $row);

        echo "<br/>\n";
    }
    flush();
}
echo "</pre>\n";

?>
