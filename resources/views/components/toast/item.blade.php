<template x-for="toast in toasts" :key="toast.id">
    <div class="toast-item"
         :style="'background:' + getBg(toast.type) + '; border-inline-start:4px solid ' + getBorderColor(toast.type)"
         x-transition:enter="toast-enter"
         x-transition:enter-start="toast-enter-start"
         x-transition:enter-end
         x-transition:leave="toast-leave"
         x-transition:leave-start
         x-transition:leave-end="toast-leave-end">

        <div class="toast-icon" :style="'--toast-bg:' + getColor(toast.type)">
            <i class="bi" :class="getIcon(toast.type)"></i>
        </div>

        <div class="toast-content">
            <template x-if="toast.title">
                <div class="toast-title" x-text="toast.title"></div>
            </template>
            <div class="toast-message" x-text="toast.message"></div>
        </div>

        <button class="toast-close" @click="remove(toast.id)" aria-label="{{ __('general.close') }}" title="{{ __('general.close') }}">
            <i class="bi bi-x"></i>
        </button>

        <div class="toast-progress">
            <div class="toast-progress-bar" :style="'width:' + toast.progress + '%; background:' + getBorderColor(toast.type)"></div>
        </div>
    </div>
</template>


