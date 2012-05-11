<?
if (!isset($user))
    die();

echo "<h2>repository</h2>";

$packages = glob($repository_path."/*/pool/main/*/*/*.deb");
$prevpackage = "";
foreach($packages as $packagefile) {
    $package = basename(dirname($packagefile));
    if ($package != $prevpackage) {
        echo "<strong>".$package."</strong><br/>";
        $prevpackage = $package;
    }
    echo basename($packagefile)."<br/>";
}

?>
