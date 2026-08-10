<x-app-layout>
    <x-slot:title>{{ __('expense.categories') }} - {{ config('app.name') }}</x-slot>
    <x-slot:page-title>{{ __('expense.categories') }}</x-slot>

    <x-category-crud
        type="expense"
        :categories="$categories"
        :storeRoute="route('expense.categories.store')"
        :updateRoute="route('expense.categories.update', ':id')"
        destroyRoute="expense.categories.destroy"
        :types="[
            'variable' => __('expense.variable'),
            'fixed' => __('expense.fixed'),
            'periodic' => __('expense.periodic'),
        ]"
        defaultColor="#EF4444"
        defaultIcon="bi-cart"
        badgeClass="badge-expense"
        showBudgetPercentage="true"
    />
</x-app-layout>
