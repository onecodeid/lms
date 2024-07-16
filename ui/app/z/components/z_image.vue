<template>
    <v-img :src="uri"></v-img>
</template>

<script>
module.exports = {
    computed : {
        uri () {
            return  this.$store.state.z.image_url+'?t='+Math.random()
        }
    },

    mounted () {
        
    }
}
</script>