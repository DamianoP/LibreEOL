<?php
/**
 * File: doctype.php
 * User: Masterplan
 * Date: 3/15/13
 * Time: 7:39 PM
 * Desc: Doctype and library of all pages
 */

global $user, $engine, $config;

?>
<!doctype html>
<head>

    <meta charset=UTF-8 />

    <title><?= $config['systemTitle']; ?></title>

    <link rel="stylesheet" href="<?= $config['themeDir'];?>jquery-ui.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="<?= $config['themeDir'];?>style.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="<?= $config['themeDir'];?>jquery.dataTables_themeroller.min.css" media="screen" type="text/css" />
    <link rel="shortcut icon" type="image/png" href="<?= $config['themeImagesDir'] ?>favicon.png"/>

    <script type="text/javascript" src="<?= $config['systemLangsDir'].$user->lang ?>/lang.js"></script>
    <script> var imageDir = "<?= $config['themeImagesDir'] ?>"; </script>
    <script> var flagDir = "<?= $config['themeFlagsDir'] ?>"; </script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery-1.11.1.min.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery-ui.min.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>timepicker.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>essentials.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>countdown.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>autoresize.jquery.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery.lightbox_me.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>dataTables.jqueryui.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery.scrollTo.min.js"></script>

    <script src="ckeditor/ckeditor.js"></script>

</head>
<body>
    <div id="container">


