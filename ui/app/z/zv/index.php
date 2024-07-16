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
      <link rel="stylesheet" href="../../../assets/css/google-fonts.css">
      <link rel="stylesheet" href="../../../assets/css/vuetify.min.css">
      <link rel="stylesheet" href="../../assets/css/global.min.css">
      <link href="https://vjs.zencdn.net/4.3/video-js.css" rel="stylesheet">
      <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Quicksand">
      <script src="https://vjs.zencdn.net/4.3/video.js"></script>
      <!-- <script src="../../../libs/one_global_clinic.js"></script> -->
   </head>

   <body>
      <div v-cloak id="app">
         <v-app id="oneApp" >
            
            <v-content class="blue lighten-5 one" >
                <v-container pt-1 pl-1 pr-1 fluid>
                    <video id="my_video_1" class="video-js vjs-default-skin vjs-4-3" controls preload="auto" 
                    data-setup='{ "asdf": true }' poster="http://video-js.zencoder.com/oceans-clip.png" >
                        <source :src="uri" type='video/mp4'>
                        <!-- <source src="https://vjs.zencdn.net/v/oceans.webm" type='video/webm'> -->
                    </video>
               </v-container>
            </v-content>
            
            <!-- <v-footer class="mb-5 footer" app color="transparent">
                <v-spacer></v-spacer>
                
            </v-footer> -->
            
            
         </v-app>
      </div>

      <!-- Vendor -->
      <script src="../../../assets/js/moment.min.js"></script>
      <script src="../../../assets/js/numeral.min.js"></script>
      <script src="../../../assets/js/moment-locale-id.js"></script>
      <script src="../../../assets/js/lodash.js"></script>
      <script src="../../../assets/js/axios.min.js"></script>
      <script src="../../../assets/js/vue.js"></script>
      <script src="../../../assets/js/vuex.js"></script>
      <script src="../../../assets/js/vuetify.js"></script>
      <script src="../../../assets/js/httpVueLoader.js"></script>
      
      <!-- <script src="../../assets/js/webcam.min.js"></script> -->
      
      <!-- App Script -->
<?php
$ts = "?ts=" . Date("ymdhis");
?>

<style scoped>

.v-content__wrap {
    padding-left: 0px
    /* padding-left: 80px */
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
    videojs.autoSetup();

        videojs('my_video_1').ready(function(){
        console.log(this.options()); //log all of the default videojs options
        
        // Store the video object
        var myPlayer = this, id = myPlayer.id();
        // Make up an aspect ratio
        var aspectRatio = 360/640; 

        function resizeVideoJS(){
            var width = document.getElementById(id).parentElement.offsetWidth;
            myPlayer.width(width).height( width * aspectRatio );

        }
        
        // Initialize resizeVideoJS()
        resizeVideoJS();
        // Then on resize call resizeVideoJS()
        window.onresize = resizeVideoJS; 
        });
},
computed : {
    uri () {
            return "https://register.zalfa.id/z/tmp_z_preview.mp4"
        }
},

methods : {
    
},
   el: '#app',
   components: {
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
