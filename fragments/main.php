<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
    <meta name="viewport" content="width=device-width">
    <title>SCloud by susemine</title>

    <!--favicon-->
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="/<?=BASEPATH?>/_css/style.bootstrap.css">
    <link rel="stylesheet" href="/<?=BASEPATH?>/_css/style.main.css">
    <link rel="stylesheet" href="/<?=BASEPATH?>/_css/style.floater.css">

    <script src="/<?=BASEPATH?>/_js/jquery-1.8.3.min.js"></script>
</head>
<body>
    <div class="navbar navbar-inverse navbar-fixed-top">
        <div class="navbar-inner">
            <div class="container">
                <button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="brand" href="./">SCloud</a>
                <div class="nav-collapse collapse">
                    <ul class="nav">
                        <li><a onclick="display_about()">About</a></li>
                        <li><a href="mailto:susemeee@gmail.com">Contact</a></li>
                        <!-- Logout button only appears when logged in -->
                        <?php 
                        if(isset($_SESSION['user'])) 
                            echo "<li class='pull-right'><a href='_core/?action=logout'>Logout</a></li>";
                        ?>
                    </ul>
                </div><!--/.nav-collapse -->

            </div>
        </div>
    </div>
    <!-- about dialog, part of the mainframe -->
    <div class="black-box">
        
    </div>
    <div id="about" class="alertbox" onclick="dismiss()">
        <div class="alertbox-header black">
                <h3>About</h3>
        </div>
            <div class="alertbox-body">
                <h2>SCloud Beta by susemine*</h2>
                <i>A new way to save your files</i><br>
                drag &amp; drop file to upload<br>
                drag file around to organize<br>
                click file to download<br>
                hover the cursor on the file<br>
                click this dialog to dismiss<br>
                Copyright 2013 &copy; susemine*, coded in one week<br>
            </div>
    </div>
    <?php
    if(isset($_REQUEST['register'])){
        require_once($_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH.'/fragments/register.php');
    }
    else if(isset($_SESSION['user'])){
        require_once($_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH.'/fragments/filelist.php');
    }
    else{
        require_once($_SERVER['DOCUMENT_ROOT'].'/'.BASEPATH.'/fragments/loginform.php');
    }
    echo "<span class='time'>Server in development mode : ".((microtime(true) - $time)*1000)."ms</span>";
    ?>

    <script>
    function display_about(){
        $('.black-box').css('display', 'block');
        setTimeout(function(){$('.black-box').css('opacity', '0.6')}, 100);
        $('#about').css('display', 'block');
        //temporarily unbind all file event handlers
        //this does not work in loginpage
        unmake_floatable_all();
    }
    function dismiss(){
        $('.black-box').css('opacity', '0');
        //position should be relative to not interrupt loginform
        setTimeout(function(){$('.black-box').css('display', 'none');}, 400);
        $('#about').css('display', 'none');
        make_floatable_all(false);
    }
    </script>
</body>
</html>