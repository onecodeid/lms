

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
      <script>
      window.local_date = '<?=date('Y-m-d');?>';
      </script>
   </head>

   <body>
      <div v-cloak id="app">
         <v-app id="oneApp" v-if="one_token" >
            <common-navbar></common-navbar>
            <common-toolbar></common-toolbar>
            <v-content class="blue lighten-5 one">
            
               <v-container pt-1 pl-1 pr-1 fluid>
                  <v-layout row>
                     <v-flex xs12>
                        <sales-order-admin-list v-if="$vuetify.breakpoint.mdAndUp"></sales-order-admin-list>
                        <sales-order-admin-list-mobile v-if="$vuetify.breakpoint.smAndDown"></sales-order-admin-list-mobile>
                        <sales-order-admin-show v-if="$vuetify.breakpoint.mdAndUp"></sales-order-admin-show>
                        <sales-order-admin-show-mobile v-if="$vuetify.breakpoint.smAndDown"></sales-order-admin-show-mobile>
                        <sales-order-quick-order v-if="$vuetify.breakpoint.mdAndUp"></sales-order-quick-order>
                        <sales-order-quick-order-mobile v-if="$vuetify.breakpoint.smAndDown"></sales-order-quick-order-mobile>
                        <sales-order-customer-list v-if="$vuetify.breakpoint.mdAndUp"></sales-order-customer-list>
                        <sales-order-customer-list-mobile v-if="$vuetify.breakpoint.smAndDown"></sales-order-customer-list-mobile>
                        <sales-order-payment-confirmation v-if="$vuetify.breakpoint.mdAndUp"></sales-order-payment-confirmation>
                        <sales-order-payment-confirmation-mobile v-if="$vuetify.breakpoint.smAndDown"></sales-order-payment-confirmation-mobile>
                        <sales-order-admin-filter></sales-order-admin-filter>
                        <common-dialog-progress></common-dialog-progress>
                        <common-dialog-warning :text="text_warning" v-if="dialog_warning"></common-dialog-warning>
                     </v-flex>
                  </v-layout>
               </v-container>
            </v-content>
            
            <!-- <v-footer class="mb-5 footer" app color="transparent">
                <v-spacer></v-spacer>
                
            </v-footer> -->
            <v-snackbar
                v-model="snackbar"
                :timeout=3000
                top
                multi-line
                >
                {{ snackbar_text }}
                <v-btn
                    color="pink"
                    flat
                    @click="snackbar = false"
                >
                    Tutup
                </v-btn>
            </v-snackbar>

         </v-app>
      </div>

      <!-- Vendor -->
      <!-- <script src="../../assets/js/moment.min.js"></script> -->
      <script src="../../assets/js/numeral.min.js"></script>
      <!-- <script src="../../assets/js/moment-locale-id.js"></script> -->
      <!-- <script src="../../assets/js/lodash.min.js"></script> -->
      <script src="../../assets/js/lodash.custom.min.js"></script>
      <!-- <script src="../../assets/js/lodash/debounce.js"></script> -->
      <script src="../../assets/js/axios.min.js"></script>
      <script src="../../assets/js/vue.js"></script>
      <script src="../../assets/js/vuex.js"></script>
      <script src="../../assets/js/vuetify.min.js"></script>
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

    this.$store.dispatch('system/search_menu_group')
    this.$store.commit('salesorder/set_common', ['sdate', '<?=date('Y-m-d');?>'])
    this.$store.commit('salesorder/set_common', ['edate', '<?=date('Y-m-d');?>'])
    // this.$store.dispatch('system/get_notif_unread')
},
computed : {
    one_token () {
        return this.$store.state.system.one_token
    },

    drawer : {
        get () { return this.$store.state.system.drawer },
        set (v) { this.$store.commit('system/set_drawer', v) }
    },

    text_warning () {
        return this.$store.state.text_warning
    },

    dialog_warning () {
        return this.$store.state.dialog_warning
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
        "common-dialog-progress" : httpVueLoader("../common/components/common-dialog-progress.vue"),
        "common-dialog-warning" : httpVueLoader("../common/components/common-dialog-warning.vue"),
        "sales-order-admin-list" : httpVueLoader("./components/sales-order-admin-list.vue"),
        "sales-order-admin-list-mobile" : httpVueLoader("./components/sales-order-admin-list-mobile.vue"),
        "sales-order-admin-show" : httpVueLoader("./components/sales-order-admin-show.vue?t=<?=date('YmdHis');?>"),
        "sales-order-admin-show-mobile" : httpVueLoader("./components/sales-order-admin-show-mobile.vue"),
        "sales-order-quick-order" : httpVueLoader("./components/sales-order-quick-order.vue?t=<?=date('YmdHis');?>"),
        "sales-order-quick-order-mobile" : httpVueLoader("./components/sales-order-quick-order-mobile.vue?t=<?=date('YmdHis');?>"),
        "sales-order-customer-list" : httpVueLoader("./components/sales-order-customer-list.vue"),
        "sales-order-customer-list-mobile" : httpVueLoader("./components/sales-order-customer-list-mobile.vue"),
        "sales-order-payment-confirmation" : httpVueLoader("./components/sales-order-payment-confirmation.vue"),
        "sales-order-payment-confirmation-mobile" : httpVueLoader("./components/sales-order-payment-confirmation-mobile.vue"),
        "sales-order-admin-filter" : httpVueLoader("./components/sales-order-admin-filter.vue")
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
