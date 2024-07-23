<template>
    <v-row>
        <v-col cols="12">
            <v-carousel>
                <v-carousel-item v-for="(item, i) in items" :key="i" :src="item.src"
                    reverse-transition="fade-transition" transition="fade-transition">
                    <v-row class="fill-height" align="center" justify="center">
                        <div class="text-h2 white--text">
                            Complete Online Courses
                        </div>
                    </v-row>
                </v-carousel-item>
            </v-carousel>
        </v-col>



        <!-- Featured Section -->
        <v-col cols="12">
            <v-row dense>
                <!-- FILTER -->
                <v-col cols="3">
                    FILTER
                </v-col>
                <!-- END OF FILTER -->
                <v-col cols="9">
                    <v-row dense>
                        <v-col v-for="(item, i) in courses" :key="i" cols="12" md="3" lg="3">
                            <v-card :loading="loading" class="mx-auto mb-1" max-width="374">
                                <template slot="progress">
                                    <v-progress-linear color="deep-purple" height="10"
                                        indeterminate></v-progress-linear>
                                </template>

                                <v-img height="250" :src="item.img_url"></v-img>

                                <v-card-title>{{ item.M_ItemName }}</v-card-title>

                                <v-card-text>
                                    <v-row align="center" class="mx-0">
                                        <v-rating :value="4.5" color="amber" dense half-increments readonly
                                            size="14"></v-rating>

                                        <div class="grey--text ms-4">
                                            4.5 (413)
                                        </div>
                                    </v-row>

                                    <div class="my-4 text-subtitle-1">
                                        Rp • {{ one_money(item.M_PriceTotal) }}
                                    </div>

                                    <div class="truncate">{{ item.M_ItemDesc }}</div>
                                </v-card-text>

                                <v-divider class="mx-4"></v-divider>

                                <!-- <v-card-title>Tonight's availability</v-card-title>

                    <v-card-text>
                        <v-chip-group v-model="selection" active-class="deep-purple accent-4 white--text" column>
                            <v-chip>5:30PM</v-chip>

                            <v-chip>7:30PM</v-chip>

                            <v-chip>8:00PM</v-chip>

                            <v-chip>9:00PM</v-chip>
                        </v-chip-group>
                    </v-card-text> -->

                                <v-card-actions>
                                    <v-btn color="deep-purple lighten-2" text @click="reserve(item)">
                                        Daftar
                                    </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-col>


            </v-row>
        </v-col>
        <!-- End of Featured Section -->

    </v-row>
</template>

<style scoped>
.truncate {
   overflow: hidden;
   text-overflow: ellipsis;
   display: -webkit-box;
   -webkit-line-clamp: 2; /* number of lines to show */
           line-clamp: 2;
   -webkit-box-orient: vertical;
}
</style>

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
            courses: s => s.home.items
        })
    },

    methods: {
        reserve() {
            this.loading = true

            setTimeout(() => (this.loading = false), 2000)
        },

        one_money(x) {
            return window.one_money(x)
        },

        reserve(x) {
            window.open("../web-register?item_id=" + x.M_ItemID)
        }
    },

    mounted() {
        this.$store.dispatch("home/search_item")
    }
}
</script>