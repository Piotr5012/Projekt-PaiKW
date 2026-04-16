<?php
session_start();
$form = $_GET['form'] ?? 'login';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie</title>
    <link rel="stylesheet" href="../inne/style.css">
</head>
<body>

    <div class="wrapper <?= $form === 'register' ? 'active' : '' ?>">
        <span class="bg-animate"></span>
        <span class="bg-animate2"></span>

        <div class="form-box login">
            <h1 class="animation" style="--i:0; --j:21;">Logowanie</h1>

            <?php if (isset($_SESSION['error_login'])): ?>
                <span class="text-sm text-error dynamic-error"><?= $_SESSION['error_login'] ?></span>
                <?php unset($_SESSION['error_login']); ?>
            <?php endif; ?>

            <form method="post" action="../skrypty-php/login-user.php">
                <div class="input-box animation" style="--i:1; --j:22;">
                    <input type="text" name="email" required>
                    <label>Email:</label>
                    <i class="icon-mail"></i>
                </div>

                <div class="input-box animation" style="--i:2; --j:23;">
                    <input type="password" name="password" required>
                    <label>Hasło:</label>
                    <i class="icon-lock"></i>
                </div>

                <button type="submit" class="btn animation" style="--i:3; --j:24;">Zaloguj się</button>

                <div class="logreg-link animation" style="--i:4; --j:25;">
                    <a href="#" class="register-link">Nie masz jeszcze konta?</a>
                </div>
            </form>
        </div>

        <div class="info-text login">
            <h2 class="animation" style="--i:0; --j:20;">Witaj</h2>
            <p class="animation" style="--i:1; --j:21;"> Strona ta jest poświęcona blogowaniu </p>
        </div>
            
        <div class="form-box register">
            <h1 class="animation" style="--i:17; --j:0;">Zarejestruj się</h1>

            <?php if (isset($_SESSION['error_register'])): ?>
                <span class="text-sm text-error2 dynamic-error"><?= $_SESSION['error_register'] ?></span>
                <?php unset($_SESSION['error_register']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <span class="text-sm text-success"><?= $_SESSION['success'] ?></span>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <form method="post" action="../skrypty-php/register-user.php">
                <div class="input-box animation" style="--i:18; --j:1;">
                    <input type="text" name="email" required>
                    <label>Email:</label>
                    <i class="icon-mail"></i>
                </div>

                <div class="input-box animation" style="--i:19; --j:2;">
                    <input type="text" name="username" required>
                    <label>Nazwa użytkownika:</label>
                    <i class="icon-user"></i>
                </div>

                <div class="input-box animation" style="--i:20; --j:3;">
                    <input type="password" name="password" required>
                    <label>Hasło:</label>
                    <i class="icon-lock"></i>
                </div>

                <div class="input-box animation" style="--i:21; --j:4;">
                    <input type="password" name="verify-password" required>
                    <label>Powtórz hasło:</label>
                    <i class="icon-lock"></i>
                </div>

                <button type="submit" class="btn animation" style="--i:22; --j:5;">Utwórz konto</button>

                <div class="logreg-link animation" style="--i:23; --j:6;">
                    <a href="#" class="login-link">Masz już konto?</a>
                </div>
            </form>
        </div>
        
        <div class="info-text rejestracja">
            <h2 class="animation" style="--i:17; --j:0;">Utwórz konto</h2>
            <p class="animation" style="--i:18; --j:1;">Aby móc korzystać ze wszystkich funkcji</p>
        </div>
    </div>

    

    <script src="../inne/script.js"></script>

</body>
</html>