<template>
	<div>
		<top-menu></top-menu>
		<transition name="fade" mode="out-in">
			<router-view :key="$route.path"></router-view>
		</transition>
		<app-footer></app-footer>
	</div>
</template>

<script>
	import TopMenu from './shared/topMenu.vue';
	import AppFooter from './shared/appFooter.vue';

	export default {
		components: {
			'top-menu': TopMenu,
			'app-footer': AppFooter
		}
	}
</script>
