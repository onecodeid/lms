<?php
$ts = "?ts=" . Date("ymdhis");
?>
<!DOCTYPE html>
<html lang="en">

   <head>
<meta name="robots" content="noindex">
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta http-equiv="X-UA-Compatible" content="ie=edge">
      <title>One</title>
      <link rel="stylesheet" href="../../assets/css/google-fonts.css">
      <link rel="stylesheet" href="../../assets/css/vuetify.min.css">
      <link rel="stylesheet" href="../assets/css/global.min.css<?= $ts; ?>">
      <link rel="icon" type="image/x-icon" href="../assets/favicon_io/favicon.ico">
      <!-- <script src="../../../libs/one_global_clinic.js"></script> -->
   </head>

   <body>
      <div v-cloak id="app">
         <v-app id="oneApp"  v-if="one_token">
            <common-toolbar></common-toolbar>
            <v-content class="blue lighten-5 one" >
            <common-navbar></common-navbar>
               <v-container pt-1 pl-1 pr-1 fluid>
                  <v-layout row>
                     <v-flex xs4 pr-2>
                        <crm-post-sales-filter></crm-post-sales-filter>
                     </v-flex>
                     <v-flex xs8>
                        <crm-post-sales-list></crm-post-sales-list>
                     </v-flex>
                  </v-layout>
               </v-container>
            </v-content>
            
            <v-footer class="mb-5 footer" app color="transparent">
                <v-spacer></v-spacer>
                
            </v-footer>
            
            <crm-post-sales-save></crm-post-sales-save>
            <crm-post-sales-wa-template></crm-post-sales-wa-template>
            <crm-post-sales-wa-presend></crm-post-sales-wa-presend>
            <crm-post-sales-show-order></crm-post-sales-show-order>
            <common-dialog-progress></common-dialog-progress>
            <crm-post-sales-broadcast></crm-post-sales-broadcast>
         </v-app>
      </div>

      <!-- Vendor -->
      <script src="../../assets/js/moment.min.js"></script>
      <script src="../../assets/js/numeral.min.js"></script>
      <script src="../../assets/js/moment-locale-id.js"></script>
      <script src="../../assets/js/lodash.custom.min.js"></script>
      <script src="../../assets/js/axios.min.js"></script>
      <script src="../../assets/js/vue.js"></script>
      <script src="../../assets/js/vuex.js"></script>
      <script src="../../assets/js/vuetify.js"></script>
      <script src="../../assets/js/httpVueLoader.js"></script>
      <script src="../../assets/js/webcam.min.js"></script>
      <script src="../assets/js/window_functions.min.js"></script>

      <script>
      window.local_date = '<?=date('Y-m-d');?>';
      </script>
      
      <!-- App Script -->
<?php
$ts = "?ts=" . Date("ymdhis");
?>

<style scoped>
.footer {
    width: 100px;
    right: 0px;
    left: auto;
}

.v-content__wrap {
    /* padding-left: 80px; */
    padding-left: 0px
}
</style>

<script type="module">

import { store } from './store.js<?php echo $ts ?>';
//for testing
window.store = store;
new Vue({
store,
data : {
   
},
mounted: function() {
    if (!this.$store.state.system.one_token)
        window.location.replace('../system-login/')

    this.$store.dispatch('system/search_menu_group')
},
computed : {
    one_token () {
        return this.$store.state.system.one_token
    },

    selected_tab () {
        return this.$store.state.packetdetail.selected_tab
    },

    snackbar : {
        get () { return this.$store.state.snackbar },
        set (v) { this.$store.commit('set_snackbar', v) }
    },

    snackbar_text () {
        return this.$store.state.snackbar_text
    }
},

methods : {
    
},
   el: '#app',
   components: {
        "common-navbar" : httpVueLoader("../common/components/common-navbar.vue"),
        "common-toolbar" : httpVueLoader("../common/components/common-toolbar.vue"),
        "crm-post-sales-filter" : httpVueLoader("./components/crm-post-sales-filter-tab.vue?t=<?=date('YmdHis');?>"),
        "crm-post-sales-list" : httpVueLoader("./components/crm-post-sales-list.vue?t=<?=date('YmdHis');?>"),
        "crm-post-sales-save" : httpVueLoader("./components/crm-post-sales-save.vue?t=<?=date('YmdHis');?>"),
        "crm-post-sales-wa-template" : httpVueLoader("./components/crm-post-sales-wa-template.vue?t=<?=date('YmdHis');?>"),
        "crm-post-sales-wa-presend" : httpVueLoader("./components/crm-post-sales-wa-presend.vue?t=<?=date('YmdHis');?>"),
        "crm-post-sales-show-order" : httpVueLoader("./components/crm-post-sales-show-order.vue?t=<?=date('YmdHis');?>"),
        "crm-post-sales-broadcast" : httpVueLoader("./components/crm-post-sales-broadcast.vue?t=<?=date('YmdHis');?>"),
        "common-dialog-progress" : httpVueLoader("../common/components/common-dialog-progress.vue?t=<?=date('YmdHis');?>")
      }
    })
    </script>
    <style>
[v-cloak] {
   display: none
}
    .v-content.one {
       //padding:64px 0px 0px !important;
    }
    </style>
   </body>

</html>
