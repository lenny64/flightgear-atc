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
  else if (isset($status) && ($status == "ID_CREATED" || $status == "ID_ALREADY_CREATED" || $status == "ID_NOT_MATCHING")) {
      ?>
    <form role="form" action="./passwordLost.php" method="post">
        <?php if ($status == "ID_CREATED") { ?>
            <div class="alert alert-success">
                A unique id has been generated. Please check your email and enter the id below.
                <br/>
                The ID is available 24 hours.
            </div>
        <?php } else if ($status == "ID_ALREADY_CREATED") { ?>
            <div class="alert alert-danger">
                A password request have already been generated. Please check your email and enter the id below (please note the ID is valid for 24 hours).
            </div>
        <?php } else if ($status == "ID_NOT_MATCHING") { ?>
            <div class="alert alert-danger">
                The ID you entered is not correct. Please check your email and enter the id below (please note the ID is valid for 24 hours).
            </div>
        <?php } else if ($status == "PLEASE_GENERATE_NEW_ID") { ?>
            <div class="alert alert-danger">
                The ID you entered is not correct. Please generate a new one below.
            </div>
        <?php } ?>
        <div class="alert alert-info">
            Please enter the ID that have been sent by email.
            <br/>
            * fields are required
        </div>
        <div class="form-group">
            <label for="email">E-mail*</label>
            <input class="form-control" type="text" name="email" id="email" required="required">
        </div>
        <div class="form-group">
            <label for="uniqueId">ID*</label>
            <input class="form-control" type="text" name="uniqueId" id="uniqueId" required="required">
        </div>
        <div class="form-group">
            <label for="password">New password*</label>
            <input class="form-control" type="password" name="password" id="password" required="required">
        </div>
        <button type="submit" class="btn btn-primary">Reset my password</button>
    </form>
  <?php }
  else if (isset($status) && $status == "PASSWORD_CHANGED") { ?>
      <div class="alert alert-info">
          Your password has been changed :)
      </div>
  <?php }
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
