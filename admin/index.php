<?php

require_once '../lib-core.php';
require_once 'headerpage.php';
require_once '../lib-auth.php';
$auth = new polrauth();
$isadmin = $auth->isadminli();
if (!is_array($auth->islogged())) {
    echo "<h3>You must login to access this page.</h3><br><a href='index.php'>Home</a>";
    require_once '../layout-footerlg.php';
    die(); //END NOT LOGGED IN PORTION
} else {
    $userinfo = $auth->islogged();
    function fetchurls($lstart = 0) {
        global $userinfo;
        global $mysqli;
        $sqr = "SELECT `baseval`,`rurl`,`date`,`lkey` FROM `redirinfo` WHERE user = '{$mysqli->real_escape_string($userinfo['username'])}' LIMIT {$lstart} , 720;";
        $res = $mysqli->query($sqr);
        $links =  mysqli_fetch_all($res, MYSQLI_ASSOC);

        $linkshtml = '<table class="table table-hover"><tr><th>Link ending</th><th>Long Link</th><th>Date</th><th>Secret</th></tr>';
        foreach ($links as $link) {
            $is_secret = "False";
            if (strlen($link['lkey']) > 1) {
                $is_secret = "True";
            }
            $linkshtml = $linkshtml . "<tr><td>" . $link['baseval'] . '</td>'
                    . "<td>" . substr($link['rurl'], 0, 170) . '</td>'
                    . "<td>" . $link['date'] . '</td>'
                    . "<td>" . $is_secret . "</td>";
        }
        $linkshtml = $linkshtml . "</tr></table>";
        return $linkshtml;
    }

    $linkshtml = fetchurls();
    echo "<h3>Polr Dashboard</h3><br>";
    echo '<ul class="nav nav-tabs" id="tabsb">
            <li class="active"><a href="#home" data-toggle="tab">Home</a></li>
            <li><a href="#links" data-toggle="tab">My links</a></li>
            <li><a href="#messages" data-toggle="tab">Messages</a></li>
            <li><a href="#settings" data-toggle="tab">Settings</a></li>';
    if ($isadmin == true) {
        echo '<li><a href="#adminpanel" data-toggle="tab">Admin Panel</a></li>';

        function fetchurlsadmin($lstart = 0, $limit = 360) {
            global $mysqli;
            $sqr = "SELECT `baseval`,`rurl`,`date`,`user`,`ip`,`lkey` FROM `redirinfo` LIMIT {$lstart} , {$limit};";
            $res = $mysqli->query($sqr);
            $links = mysqli_fetch_all($res, MYSQLI_ASSOC);
            $linkshtml = '<table class="table table-hover"><tr><th>Link ending</th><th>Long Link</th><th>Date</th><th>Link Owner</th><th>IP</th><th>Secret</th><th>Disable/Enable</th></tr>';
            foreach ($links as $link) {
                $is_secret = "False";
                if (strlen($link['lkey']) > 1) {
                    $is_secret = "True";
                }
                $linkshtml = $linkshtml . "<tr><td>" . $link['baseval'] . '</td>'
                        . "<td>" . substr($link['rurl'], 0, 170) . '</td>'
                        . "<td>" . $link['date'] . '</td>'
                        . "<td>" . $link['user'] . '</td>'
                        . "<td>" . $link['ip'] . '</td>'
                        . "<td>" . $is_secret . "</td>";
                if ($link['rurl'] == 'disabled') {
                    $linkshtml = $linkshtml . '<td><span class=' . $link['baseval'] . '><input type="button" value="Enable" onClick="doenable(\'' . $link['baseval'] . '\');" class="btn btn-sm btn-success enablelink" id="' . $link['baseval'] . '" />' . '</span></td></tr>';
                } else {
                    $linkshtml = $linkshtml . "<td>" . '<span class=' . $link['baseval'] . '><input type="button" value="Disable" onClick="dodisable(\'' . $link['baseval'] . '\');" class="btn btn-sm btn-danger disablelink" id="' . $link['baseval'] . '" />' . '</span></td></tr>';
                }
            }
            $linkshtml = $linkshtml . "</tr></table>";
            return $linkshtml;
        }

        function fetchusersadmin($lstart = 0) {
            global $mysqli;
            $sqr = "SELECT `username`,`email`,`valid` FROM `auth` LIMIT {$lstart} , 720;";
            $res = $mysqli->query($sqr);
            $links = mysqli_fetch_all($res, MYSQLI_ASSOC);
            $usershtml = '<table class="table table-hover"><tr><th>Username</th><th>Email</th><th>Activated?</th></tr>';
            foreach ($links as $link) {
                $usershtml = $usershtml . "<tr><td>" . $link['username'] . '</td>';
                $usershtml = $usershtml . "<td>" . $link['email'] . '</td>';
                $usershtml = $usershtml . "<td>" . $link['valid'] . '</td></tr>';
            }
            $usershtml = $usershtml . "</tr></table>";
            return $usershtml;
        }

        $usersadmin = fetchusersadmin();
        $linksadmin = fetchurlsadmin();
    }
    if (!$isadmin) {
        // Shown to users
        $status = file_get_contents('https://raw.githubusercontent.com/Cydrobolt/polr/notices/unotices'); // fetch notices from Github
        if (strlen($status)<4) {
            $msges = '<div class="tab-pane" id="messages"><br><b>There are <span style="color:green">no new messages</span>.</b></div>';
        }
        else {
            $msges = '<div class="tab-pane" id="messages"><br>'.$status.'</div>';
        }
    }
    else if ($isadmin == true) {
        $status = file_get_contents('https://raw.githubusercontent.com/Cydrobolt/polr/notices/anotices'); // fetch notices from Github
        if (strlen($status)<4) {
            $msges = '<div class="tab-pane" id="messages"><br><b>There are <span style="color:green">no new messages</span>.</b></div>';
        }
        else {
            $msges = '<div class="tab-pane" id="messages"><br>'.$status.'</div>';
        }
    }

    echo '</ul>';
    echo '<div class="tab-content">
              <div class="tab-pane active" id="home"><br><h2>Welcome to '.$wsn.'\'s Polr dashboard.</div>
              <div class="tab-pane" id="links"><br>' . $linkshtml . '</div>

              '.$msges.'
              <div class="tab-pane" id="settings"><br>
              <h3>Change password</h3>
              <form action=\'ucp-settingsp.php\' method=\'POST\'>
                  <input type=\'hidden\' name=\'action\' value=\'changepw\' />
                  Old Password: <input type=\'password\' name=\'currpw\' />
                  New Password: <input type=\'password\' name=\'newpw\' />
                  <input type=\'submit\' class=\'btn btn-success\'/>
              </form>
          </div>';
    if ($isadmin == true) {
        echo '<div class="tab-pane" id="adminpanel"><br>Polr Links - Limited @ 720:' . $linksadmin . '<br>Polr Users - Limited @ 360:' . $usersadmin. '<script src="../js/ucp.js"></script>';
        if ($debug == 1) {
            '<br>Debug Variables: <br>Default IP Fetch: ' . $ip . '<br>X-Forwarded-For:' . @$headers['X-Forwarded-For'] . '<br>Forwarded-For' . @$headers['forwarded-for'];
        }
    }


    echo '</div>';
}
