<?php
require_once('env.php');
session_start();
if (isset($_SESSION['email'])) {
    echo '<script>window.location.href="home.php"</script>';
}
?>

<!-- Sign In Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage VISAs - Sign In</title>
    <link rel="icon" href="<?php echo $BASE_URL?>favicon.png">
    <!-- include Tailwind CSS stylesheet -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- include Tailwind CSS custom configration -->
    <script type="text/javascript" src="./js/tailwind.config.js"></script>
    <!-- include jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- include Toastr Notification -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
</head>
<body class="w-full h-screen bg-primary-normal font-[Inter]">
    <div class="flex justify-center">
        <div class="w-[360px] h-[734px]">
            <!-- Headerline -->
            <h1 class="mt-[200px] text-center text-white text-[30px] font-semibold leading-[38px]">Welcome שלום</h1>
            <!-- Form Section -->
            <div>
                <!-- Email Form Input -->
                <div class="mt-[24px]">
                    <label for="email" class="mb-1 block text-white text-[14px] font-medium leading-[20px]">Email דוא”ל</label>
                    <input type="text" id="email" name="email" class="w-full px-[10px] py-[14px] h-[44px] rounded-[8px] border border-solid border-gray-300" placeholder="Enter your email">
                </div>
                <!-- Password Form Input -->
                <div class="mt-[24px]">
                    <label for="password" class="mb-1 block text-white text-[14px] font-medium leading-[20px]">Password סיסמה</label>
                    <input type="password" id="password" name="password" class="w-full px-[14px] py-[10px] h-[44px] rounded-[8px] border border-solid border-gray-300" placeholder="Enter your password">
                </div>
                <!-- Remember Password Form Checkbox -->
                <div class="mt-[24px]">
                    <input type="checkbox" id="remember" name="remember" class="m-[2px] w-[16px] h-[16px] leading-[20px] rounded-[4px]">
                    <label for="remember" class="text-white text-[14px] font-medium leading-[20px] align-top">Remember me זכור אותי</label>
                </div>
                <!-- Sign In Button -->
                <div class="mt-[24px]">
                    <button id="sign-in" style="box-shadow: 0px 1px 2px rgba(16, 24, 40, 0.8);" class="w-full h-[44px] px-[18px] py-[10px] bg-primary-light text-white font-semibold rounded-[8px] duration-200 hover:bg-primary-lighter">Sign in - להתחבר</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom JS -->
    <script type="text/javascript" src="js/config.keys.js"></script>
    <script type="text/javascript" src="js/sign-in.js"></script>
</body>
</html>