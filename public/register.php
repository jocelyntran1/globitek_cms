<?php

  require_once('../private/initialize.php');


  // Set default values for all variables the page needs.
  $first_name = "";
  $last_name = "";
  $email = "";
  $username = "";

  // if this is a POST request, process the form
  if(is_post_request()) {

    // Confirm that POST values are present before accessing them.
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';

    // Perform Validations
    $errors = [];

    if (is_blank($_POST['first_name'])) {
      $errors[] = "First name cannot be blank.";
    } elseif (!has_length($_POST['first_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "First name must be between 2 and 255 characters.";
    }

    if (is_blank($_POST['last_name'])) {
      $errors[] = "Last name cannot be blank.";
    } elseif (!has_length($_POST['last_name'], ['min' => 2, 'max' => 255])) {
      $errors[] = "Last name must be between 2 and 255 characters.";
    }

    if (is_blank($_POST['username'])) {
      $errors[] = "Username cannot be blank.";
    } elseif (!has_length($_POST['username'], ['min' => 8])) {
      $errors[] = "Username must be at least 8 characters.";
    } elseif (!has_length($_POST['username'], ['max' => 255])) {
      $errors[] = "Username must less than 255 characters.";
    }

    if (is_blank($_POST['email'])) {
      $errors[] = "Email cannot be blank.";
    } elseif (!has_valid_email_format($_POST['email'])) {
      $errors[] = "Email must be a valid form.";
    }

    // No errors, submit data to database
    if (empty($errors)) {

      $created_at = date("Y-m-d H:i:s");

      // Sanitize variables
      $first_name = filter_var($first_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $last_name = filter_var($last_name, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
      $email = filter_var($email, FILTER_SANITIZE_EMAIL);
      $username = filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

      // Write SQL INSERT statement
      $sql = "INSERT INTO users (first_name, last_name, email, username, created_at) VALUES (";
      $sql .= "'{$first_name}', '{$last_name}', '{$email}', '{$username}', '{$created_at}')";

      // For INSERT statments, $result is just true/false
      $result = db_query($db, $sql);
      if($result) {
        db_close($db);

        // Redirect user to success page
        redirect_to( './registration_success.php' );

      } else {
       
        // The SQL INSERT statement failed.
        // Just show the error, not the form
        echo db_error($db);
        db_close($db);
        exit;
      }

    }
  }

?>

<?php $page_title = 'Register'; ?>
<?php include(SHARED_PATH . '/header.php'); ?>

<div id="main-content">
  <h1>Register</h1>
  <p>Register to become a Globitek Partner.</p>

  <?php
    // Display form errors
    if(!empty($errors)) {
      echo display_errors($errors);
    }
  ?>

  <form action="register.php" method="post">

    First Name:<br/ >
    <input type="text" name="first_name" value="<?php if (isset($first_name)) { echo ($first_name); } ?>"/>
    <br/ >Last Name:<br/ >
    <input type="text" name="last_name" value="<?php if (isset($last_name)) { echo ($last_name); } ?>"/>
    <br/ >Email:<br/ >
    <input type="text" name="email" value="<?php if (isset($email)) { echo ($email); } ?>"/>
    <br/ >Username:<br/ >
    <input type="text" name="username" value="<?php if (isset($username)) { echo ($username); } ?>"/>
    <br />

    <input type="submit" name="submit" value="Submit"/>

  </form>

</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
