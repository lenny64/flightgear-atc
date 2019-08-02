<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<!-- LE CODE COMMENCE ICI -->
<div class="jumbotron">
    <div class="container">
        <h2>Information</h2>
        <p>
            Important information about the flightgear-atc network and website
        </p>
    </div>
</div>

<div class="container">

    <div class="panel panel-default">
        <div class="panel-heading">2019-08-02 - <strong>Password reset feature</strong></div>
        <div class="panel-body">
            <p>
                This update adds a password reset ability for those who forgot their credentials.
            </p>
            <p>
                In the <a href="http://flightgear-atc.alwaysdata.net/dashboard.php" target="_blank">"ATC Dashboard"</a> page you'll find a button <b>"I lost my password"</b> to reset your password.
                <br/>
                <ul>
                    <li>Please enter your email address. An email will be sent to you, containing an unique ID. This ID is valid for only 24 hours.</li>
                    <li>Re-enter your email address, enter the ID contained in the email and your new password.</li>
                    <li>Click on <button class="btn btn-primary">Reset my password</button></li>
                </ul>
            </p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">2019-03-05 - <strong>Malicious users</strong></div>
        <div class="panel-body">
            <p>
                There has been some recents attacks on the flightgear-atc website (and the multiplayer servers of FGFS) that caused harm to some users of the flightgear-atc website.
                <br/>
                Indeed some people created accounts on the website and tried to give wrong information on the other users.
            </p>
            <p>
                As a result, we decided to create a list of "trusted ATCs" on which we know we can rely on. Indeed, when a "trusted ATC" creates a session his username will be written in a green label. Example :
                <br/>
                <span class="label label-success">Hosted by user</span>
            </p>
            <p>
                In order to request your account verification please log in on your <a href="./dashboard.php">dashboard page</a> and read the instructions. Click here to check your verification :
                <br/>
                <a class="btn btn-primary" href="./dashboard.php?getVerified">Check</a>
            </p>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">2017-04-28 - <strong>Maintenance operation</strong></div>
        <div class="panel-body">
            <p>
                Due to an internal migration on alwaysdata host services, a maintenance operation is planned on Thursday may 25th from 19:00 to 21:00 UTC. The main change will occur on webpages extensions. Indeed, the regular ".php5" pages will be renamed in ".php" according to the new PHP5 version hosted by Alwaysdata.
                <br/>
                This operation won't affect ATCs and pilots regular operations (planning an ATC session, filling a flight plan, etc...).
            </p>
            <p>
                WARNING!
                <br/>
                This operation will affect API users.
                <br/>
                We kindly ask our API users to call the new version (dev2017_04_28) :
                <br/>
                <a href="http://flightgear-atc.alwaysdata.net/dev2017_04_28.php" target="_blank">http://flightgear-atc.alwaysdata.net/dev2017_04_28.php</a>
            </p>
        </div>
    </div>

</div>

<?php include('./include/footer.php'); ?>
