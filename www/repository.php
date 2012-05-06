<?
if (!isset($user))
    die();

echo "<h2>repository</h2>";

echo "<table>";
echo "<tr>";
echo "<td><strong>package</strong></td>";
foreach($dists as $dist) {
    foreach($archs as $arch) {
        echo "<td><strong>".$dist."/".$arch."</strong></td>";
    }
}
echo "</tr>";
?>
