<?php

namespace App\Console\Commands;

use App\Models\AdminNotification;
use App\Models\User;
use App\Services\AdminNotificationService;
use Illuminate\Console\Command;

class SeedAdminNotifications extends Command
{
    protected $signature = 'notifications:seed-admin {count=15}';

    protected $description = 'Seed sample admin notifications for testing';

    public function handle(AdminNotificationService $service): int
    {
        $count = (int) $this->argument('count');
        $users = User::take(3)->get();

        if ($users->isEmpty()) {
            $this->error('No users found. Create users first.');

            return 1;
        }

        $locales = ['en', 'ar', 'fr'];
        $locale = $locales[array_rand($locales)];
        $created = 0;

        $types = [
            'new_user' => fn () => $service->newUserRegistered($users->random()),
            'new_payment' => fn () => $this->seedPaymentNotification($service, $users),
            'subscription_activated' => fn () => $service->subscriptionActivated(
                $users->random()->name,
                ['Personal', 'Professional', 'Enterprise'][array_rand(['Personal', 'Professional', 'Enterprise'])],
                $users->random(),
            ),
            'backup_completed' => fn () => $service->backupCompleted('backup_'.now()->format('Y-m-d_His').'.zip'),
            'system_alert' => fn () => $service->systemAlert(
                ['en' => 'System Alert', 'ar' => 'تنبيه النظام', 'fr' => 'Alerte système'],
                ['en' => 'Disk usage reached 85%. Consider cleaning up old backups.', 'ar' => 'استخدام القرص وصل 85%. يُنصح بتنظيف النسخ الاحتياطية القديمة.', 'fr' => "L'utilisation du disque a atteint 85%."],
            ),
        ];

        $typeKeys = array_keys($types);

        for ($i = 0; $i < $count; $i++) {
            $type = $typeKeys[array_rand($typeKeys)];
            try {
                $types[$type]();
                $created++;
            } catch (\Throwable $e) {
                $this->warn("Skipped {$type}: {$e->getMessage()}");
            }
        }

        $this->info("Created {$created} admin notifications.");

        return 0;
    }

    private function seedPaymentNotification(AdminNotificationService $service, $users): void
    {
        $amount = [990, 1990, 4990, 9990][array_rand([990, 1990, 4990, 9990])];
        $method = ['chargily', 'paypal', 'stripe', 'wise'][array_rand(['chargily', 'paypal', 'stripe', 'wise'])];
        $user = $users->random();

        $titles = [
            'en' => 'New Payment Received',
            'ar' => 'دفعة جديدة مستلمة',
            'fr' => 'Nouveau paiement reçu',
        ];
        $messages = [
            'en' => "Payment of \${$amount} from {$user->name} via {$method}.",
            'ar' => "دفعة بقيمة \${$amount} من {$user->name} عبر {$method}.",
            'fr' => "Paiement de \${$amount} de {$user->name} via {$method}.",
        ];

        AdminNotification::create([
            'type' => 'new_payment',
            'title_en' => $titles['en'],
            'title_ar' => $titles['ar'],
            'title_fr' => $titles['fr'],
            'message_en' => $messages['en'],
            'message_ar' => $messages['ar'],
            'message_fr' => $messages['fr'],
            'data' => ['amount' => $amount, 'currency' => 'DZD', 'method' => $method, 'user_id' => $user->id],
        ]);
    }
}
