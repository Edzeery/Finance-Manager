<x-app-layout>
    <x-slot:title>{{ __('income.categories') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('income.categories') }}</x-slot>

    <x-category-crud
        type="income"
        :categories="$categories"
        :storeRoute="route('income.categories.store')"
        :updateRoute="route('income.categories.update', ':id')"
        destroyRoute="income.categories.destroy"
        :types="[
            'variable' => __('income.variable'),
            'fixed' => __('income.fixed'),
            'recurring' => __('income.recurring'),
        ]"
        defaultColor="#22C55E"
        defaultIcon="bi-currency-dollar"
        badgeClass="badge-income"
    />
</x-app-layout>
