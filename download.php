<?php
    //error_reporting(E_ALL); ini_set("display_errors", 1);

    date_default_timezone_set('UTC');

    include('./include/config.php');

    $Down = './docs/'.$_GET['file'];
?>

<html>
    <head>
        <meta http-equiv="refresh" content="0;url=<?php echo $Down; ?>">
    </head>
    <body>

        <?php
        $db = new PDO("mysql:host=".SQL_SERVER.";dbname=".SQL_DB, SQL_LOGIN, SQL_PWD);
        $db->query("INSERT INTO downloads (dateTime, file) VALUES(NOW(),'$Down')");

        ?>

    </body>
</html>
