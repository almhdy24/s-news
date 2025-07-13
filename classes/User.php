<?php
require_once __DIR__ . '/JSONDB.php';

class User extends JSONDB
{
    private array $usersCache = [];

    public function __construct()
    {
        parent::__construct(__DIR__ . '/../data/users.json');
        $this->usersCache = $this->all();
    }

    /**
     * Create a new user, returns user ID.
     */
    public function create(array $userData): string
    {
        if (!isset($userData['id'])) {
            $userData['id'] = uniqid();
        }

        // Basic sanitize (you may want to do better sanitization externally)
        $userData['username'] = trim($userData['username'] ?? '');
        $userData['email'] = filter_var($userData['email'] ?? '', FILTER_VALIDATE_EMAIL) ?: '';

        // Password should already be hashed before passing here
        if (!isset($userData['password']) || empty($userData['password'])) {
            throw new InvalidArgumentException('Password must be set and hashed.');
        }

        $this->usersCache[] = $userData;
        $this->save($this->usersCache);

        return $userData['id'];
    }

    /**
     * Find user by username.
     */
    public function findByUsername(string $username): ?array
    {
        foreach ($this->usersCache as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }

    /**
     * Attempt login with username and password.
     */
    public function attempt(string $username, string $password): bool|array
    {
        $user = $this->findByUsername($username);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

    /**
     * Update user data by ID.
     */
    public function update(string $id, array $newData): bool
    {
        foreach ($this->usersCache as &$user) {
            if ($user['id'] === $id) {
                // Prevent overwriting ID
                unset($newData['id']);

                // Optionally sanitize or validate fields here
                $user = array_merge($user, $newData);
                $this->save($this->usersCache);
                return true;
            }
        }
        return false;
    }

    /**
     * Delete user by ID.
     */
    public function delete(string $id): bool
    {
        $initialCount = count($this->usersCache);
        $this->usersCache = array_values(array_filter($this->usersCache, fn($user) => $user['id'] !== $id));
        if (count($this->usersCache) < $initialCount) {
            $this->save($this->usersCache);
            return true;
        }
        return false;
    }
}