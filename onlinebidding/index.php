<?php
require_once('dbConnection.php');
?>
<html>

<head>

    <title>online bidding system</title>
	
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/loginregister.css" rel="stylesheet">
    <script src="js/jquery.js"></script>
<link href="index.css" rel="stylesheet">
    <script src="js/loginregister.js"></script>
    <script src="js/bootstrap.min.js"></script>

</head>

<body background="‪D:\bid.jpg">


    <div class="container">
        <div class="row">
            <div class=" col-md-6 col-md-offset-3" >
                <div class="panel panel-login">
                    <div   class="panel-heading">
                        <div class="row">
                            <div class="col-xs-6">
                                <a href="" class="active" id="login-form-link">Login</a>
                            </div>
                            <div class="col-xs-6">
                                <a href="" id="register-form-link">Register</a>
                            </div>
                        </div>
                        <hr>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <form id="login-form" action="login.php" method="post" >
                                    <div class="form-group">
                                        <input type="text" name="username" id="username" pattern="[A-Za-z_]*$" title="must contain only alphabets" class="form-control" placeholder="Username" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password"  class="form-control" placeholder="Password" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-sm-offset-3" >
                                                <input type="submit" name="login-submit" id="login-submit"  class="form-control btn btn-login" value="Log In">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <form id="register-form" action="signup.php" method="post" role="form"  style="display: none;">
                                    <div class="form-group">
                                        <input type="text" name="username" id="username" pattern="[A-Za-z_]*$" title="must contain only alphabets" class="form-control" placeholder="Username" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="firstname" id="firstname" pattern="[A-Za-z_]*$" title="must contain only alphabets" class="form-control" placeholder="First Name" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="lastname" id="lastname" pattern="[A-Za-z_]*$" title="must contain only alphabets" class="form-control" placeholder="Last Name" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="email" id="email"  pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$" class="form-control" placeholder="Email addr" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="text" name="dob" id="dob"  class="form-control" placeholder="DD/MM/YYYY or DD\MM\YYYY" value="" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="filter">Role</label>
                                        <select class="form-control" id="role" name="role">
                                            <?php $sql = 'SELECT * FROM Roles';
                                                    foreach ($db->query($sql) as $row) { ?>
                                                <option value="<?php echo $row['role_id']; ?>">
                                                    <?php echo htmlspecialchars($row['role']); ?>
                                                </option>
                                                <?php } ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                                    </div>
                                    <div class="form-group">
                                        <input type="password" name="confirm-password" id="confirm-password" class="form-control" placeholder="Confirm Password" required>
                                    </div>
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-sm-6 col-sm-offset-3">
                                                <input type="submit" name="register-submit" id="register-submit"  class="form-control btn btn-register" value="Register Now">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
	<div id="errorlog" style="visibility:hidden"></div>
<?php
        if(isset($_GET['val'])){
            if($_GET['val']=="success"){
                echo "<script>
                        $(function() {
                            $('#errorlog').text('Registration Successful!').css('background-color','#1CA347').css('visibility','visible');
                            $('#errorlog').delay(2000).fadeOut('slow');
                        });
                     </script>";
            }else{
                $errString;
                if($_GET['val']==1){
                    $errString = 'Passwords do not match!';
                }else if($_GET['val']==2){
                    $errString = 'Missing inputs!';
                }else{
                    $errString = 'Username or email already exists!';
                }

                echo "<script>
                        $(function() {
                            $('#errorlog').text('".$errString."').css('background-color','#FF072D').css('visibility','visible');
                            $('#errorlog').delay(2000).fadeOut('slow');
                            $('#login-form').fadeOut(8);
                            $('#register-form').delay(10).fadeIn(10);
                            $('#login-form-link').removeClass('active');
                            $('#register-form-link').addClass('active');
                        });
                     </script>";
            }
        }

        


    ?>
</body>
</html>