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
                     <v-flex xs12>
                        <master-leadsource-list v-show="selected_tab.id=='TAB.SOURCE'"></master-leadsource-list>
                        <master-leadtype-list v-show="selected_tab.id=='TAB.TYPE'"></master-leadtype-list>
                        <master-leadattr-list v-show="selected_tab.id=='TAB.ATTR'"></master-leadattr-list>
                     </v-flex>
                  </v-layout>
               </v-container>
            </v-content>
            
            <v-footer class="mb-5 footer" app color="transparent">
                <v-spacer></v-spacer>
                
            </v-footer>
            
            <master-leadsource-new></master-leadsource-new>
            <master-leadtype-new></master-leadtype-new>
            <master-leadattr-new></master-leadattr-new>
         </v-app>
      </div>

      <!-- Vendor -->
      <script src="../../assets/js/moment.min.js"></script>
      <script src="../../assets/js/numeral.min.js"></script>
      <script src="../../assets/js/moment-locale-id.js"></script>
      <script src="../../assets/js/lodash.js"></script>
      <script src="../../assets/js/axios.min.js"></script>
      <script src="../../assets/js/vue.js"></script>
      <script src="../../assets/js/vuex.js"></script>
      <script src="../../assets/js/vuetify.js"></script>
      <script src="../../assets/js/httpVueLoader.js"></script>
      <script src="../../assets/js/webcam.min.js"></script>
      <script src="../assets/js/window_functions.min.js"></script>
      
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
    this.$store.dispatch('leadsource/search', {})
},
computed : {
    one_token () {
        return this.$store.state.system.one_token
    },

    selected_tab () {
       if (!this.$store.state.selected_tab) return {}
       return this.$store.state.selected_tab
    }
},

methods : {
    
},
   el: '#app',
   components: {
        "common-navbar" : httpVueLoader("../common/components/common-navbar.vue"),
        "common-toolbar" : httpVueLoader("../common/components/common-toolbar.vue"),
        "master-leadsource-list" : httpVueLoader("./components/master-leadsource-list.vue?t=<?=date('YmdHis');?>"),
        "master-leadsource-new" : httpVueLoader("../master-leadsource/components/master-leadsource-new.vue?t=<?=date('YmdHis');?>"),
        "master-leadtype-list" : httpVueLoader("./components/master-leadtype-list.vue?t=<?=date('YmdHis');?>"),
        "master-leadtype-new" : httpVueLoader("../master-leadtype/components/master-leadtype-new.vue?t=<?=date('YmdHis');?>"),
        "master-leadattr-list" : httpVueLoader("./components/master-leadattr-list.vue?t=<?=date('YmdHis');?>"),
        "master-leadattr-new" : httpVueLoader("./components/master-leadattr-new.vue?t=<?=date('YmdHis');?>")
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
