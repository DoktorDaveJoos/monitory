<template>
    <button
        class="inline-flex shadow-md items-center px-4 py-2 bg-primary border border-transparent rounded-lg font-bold text-text-light text-xs uppercase tracking-widest hover:bg-primary/90 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150"
    >
        <slot />


        <slot name="icon" />
    </button>
</template>
