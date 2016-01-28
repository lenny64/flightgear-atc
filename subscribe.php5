<?php include('./include/header.php5'); ?>
<?php include('./include/menu.php5'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="container">

  <div class="jumbotron" id="jumbotron_mainPage" style="background: #f0f0f0 url('./img/header_ATCDashboard.jpg') no-repeat center center;">
    <div id='bg-overlay'>
        <div class="container">
            <h2 id="depecheMainTitle">ATCs are the masters of the sky</h2>
        </div>
    </div>
  </div>

  <?php include './include/controller_subscribe.php5'; ?>

  <?php
  if (isset($newUserCreated) AND $newUserCreated === TRUE)
  {
    ?>
    <div class="alert alert-success">
      Your account has been validated.
      <br/>
      <br/>
      <a href="./dashboard.php5">Go to my dashboard</a>.
    </div>
    <?php
  }
  else
  {
  ?>

    <form role="form" action="./subscribe.php5" method="post">
        <div class="alert alert-info">
          Subscribe to create new Flightgear ATC events.
          <br/>
          * fields are required
        </div>
        <div class="form-group">
            <label for="email">E-mail*</label>
            <input class="form-control" type="text" name="email" id="email" required="required">
        </div>
        <div class="form-group">
            <label for="password">Password*</label>
            <input class="form-control" type="password" name="password" id="password" required="required">
        </div>
        <button type="submit" class="btn btn-primary">Create my account</button>
    </form>

  <?php
  }
  ?>

<br/>
<br/>

</div>

<?php include('./include/footer.php5'); ?>
