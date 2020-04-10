<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="jumbotron">
    <div class="container">
        <h1 class="display-3">Information</h1>
        <p class="lead">
            Important information about the flightgear-atc network and website
        </p>
    </div>
</div>

<div class="container">

    <div class="card card-default my-2">
        <div class="card-header">2020-04-10 - <strong>New look</strong></div>
        <div class="card-body">
            <p>
                This is a major upgrade for users:
            </p>
            <p>
                <ul>
                    <li>Cookie management: cookies are only saved if user consents. All functionalities remain active except the ATC dashboard connection.</li>
                    <li>Fresh new look: upgrading to Bootstrap v4</li>
                    <li>Map on the home page: possibility to hide the map (map visibility is remembered if cookies are enabled)</li>
                    <li>Home page ATC events presentation: new pseudo-calendar format with 4-day presentation</li>
                    <li>Possibility to move from 4 days to 4 days and select a date by clicking on the <button class="btn btn-sm btn-info"><span class="oi oi-chevron-bottom" aria-hidden="true" title="Expand"></span></button> button and selecting a date manually</li>
                    <li>On the home page: possibility to hide the events details for a more compact view (details visibility is remembered if cookies are enabled) by clicking on the <button class="btn btn-sm btn-info"><span class="oi oi-chevron-bottom" aria-hidden="true" title="Expand"></span></button> button and then on the <button class="btn btn-info btn-sm"><span class="oi oi-collapse-down" title="collapse" aria-hidden="true"></span> Collapse/expand events</button> button</li>
                    <li>The number of <span class="badge badge-info">flightplans</span> indicated next to the day represent the total number of flight plans for the day.</li>
                    <li>The number of <span class="badge badge-info">flightplans</span> indicated next to the ATC event represent the number of flight plans for this specific event (from/to the airport between begin and end time)</li>
                    <li>Clicking on an event shows more information about the event, flight plans and controller. This page also shows statistics regarding the airport and controller.</li>
                </ul>
            </p>
            <p>
                API minor changes:
                <ul>
                    <li>GET getATCSessions -> response is now ordered by date descending</li>
                </ul>
            </p>
        </div>
    </div>
    <div class="card card-default my-2">
        <div class="card-header">2020-01-26 - <strong>Stronger passwords</strong></div>
        <div class="card-body">
            <p>
                This update improves password security.
            </p>
            <p>
                From January 26th a new version of password encryption is available. You are thus required to change your password from your <a href="./dashboard.php?settings" target="_blank">"ATC Dashboard"</a>.
                <br/>
                There is no password constraint however you are invited to use letters (in upper and lower case), digits and special characters (\!%$£€#).
            </p>
        </div>
    </div>
    <div class="card card-default my-2">
        <div class="card-header">2019-08-02 - <strong>Password reset feature</strong></div>
        <div class="card-body">
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
    <div class="card card-default my-2">
        <div class="card-header">2019-03-05 - <strong>Malicious users</strong></div>
        <div class="card-body">
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
    <div class="card card-default my-2">
        <div class="card-header">2017-04-28 - <strong>Maintenance operation</strong></div>
        <div class="card-body">
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
