<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="jumbotron">
    <div class="container">
        <h2>Be part of the ATC crowd</h2>
        <p>
            If you want to control an airport, you will need an ATC client to install on your computer. Download one of the two programs below that will fit your needs.
        </p>
    </div>
</div>

<div class="container">
    
    <h3>Download ATC clients</h3>

        <table class="table">
            <thead>
                <tr>
                    <th></th>
                    <th>Openradar</th>
                    <th>ATC-pie</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Download</td>
                    <td><a href="http://wiki.flightgear.org/OpenRadarDownload" target="_blank">Latest stable</a></td>
                    <td><a href="http://sourceforge.net/projects/atc-pie/" target="_blank">Latest stable (.tar.gz)</a></td>
                </tr>
                <tr>
                    <td>Developer</td>
                    <td>Wolfram Wagner</td>
                    <td>Michael Filhol</td>
                </tr>
                <tr>
                    <td>Wiki page</td>
                    <td><a href="http://wiki.flightgear.org/OpenRadar" target="_blank">Flightgear Wiki</a></td>
                    <td><a href="http://wiki.flightgear.org/ATC-pie" target="_blank">Flightgear Wiki</a></td>
                </tr>
            </tbody>
        </table>


    <h3>Get an ATC ident to file Flight Plans</h3>

    <form role="form" action="./dev2014_01_13.php5?request_auth" method="post">
        <input type="hidden" name="userInfo[]" value="virtualAirline"/>
        <div class="form-group">
            <label for="email">Your email address</label>
            <input type="text" name="mail" class="form-control" id="email" placeholder="Email address">
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>

    <br/>
    <br/>
</div>

<?php include('./include/footer.php5'); ?>
