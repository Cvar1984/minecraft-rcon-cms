<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Cvar1984\Minecraft\MinecraftUser;
use \Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
$whitelistPath = $_ENV['WHITELIST_PATH'];
$rconHost = $_ENV['RCON_HOST'];
$rconPort = (int)$_ENV['RCON_PORT'];
$rconPassword = $_ENV['RCON_PASS'];
$message = '';

if (isset($_POST['username'])) {
    try {
        $username = $_POST['username'];
        $minecraftUser = (new MinecraftUser($whitelistPath))->setupRcon($rconHost, $rconPort, $rconPassword);
        $minecraftUser
            ->addToWhitelist($username, MinecraftUser::ID_ONLINE_ONLY)
            ->addToWhitelist($username, MinecraftUser::ID_OFFLINE_ONLY)
            ->reloadWhitelist();
            $message = 'Berhasil menambahkan ' . htmlspecialchars($username) . ' ke whitelist';
    } catch (Exception $e) {
        $message = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<!--
 ____________________________
< Nothing to see here mooOoo >
 ----------------------------
        \   ^__^
         \  (oo)\_______
            (__)\       )\/\
                ||----w |
                ||     ||

-->
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="https://cvar1984.my.id/">
    <title>VRChat Indonesia Minecraft Server</title>
    <style>
        @font-face {
            font-family: bios;
            src: url(https://cvar1984.github.io/fonts/ibm/3270-Regular.ttf);
        }

        :root {
            height: 100%;
            font-family: bios;
        }

        body {
            display: flex;
            margin: 0;
            height: 100%;
            background-color: #000;
        }

        main {
            margin: auto;
            /* margin: calc(50vh - 3rem) auto auto auto; */
            text-align: center;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        input {
            padding: 0.5rem;
            margin: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid lime;
            background-color: #112;
            color: lime;
        }

        button {
            padding: 0.5rem;
            margin: 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid lime;
            background-color: #112;
            color: lime;
            cursor: pointer;
        }

        h1,
        p {
            margin: 0;
            padding: 1rem 0;
            font-size: 1.5rem;
            color: lime;
        }

        pre {
            margin: 1rem;
            padding: 0.5rem;
            border-radius: 0.5rem;
            background-color: #112;
            color: #a99;
            font-family: bios;
        }

        a {
            text-decoration: none;
            color: lime;
        }
    </style>
    <?php if (!empty($message)) : ?>
        <script>
            alert("<?= addslashes($message); ?>");
        </script>
    <?php endif; ?>
</head>

<body>
    <main>
        <h1>Masukan Username Minecraft anda dibawah untuk daftar</h1>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <button type="submit">Daftar</button>
        </form>
    </main>
</body>

</html>