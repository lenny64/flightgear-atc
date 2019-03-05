<?php


// Page called by passwordLost.php

if (isset($_POST['email']))
{
  if ($_POST['email'] != NULL)
  {
    $email = htmlspecialchars($_POST['email']);

    $db_email = getInfo('mail', 'users', 'mail', $email);
    $db_password = getInfo('password', 'users', 'mail', $email);

    // IF the user is already known we will guide him through the password reset
    if ($email == $db_email)
    {
        // 1 - We create a single id (4-digit) to change his password and we send it by email
        // 2 - We ask him this id
        // 3 - We check this id - if correct we change his password
        
    }

    // If the user is not known we will ask him to subscribe
    else
    {
        ?>
        <div class="alert alert-success">
          You do not have any account with this email address. Please create one <a href="./subscribe.php">here</a>.
        </div>
        <?php
    }
  }
}




?>
