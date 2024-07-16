<template>
    <v-card elevation="1" flat class="watzap-filter">
        <v-card-title primary-title class="light-green white--text px-3 py-3">
            <b>KONFIGURASI PENGIRIMAN</b>
        </v-card-title>
        <v-card-text class="px-3 py-2">
            <v-layout>
                <v-flex xs12>
                    <v-select
                        :items="watzap_numbers"
                        v-model="selected_watzap_number"
                        item-value="key"
                        item-text="wa_number"
                        return-object
                        label="Nomor Pengirim"
                    ></v-select>
                </v-flex>
            </v-layout>

            <v-layout>
                <v-flex xs12>
                    <v-select
                        :items="intervals"
                        v-model="selected_interval"
                        item-value="id"
                        item-text="label"
                        return-object
                        label="Interval Pengiriman"
                    ></v-select>
                </v-flex>
            </v-layout>
            
            <v-layout row wrap>
                <v-flex xs12>
                    <v-text-field
                        label="Jumlah Pesan dalam Sekali Kirim"
                        v-model="cnt"
                        type="number"
                        min="1"
                        max="5"
                    ></v-text-field>
                </v-flex>
            </v-layout>

            <v-layout row wrap>
                <v-flex xs12 class="watzap-text-container pa-2">
                        <v-layout row wrap v-show="watzap_img!=''">
                            <v-flex xs12>
                                <v-img :src="watzap_img"></v-img>
                            </v-flex>
                        </v-layout>
                        
                        <v-textarea
                        :value="watzap_text"
                        rows="10"
                        readonly
                        class="pa-2 watzap-chat"
                        ></v-textarea>
                    </v-flex>
            </v-layout>
            

            <v-layout row wrap>
                <!-- <v-flex xs6 pr-2>
                    <v-btn color="primary lighten-2" block @click="dialog=!dialog">
                    <v-icon small class="mr-2">close</v-icon>
                    T U T U P</v-btn>
                    
                </v-flex> -->
                <v-flex xs12>
                    
                </v-flex>
            </v-layout>
            
        </v-card-text>

        <v-card-actions class="px-3 py-2">
            <v-btn color="success" block @click="broadcast" large
                :disabled="!selected_watzap_number||!selected_interval">
                    <v-icon small class="mr-2">send</v-icon>
                    <b>K I R I M</b></v-btn>
        </v-card-actions>
    </v-card>
</template>
<style scoped>
.rounded {
    border-radius: 10px;
}
.btn-item-delete {
    flex-grow: 0;
}

.watzap-filter {
    border-radius: 0px;
}

.watzap-text-container {
    background-image:url(../../assets/img/bg-whatsapp.png);
    font-family: "HelveticaNeue-Light", "Helvetica Neue Light", "Helvetica Neue", 
  Helvetica, Arial, "Lucida Grande", sans-serif;
  min-height: 300px;
}

.watzap-chat {
    background-color: #dcf8c6;
    border-radius: 5px;
}
</style>
<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            tab: null,
            tab_titles: [
                'Customer|& Sales', 'Ekspedisi|& Area', 'Produk'
            ],
            text: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'
        }
    },

    computed : {
        watzap () {
            return this.$store.state.broadcast.watzap
        },

        watzap_numbers () {
            return this.$store.state.broadcast.watzap_numbers
        },

        selected_watzap_number : {
            get () { return this.$store.state.broadcast.selected_watzap_number },
            set (v) { this.$store.commit('broadcast/set_object', ['selected_watzap_number', v]) }
        },

        watzap_text () {
            return this.$store.state.broadcast.watzap_text.replace(/(\r\n?|\n|\t)/g, "\n")
        },

        watzap_img () {
            return this.$store.state.broadcast.watzap_img
        },

        watzap_numbers () {
            return this.$store.state.broadcast.watzap_numbers
        },

        selected_watzap_number : {
            get () { return this.$store.state.broadcast.selected_watzap_number },
            set (v) { this.$store.commit('broadcast/set_object', ['selected_watzap_number', v]) }
        },

        intervals () {
            return this.$store.state.broadcast.intervals
        },

        selected_interval : {
            get () { return this.$store.state.broadcast.selected_interval },
            set (v) { this.$store.commit('broadcast/set_object', ['selected_interval', v]) }
        },

        cnt : {
            get () { return this.$store.state.broadcast.cnt },
            set (v) { this.$store.commit('broadcast/set_object', ['cnt', v]) }
        },

        recipients () {
            return this.$store.state.broadcast.recipients
        },

        timeout () {
            return this.$store.state.broadcast.timeout
        }
    },

    methods: {
        broadcast () {
            this.$store.commit('broadcast/set_object', ['n', 0])
            this.$store.commit('broadcast/set_object', ['timeout', 0])
            this.$store.dispatch('broadcast/broadcast')
        },

        format_text(text) {
            return text;
            // return text.replace(/(?:\*)(?:(?!\s))((?:(?!\*|\n).)+)(?:\*)/g,'<b>$1</b>')
            // .replace(/(?:_)(?:(?!\s))((?:(?!\n|_).)+)(?:_)/g,'<i>$1</i>')
            // .replace(/(?:~)(?:(?!\s))((?:(?!\n|~).)+)(?:~)/g,'<s>$1</s>')
            // .replace(/(?:--)(?:(?!\s))((?:(?!\n|--).)+)(?:--)/g,'<u>$1</u>')
            // .replace(/(?:```)(?:(?!\s))((?:(?!\n|```).)+)(?:```)/g,'<tt>$1</tt>');

            // extra:
            // --For underlined text--
            // ```Monospace font```
        }
    },

    mounted () {
        this.$store.dispatch("broadcast/get_watzap")
    }
}
</script>