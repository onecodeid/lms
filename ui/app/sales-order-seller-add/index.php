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
                  <v-layout column>
                     <v-flex xs12 v-show="selected_tab == 1">
                        <sales-order-seller-add-form></sales-order-seller-add-form>
                     </v-flex>

                     <v-flex xs12 v-show="selected_tab == 2">
                        <sales-order-seller-payment-method></sales-order-seller-payment-method>
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
      <!-- <script src="../../assets/js/moment.min.js"></script> -->
      <script src="../../assets/js/numeral.min.js"></script>
      <!-- <script src="../../assets/js/moment-locale-id.js"></script> -->
      <script src="../../assets/js/lodash.custom.min.js"></script>
      <script src="../../assets/js/axios.min.js"></script>
      <script src="../../assets/js/vue.js"></script>
      <script src="../../assets/js/vuex.js"></script>
      <script src="../../assets/js/vuetify.js"></script>
      <script src="../../assets/js/httpVueLoader.min.js"></script>
      <!-- <script src="../../assets/js/webcam.min.js"></script> -->
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

    if (this.$vuetify.breakpoint.smAndDown)
        window.location.replace('../sales-order-seller-add-mobile/')
        
    this.$store.dispatch('system/search_menu_group')

    <?php if (isset($_GET['lead'])) { ?>
      this.$store.commit('salesorder/set_common', ['lead_id', <?= $_GET['lead']; ?>])
      this.$store.dispatch('salesorder/search_lead')
   <?php } ?>
},
computed : {
    one_token () {
        return this.$store.state.system.one_token
    },

    selected_tab () {
        return this.$store.state.salesorder.selected_tab
    }
},

methods : {
    
},
   el: '#app',
   components: {
        "common-navbar" : httpVueLoader("../common/components/common-navbar.vue"),
        "common-toolbar" : httpVueLoader("../common/components/common-toolbar.vue"),
        "sales-order-seller-add-form" : httpVueLoader("./components/sales-order-seller-add-form.vue?t=<?=date('YmdHis');?>"),
        "sales-order-seller-payment-method" : httpVueLoader("./components/sales-order-seller-payment-method.vue?t=<?=date('YmdHis');?>")
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
