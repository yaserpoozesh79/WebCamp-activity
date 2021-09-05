<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <?php 
        include "info.php";
        $message1 = "";
        $message2 = "";
        
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            if(isset($_POST['pass-signup']) === true){ // user sent the signup form

                $name = $_POST['fullname']    ?? '';
                $phone_num = $_POST['number'] ?? '';
                $birth_date = $_POST['birth'] ?? '';
                $email = $_POST['email2']     ?? '';
                $pass = $_POST['pass-signup'] ?? '';
                $repass = $_POST['repass']    ?? '';

                if($name=='' || $phone_num=='' || $email=="" ||
                $pass=='' || $repass==''){
                    $message2 = "<div class='alert alert-danger'>اطلاعات مورد نیاز را کامل وارد کنید</div>";
                }elseif($pass != $repass){
                    $message2 = "<div class='alert alert-danger'>رمز با تکرار آن مطابقت ندارد</div>";
                }else{
                    $conn = new mysqli($server, $username, $password, $database);
                    if($conn -> connect_error){
                        $message2 = "<div class='alert alert-danger'>در اتصال به سرور مشکلی رخ داد</div>";
                    }else{
                        $sql_command = "INSERT INTO users (id, email, password, name, phone_number, birth_date) VALUES (null, ?, ?, ?, ?, ?)";
                        $statement = $conn -> prepare($sql_command);
                        $statement -> bind_param("sssss", $email, $pass, $name, $phone_num, $birth_date);
                        $res = $statement -> execute();
                        if($res)
                            $message2 = "<div class='alert alert-success'>اطلاعال با موفقیت ثبت شد</div>";    
                        elseif(strpos($statement->error, 'Duplicate entry') !== false && strpos($statement->error, 'email') !== false)
                            $message2 = "<div class='alert alert-danger'>این ایمیل قبلا وارد شده است</div>";
                        else
                            $message2 = "<div class='alert alert-danger'>مشکل ناشناخته ای رخ داد</div>";
                        $statement -> close();
                    }
                    $conn -> close();
                }
            }elseif(isset($_POST['pass-login'])){ //user sent the login form
                $email = $_POST['email1']    ?? '';
                $pass = $_POST['pass-login'] ?? '';

                if($email=='' || $pass==''){
                    $message1 = "<div class='alert alert-danger'>اطلاعات مورد نیاز را کامل وارد کنید</div>";
                }else{
                    $conn = new mysqli($server, $username, $password, $database);
                    if($conn -> connect_error){
                        $message1 = "<div class='alert alert-danger'>در اتصال به سرور مشکلی رخ داد</div>";
                    }else{
                        $sql_command = "SELECT email,password FROM users WHERE email = ? ";
                        $statement = $conn -> prepare($sql_command);
                        $statement -> bind_param("s", $email);
                        $statement -> execute();
                        $result = $statement -> get_result();
                        $row = $result -> fetch_assoc();
                        if($row != NULL){
                            if($row['password'] == $pass)
                                $message1 = "<div class='alert alert-success'>شما وارد شدید</div>";
                            else
                                $message1 = "<div class='alert alert-danger'>رمز اشتباه است</div>";    
                        }else{
                            $message1 = "<div class='alert alert-danger'>این ایمیل ثبت نشده است</div>";
                        }
                        $statement -> close();
                    }
                    $conn -> close();
                }
            }
        }

    ?>
    <title>فرم ورود و ثبت نام</title>
    <style>
        *{
            font-family: shabnam;
        }

        .flex-container{
            display: flex;
            padding:10px;
            justify-content: center;
            align-items: center;
        }
        .flex-container > div{
            margin: 30px;
            padding:20px;
            background-color: white;
            border-radius: 10px;
            align-self: flex-start;
        }
        label, input, a{
            font-size:16px;
        }
        label, input{
            margin:3px;
            border-radius: 5px;
        }
        input{
            width: 30vw;
            height: 40px;
            margin-bottom: 10px;
            vertical-align: center;
            text-align: right;
            padding-right: 10px;
            border: 2px solid lightgray;
        }
        .submitbutton, .resetButton{
            width:12vw;
            height:35px;
            text-align: center;
        }
        .submitbutton{
            background-color: lightskyblue;
            border-color: lightskyblue;
        }
        .resetButton{
            background-color: lightgrey;
            border-color: lightgrey;
        }
        span{
            color: red;
        }
        @media (max-width: 650px){
            input{
                width: 50vw;
            }
            .flex-container{
                flex-direction: column;
            }
            .flex-container > div{
                align-self: center;
            }
        }
    </style>
</head>    
<body style="background-color: rgb(13,144,252);">
    <div class="flex-container">
        <div >
            <?php echo $message1;?>
            <form action="" method='POST'>
                <label for="email1">ایمیل<span>*</span></label><br>
                <input type="email" name="email1" id="email1" placeholder="مثال: salibrother650@gmali.com" required><br>

                <label for="pass-login">رمز عبور<span>*</span></label><br>
                <input type="password" name="pass-login" id="pass-login" placeholder="رمز عبور خود را وارد کنید" required><br>

                <input class="submitbutton" type="submit" value="ورود"><br>
                <a href="">فراموشی رمز عبور!</a>
            </form> 
        </div>
        <div>
        <?php echo $message2;?>
            <form action="" method='POST'>
                <label for="fullname">نام و نام خانوادگی<span>*</span></label><br>
                <input type="text" id="fullname" name="fullname" placeholder="مثال:یاسر پوزش" required><br>

                <label for="number">شماره تماس<span>*</span></label><br>
                <input type="text" id="number" name="number" placeholder="مثال:744322" required><br>

                <label for="birth">تاریخ تولد</label><br>
                <input type="date" name="birth" id="birth"><br>

                <label for="email2">ایمیل<span>*</span></label><br>
                <input type="email" name="email2" id="email2" placeholder="مثال: salibrother650@gmali.com" required><br>

                <label for="pass-signup">رمز عبور<span>*</span></label><br>
                <input type="password" name="pass-signup" id="pass-signup" placeholder="رمز عبور خود را وارد کنید" required><br>

                <label for="repass">رمز عبور<span>*</span></label><br>
                <input type="password" name="repass" id="repass" placeholder="تکرار رکز عبور" required><br>

                <input class="submitbutton" type="submit" value="ثبت نام">
                <input class="resetButton" type="reset" value="پاک کن!">
            </form> 
        </div>
    </div>
</body>
</html>
