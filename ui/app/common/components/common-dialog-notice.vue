<template>
    <v-dialog
        v-model="dialog"
        max-width="500px"
        transition="dialog-transition"
        content-class="zalfa-dialog-print"
    >
        <v-card class="fill-height" style="display:flex;flex-direction:column">
            <v-card-title class="cyan white--text pb-2 pt-2" style="flex:0">
                <h3 class="ml-2">INFORMASI</h3>
                <v-spacer></v-spacer>
                <v-btn color="red" dark @click="dialog=!dialog" class="ma-0" small style="min-width:0px">
                    <v-icon>clear</v-icon>
                </v-btn>
            </v-card-title>

            <v-card-text class="grow pa-3" grow style="flex:1">
                <v-layout row wrap mb-2>
                    <v-flex xs12 v-for="(m, n) in messages" mb-2>
                        <v-card class="grey lighten-4" @mouseover="setSoftRead(m)">
                            <v-img
                            :src="m.message_img"
                            aspect-ratio="2.75"
                            v-show="m.message_img!=''"
                            ></v-img>

                            <v-card-title primary-title class="px-2 pt-2 pb-0 mt-2">
                                <div>
                                    <h3 class="title mb-2">{{ m.message_title }}</h3>
                                </div>
                            </v-card-title>

                            <v-card-text class="pa-2">
                                <div>
                                    <div v-show="m.message_content.length>200" v-html="m.message_excerpt"></div>
                                    <div v-show="m.message_content.length<=200" v-html="m.message_content"></div>
                                </div>
                            </v-card-text>

                            <v-card-actions><v-spacer></v-spacer>
                            <v-btn flat color="red" @click="setRead(m)">Saya Sudah Baca</v-btn>
                            <v-btn flat color="orange" v-show="m.message_content.length>250" @click="detailInfo(m)">Baca Detail</v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-flex>
                </v-layout>

                <!-- <v-layout row wrap>
                    <v-flex xs12 py-1>
                        <v-card class="pa-1" depressed>
                            <v-layout row wrap>
                                <v-flex xs2>
                                    <v-img
                                    src="https://app.zalfa.id/api/master/itemimg/show_tmb?id=28&t=1673607148"
                                    ></v-img>
                                </v-flex>
                                <v-flex xs10 pa-2>
                                    <v-layout row wrap>
                                        <v-flex xs9>
                                            <h3 class="body-2">Night Cream Lightening</h3>
                                            <p class="mb-1">13 Jan 2023 - 17 Jan 2023</p>
                                            <p>Harga <span style="text-decoration: line-through;" class="red--text mb-1">Rp 30.000</span> <span class="cyan--text"><b>Rp 25.000</b></span></p>
                                        </v-flex>
                                        <v-flex xs3 class="cyan--text text-xs-right"><b>PROMO !</b></v-flex>
                                    </v-layout>
                                </v-flex>
                            </v-layout>
                            
                        </v-card>
                        
                    </v-flex>
                </v-layout> -->
            </v-card-text>

            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="red" dark @click="dialog=!dialog">Tutup</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    computed : {
        dialog : {
            get () { return this.$store.state.system.dialog_notice },
            set (v) { this.$store.commit('system/set_object', ['dialog_notice', v]) }
        },

        messages () {
            return this.$store.state.system.popup_notices
        }
    },

    methods : {
        select (x) {
            this.$store.commit("system/set_object", ["selected_notice", x])
        },

        detailInfo (x) {
            this.select(x)
            this.$store.dispatch("system/set_notice_read").then((y) => {
                window.open(this.$store.state.system.URL + '../ui/app/crm-info-2/preview/' + y.message_id)
            })
        },

        setRead (x) {
            this.select(x)
            this.$store.dispatch("system/set_notice_read").then((x) => {
                this.$store.dispatch("system/get_notice_unread")
            })
        },

        setSoftRead (x) {
            this.select(x)
            if (x.read_status != 'Y' && x.read_status != 'S')
                this.$store.dispatch("system/set_notice_softread").then((y) => {
                    this.$store.dispatch("system/get_notice_unread")
                })
        }
    }
}
</script>