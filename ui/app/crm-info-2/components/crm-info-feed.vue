<template>
    <v-layout row wrap>
        <v-flex xs12>
            <v-card v-for="info in infos" :key="info_id" class="mb-2">

                <v-layout row wrap>
                <v-flex xs2 pa-1>
                    <v-img :src="info.img_url" aspect-ratio="1"></v-img>
                </v-flex>
                <v-flex xs10>
                    <v-card-title class="title py-2">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <h3>{{ info.info_title }}</h3>
                                <span class="caption d-block mt-2 grey--text"><i>{{ info.info_date }}</i></span>
                            </v-flex>
                        </v-layout>
                        
                    </v-card-title>
                    <v-card-text class="py-1">{{ info.info_excerpt }}</v-card-text>
                    <v-card-actions>
                    <v-btn :href="'../preview/'+info.info_id">Read More</v-btn>
                    </v-card-actions>
                </v-flex>
                </v-layout>
            </v-card>
        </v-flex>

        <v-flex xs12>
            <v-card>
                <v-card-text>
                    <v-layout row wrap>
                        <v-flex xs6 pt-2>
                            Menampilkan {{ 1 + ((currPage-1) * feedLimit) }} - <span>{{ ((currPage) * feedLimit) > total ? total : ((currPage) * feedLimit) }}</span> dari {{ total }} entri
                        </v-flex>
                        <v-flex xs6 class="text-xs-right">
                            <v-pagination
                            v-model="currPage"
                            :length="totalPage"
                            @input="changePage"
                            circle
                            ></v-pagination>
                        </v-flex>
                    </v-layout>
                </v-card-text>
            </v-card>
          
        </v-flex>
    </v-layout>
  </template>
  
  <script>
 module.exports = {
    data() {
      return {
        
      };
    },

    computed : {
        infos () {
            return this.$store.state.info.infos
        },

        total () {
            return this.$store.state.info.total_info
        },

        totalPage () {
            return this.$store.state.info.total_page
        },

        currPage : {
            get () { return this.$store.state.info.current_page },
            set (v) { this.$store.commit('info/set_object', ['current_page', v]) }
        },

        feedLimit () {
            return this.$store.state.info.feed_limit
        }
    },

    methods : {
        changePage(x) {
            this.currPage = x
        }
    }
  };
  </script>
  