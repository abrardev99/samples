<template>
    <vue-bootstrap4-table :rows="users" 
                :columns="columns" 
                :config="config" 
                :actions="actions"
                @on-internal="onInternal"
                @on-external="onExternal">
        </vue-bootstrap4-table>
</template>

<script>
  import {api} from "../../config";
  import VueBootstrap4Table from 'vue-bootstrap4-table'

  export default{
    components:{
      'vue-bootstrap4-table': VueBootstrap4Table
    },
    data(){
      return{
        users: [],
        internalUsers:[],
        externalUsers:[],
        userCopy:[],
        columns: [{
                    label: "id",
                    name: "id",
                    sort: true,
                },
                {
                    label: "First Name",
                    name: "first_name",
                    sort: true,
                },
                {
                    label: "Last Name",
                    name: "last_name",
                    sort: true,
                },
                {
                    label: "Email",
                    name: "email",
                    sort: true,
                },
                {
                    label: "Type",
                    name: "type", 
                }],
                actions:[
                {
                  btn_text: "Internal User",
                  event_name: "on-internal",
                  class:"btn-primary btn-primary--admin modal-trigger",
                  event_payload:{
                    msg: "2"
                  }
                },
                {
                  btn_text: "External User",
                  event_name: "on-external",
                  class:"btn-primary btn-primary--admin modal-trigger",
                  event_payload:{
                    msg: "3"
                  }
                }
                  ],
                config: {
                rows_selectable: true,
                pagination: true,
                pagination_info: true, 
                num_of_visibile_pagination_buttons: 7,
                per_page: 5, 
                per_page_options:  [5,  10,  20,  30],
                global_search:{
                  visibility: false
                }
            }
      }
    }, 
    methods:{
      getUsers(){
           axios.get(api.users)
                .then(res => {

                  const results = [];
                  for(let key in res.data){
                    results.push(res.data[key])
                  }
                  this.users = results;
                  this.userCopy = results;
                  console.log(this.users);
                })
                .catch(err => {
                  (err.response.data.error) && this.$noty.error(err.response.data.error);

                  (err.response.data.errors)
                          ? this.setErrors(err.response.data.errors)
                          : this.clearErrors();

                });
      },
      onInternal(payload){
        this.users=[];
        this.internalUsers=[];
        this.users=this.userCopy;
         for(let i=0; i<this.users.length; i++)
         {
          if(this.users[i].role_id == payload.event_payload.msg)
          {
            this.internalUsers.push(this.users[i]);
          }
         }
         this.users= this.internalUsers;
         console.log(this.users);
      },
       onExternal(payload){
        this.users=[];
        this.externalUsers=[];
        this.users=this.userCopy;
        for(let i=0; i<this.users.length; i++)
         {
          if(this.users[i].role_id == payload.event_payload.msg)
          {
            this.externalUsers.push(this.users[i]);
          }
         }
         this.users= this.externalUsers;
         console.log(this.users);
      }

    },
    created(){
      this.getUsers();
    }
  }

</script>
