<div class="container" style="padding-top:50px">
    <div class="row">
        <div
            class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xs-offset-0 col-sm-offset-0 col-md-offset-3 col-lg-offset-3 toppad">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h3 class="panel-title">Your Profile</h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <form id="profile_form" action="updateProfile.php" method="post" enctype="multipart/form-data">
                            <input hidden name="userID" value="<?php echo $user; ?>"/>
                           
                          
                            <div class=" col-md-9 col-lg-9 ">
                                <table class="table table-user-information">
                                    <tbody>
                                    <tr>
                                        <td>Username</td>
                                        <td><input type="text" disabled name="username"
                                                   value= <?php echo $data['username']; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>First Name</td>
                                        <td><input type="text" disabled name="firstName"
                                                   value= <?php echo $data['first_name']; ?>></td>
                                    </tr>
                                    <tr>
                                        <td>Last Name</td>
                                        <td><input type="text" disabled name="lastName"
                                                   value= <?php echo $data['last_name']; ?>>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Date of Birth</td>
                                        <td><input type="text" disabled name="dob"
                                                   value= <?php echo $data['birthdate']; ?>>
                                        </td>
                                    </tr>

                                    <tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><input type="text" disabled name="email"
                                                   value= <?php echo $data['email']; ?>>
                                        </td>
                                    </tr>
                                   
                                    
                                    <td>
                                        
                                    </td>
                                    </tr>
                                    </tbody>
                                </table>
                               
                                </a>
                            </div>
                        </form>
                        

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
