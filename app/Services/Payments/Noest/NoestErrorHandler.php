<?php

namespace App\Services\Payments\Noest;

class NoestErrorHandler
{
    private static array $messages = [
        'account_suspended'                          => 'الحساب موقوف، يرجى التواصل مع الدعم.',
        'commune inexistante ou non activée'         => 'البلدية غير موجودة أو غير مفعّلة.',
        'zip_code invalide'                          => 'الرمز البريدي غير صحيح.',
        'Le code de wilaya est different'            => 'كود الولاية لا يتطابق مع كود المكتب.',
        'Aucune commune liee a la station choisie'   => 'لا توجد بلدية مرتبطة بالمكتب المختار.',
        'Il faut choisir un code de station valide'  => 'يرجى اختيار مكتب توصيل صحيح.',
        'Module stopdesk désactivé'                  => 'خدمة الاستلام من المكتب غير متاحة لهذه الولاية.',
        'montant doit être inferieur'                => 'المبلغ يتجاوز الحد المسموح به.',
    ];

    public static function translate(string $apiError): string
    {
        foreach (self::$messages as $key => $msg) {
            if (str_contains($apiError, $key)) {
                return $msg;
            }
        }
        return 'حدث خطأ أثناء إنشاء الطلب، يرجى المحاولة مجدداً.';
    }
}
