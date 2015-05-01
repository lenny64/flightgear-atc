<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<h3>Download ATC resources</h3>

<p class="normal_content">
    You will find below two ATC clients to download.
    
    <table style="margin: 2% 5%; width: 50%;">
        <tr style="font-weight: bold;">
            <td></td>
            <td>OpenRadar</td>
            <td>ATC-pie</td>
        </tr>
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
    </table>
</p>


<h3>Get an ATC ident to file Flight Plans</h3>

<p class="normal_content">
    <form action="./dev2014_01_13.php5?request_auth" method="post" style="margin: 2% 5%;">
        <input type="hidden" name="userInfo[]" value="virtualAirline"/>
        Your email address : <input type="text" name="mail" size="35"/>
        <br/>
        <br/>
        <input type="submit" value="Create my ident"/>
    </form>
</p>

<br/>
<br/>

<?php include('./include/footer.php5'); ?>
