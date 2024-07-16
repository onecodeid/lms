<template>
    <v-row no-gutters>
        <v-col cols="12">
            <v-img src="https://www.eliteceu.com/app/uploads/2016/03/custom-training-header.jpg"></v-img>
            <!-- <v-carousel>
                <v-carousel-item v-for="(item, i) in items" :key="i" :src="item.src"
                    reverse-transition="fade-transition" transition="fade-transition">
                    <v-row class="fill-height" align="center" justify="center">
                        <div class="text-h2 white--text">
                            Complete Online Courses
                        </div>
                    </v-row>
                </v-carousel-item>
            </v-carousel> -->
        </v-col>

        <v-col cols="12">
            <v-row no-gutters>
                <v-col cols="4" v-for="n in 5" :key="n">
                    <v-card :elevation="0" class="fill-height pa-10">
                        <v-card-text class="pa-0">
                            <v-card :loading="loading" class="mx-auto mb-5" max-width="374">
                                <template slot="progress">
                                    <v-progress-linear color="deep-purple" height="10"
                                        indeterminate></v-progress-linear>
                                </template>

                                <v-img height="250" src="https://cdn.vuetifyjs.com/images/cards/cooking.png"></v-img>

                                <v-card-title>Cafe Badilico</v-card-title>

                                <v-card-text>
                                    <v-row align="center" class="mx-0">
                                        <v-rating :value="4.5" color="amber" dense half-increments readonly
                                            size="14"></v-rating>

                                        <div class="grey--text ms-4">
                                            4.5 (413)
                                        </div>
                                    </v-row>

                                    <div class="my-4 text-subtitle-1">
                                        $ • Italian, Cafe
                                    </div>

                                    <div>Small plates, salads & sandwiches - an intimate setting with 12 indoor seats
                                        plus patio
                                        seating.</div>
                                </v-card-text>

                                <v-divider class="mx-4"></v-divider>

                                <v-card-actions>
                                    <v-btn color="deep-purple lighten-2" text @click="reserve">
                                        Reserve
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-col>


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
                }
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
            selection: 1
        }
    },

    computed: {
        ...Vuex.mapState('register', ['sexes'])
    },

    methods: {
        reserve() {
            this.loading = true

            setTimeout(() => (this.loading = false), 2000)
        },
    },

    mounted() {

    }
}
</script>