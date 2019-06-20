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
    <link rel="stylesheet" href="<?= $config['themeDir'];?>style.css?ver=22" media="screen" type="text/css" />
    <link rel="stylesheet" href="<?= $config['themeDir'];?>jquery.dataTables_themeroller.min.css?ver=22" media="screen" type="text/css" />
    <link rel="shortcut icon" type="image/png" href="<?= $config['themeImagesDir'] ?>favicon.png"/>



    <script type="text/javascript" src="<?= $config['systemLangsDir'].$user->lang ?>/lang.js?ver=21"></script>
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



    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery.dragon.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery.dragon-slider.js"></script>
    <script type="text/javascript" src="<?= $config['systemLibsDir'] ?>jquery.Jcrop.js"></script>


    <script src="ckeditor/ckeditor.js"></script>


</head>
<body>
    <div id="container">



<?php 
if( isset($_SESSION) && isset($_SESSION["privacy"]) && $_SESSION["privacy"]==0 &&
    $user->role=="s"){
    ?>
    <div id="privacyDIV" style="color:#a94442;background-color:#f2dede;border-color:#ebccd1;padding:10px;border:1px solid transparent;border-radius:4px">
        <strong><?= ttPrivacy3; ?></strong>
        <a class="normal button" style="width:60px;padding:7px" id="login" onclick="privacy();">OK</a>
    </div>
    <script>
        function privacy(){
            $.ajax({
            url     : "index.php?page=admin/Acceptprivacy",
            success : function (data, status) {
                if(data == "ACK"){
			document.getElementById("privacyDIV").style.display="none"
                }else{
                    alert(data);
                }
            },
            error : function (request, status, error) {
                alert("jQuery AJAX request error:".error);
            }
            });
        }
    </script>
<?php
}
?>

