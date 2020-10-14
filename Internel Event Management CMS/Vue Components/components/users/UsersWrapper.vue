<template>
	<div>
	<user-topbar></user-topbar>
	<user></user>
</div>
</template>

<script>
	import UsersTopBar from './../users/UsersTopBar.vue'
	import Users from './../users/Users.vue'
	export default {
		components:{
			'user-topbar': UsersTopBar,
			'user': Users
		}
	}
</script>

