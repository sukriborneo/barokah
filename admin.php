<?php
/*
 *  Copyright (C) 2018 Laksamadi Guko.
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();
// hide all error
error_reporting(0);

ob_start("ob_gzhandler");

// check url
$url = $_SERVER['REQUEST_URI'];

$id = $_GET['id'];
// load session MikroTik
$session = $_GET['session'];
$_SESSION["$session"] = $session;
$setsession = $_SESSION["$session"];

if (empty($id)) {
  echo "<script>window.location='./admin.php?id=sessions'</script>";
}
$router = $_GET['router'];
$logo = $_GET['logo'];

// load config
include_once('./include/headhtml.php');
include('./include/config.php');
include('./include/readcfg.php');
include_once('./lib/routeros_api.class.php');
include_once('./lib/formatbytesbites.php');
?>
    
<?php
if ($id == "login" || substr($url, -1) == "p") {

  if (isset($_POST['login'])) {
    $user = $_POST['user'];
    $pass = $_POST['pass'];
    if ($user == $useradm && $pass == decrypt($passadm)) {
      $_SESSION["mikhmon"] = $user;


        echo "<script>window.location='./admin.php?id=sessions'</script>";

    } else {
      $error = '<div style="width: 100%; padding:5px 0px 5px 0px; border-radius:5px;" class="bg-danger"><i class="fa fa-ban"></i> Alert!<br>Invalid username or password.</div>';
    }
  }

  include_once('./include/login.php');
} elseif (!isset($_SESSION["mikhmon"])) {
  echo "<script>window.location='./admin.php?id=login'</script>";
} elseif (substr($url, -1) == "/" || substr($url, -4) == ".php") {
  echo "<script>window.location='./admin.php?id=sessions'</script>";

} elseif ($id == "sessions") {
  $_SESSION["connect"] = "";
  include_once('./include/menu.php');
  include_once('./settings/sessions.php');
  echo '
  <script type="text/javascript">
    document.getElementById("sessname").onkeypress = function(e) {
    var chr = String.fromCharCode(e.which);
    if (" _!@#$%^&*()+=;|?,~".indexOf(chr) >= 0)
        return false;
    };
    </script>';
} elseif ($id == "settings") {
  include_once('./include/menu.php');
  include_once('./settings/settings.php');
  echo '
  <script type="text/javascript">
    document.getElementById("sessname").onkeypress = function(e) {
    var chr = String.fromCharCode(e.which);
    if (" _!@#$%^&*()+=;|?,~".indexOf(chr) >= 0)
        return false;
    };
    </script>';
} elseif ($id == "connect") {
  include_once('./include/menu.php');
  $API = new RouterosAPI();
  $API->debug = false;
  $API->connect($iphost, $userhost, decrypt($passwdhost));
  $getidentity = $API->comm("/system/identity/print");
  $identity = $getidentity[0]['name'];
  if ($identity == "") {
    $_SESSION["connect"] = "<b class='text-red'>Not connected</b>";
    echo "<script>window.location='./admin.php?id=settings&session=" . $session . "'</script>";
  } else {
    $_SESSION["connect"] = "<b class='text-green'>Connected</b>";
    echo "<script>window.location='./admin.php?id=settings&session=" . $session . "'</script>";
  }
} elseif ($id == "uplogo") {
  include_once('./include/menu.php');
  include_once('./settings/uplogo.php');
} elseif ($id == "reboot") {

  include_once('./process/reboot.php');
} elseif ($id == "remove" && $session != "") {
  include_once('./include/menu.php');

  $fc = file("./include/config.php");
  $f = fopen("./include/config.php", "w");
  foreach ($fc as $line) {
    if (!strstr($line, $session))
      fputs($f, $line);
  }
  fclose($f);
  echo "<meta http-equiv='refresh' content='0;url=./admin.php?id=sessions' />";
} elseif ($id == "about") {
  include_once('./include/menu.php');
  include_once('./include/about.php');
} elseif ($id == "logout") {
  include_once('./include/menu.php');
  echo "<b class='cl-w'><i class='fa fa-circle-o-notch fa-spin' style='font-size:24px'></i> Logout...</b>";
  session_destroy();
  echo "<script>window.location='./admin.php?id=login'</script>";
} elseif ($id == "remove" && $logo != "") {
  include_once('./include/menu.php');
  $logopath = "./img/";
  $remlogo = $logopath . $logo;
  unlink("$remlogo");
  echo "<script>window.location='./admin.php?id=uplogo&session=kemangi41'</script>";
} elseif ($id == "editor") {
  include_once('./include/menu.php');
  include_once('./settings/vouchereditor.php');
}
?>
<script src="js/mikhmon-ui.js"></script>

</body>
</html>