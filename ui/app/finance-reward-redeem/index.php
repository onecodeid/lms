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
      <script>
      window.local_date = '<?=date('Y-m-d');?>';
      </script>
   </head>

   <body>
      <div v-cloak id="app">
         <v-app id="oneApp"  v-if="one_token">
            <common-toolbar></common-toolbar>
            <v-content class="blue lighten-5 one" >
            <common-navbar></common-navbar>
               <v-container pt-1 pl-1 pr-1 fluid>
                  <v-layout row wrap>
                     <v-flex xs8 pa-2>
                     <h3 class="display-1 font-weight-light zalfa-text-title">PENUKARAN POIN REWARD</h3>
                     </v-flex>
                     <v-flex xs2 pr-1 v-for="(tab,n) in tabs" :key="n">
                        <v-btn color="success" block :dark=true depressed @click="select(tab)"
                           :outline="selected_tab.id!=tab.id" large class="mb-0 mt-2"><b>{{tab.label}}</b></v-btn>
                     </v-flex>
                  </v-layout>

                  <v-layout row v-show="selected_tab.id==1">
                     <v-flex xs3>
                        <finance-reward-redeem-member></finance-reward-redeem-member>
                     </v-flex>
                     <v-flex xs9 pl-2>
                        <finance-reward-redeem-reward></finance-reward-redeem-reward>
                     </v-flex>
                  </v-layout>

                  <v-layout row wrap>
                     <v-flex xs12 v-show="selected_tab.id==2">
                        <finance-reward-redeem-list></finance-reward-redeem-list>
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
    this.$store.dispatch('reward/search', {})
},
computed : {
    one_token () {
        return this.$store.state.system.one_token
    },

    selected_tab : {
       get () { return this.$store.state.selected_tab },
       set (v) { this.$store.commit('set_selected_tab', v) }
    },

    tabs () {
       return this.$store.state.tabs
    }
},

methods : {
    select (x) {
       this.selected_tab = x
    }
},
   el: '#app',
   components: {
        "common-navbar" : httpVueLoader("../common/components/common-navbar.vue"),
        "common-toolbar" : httpVueLoader("../common/components/common-toolbar.vue"),
        "finance-reward-redeem-reward" : httpVueLoader("./components/finance-reward-redeem-reward.vue"),
        "finance-reward-redeem-list" : httpVueLoader("./components/finance-reward-redeem-list.vue"),
        "finance-reward-redeem-member" : httpVueLoader("./components/finance-reward-redeem-member.vue"),
      //   "master-reward-new" : httpVueLoader("./components/master-reward-new.vue")
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
