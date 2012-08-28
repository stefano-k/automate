<?
# this script create links to all instance repository,
# to make them available as http:// under cowbuilder

require ("../config.php");

foreach($instances as $instance) {
    $instance_path = $automate_path."/instances/".$instance;
    if (is_link($instance)) {
        echo "$instance already exists\n";
    }
    else {
        symlink($instance_path."/repository/", $instance);
        echo "$instance created\n";
    }
}
?>
