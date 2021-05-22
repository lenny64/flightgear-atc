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

    <div class="card my-1" id="basedonstatsevents">
        <div class="card-header">What are "Based on stats" events appearing below regular events?</div>
        <div class="card-body">
            <p>
                <span class="oi oi-pulse"></span> <b>Based on stats</b>
            </p>
            <div class="row">
                <div class="col-12 col-md-8">
                    <div class="card my-2 border-success">
                        <div class="card-body py-2">
                            <h6 class="text-success"><img src="./img/menu_controlled.png"/> ICAO <span class="text-muted small">of the controlled airport</span> <span class="badge badge-success">begin time</span> &rarr; <span class="badge badge-success">end time</span></h6>
                            <p class="text-success my-1">
                                <span class="badge badge-success">91 %</span> of chances that this airport will be controlled ~ 2 hours. *
                                <br/>
                                <span class="text-muted">(controlled <b>6 Mondays</b> / 6 weeks) **</span>
                                <br/>
                                <span class="text-success"><b>Regularly controller by </b></span><span class="badge badge-success">a super ATC</span>
                            </p>
                            <p class="text-muted small">
                                * Depends on how often the airport is controlled, and the data accuracy (basically if there is enough data)
                                <br/>
                                ** Indicates how many mondays it has been controlled since the last 6 weeks
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <p>
                Air traffic network is regularly checked (several times per hour). This data analysed allows us to see when some airports are regularly controlled.
            </p>
            <p>
                Some algorithms are running to show you where there might be controlled airports every day, based on the number of times the airport has been controlled each hour and other statistics data. It is based on airports real activity in game, and also based on whether an ATC published an event on the website or not (an ATC who publish his events on the website will be better ranked).
            </p>
            <table class="table">
                <thead class="thead-light">
                    <tr>
                        <th>Airport ATC is ranked like this:</th>
                        <th>Explanations</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>The airport is controlled regularly</td>
                        <td>The algorithm determines how many similar weekdays the airport has been controlled since the last 6 weeks, and also at which hours. The more consistent it has been controlled, higher the rank will be. Ie: if an airport is controlled every Monday from 20:00 to 22:00 it will be better ranked than one Monday every two weeks.</td>
                    </tr>
                    <tr>
                        <td>The airport is controlled approximately the same amount of time</td>
                        <td>The algorithm also checks the regularity in duration: the more the duration will be consistent the higher the rank will be.</td>
                    </tr>
                    <tr>
                        <td>The ATC has published his events on the website</td>
                        <td>The algorithm checks whether a published event for a given airport at a given date and time is effectively controlled. If an event exists on the website and the airport is controlled as planned, its rank will be higher.</td>
                    </tr>
                    <tr>
                        <td>The airport is controlled at least 1 hour</td>
                        <td>If the airport is controlled less than 1 hour its ranking will be downgraded. However, if the airport is controlled more than 5 hours by the same controller a warning message will appear and the event will be a bit downrated.</td>
                    </tr>
                </tbody>
            </table>
            <p>
                Not all airports appear in the list, because they are filtered on some factors such as recurrency, number of days controlled, number of consecutive hours controlled, if the event has been published on the website, and other data. This is why the airport you control may not appear in the "Based on stats" events.
            </p>
            <p>
                <b>I'm an ATC. Why the airport i regularly control at is not listed?</b>
                Here are some reasons:
                <ul>
                    <li>you were not controlling it at least 6 weeks ago <br/>&rarr; continue to control it <b>the days you are accustomed to, and the times you are accustomed to</b> (ie on Mondays from 18:00 to 20:00)</li>
                    <li>you were not publishing your events on the website <br/>&rarr; you would probably want to make your events visible on the website by creating an event from <a href="dashboard.php">your dashboard</a></li>
                    <li>you are controlling it at different days in week: some weeks on mondays, other on tuesdays etc... <br/>&rarr; algorithms check the <b>recurrency of days in week</b>, try to control it every day through the weeks (for instance only mondays and wednesdays or only saturdays) at <b>regular hours</b></li>
                    <li>there is no enough data <br/>&rarr; try controlling your airport more than one hour to make sure there is enough relevant data</li>
                    <li>your event is too long <br/>&rarr; if you control an airport for more than 5 hours in a row it may be downrated</li>
                    <li>algorithms problems <br/>&rarr; if you followed all the above guidelines feel free to <a href="contact.php" target="_blank">contact me</a></li>
                </ul>
            </p>
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
