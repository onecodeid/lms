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
   </head>

   <body>
      <div v-cloak id="app">
         <v-app id="oneApp" v-if="one_token" >
            <common-toolbar></common-toolbar>
            <v-content class="blue lighten-5 one" >
            <common-navbar></common-navbar>
               <v-container pt-1 pl-1 pr-1 fluid>
                  <v-layout row>
                     <v-flex xs12>
                        <finance-payment-admin-list></finance-payment-admin-list>
                        <finance-payment-admin-show></finance-payment-admin-show>
                     </v-flex>
                  </v-layout>
               </v-container>
            </v-content>
            
            <v-footer class="mb-5 footer" app color="transparent">
                <v-spacer></v-spacer>
                
            </v-footer>
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
},
computed : {
   one_token () {
        return this.$store.state.system.one_token
    }
},

methods : {
    
},
   el: '#app',
   components: {
        "common-navbar" : httpVueLoader("../common/components/common-navbar.vue"),
        "common-toolbar" : httpVueLoader("../common/components/common-toolbar.vue"),
        "finance-payment-admin-list" : httpVueLoader("./components/finance-payment-admin-list.vue"),
        "finance-payment-admin-show" : httpVueLoader("./components/finance-payment-admin-show.vue")
      }
    })
    </script>
    <style>
[v-cloak] {
   display: none
}
    .v-content.one {
       /* padding:64px 0px 0px !important; */
    }
    </style>
   </body>

</html>
