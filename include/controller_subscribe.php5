<?php


// Page called by subscribe.php5

if (isset($_POST['email']) AND isset($_POST['password']))
{
  if ($_POST['email'] != NULL AND $_POST['password'] != NULL)
  {
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    $db_email = getInfo('mail', 'users', 'mail', $email);
    $db_password = getInfo('password', 'users', 'mail', $email);

    // IF the user is yet known we don't create it
    if ($email == $db_email)
    {
      ?>
      <div class="alert alert-info">
        You already have an account. Please log in <a href="./dashboard.php5">here</a>.
      </div>
      <?php
    }

    // If the user is not known we create it into the DB
    else
    {
      $User = new User();
      $User->create($email, $password, $_SERVER['REMOTE_ADDR']);

      $newUserCreated = TRUE;
    }
  }
}




?>
