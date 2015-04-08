<?php
if (!@include('config.php')) {
    header('Location:setup.php');
}
@session_start();
if (strlen($_SESSION['username']) < 1 && $li_show_front === true) {
    die("<h1>401 Unauthorized</h1><em><a href='login.php'>Login</a> to access this resource.</em>");
}

require_once('layout-headerlg.php');
?>
<h1 class='title'><?php require_once('config.php');echo $wsn;?> - beta</h1>
<h4>URL shorteners <strong>suck</strong>; they take us for <strong>sheep</strong>. Insecure lumbering garbage isn't acceptable.</h4>
<h4>Well, that's why <a href="https://gp.gg/">gp.gg</a> exists. Here's to the lost art of efficiency. -- <a href="https://twitter.com/pavgup">@pavgup</a></h2>
<form id='shortenform' method="POST" action="createurl.php" role="form">
    <input type="text" class="form-control" placeholder="URL" id="url" value="http://" name="urlr" />
    <div id='options'>
        <br />
        <div class="btn-group btn-toggle" data-toggle="buttons">
			<label class="btn btn-primary btn-sm active">
			  <input type="radio" name="options" value="p" checked=""> Public
			</label>
			<label class="btn btn-sm btn-default">
			  <input type="radio" name="options" value="s"> Secret
			</label>
	    </div> <br /><br />
        <br>Customize link: <br><div style='color: green'><h2 style='display:inline'><?php require_once('config.php');echo $wsa;?>/</h2><input type='text' id='custom' title='After entering your custom ending, if the ending is available, enter your long URL into box above and press "Shorten"!' name='custom' /><br>
            <a href="#" class="btn btn-inverse btn-sm" id='checkavail'>Check Availability</a><div id='status'></div></div>
    </div>
    <br><input type="submit" class="btn btn-info" id='shorten' value="Shorten!"/>   <a href="#" class="btn btn-warning" id='showoptions'>Link Options</a>
    <input type="hidden" id="hp" name="hp" value="<?php echo $hp; ?>"/>
</form>
<br><br><div id="tips" class='text-muted'><i class="fa fa-spinner"></i> Loading Tips...</div>
<?php require_once('layout-footerlg.php');
