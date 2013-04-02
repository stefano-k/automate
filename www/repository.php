<?
if (!isset($user))
    die();
?>
<table class="table table-condensed">
<tr>

<?

include 'functions.php';
cleanParams();


$distributions = array("debian", "ubuntu");

foreach($distributions as $distribution) {

    if (file_exists($repository_path."/".$distribution."/")) {

        echo "<td>";
        echo "<h5>".$distribution."</h5>";

        echo "<table class='table table-condensed'>";

        $packages = glob($repository_path."/".$distribution."/pool/main/*/*/*.deb");
        $prevpackage = "";
        foreach($packages as $packagefile) {
            $package = basename(dirname($packagefile));
            if ($package != $prevpackage) {
                $prevpackage = $package;
                echo "<tr><td colspan='2' style='vertical-align:top;'></td></tr>";
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
