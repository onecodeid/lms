<template>
    <v-row>
        <v-col cols="12">
            <v-carousel>
                <v-carousel-item v-for="(item, i) in items" :key="i" :src="item.src"
                    reverse-transition="fade-transition" transition="fade-transition">
                    <v-row
                    class="fill-height"
                    align="center"
                    justify="center"
                    >
                    <div class="text-h2 white--text">
                        Complete Online Courses
                    </div>
                    </v-row>
                </v-carousel-item>
            </v-carousel>
        </v-col>

        <v-col cols="12">
            <v-row no-gutters>
                <v-spacer></v-spacer>
                <v-col cols="12" md="4" lg="3">
                    <!-- <v-sheet class="pa-12" color="grey lighten-3"> -->
                    <v-sheet :elevation="6" class="mx-auto" height="auto" width="100%">
                        <v-img :aspect-ratio="16 / 16" width="100%"
                            src="https://themewagon.github.io/elearn/images/featured.jpg"></v-img>
                    </v-sheet>
                    <!-- </v-sheet> -->
                </v-col>
                <v-col cols="12" md="8" lg="5">
                    <v-sheet color="#f2f1f8" :elevation="6" class="fill-height pa-10">
                        <h2 class="my-5">Online Literature Course</h2>
                        <p>Maecenas rutrum viverra sapien sed fermentum. Morbi tempor odio eget lacus tempus pulvinar. Donec vehicula efficitur nibh, in pretium nulla interdum non.</p>
                    </v-sheet>
                </v-col>
                <v-spacer></v-spacer>
            </v-row>
        </v-col>

        <!-- Featured Section -->
        <v-col cols="12">
            <v-row>
                {{ courses }}
                <v-spacer></v-spacer>
                <v-col v-for="(item, i) in featuredItems" :key="i" cols="12" md="3" lg="3">
                    <v-card :color="item.color" dark>
                        <div class="d-flex flex-no-wrap justify-space-between">
                            <div>
                                <v-card-title class="text-h5" v-text="item.title"></v-card-title>

                                <v-card-subtitle v-text="item.artist"></v-card-subtitle>

                                <v-card-actions>
                                    <v-btn v-if="item.artist === 'Ellie Goulding'" class="ml-2 mt-3" fab icon
                                        height="40px" right width="40px">
                                        <v-icon>mdi-play</v-icon>
                                    </v-btn>

                                    <v-btn v-else class="ml-2 mt-5" outlined rounded small>
                                        START RADIO
                                    </v-btn>
                                </v-card-actions>
                            </div>

                            <v-avatar class="ma-3" size="125" tile>
                                <v-img :src="item.src"></v-img>
                            </v-avatar>
                        </div>
                    </v-card>
                </v-col>
                <v-spacer></v-spacer>
            </v-row>
        </v-col>
        <!-- End of Featured Section -->

        <!-- Top seller section -->
        <v-col cols="12">
            <v-row>
                <v-card :loading="loading" class="mx-auto my-12" max-width="374">
                    <template slot="progress">
                        <v-progress-linear color="deep-purple" height="10" indeterminate></v-progress-linear>
                    </template>

                    <v-img height="250" src="https://cdn.vuetifyjs.com/images/cards/cooking.png"></v-img>

                    <v-card-title>Cafe Badilico</v-card-title>

                    <v-card-text>
                        <v-row align="center" class="mx-0">
                            <v-rating :value="4.5" color="amber" dense half-increments readonly size="14"></v-rating>

                            <div class="grey--text ms-4">
                                4.5 (413)
                            </div>
                        </v-row>

                        <div class="my-4 text-subtitle-1">
                            $ • Italian, Cafe
                        </div>

                        <div>Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio
                            seating.</div>
                    </v-card-text>

                    <v-divider class="mx-4"></v-divider>

                    <v-card-title>Tonight's availability</v-card-title>

                    <v-card-text>
                        <v-chip-group v-model="selection" active-class="deep-purple accent-4 white--text" column>
                            <v-chip>5:30PM</v-chip>

                            <v-chip>7:30PM</v-chip>

                            <v-chip>8:00PM</v-chip>

                            <v-chip>9:00PM</v-chip>
                        </v-chip-group>
                    </v-card-text>

                    <v-card-actions>
                        <v-btn color="deep-purple lighten-2" text @click="reserve">
                            Reserve
                        </v-btn>
                    </v-card-actions>
                </v-card>
            </v-row>
        </v-col>
        <!-- end of top seller section -->
    </v-row>
</template>

<style scoped></style>

<script>
module.exports = {
    data() {
        return {
            items: [
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/squirrel.jpg',
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/sky.jpg',
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/bird.jpg',
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/planet.jpg',
                },
            ],

            featuredItems: [
                {
                    color: '#1F7087',
                    src: 'https://cdn.vuetifyjs.com/images/cards/foster.jpg',
                    title: 'Supermodel',
                    artist: 'Foster the People',
                },
                {
                    color: '#952175',
                    src: 'https://cdn.vuetifyjs.com/images/cards/halcyon.png',
                    title: 'Halcyon Days',
                    artist: 'Ellie Goulding',
                },
            ],

            loading: false,
            selection: 1,
        }
    },

    computed: {
        ...Vuex.mapState({
            courses:s=>s.home.items
        })
    },

    methods: {
        reserve() {
            this.loading = true

            setTimeout(() => (this.loading = false), 2000)
        },
    },

    mounted() {
        this.$store.dispatch("home/search_item")
    }
}
</script>