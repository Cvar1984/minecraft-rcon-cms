<?php

namespace Cvar1984\Minecraft;

final class MinecraftUser
{
    private string $whitelistPath;
    private ?\Thedudeguy\Rcon $rconInstance;

    public const ID_OFFLINE_ONLY = 0;
    public const ID_ONLINE_ONLY = 1;
    public const ID_ONLINE_PRIORITY = 2;

    public function __construct(string $whitelistPath)
    {
        $this->whitelistPath = $whitelistPath;
    }

    public function setupRcon(string $host, string $port, string $password, int $timeout = 3): self
    {
        try {
            $this->rconInstance = new \Thedudeguy\Rcon($host, $port, $password, $timeout);
            $this->rconInstance->connect();
        } catch (\Exception $e) {
            echo "RCON connection error: " . $e->getMessage() . "\n";
        }
        return $this;
    }

    private function getOnlineUuid(string $username): false|string
    {
        $url = "https://api.mojang.com/users/profiles/minecraft/" . urlencode($username);
        $response = @file_get_contents($url);
        if ($response === false) {
            return false; // Fail gracefully
        }
        $data = json_decode($response, true);
        return $this->formatUuid($data['id']) ?: false;
    }

    private function getOfflineUuid(string $username): string
    {
        $data = "OfflinePlayer:" . $username;
        $hash = md5($data, true);
        $hash[6] = chr(ord($hash[6]) & 0x0f | 0x30); // Set version to 3
        $hash[8] = chr(ord($hash[8]) & 0x3f | 0x80); // Set IETF variant
        $hex = bin2hex($hash);
        return $this->formatUuid($hex);
    }

    private function formatUuid(string $uuid): string
    {
        return sprintf(
            '%s-%s-%s-%s-%s',
            substr($uuid, 0, 8),
            substr($uuid, 8, 4),
            substr($uuid, 12, 4),
            substr($uuid, 16, 4),
            substr($uuid, 20, 12)
        );
    }

    public function getUuid(string $username, int $method = self::ID_ONLINE_PRIORITY): false|string
    {
        $uuid = '';
        switch ($method) {
            case self::ID_ONLINE_ONLY:
                $uuid = $this->getOnlineUuid($username);
                break;
            case self::ID_OFFLINE_ONLY:
                $uuid = $this->getOfflineUuid($username);
                break;
            case self::ID_ONLINE_PRIORITY:
            default:
                $uuid = $this->getOnlineUuid($username) ?? $this->getOfflineUuid($username);
                break;
        }
        return $uuid ?: false;
    }

    public function addToWhitelist(string $username, int $method = self::ID_ONLINE_PRIORITY): self
    {
        $uuid = $this->getUuid($username, $method);
        if ($uuid === false) {
            return $this;
        }

        // Read current whitelist
        $whitelist = json_decode(file_get_contents($this->whitelistPath), true) ?: [];

        // Check if the player is already whitelisted
        foreach ($whitelist as $entry) {
            if ($entry['uuid'] === $uuid) {
                return $this;
            }
        }

        // Add new player entry
        $whitelist[] = ['uuid' => $uuid, 'name' => $username];

        // Save the updated whitelist
        file_put_contents($this->whitelistPath, json_encode($whitelist, JSON_PRETTY_PRINT));
        return $this;
    }

    public function reloadWhitelist(): self
    {
        $this->rconInstance->sendCommand("whitelist reload");
        return $this;
    }
    public function getWhitelist(): array
    {
        return json_decode(file_get_contents($this->whitelistPath), true) ?: [];
    }
}
