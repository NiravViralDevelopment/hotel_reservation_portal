<?php

namespace App\Support;

use App\Models\AuditLog;

class ActivityDescription
{
    /**
     * @var array<string, string>
     */
    private const MODULE_LABELS = [
        'enquiries' => 'enquiry',
        'group_bookings' => 'group booking',
        'cancelled_bookings' => 'cancelled booking',
        'cancelled_inquiries' => 'cancelled inquiry',
        'group-booking-attachments' => 'group booking attachment',
        'group-booking-contract-photos' => 'contract photo',
        'group-booking-contracts' => 'booking contract',
        'companies' => 'company',
        'hotels' => 'hotel',
        'travel_agencies' => 'travel agency',
        'users' => 'user',
        'roles' => 'role',
        'documents' => 'document',
        'contacts' => 'contact',
        'status_masters' => 'status',
        'company-contracts' => 'company contract',
        'profile' => 'profile',
        'settings' => 'settings',
        'bob' => 'BOB',
        'auth' => 'account',
    ];

    /**
     * @var array<string, string>
     */
    private const ACTION_VERBS = [
        'created' => 'Created',
        'updated' => 'Updated',
        'deleted' => 'Deleted',
        'cancelled' => 'Cancelled',
        'confirmed' => 'Confirmed',
        'exported' => 'Exported',
        'uploaded' => 'Uploaded',
        'downloaded' => 'Downloaded',
        'activated' => 'Activated',
        'deactivated' => 'Deactivated',
        'imported' => 'Imported',
        'logged_in' => 'Signed in',
        'logged_out' => 'Signed out',
    ];

    public static function for(AuditLog $log): string
    {
        $action = strtolower((string) $log->action);
        $module = (string) $log->module;
        $details = trim((string) ($log->details ?? ''));
        $moduleLabel = self::MODULE_LABELS[$module] ?? str_replace(['_', '-'], ' ', $module);

        if ($action === 'logged_in') {
            return 'Signed in to the system';
        }

        if ($action === 'logged_out') {
            return 'Signed out of the system';
        }

        if (str_starts_with($module, 'reports-')) {
            $report = str_replace('-', ' ', substr($module, 8));

            return 'Exported '.$report.' report'.($details !== '' ? ' ('.$details.')' : '');
        }

        $verb = self::ACTION_VERBS[$action] ?? ucfirst(str_replace('_', ' ', $action));

        if ($module === 'profile' && str_contains(strtolower($details), 'password')) {
            return 'Changed account password';
        }

        if ($module === 'profile') {
            return 'Updated profile details'.($details !== '' ? ' ('.$details.')' : '');
        }

        if ($module === 'settings') {
            return 'Updated application settings';
        }

        if ($action === 'exported') {
            return 'Exported '.$moduleLabel.' data'.($details !== '' ? ' ('.$details.')' : '');
        }

        if ($action === 'imported') {
            return 'Imported '.$moduleLabel.($details !== '' ? ' ('.$details.')' : '');
        }

        return $verb.' '.$moduleLabel.($details !== '' ? ': '.$details : '');
    }

    public static function tone(string $action): string
    {
        return match (strtolower($action)) {
            'created', 'confirmed', 'activated', 'uploaded', 'imported', 'logged_in' => 'success',
            'updated', 'exported', 'downloaded' => 'info',
            'cancelled', 'deactivated', 'logged_out' => 'warning',
            'deleted' => 'danger',
            default => 'neutral',
        };
    }

    public static function icon(string $action): string
    {
        return match (strtolower($action)) {
            'created' => 'plus-circle',
            'updated' => 'pencil-square',
            'deleted' => 'trash',
            'cancelled' => 'x-circle',
            'confirmed' => 'check-circle',
            'exported' => 'download',
            'uploaded' => 'upload',
            'downloaded' => 'file-earmark-arrow-down',
            'activated' => 'toggle-on',
            'deactivated' => 'toggle-off',
            'imported' => 'file-earmark-plus',
            'logged_in' => 'box-arrow-in-right',
            'logged_out' => 'box-arrow-right',
            default => 'activity',
        };
    }

    public static function moduleLabel(string $module): string
    {
        if (str_starts_with($module, 'reports-')) {
            return 'Reports';
        }

        if (isset(self::MODULE_LABELS[$module])) {
            $label = self::MODULE_LABELS[$module];

            return $label === 'BOB' ? 'BOB' : ucwords($label);
        }

        return ucwords(str_replace(['_', '-'], ' ', $module));
    }
}
