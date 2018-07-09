<?php
/**
 * Created by PhpStorm.
 * User: marc
 * Date: 09.07.18
 * Time: 18:36
 */
require "classes.php";
?>

<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.1/css/bulma.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css"
          integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
    <style>
        .centered {
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container is-fluid">
    <div class="notification centered">
        <div class="columns is-centered">
            <div class="column has-background-primary is-one-fifth\">
                <i class="fas fa-globe"></i>
            </div>
            <div class="column has-background-primary is-one-fifth\">
                <i class="fab fa-chrome"></i>
            </div>
            <div class="column has-background-primary is-one-fifth\">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="column has-background-primary is-one-fifth\">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="column has-background-primary is-one-fifth\">
                <i class="fas fa-user-secret"></i>
            </div>
        </div>

        <?php
        $visitors = db::recent50visitors();


        foreach ($visitors as $visitor) {
            echo "<div class=\"columns is-centered\">";

            echo "<div class=\"column has-background-primary is-one-fifth\">";
            echo($visitor["country"] . " (" . $visitor["countrycode"] . ")");
            echo "</div>";

            echo "<div class=\"column has-background-primary is-one-fifth\">";
            echo($visitor["platform"]);
            echo "</div>";

            echo "<div class=\"column has-background-primary is-one-fifth\">";
            echo($visitor["ineu"]);
            echo "</div>";

            echo "<div class=\"column has-background-primary is-one-fifth\">";
            echo($visitor["date"]);
            echo "</div>";

            echo "<div class=\"column has-background-primary is-one-fifth\">";
            echo(substr($visitor["ip"], 0, -100) . "...");
            echo "</div>";

            echo "</div>";
        }

        ?>
    </div>
</div>
</body>
</html>
