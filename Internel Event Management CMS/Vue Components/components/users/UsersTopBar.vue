<template>
<section class="topbar">
      <div class="container">
        <div class="topbar-left">
          <h1 class="headline">Employees</h1>
        </div>
        <div class="topbar-right">
          <button class="btn-secondary">
             <img :src="siteUrl + '/images/import.svg'">
            <span>Import CSV</span>
          </button>
          <button class="btn-primary btn-primary--admin modal-trigger" data-modal="employee">
                     <img :src="siteUrl + '/images/employee.svg'">
            <span>Add Employee</span>
          </button>
        </div>
      </div>
    </section>
</template>

<script>
  import { siteUrl } from "./../../config";
  import {siteName} from './../../config';
	export default {
		data() {
			return {
        siteName: siteName,
        siteUrl : siteUrl
			}
		},
		methods: {}
	}
</script>