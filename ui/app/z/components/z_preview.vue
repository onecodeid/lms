<template>
    <v-card>
        <v-card-title primary-title>
            <h3>Preview</h3>
        </v-card-title>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs12 v-if="!upload && selected_type && ['P-VIDEO'].indexOf(selected_type.id)>-1">
                    <zv v-if="!upload && selected_type && ['P-VIDEO'].indexOf(selected_type.id)>-1"></zv>
                </v-flex>

                <v-flex xs12 v-if="yt && selected_type && ['P-YOUTUBE','Z-VIDEO'].indexOf(selected_type.id)>-1">
                    <zy></zy>
                </v-flex>

                <v-flex xs12 v-if="!upload && selected_type && ['P-IMAGE'].indexOf(selected_type.id)>-1">
                    <zi v-if="!upload && selected_type && ['P-IMAGE'].indexOf(selected_type.id)>-1"></zi>
                </v-flex>

                <v-flex xs12 pt-2>
                    <h3 class="headline image-title font-weight-bold">{{image_title}}</h3>
                </v-flex>

                <v-flex xs12 pt-1>
                    <div class="caption">{{image_view}} views</div>
                </v-flex>

                <v-flex xs12 pt-2>
                    <p class="body-2 image-caption">{{image_caption}}</p>
                </v-flex>
            </v-layout>
        </v-card-text>
    </v-card>
</template>

<style scoped>
.image-title,.image-caption {
    font-family: 'Quicksand' !important
}
</style>

<script>
module.exports = {
    components : {
        "zv": httpVueLoader("./z_video.vue"),
        "zy": httpVueLoader("./z_youtube.vue"),
        "zi": httpVueLoader("./z_image.vue")
    },

    computed : {
        uri () {
            return "https://register.zalfa.id/z/tmp_z_preview.mp4"
        },

        upload () { return this.$store.state.z.upload },
        image_title () { return this.$store.state.z.image_title },
        image_caption () { return this.$store.state.z.image_caption },
        image_view () { return this.$store.state.z.image_view },

        yt () { return this.$store.state.z.yt },
        selected_type () { return this.$store.state.z.selected_type }
    },

    methods : {
        
    },

    mounted () {
        
    }
}
</script>
