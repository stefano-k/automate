<?
if (!isset($user))
    die();
?>
<table cellspacing=10 cellpadding=10>
<tr>

<?

$distributions = array("debian", "ubuntu");

foreach($distributions as $distribution) {
    
    if (file_exists($repository_path."/".$distribution."/")) {

        echo "<td>";
        echo "<h1>".$distribution."</h1>";

        echo "<table cellpadding='2'>";
        
        $packages = glob($repository_path."/".$distribution."/pool/main/*/*/*.deb");
        $prevpackage = "";
        foreach($packages as $packagefile) {
            $package = basename(dirname($packagefile));
            if ($package != $prevpackage) {
                $prevpackage = $package;
                echo "<tr><td colspan='2'><hr></td></tr>";
            }
            else {
                $package = "";
            }
            echo "<tr><td><strong>".$package."</strong></td><td>".basename($packagefile)."</td></tr>";
        }

        echo "</table>";

        echo "</td>";
    }
}
?>
</tr>
</table>
