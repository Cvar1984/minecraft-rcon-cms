<?php

// Example Usage
try {
    $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__, '.env'); // Point to the directory containing .env
    $dotenv->load();
    $whitelistPath = $_ENV['WHITELIST_PATH'];
    $rconHost = $_ENV['RCON_HOST'];
    $rconPort = (int)$_ENV['RCON_PORT'];
    $rconPassword = $_ENV['RCON_PASS'];
    $username = $argv[1] ?? 'Steve';

    $minecraftUser = (new MinecraftUser($whitelistPath))->setupRcon($rconHost, $rconPort, $rconPassword);
    $minecraftUser
        ->addToWhitelist($username, MinecraftUser::ID_ONLINE_ONLY)
        ->addToWhitelist($username, MinecraftUser::ID_OFFLINE_ONLY)
        ->reloadWhitelist();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
