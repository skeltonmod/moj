<?php
include 'navbar.php';

if(isset($_SESSION['user_id'])){
    header('Location:index.php');
}
?>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Municipality of Jasaan</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
</head>


<body>
<div class="container">
    <div class="card" style="margin-top: 30px;">
        <div class="card-body">
            <form id="form">
                <div class="form-group">
                    <label for="emailAddress">Email</label>
                    <input type="email" class="form-control" id="emailAddress" name="email" placeholder="Email">
                </div>
                <div class="form-group">
                    <label for="passwordInput">Password</label>
                    <input type="password" class="form-control" id="passwordInput" name="password" placeholder="Password">

                </div>
                <a href="#" >Forgot Password</a>
                <button id="login" type="submit" class="btn btn-block btn-outline-primary" style="margin-top: 1.3rem">Login</button>
            </form>
        </div>
    </div>

</div>
</body>
<script>
    $('form').on("submit",function (e) {
        $("#form").validate({
            rules:{
                email: {
                    required:true,
                    email: true
                },
                password: {
                    required: true,
                }
            }

        })
        e.preventDefault()
        let email = $("#emailAddress").val();
        let password = $("#passwordInput").val()
        if($("#form").valid()){
            $.ajax({
                url: 'credentials.php',
                method: 'post',
                dataType: 'json',
                data:{
                    email: email,
                    password: password,
                    key: 'login'
                },
                success: function (response) {
                    if(response.data[0].error === false){
                        window.location.href = 'index.php'
                    }
                }

            })
        }
    })
</script>

</html>
