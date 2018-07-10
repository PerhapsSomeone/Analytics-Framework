<?php
/**
 * Created by PhpStorm.
 * User: marc
 * Date: 10.07.18
 * Time: 17:14
 */
$success = false;
require "classes.php";
try {
    $conn = db::getConn();

    $conn->query("CREATE TABLE `visitors` ( `ip` varchar(128) NOT NULL, `browser` varchar(200) NOT NULL, `platform` varchar(200) NOT NULL, `country` varchar(200) NOT NULL, `page` varchar(1000) NOT NULL, `countrycode` varchar(10) NOT NULL, `ineu` varchar(5) NOT NULL, `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `uuid` varchar(500) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

    $success = true;
} catch (Exception $e) {
    $success = false;
}

?>

<html>
<head>
    <title>Setup</title>
</head>
<body>
<div style="text-align: center;">
    <?php
    if ($success) {
        echo "<b>All done! Now adjust client.js settings and delete the setup file manually.</b>";
    } else {
        echo "<b>ERROR! Check if the DB credentials in classes.php are correct or wheter the table already exists.</b><br />";
        echo "Full stacktrace: " . $e;
    }
    ?>
</div>
</body>
</html>
