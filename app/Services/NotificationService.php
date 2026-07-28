<?php

namespace App\Services;

use App\Mail\NotificationEmail;
use App\Models\Notification;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    private const ICON_MAP = [
        'budget_exceeded' => ['icon' => 'bi-exclamation-triangle', 'color' => '#ef4444'],
        'budget_nearing_limit' => ['icon' => 'bi-exclamation-circle', 'color' => '#f59e0b'],
        'debt_reminder' => ['icon' => 'bi-credit-card-2-front', 'color' => '#f59e0b'],
        'goal_achieved' => ['icon' => 'bi-flag-fill', 'color' => '#22c55e'],
        'goal_milestone' => ['icon' => 'bi-flag', 'color' => '#22c55e'],
        'goal_deadline' => ['icon' => 'bi-clock-history', 'color' => '#3b82f6'],
        'zakat_reminder' => ['icon' => 'bi-heart-fill', 'color' => '#8b5cf6'],
        'zakat_approaching' => ['icon' => 'bi-hourglass-split', 'color' => '#6366f1'],
        'login_new_device' => ['icon' => 'bi-phone', 'color' => '#3b82f6'],
        'login_suspicious' => ['icon' => 'bi-shield-exclamation', 'color' => '#ef4444'],
        'password_changed' => ['icon' => 'bi-key', 'color' => '#f59e0b'],
        'two_factor_enabled' => ['icon' => 'bi-shield-lock', 'color' => '#22c55e'],
        'two_factor_disabled' => ['icon' => 'bi-shield-x', 'color' => '#ef4444'],
        'session_revoked' => ['icon' => 'bi-box-arrow-right', 'color' => '#f97316'],
        'email_changed' => ['icon' => 'bi-envelope-at', 'color' => '#3b82f6'],
        'workspace_member_login' => ['icon' => 'bi-person-check', 'color' => '#15b76c'],
    ];

    public function create(int $userId, string $type, array $titles, array $messages, ?array $data = null): ?Notification
    {
        $notification = null;

        // In-app notification
        if (NotificationPreference::isEnabledFor($userId, $type, 'in_app')) {
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title_ar' => $titles['ar'],
                'title_fr' => $titles['fr'],
                'title_en' => $titles['en'],
                'message_ar' => $messages['ar'],
                'message_fr' => $messages['fr'],
                'message_en' => $messages['en'],
                'data' => $data,
                'is_read' => false,
            ]);
        }

        // Email notification
        if (NotificationPreference::isEnabledFor($userId, $type, 'email')) {
            $this->sendEmail($userId, $type, $titles, $messages);
        }

        return $notification;
    }

    private function sendEmail(int $userId, string $type, array $titles, array $messages): void
    {
        $user = User::find($userId);
        if (! $user || ! $user->email) {
            return;
        }

        $locale = $user->locale ?? app()->getLocale();
        $title = $titles[$locale] ?? $titles['en'] ?? $titles[array_key_first($titles)];
        $message = $messages[$locale] ?? $messages['en'] ?? $messages[array_key_first($messages)];
        $iconMeta = self::ICON_MAP[$type] ?? ['icon' => 'bi-bell', 'color' => '#15b76c'];

        Mail::to($user)->send(new NotificationEmail(
            $user,
            $title,
            $message,
            $iconMeta['icon'],
            $iconMeta['color'],
        ));
    }

    public function budgetExceeded(int $userId, string $budgetName, float $amount): Notification
    {
        return $this->create($userId, 'budget_exceeded', [
            'ar' => 'تجاوز الميزانية',
            'fr' => 'Budget dépassé',
            'en' => 'Budget exceeded',
        ], [
            'ar' => "تم تجاوز ميزانية $budgetName بمبلغ $amount",
            'fr' => "Le budget $budgetName a été dépassé de $amount",
            'en' => "Budget $budgetName has been exceeded by $amount",
        ]);
    }

    public function debtReminder(int $userId, string $counterparty, float $amount, string $dueDate): Notification
    {
        return $this->create($userId, 'debt_reminder', [
            'ar' => 'تذكير دين',
            'fr' => 'Rappel de dette',
            'en' => 'Debt reminder',
        ], [
            'ar' => "الدين المستحق لـ $counterparty بمبلغ $amount تاريخ $dueDate",
            'fr' => "Dette due à $counterparty de $amount due le $dueDate",
            'en' => "Debt owed to $counterparty of $amount due on $dueDate",
        ]);
    }

    public function budgetNearingLimit(int $userId, string $budgetName, float $spentPercent): Notification
    {
        $threshold = $spentPercent >= 100 ? 100 : (int) (floor($spentPercent / 10) * 10);
        $level = $spentPercent >= 100 ? 'exceeded' : "{$threshold}%";

        return $this->create($userId, 'budget_nearing_limit', [
            'ar' => "الميزانية تقترب من الحد: $level",
            'fr' => "Budget proche de la limite : $level",
            'en' => "Budget nearing limit: $level",
        ], [
            'ar' => "ميزانية $budgetName وصلت إلى {$spentPercent}% من الحد المسموح",
            'fr' => "Le budget $budgetName a atteint {$spentPercent}% de la limite",
            'en' => "Budget $budgetName has reached {$spentPercent}% of the limit",
        ], ['budget_id' => $budgetName, 'percent' => $spentPercent]);
    }

    public function goalAchieved(int $userId, string $goalName): Notification
    {
        return $this->create($userId, 'goal_achieved', [
            'ar' => 'تم تحقيق الهدف',
            'fr' => 'Objectif atteint',
            'en' => 'Goal achieved',
        ], [
            'ar' => "تهانينا! لقد حققت هدف $goalName",
            'fr' => "Félicitations ! Vous avez atteint l'objectif $goalName",
            'en' => "Congratulations! You have achieved the goal $goalName",
        ]);
    }

    public function goalMilestoneReached(int $userId, string $goalName, int $percent): Notification
    {
        return $this->create($userId, 'goal_milestone', [
            'ar' => "إنجاز في الهدف: $percent%",
            'fr' => "Progrès sur l'objectif : $percent%",
            'en' => "Goal milestone: $percent%",
        ], [
            'ar' => "هدف $goalName وصل إلى $percent% من الإنجاز",
            'fr' => "L'objectif $goalName a atteint $percent% de réalisation",
            'en' => "Goal $goalName has reached $percent% completion",
        ], ['percent' => $percent]);
    }

    public function zakatReminder(int $userId): Notification
    {
        return $this->create($userId, 'zakat_reminder', [
            'ar' => 'حساب الزكاة واجب الآن',
            'fr' => 'Zakat calculation due',
            'en' => 'Zakat calculation due',
        ], [
            'ar' => 'اكتمل حَوْل الزكاة — الزكاة واجبة الآن. يرجى حساب أصولك وحفظ السجل.',
            'fr' => 'Le Haul de la Zakat est terminé. La Zakat est due. Veuillez calculer vos actifs.',
            'en' => 'Your Zakat haul is complete — Zakat is now due. Please calculate your assets.',
        ]);
    }

    public function zakatApproachingReminder(int $userId, int $daysLeft): Notification
    {
        $title = match (true) {
            $daysLeft <= 1 => [
                'ar' => 'الزكاة واجبة غداً!',
                'fr' => 'Zakat due demain !',
                'en' => 'Zakat due tomorrow!',
            ],
            $daysLeft <= 7 => [
                'ar' => 'تذكير: موعد الزكاة قريب',
                'fr' => 'Rappel : Zakat proche',
                'en' => 'Reminder: Zakat approaching',
            ],
            default => [
                'ar' => 'تذكير: اقتراب موعد الزكاة',
                'fr' => 'Rappel : Échéance de Zakat',
                'en' => 'Reminder: Zakat due date ahead',
            ],
        };

        $message = [
            'ar' => "باقي $daysLeft يوماً على اكتمال حَوْل الزكاة. يرجى التحضير لحساب الأصول.",
            'fr' => "Il reste $daysLeft jours avant l'achèvement du Haul. Veuillez préparer le calcul.",
            'en' => "$daysLeft days until your Zakat haul completes. Please prepare to calculate.",
        ];

        return $this->create($userId, 'zakat_approaching', $title, $message, [
            'days_left' => $daysLeft,
            'route' => 'zakat.calculator',
        ]);
    }

    public function goalDeadlineApproaching(int $userId, string $goalName, int $daysLeft): Notification
    {
        return $this->create($userId, 'goal_deadline', [
            'ar' => 'اقتراب موعد الهدف',
            'fr' => "Échéance d'objectif proche",
            'en' => 'Goal deadline approaching',
        ], [
            'ar' => "باقي $daysLeft يوماً على الموعد النهائي لهدف $goalName",
            'fr' => "Il reste $daysLeft jours avant l'échéance de l'objectif $goalName",
            'en' => "$daysLeft days left until the deadline for goal $goalName",
        ], ['days_left' => $daysLeft]);
    }

    // ── Security Notifications ──────────────────────────────────

    public function loginNewDevice(int $userId, string $device, string $browser, string $os, string $ip): Notification
    {
        return $this->create($userId, 'login_new_device', [
            'ar' => 'تسجيل دخول من جهاز جديد',
            'fr' => 'Connexion depuis un nouvel appareil',
            'en' => 'Login from new device',
        ], [
            'ar' => "تم تسجيل الدخول من $device ($browser على $os) — IP: $ip",
            'fr' => "Connexion depuis $device ($browser sur $os) — IP : $ip",
            'en' => "Logged in from $device ($browser on $os) — IP: $ip",
        ], ['device' => $device, 'browser' => $browser, 'os' => $os, 'ip' => $ip]);
    }

    public function loginSuspicious(int $userId, string $ip, string $reason): Notification
    {
        return $this->create($userId, 'login_suspicious', [
            'ar' => 'محاولة دخول مشبوهة',
            'fr' => 'Tentative de connexion suspecte',
            'en' => 'Suspicious login attempt',
        ], [
            'ar' => "تم رصد محاولة دخول مشبوهة من IP: $ip — $reason",
            'fr' => "Tentative de connexion suspecte depuis IP : $ip — $reason",
            'en' => "Suspicious login attempt from IP: $ip — $reason",
        ], ['ip' => $ip, 'reason' => $reason]);
    }

    public function passwordChanged(int $userId): Notification
    {
        return $this->create($userId, 'password_changed', [
            'ar' => 'تم تغيير كلمة المرور',
            'fr' => 'Mot de passe modifié',
            'en' => 'Password changed',
        ], [
            'ar' => 'تم تغيير كلمة المرور بنجاح. إذا لم تقم بذلك، يرجى تغييرها فوراً.',
            'fr' => 'Votre mot de passe a été modifié. Si ce n\'est pas vous, changez-le immédiatement.',
            'en' => 'Your password was changed. If you did not do this, change it immediately.',
        ]);
    }

    public function twoFactorEnabled(int $userId, string $method): Notification
    {
        return $this->create($userId, 'two_factor_enabled', [
            'ar' => 'تم تفعيل المصادقة الثنائية',
            'fr' => 'Authentification à deux facteurs activée',
            'en' => 'Two-factor authentication enabled',
        ], [
            'ar' => "تم تفعيل المصادقة الثنائية عبر $method.",
            'fr' => "L'authentification à deux facteurs a été activée via $method.",
            'en' => "Two-factor authentication has been enabled via $method.",
        ], ['method' => $method]);
    }

    public function twoFactorDisabled(int $userId): Notification
    {
        return $this->create($userId, 'two_factor_disabled', [
            'ar' => 'تم تعطيل المصادقة الثنائية',
            'fr' => 'Authentification à deux facteurs désactivée',
            'en' => 'Two-factor authentication disabled',
        ], [
            'ar' => 'تم تعطيل المصادقة الثنائية. حسابك أصبح أقل أماناً.',
            'fr' => "L'authentification à deux facteurs a été désactivée. Votre compte est moins sécurisé.",
            'en' => 'Two-factor authentication has been disabled. Your account is less secure.',
        ]);
    }

    public function sessionRevoked(int $userId, string $sessionInfo): Notification
    {
        return $this->create($userId, 'session_revoked', [
            'ar' => 'تم إلغاء جلسة',
            'fr' => 'Session révoquée',
            'en' => 'Session revoked',
        ], [
            'ar' => "تم إلغاء الجلسة: $sessionInfo",
            'fr' => "Session révoquée : $sessionInfo",
            'en' => "Session revoked: $sessionInfo",
        ], ['session_info' => $sessionInfo]);
    }

    public function emailChanged(int $userId, string $newEmail): Notification
    {
        return $this->create($userId, 'email_changed', [
            'ar' => 'تم تغيير البريد الإلكتروني',
            'fr' => 'Adresse email modifiée',
            'en' => 'Email address changed',
        ], [
            'ar' => "تم تغيير البريد الإلكتروني إلى: $newEmail",
            'fr' => "Votre adresse email a été changée en : $newEmail",
            'en' => "Your email address has been changed to: $newEmail",
        ], ['new_email' => $newEmail]);
    }

    public function workspaceMemberLoggedIn(int $ownerUserId, string $memberName, string $memberEmail, int $daysSinceLastLogin): Notification
    {
        $absence = $daysSinceLastLogin === 0
            ? __('notifications.new_member_first_login')
            : __('notifications.member_absence_days', ['days' => $daysSinceLastLogin]);

        return $this->create($ownerUserId, 'workspace_member_login', [
            'ar' => 'دخول عضو في المساحة',
            'fr' => 'Connexion d\'un membre',
            'en' => 'Workspace member login',
        ], [
            'ar' => "قام $memberName ($memberEmail) بتسجيل الدخول — $absence.",
            'fr' => "$memberName ($memberEmail) s'est connecté — $absence.",
            'en' => "$memberName ($memberEmail) logged in — $absence.",
        ], ['member_name' => $memberName, 'member_email' => $memberEmail, 'days' => $daysSinceLastLogin]);
    }
}
