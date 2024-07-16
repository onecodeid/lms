<template>
    <div class="videoWrapper">
    <!-- Copy & Pasted from YouTube -->
        <iframe width="560" height="349" :src="'http://www.youtube.com/embed/'+video_id+'?rel=0&hd=1'" frameborder="0" allowfullscreen></iframe>
        
    </div>
</template>

<style scoped>
.videoWrapper {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    padding-top: 25px;
    height: 0;
    }
    .videoWrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

<script>
module.exports = {
    computed : {
        video_id () {
            return this.$store.state.z.video_id
        }
    }
}
</script>