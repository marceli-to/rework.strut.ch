<script setup>
// Single source: resources/svg/logo.svg (also used by the Blade <x-icons.logo>)
import logo from '../../../../svg/logo.svg?raw'
</script>

<template>
	<span class="block [&>svg]:w-full [&>svg]:h-auto" v-html="logo" />
</template>
