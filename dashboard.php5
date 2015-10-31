<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>
<?php include('./include/restriction.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="container">

    
<div class="jumbotron" id="jumbotron_mainPage" style="background: #f0f0f0 url('./img/header_ATCDashboard.jpg') no-repeat center center;">
    <div id='bg-overlay'>
        <div class="container">
            <h2 id="depecheMainTitle">ATCs are the masters of the sky</h2>
        </div>
    </div>
</div>

    
    <?php
    
    // TABS CLASS MANAGEMENT
    if (isset($_GET['settings']))
    {
        $eventsClass = '';
        $settingsClass = 'active';
    }
    else
    {
        $eventsClass = 'active';
        $settingsClass = '';
    }
    
    ?>
    
    <ul class="nav nav-pills">
        <li role="presentation" class="<?php echo $eventsClass;?>"><a href="./dashboard.php5">My events</a></li>
        <li role="presentation" class="<?php echo $settingsClass;?>"><a href="./dashboard.php5?settings">My settings</a></li>
    </ul>
    
    <?php
    
    if (isset($_GET['settings']))
    {
        include('./include/dashboard_settings.php5');
    }
    else
    {
        include('./include/dashboard_events.php5');
    }
    
    ?>
    
<br/>
<br/>

</div>

<?php include('./include/footer.php5'); ?>
