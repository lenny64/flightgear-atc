
<?php


// Variable to see if there is a login entered
$err_noLogin = true;

// If the user wants to log in
if (isset($_POST['email']) AND isset($_POST['password']) AND $_POST['email'] != NULL AND $_POST['password'] != NULL)
{
    $err_noLogin = false;

    // Are the information correct ?
    if ($_POST['email'] == getInfo('mail', 'users', 'mail', $_POST['email']) AND $_POST['password'] == getInfo('password', 'users', 'mail', $_POST['email']))
    {
        $userAuthenticated = true;
        $User = new User();
        $User->selectById(getInfo('userId', 'users', 'mail', $_POST['email']));
        $User->connect($User->id);
    }
    else
    {
        $userAuthenticated = false;
    }
}
// If there is already a session open
else if ($_SESSION['mode'] == 'connected')
{
    $err_noLogin = false;
    $userAuthenticated = true;
}

// Finally, if there is an error during the login
if ($err_noLogin == true OR $userAuthenticated == false)
{
    ?>
    <div class="container">
      <div class="jumbotron" id="jumbotron_mainPage" style="background: #f0f0f0 url('./img/header_ATCDashboard.jpg') no-repeat center center;">
        <div id='bg-overlay'>
            <div class="container">
                <h2 id="depecheMainTitle">ATCs are the masters of the sky</h2>
            </div>
        </div>
    </div>

    <?php
    if (isset($userAuthenticated) AND $userAuthenticated == false) { echo "<div class='alert alert-info'>Your password and/or e-mail is not correct</div>"; }
    if ($err_noLogin == true) { echo "<div class='alert alert-info'>Please enter your e-mail and password</div>"; }
    ?>
    <br/>
    <?php
    // If the user wanted to edit the event
    if (isset($_GET['eventId']) AND $_GET['eventId'] != NULL) echo '<form role="form" action="./edit_event.php?eventId='.$_GET['eventId'].'" method="post">';
    // Else if the user wanted to just log in
    else echo '<form class="dashboard_connectionForm" role="form" action="./dashboard.php" method="post">'; ?>
        <div class="form-group">
            <label for="email">E-mail</label>
            <input class="form-control" type="text" name="email" id="email">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input class="form-control" type="password" name="password" id="password">
        </div>
        <button type="submit" class="btn btn-primary">Connect</button>
        <div class="form-group">
          <br/>
          <a href="./subscribe.php">Create an account</a>
          <br/>
          <a href="./passwordLost.php">I lost my password</a>
          <br/>
          <!--<a href="./passwordLost.php">I've lost my password</a>-->
        </div>
    </form>
    </div>
    <br/>
    <br/>
    <?php
    include('./include/footer.php');
    exit;
}
?>
