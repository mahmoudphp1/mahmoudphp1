<?php

use App\Database\Models\User;
use App\Http\Requests\Validation;
use App\Mails\resetpassword;

$title = "Forget password";

include "layouts/header.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // validation
    $validation = new Validation;
    $validation->setInput('email')->setValue($_POST['email'])
        ->required()->regex('/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/')->exists('users', 'email');
    if (empty($validation->getErrors())) {
        // no validation error
        $forgetPasswordCode = rand(100000, 999999);
        $user = new User;
        $result = $user->setEmail($_SESSION['email'])->setVerification_code($forgetPasswordCode);
        if ($user->updateCode()) {
            $forgetPasswordMail = new resetpassword;
            $subject = "Forget Password Code";
            $body = "<p>Hello {$_POST['email']}</p>
            <p>Your Forget Password Code: <b style='color:blue;'>{$forgetPasswordCode}</b></p>
            <p>Thank You.</p>";
            if ($forgetPasswordMail->send($_POST['email'], $subject, $body)) {
                $_SESSION['email'] = $_POST['email'];
                header('location:checkverifiy.php?page=forget');
                die;
            } else {
                $error = "<div class='alert alert-danger text-center'> Please Try Again Later </div>";
            }
        } else {
            $error = "<div class='alert alert-danger text-center'> Something Went Wrong </div>";
        }
        $error = "<div class='alert alert-danger text-center'> Wrong Verification Code </div>";
    }
}
?>
<div class="login-register-area ptb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col-md-12 ml-auto mr-auto">
                <div class="login-register-wrapper">
                    <div class="login-register-tab-list nav">
                        <a class="active" data-toggle="tab" href="#lg1">
                            <h4> <?= $title ?></h4>
                        </a>
                    </div>
                    <div class="tab-content">
                        <div id="lg1" class="tab-pane active">
                            <div class="login-form-container">
                                <div class="login-register-form">
                                    <form action="" method="post">
                                        <?= $error ?? "" ?>
                                        <?= $success ?? "" ?>
                                        <input type="email" name="email" placeholder="email address">
                                        <?= isset($validation) ? $validation->getMessage('email') : '' ?>
                                        <div class="button-box">
                                            <button type="submit"><span>Verify</span></button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
include "layouts/scripts.php";
?>