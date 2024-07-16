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
      <link rel="stylesheet" href="assets/css/google-fonts.css">
      <link rel="stylesheet" href="assets/css/vuetify.min.css">
      <link rel="stylesheet" href="app/assets/css/global.min.css">
   </head>

   <body>
      <div v-cloak id="app">
         <v-app id="oneApp">
            <v-content class="blue lighten-5 one" >
               <v-container pt-1 pl-1 pr-1 fluid>
                  <v-layout row>
                     <v-flex md4 offset-md4>
                        <member-search></member-search>
                     </v-flex>
                  </v-layout>
               </v-container>
            </v-content>
         </v-app>
      </div>

      <!-- Vendor -->
      
      <script src="assets/js/axios.min.js"></script>
      <script src="assets/js/vue.min.js"></script>
      <script src="assets/js/vuex.min.js"></script>
      <script src="assets/js/vuetify.min.js"></script>
      <script src="assets/js/httpVueLoader.min.js"></script>
      
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
        "member-search" : httpVueLoader("./components/member-search.vue")
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
