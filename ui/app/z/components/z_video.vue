<template>
    <video id="my_video_1" class="video-js vjs-default-skin vjs-4-3" controls preload="auto" 
            data-setup='{ "asdf": true }' poster="http://video-js.zencoder.com/oceans-clip.png" >
                <source :src="uri" type='video/mp4'>
                <!-- <source src="https://vjs.zencdn.net/v/oceans.webm" type='video/webm'> -->
            </video>
</template>

<script>
module.exports = {
    computed : {
        uri () {
            return  this.$store.state.z.image_url+'?t='+Math.random()
        }
    },

    mounted () {
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
    }
}
</script>