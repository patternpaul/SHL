<?php

    ini_set('display_errors', 1);
    ini_set('log_errors', 1);
    ini_set('error_log', dirname(__FILE__) . '/error_log.txt');
    error_reporting(E_ALL);
    require_once('./globals/globalIncludes.php');

    $message = "";
    if(isset ($_POST['loginIndicator'])){
        if($_POST['loginIndicator'] == 1){

            $message = login($_POST['username'],$_POST['password']);

        }else{
            logout();
        }
    }


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional //EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="SHL, Sunday Hockey League" />
    <meta name="keywords" content="shl, sunday, hockey, floor, floorhockey, league" />
    <meta name="author" content="Paul Everton  / Original design: Andreas Viklund - http://andreasviklund.com/" />
    


    <title>SHL</title>
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/themes/base/jquery-ui.css" type="text/css" media="all" />

    <link rel="stylesheet" href="/styles/bootstrap.min.css" />
    <link rel="stylesheet" href="/styles/custom.css" />
    <script src="https://www.google.com/jsapi?key=ABQIAAAALvDvl4dphzscfcSikdbXIhQusXYgS1TS2uIYp8pZLqolFpocNhQbxtDGIufuqRZ0wG3jjWNuah5nlQ" type="text/javascript"></script>

    <script language="Javascript" type="text/javascript">
        //load libs
        google.load("jquery", "1");
        google.load("jqueryui", "1");
    </script>
    

    <script src="/scripts/jquery.tablesorter.min.js"  type="text/javascript"></script>
    <script src="/scripts/jquery.tablesorter.widgets.js"  type="text/javascript"></script>
    <script src="/scripts/bootstrap-dropdown.js" type="text/javascript"></script>
    <script src="/scripts/bootstrap-twipsy.js" type="text/javascript"></script>
    <script type="text/javascript" src="/scripts/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
    
    <script src="//static.getclicky.com/js" type="text/javascript"></script>
   
    
    
    
    <script type="text/javascript">





    $(document).ready(function(){

            $.tablesorter.defaults.sortInitialOrder = 'desc';
        
            $(".player_stats").tablesorter( {
                sortList: [[0,0]],
                widgets: ["zebra", "columns"],
                cssAsc: "mySortAsc",
                cssDesc: "mySortDesc",
                cssHeader: "mySortUnsort"
               
            } );
            
            $(".matchup_stats").tablesorter( {
                sortList: [[1,1]],
                widgets: ["zebra", "columns"],
                cssAsc: "mySortAsc",
                cssDesc: "mySortDesc",
                cssHeader: "mySortUnsort"
               
            } );
            
            $(".player-info").tablesorter( {
                widgets: ["zebra"],
                headers: { 
                  // assign the secound column (we start counting zero) 
                  1: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  2: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  3: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    }
                }
               
            } );
            
            $(".menu_stats").tablesorter( {
                widgets: ["zebra"],
                headers: { 
                  // assign the secound column (we start counting zero) 
                  1: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  2: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  3: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    }
                }
               
            } );
            
            $(".last_game_stats").tablesorter( {
                widgets: ["zebra"],
                headers: { 
                  // assign the secound column (we start counting zero) 
                  1: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  2: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  3: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  4: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  5: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    },
                  6: { 
                    // disable it by setting the property sorter to false 
                    sorter: false 
                    }
                }
               
            } );
            
            //$(".zebra-striped tr:even").addClass("alt_hack");
            $('#topbar').dropdown();
            $('.twipsy-head').twipsy();
            
        });
    </script>
    
</head>

  <body>

    <div class="topbar" id="topbar">
      <div class="fill">
        <div class="container">
          <a class="brand" href="/">SHL</a>
          <ul class="nav">
              
            <?php 
                $ls_aboutActive = "";
                $ls_playerActive = "";
                $ls_recordActive = "";
                
                if($_SERVER['SCRIPT_NAME'] == "/aboutPage.php"){
                    $ls_aboutActive = "active";
                }elseif($_SERVER['SCRIPT_NAME'] == "/playerList.php"){
                    $ls_playerActive = "active";
                }elseif($_SERVER['SCRIPT_NAME'] == "/recordList.php"){
                    $ls_recordActive = "active";
                }


            ?>
             <li class="<?php echo $ls_aboutActive; ?>"><a href="/aboutPage.php">About</a></li>
            <li class="<?php echo $ls_playerActive; ?>"><a href="/playerList.php">Stats</a></li>
            <li class="<?php echo $ls_recordActive; ?>"><a href="/recordList.php">Records</a></li>
            
            <?php
                if(hasAccessLevel(1)){
            ?>
                <li class="dropdown">
                  <a href="#" class="dropdown-toggle">Admin</a>
                  <ul class="dropdown-menu">
                    <li><a href="/addEditPlayer.php">Add Player</a></li>
                    <li><a href="/addEditGame.php">Add Game</a></li>
                    <li><a href="/postList.php">Post List</a></li>
                    <li><a href="/addEditAbout.php">Edit About</a></li>
                    <li><a href="/requestList.php">Request List</a></li>
                  </ul>
                </li>
            
            <?php } ?>
          </ul>
          <form name="loginForm" method="post" id="myform" class="pull-right">
                <?php
                    if(isLoggedIn()){
                        
                        echo "<a href=\"/addEditPlayer.php?playerid=".userID()."\">[Edit Profile]</a>";
                ?>
              
                    <input type="hidden" name="loginIndicator" value="0">
                    <button class="btn" type="submit">Log Out</button>
               <?php
                    }else{
                ?>

                    <input type="hidden" name="loginIndicator" value="1" />
                    <input class="input-small" type="text" name="username" id="username" placeholder="Username" />
                    <input class="input-small" type="password" name="password" id="password" placeholder="Password" />
                    <button class="btn" type="submit">Sign in</button>

                <?php } ?>
              

          </form>
        </div>
      </div>
    </div>

    <div class="container">

      <div class="content">
        <div class="page-banner">
          <img src="/img/newshlbanner.jpg" width="100%"/>
        </div>
        <div class="row">
