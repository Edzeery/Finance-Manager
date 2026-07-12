<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function create(int $userId, string $type, array $titles, array $messages, ?array $data = null): Notification
    {
        return Notification::create([
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

        return $this->create($userId, "budget_nearing_limit", [
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
            'ar' => 'تذكير بحساب الزكاة',
            'fr' => 'Rappel de calcul de Zakat',
            'en' => 'Zakat calculation reminder',
        ], [
            'ar' => 'لم تقم بحساب الزكاة منذ أكثر من عام. يرجى تحديث أصولك وحساب الزكاة.',
            'fr' => "Vous n'avez pas calculé la Zakat depuis plus d'un an. Veuillez mettre à jour vos actifs.",
            'en' => 'You have not calculated Zakat for over a year. Please update your assets.',
        ]);
    }

    public function goalDeadlineApproaching(int $userId, string $goalName, int $daysLeft): Notification
    {
        return $this->create($userId, 'goal_deadline', [
            'ar' => "اقتراب موعد الهدف",
            'fr' => "Échéance d'objectif proche",
            'en' => 'Goal deadline approaching',
        ], [
            'ar' => "باقي $daysLeft يوماً على الموعد النهائي لهدف $goalName",
            'fr' => "Il reste $daysLeft jours avant l'échéance de l'objectif $goalName",
            'en' => "$daysLeft days left until the deadline for goal $goalName",
        ], ['days_left' => $daysLeft]);
    }
}
