<?php

namespace App\Services;

use App\Models\AdminNotification;
use App\Models\Payment;
use App\Models\User;

class AdminNotificationService
{
    public function create(string $type, array $titles, array $messages, ?array $data = null): AdminNotification
    {
        return AdminNotification::create([
            'type' => $type,
            'title_en' => $titles['en'] ?? null,
            'title_ar' => $titles['ar'] ?? null,
            'title_fr' => $titles['fr'] ?? null,
            'message_en' => $messages['en'] ?? null,
            'message_ar' => $messages['ar'] ?? null,
            'message_fr' => $messages['fr'] ?? null,
            'data' => $data,
        ]);
    }

    public function newUserRegistered(User $user): AdminNotification
    {
        return $this->create(
            'new_user',
            [
                'en' => 'New User Registered',
                'ar' => 'مستخدم جديد مسجل',
                'fr' => 'Nouvel utilisateur inscrit',
            ],
            [
                'en' => "{$user->name} ({$user->email}) has registered.",
                'ar' => "{$user->name} ({$user->email}) قام بالتسجيل.",
                'fr' => "{$user->name} ({$user->email}) s'est inscrit.",
            ],
            ['user_id' => $user->id, 'user_name' => $user->name, 'user_email' => $user->email]
        );
    }

    public function newPaymentReceived(Payment $payment, User $user): AdminNotification
    {
        $methodKey = $payment->paymentMethod?->key;

        return $this->create(
            'new_payment',
            [
                'en' => 'New Payment Received',
                'ar' => 'دفعة جديدة مستلمة',
                'fr' => 'Nouveau paiement reçu',
            ],
            [
                'en' => "Payment of \${$payment->amount} from {$user->name} via {$methodKey}.",
                'ar' => "دفعة بقيمة \${$payment->amount} من {$user->name} عبر {$methodKey}.",
                'fr' => "Paiement de \${$payment->amount} de {$user->name} via {$methodKey}.",
            ],
            ['payment_id' => $payment->id, 'amount' => $payment->amount, 'currency' => $payment->currency, 'method' => $methodKey, 'user_id' => $user->id]
        );
    }

    public function subscriptionActivated(string $userName, string $planName, User $user): AdminNotification
    {
        return $this->create(
            'subscription_activated',
            [
                'en' => 'Subscription Activated',
                'ar' => 'تم تفعيل الاشتراك',
                'fr' => 'Abonnement activé',
            ],
            [
                'en' => "{$userName} subscribed to {$planName} plan.",
                'ar' => "{$userName} اشترك في خطة {$planName}.",
                'fr' => "{$userName} s'est abonné au plan {$planName}.",
            ],
            ['user_id' => $user->id, 'plan_name' => $planName]
        );
    }

    public function backupCompleted(string $fileName): AdminNotification
    {
        return $this->create(
            'backup_completed',
            [
                'en' => 'Backup Completed',
                'ar' => 'اكتمل النسخ الاحتياطي',
                'fr' => 'Sauvegarde terminée',
            ],
            [
                'en' => "Backup {$fileName} was created successfully.",
                'ar' => "تم إنشاء النسخ الاحتياطي {$fileName} بنجاح.",
                'fr' => "La sauvegarde {$fileName} a été créée avec succès.",
            ],
            ['file_name' => $fileName]
        );
    }

    public function systemAlert(array $title, array $message, ?array $data = null): AdminNotification
    {
        return $this->create(
            'system_alert',
            $title,
            $message,
            $data
        );
    }

    public function markAsRead(int $id): void
    {
        $notification = AdminNotification::find($id);
        if ($notification) {
            $notification->markAsRead();
        }
    }

    public function markAllAsRead(): void
    {
        AdminNotification::where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
    }

    public function getUnreadCount(): int
    {
        return AdminNotification::where('is_read', false)->count();
    }

    public function getRecent(int $limit = 20): iterable
    {
        return AdminNotification::latest()->take($limit)->get();
    }
}
