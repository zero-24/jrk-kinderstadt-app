<?php
/**
 * Main entry point for the public app
 *
 * @copyright  Copyright (C) 2023 - 2024 Tobias Zulauf. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License Version 2 or Later
 */

include '../../includes/publicApp.php';

header("content-security-policy: default-src 'self';");
?>
<html>
    <head>
        <title><?php echo SITE_TITLE_PUBLIC ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="robots" content="<?php echo SITE_ROBOTS ?>">
        <meta http-equiv="x-ua-compatible" content="IE=edge">
        <!-- Favicon -->
        <link rel="apple-touch-icon" sizes="120x120" href="../apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../favicon-16x16.png">
        <link rel="manifest" href="../site.webmanifest">
        <meta name="msapplication-TileColor" content="#da532c">
        <meta name="theme-color" content="#ffffff">
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="../media/css/bootstrap.min.css" integrity="sha384-2diKOETIi1xfrzQsm1wbWyFuiEELWcgL5bZMfLj0fZPSNrBaPVYW5sZfu2hBpKve" crossorigin="anonymous">
        <link rel="stylesheet" href="../media/css/fontawesome.css" />
    </head>
    <body>
        <div class="alert alert-info">
            <h3>Du wurdest abgemeldet.</h3>
        </div>
    </body>
</html>
