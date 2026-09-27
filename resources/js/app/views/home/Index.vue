<script setup>
import PageHeader from '@/components/layout/PageHeader.vue'
import GridEditor from '@/components/grid/GridEditor.vue'
</script>

<template>
	<div>
		<PageHeader title="Startseite" />
		<GridEditor context="home" owner="home" />
	</div>
</template>
