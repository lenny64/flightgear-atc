<?php include('./include/header.php'); ?>
<?php include('./include/menu.php'); ?>

<!-- LE CODE COMMENCE ICI -->

<div class="container">

  <div class="jumbotron" id="jumbotron_mainPage" style="background: #f0f0f0 url('./img/header_ATCDashboard.jpg') no-repeat center center;">
    <div id='bg-overlay'>
        <div class="container">
            <h2 id="depecheMainTitle">ATCs are the masters of the sky</h2>
        </div>
    </div>
  </div>

  <?php include './include/controller_passwordLost.php'; ?>

  <?php
  if (isset($newUserCreated) AND $newUserCreated === TRUE)
  {
    ?>
    <div class="alert alert-success">
      Your account has been validated.
      <br/>
      <br/>
      <a href="./dashboard.php">Go to my dashboard</a>.
    </div>
    <?php
  }
  else
  {
  ?>

    <form role="form" action="./passwordLost.php" method="post">
        <div class="alert alert-info">
          You have lost your password. Please fill-in your email address.
          <br/>
          * fields are required
        </div>
        <div class="form-group">
            <label for="email">E-mail*</label>
            <input class="form-control" type="text" name="email" id="email" required="required">
        </div>
        <button type="submit" class="btn btn-primary">Reset my password</button>
    </form>

  <?php
  }
  ?>

<br/>
<br/>

</div>

<?php include('./include/footer.php'); ?>
