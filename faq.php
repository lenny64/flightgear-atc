<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="jumbotron">
    <h1 class="display-3">Frequently Asked Questions</h1>
</div>

<div class="container">

    <div class="card mb-3">
        <div class="card-header">Is the website free?</div>
        <div class="card-body">
            Yes, it is entirely free.
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">How can i become an ATC (Air Traffic Controller) and begin scheduling events?</div>
        <div class="card-body">
            If this is the first time you want to schedule an event, just click on "New Event" in the "ATC Events" at the date you expect to control. Fill in your e-mail address and choose a password. Make sure you remember your login information for the next schedules.
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">I already created an event, how can I create a new one?</div>
        <div class="card-body">
            If you already created an event, then your email address is known. You should just connect from the "ATC Dashboard" section.
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Why are there so many airports listed when i create a new event?</div>
        <div class="card-body">
            When you are not connected as an Air Traffic Controller, if you create a new Event from the main page, every known airport will be shown.
            <br/>
            <ul>
                <li>If you have never created an event, select the airport in the list or create a new one. The interface will remember your favorite(s) airport(s) when you will connect from "ATC Dashboard" section.</li>
                <li>If you already have created an event, connect from "ATC Dashboard" and only airport your favorite(s) airport(s) will be shown.</li>
            </ul>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Is filling a flight plan mandatory?</div>
        <div class="card-body">
            In the real life, IFR (Instrument Flight Rules) pilots file their flight plans.
            <br/>
            VFR (Visual Flight Rules) pilots are however not requested to file a flight plan. However, Air Traffic Controllers appreciate that.
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">What is a "Special Event"?</div>
        <div class="card-body">
            Sometimes several Air Traffic Controllers want to organize a bigger event with many airports. A "Special Event" usually occurs in a focused area and more than 3 or 4 airports are controlled at the same time.
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">What are "cookies" and why do we use them?</div>
        <div class="card-body">
            <p>
                Cookies are small text files stored on your computer to provide a better user experience. We do not store commercial nor advertising cookies. There are two kinds of cookies that can be stored on your computer from flightgear-atc website:
                <ul>
                    <li>Technical cookies</li>
                    <li>Statistics cookies</li>
                </ul>
            </p>
            <p>
                There are no cookies, nor personal information that are provided to others: these cookies are solely aimed to provide a better user experience, and get statistics from visit information such as IP address, global location (country/city), browser type.
            </p>
            <p>
                Here are the cookies we may use to provide you a better experience:
            </p>
            <table class="table table-sm">
                <thead class="thead-light">
                    <tr>
                        <th>Name of the cookie</th>
                        <th>Signification</th>
                        <th>Behavior</th>
                        <th>Period of validity</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>PHPSESSID</td>
                        <td>Cookie used to store your anonymized session information. It allows you to connect to this website and navigate from page to page without having to re-connect everytime.</td>
                        <td>created on cookie accept</td>
                        <td>session</td>
                    </tr>
                    <tr>
                        <td>cookieConsentAnswer</td>
                        <td>The value of this cookie is 1 if you did consent to cookies</td>
                        <td>created on cookie accept</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td>collapseEvents</td>
                        <td>Used to show or hide event details in the calendar of the home page. The value changes when you click on <button class="btn btn-sm btn-info">collapse/expand events</button> button in the home page</td>
                        <td>created on cookie accept</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td>showMap</td>
                        <td>Used to show or hide the map when you click on <button class="btn btn-sm btn-info">Hide the map</button> button in the home page</td>
                        <td>created on cookie accept</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td>hasSeenNewInfo</td>
                        <td>Used to determine whether you viewed a fresh new info or not.</td>
                        <td>created on cookie accept</td>
                        <td>1 year</td>
                    </tr>
                    <tr>
                        <td>_ga</td>
                        <td>Statistic cookie</td>
                        <td>created on cookie accept</td>
                        <td>2 years</td>
                    </tr>
                    <tr>
                        <td>_gid</td>
                        <td>Statistic cookie</td>
                        <td>created on cookie accept</td>
                        <td>variable</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">If you have other questions or suggestions, please <a href="./contact.php">contact me</a> and I will be happy to answer.</div>
    </div>



</div>

<?php include('./include/footer.php'); ?>
