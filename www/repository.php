<?
if (!isset($user))
    die();
?>
<table cellspacing=10 cellpadding=10>
<tr><td>
<h1>Ubuntu repository</h1>

<table cellpadding=2>
<?
$packages = glob($repository_path."/ubuntu/pool/main/*/*/*.deb");
$prevpackage = "";
foreach($packages as $packagefile) {
    $package = basename(dirname($packagefile));
    if ($package != $prevpackage) {
        $prevpackage = $package;
        echo "<tr><td colspan=2><hr></td></tr>";
    }
    else {
        $package = "";
    }
    echo "<tr><td><strong>".$package."</strong></td><td>".basename($packagefile)."</td></tr>";
}

?>

</table>

</td><td>

<h1>Debian repository</h1>

<table cellpadding=2>
<?
$packages = glob($repository_path."/debian/pool/main/*/*/*.deb");
$prevpackage = "";
foreach($packages as $packagefile) {
    $package = basename(dirname($packagefile));
    if ($package != $prevpackage) {
        $prevpackage = $package;
        echo "<tr><td colspan=2><hr></td></tr>";
    }
    else {
        $package = "";
    }
    echo "<tr><td><strong>".$package."</strong></td><td>".basename($packagefile)."</td></tr>";
}

?>

</table>

</td></tr>
</table>
