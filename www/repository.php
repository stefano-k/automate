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

$packages = array();
$sources = array();
foreach($dists as $dist) {
    foreach($archs as $arch) {
        $packages_array = "packages_".$dist."_".$arch;
        $$packages_array = json_decode(file_get_contents($repo_path."/packages-$dist-$arch.json"), true);
        foreach($$packages_array as $package) {
            if (!in_array($package['Package'], $packages)) {
                $packages[] = $package['Package'];
            }
        }
        $sources_array = "sources_".$dist."_".$arch;
        $$sources_array = json_decode(file_get_contents($repo_path."/sources-$dist-$arch.json"), true);
        foreach($$sources_array as $source) {
            if (!in_array($source['Package'], $sources)) {
                $sources[] = $source['Package'];
            }
        }
    }
}

foreach ($packages as $packagename) {
    $package = package_from_name($packagename, $packages_wheezy_i386);
    echo "<tr>";
    echo "<td>".$packagename."</td>";
    foreach($dists as $dist) {
        foreach($archs as $arch) {
            $packages_array = "packages_".$dist."_".$arch;
            $package_d_a = package_from_name($packagename, $$packages_array);
            if ($package_d_a != null)
                echo "<td>".$package_d_a['Version']."</td>";
        }
    }
    echo "</tr>";
}
echo "</table>";
?>
