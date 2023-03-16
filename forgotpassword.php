<?php 
require_once './Controllers/loginController.php';
if( (isset($_SESSION["userid"])) || (isset($_SESSION["adminid"])) )
{
  header("Location:index.php");
}
?>
<!DOCTYPE html>

<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Forgot Password</title>

    <meta name="description" content="" />

    <?php require_once 'Includes/headerFiles.php'; ?>


  </head>

  <body>
    <!-- Content -->

    <div id="demo"></div>

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center">
                <a href="#" class="app-brand-link gap-2">
                  <img src="assets/img/logo.png" width=40 height=40/>
                  <span class="app-brand-text demo text-body fw-bolder">Taaj Book</span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Forgot Password?</h4>


              <br/>
             
              <form class="mb-3" action="" method="POST">
                
              <div class="mb-3">
                  <label for="email" class="form-label">Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    name="username"
                    placeholder="Enter username associated with your User account."
                    autofocus
                    required
                  />
                </div>

                <div class="mb-3">
                  <label for="email" class="form-label">Email Account</label>
                  <input
                    type="email"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter email associated with your User account."
                    autofocus
                    required
                  />
                </div>

                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit" name="resetUser">Share Reset Link</button>
                </div>
              </form>

              <p class="text-center">
                <span>Remember your credentials?</span>
                <a href="login.php">
                  <span>Proceed to Login</span>
                </a>
              </p>
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->
    <?php require_once './Includes/footerFiles.php'; ?>
 
  </body>
</html>
