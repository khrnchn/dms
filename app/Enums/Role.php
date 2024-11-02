<?php

namespace App\Enums;

enum Role: string
{
    case SYSTEM_ADMIN = 'system_admin';
    case MANAGER = 'manager';
    case FILE_ADMIN = 'file_admin';
    case STAFF = 'staff';

    /**
     * Get all values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get human-readable label
     */
    public function label(): string
    {
        return match ($this) {
            self::SYSTEM_ADMIN => 'System Administrator',
            self::MANAGER => 'Manager',
            self::FILE_ADMIN => 'File Administrator',
            self::STAFF => 'Staff Member',
        };
    }

    /**
     * Get all cases as an array of [value => label]
     */
    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($role) => [
            $role->value => $role->label()
        ])->all();
    }

    /**
     * Check if role has system admin privileges
     */
    public function isSystemAdmin(): bool
    {
        return $this === self::SYSTEM_ADMIN;
    }

    /**
     * Check if role has manager privileges
     */
    public function isManager(): bool
    {
        return $this === self::MANAGER;
    }

    /**
     * Check if role has file admin privileges
     */
    public function isFileAdmin(): bool
    {
        return $this === self::FILE_ADMIN;
    }

    /**
     * Check if role is staff
     */
    public function isStaff(): bool
    {
        return $this === self::STAFF;
    }

    /**
     * Check if role has admin level privileges (system_admin or file_admin)
     */
    public function isAdmin(): bool
    {
        return in_array($this, [self::SYSTEM_ADMIN, self::FILE_ADMIN]);
    }
}
