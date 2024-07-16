<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="350px"
        transition="dialog-transition"
        content-class="dialog-wa"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text py-1 pr-1">
                <v-layout row wrap>
                    <v-flex class="pt-2">
                        <h3>KIRIM WA</h3>        
                    </v-flex>
                </v-layout>
                
            </v-card-title>
            <v-card-text pa-2>
                
                <v-layout row wrap>
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
                    <v-flex xs12>
                        <v-text-field
                            label="Nomor Tujuan"
                            v-model="watzap_destination"
                        ></v-text-field>
                    </v-flex>
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
               
            </v-card-text>

            <v-card-actions class="py-2 px-3">
                <v-btn @click="dialog=!dialog">Tutup</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="success" @click="send"
                :disabled="!selected_watzap_number">Kirim</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}

.dialog-wa .wa-container .v-card .v-card__text {
    max-height: 180px;
    overflow-y: scroll;
}
.dialog-wa .v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.dialog-wa .v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
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
    },

    data () {
        return {
        }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.post_sales.dialog_wa_send },
            set (v) { this.$store.commit('post_sales/set_common', ['dialog_wa_send', v]) }
        },

        watzap () {
            return this.$store.state.post_sales.watzap
        },

        watzap_numbers () {
            return this.$store.state.post_sales.watzap_numbers
        },

        selected_watzap_number : {
            get () { return this.$store.state.post_sales.selected_watzap_number },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_watzap_number', v]) }
        },

        watzap_text () {
            return this.$store.state.post_sales.watzap_text
        },

        watzap_img () {
            return this.$store.state.post_sales.watzap_img
        },

        watzap_destination : {
            get () { return this.$store.state.post_sales.watzap_destination },
            set (v) { this.$store.commit('post_sales/set_common', ['watzap_destination', v]) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('watemplate_new/save')
        },

        wame (y) {
            this.select(y)

            let x = this.$store.state.post_sales.selected_order
            wa_url = this.$store.state.system.wa_url
            this.$store.dispatch('post_sales/wa_format', {content:y.watemplate_content_send,order:x}).then(res => {
                window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + res + "&type=phone_number")
            })
            // window.open(wa_url + "?phone=" + x.customer_phone + "&text=" + y.watemplate_content_send + "&type=phone_number")
        },

        send () {
            this.$store.commit('set_dialog_progress', true)
            this.$store.dispatch('post_sales/watzap').then(res => {
                this.$store.commit('post_sales/set_common', ['dialog_wa_send', false])
                this.$store.commit('set_dialog_progress', false)
            })
        }
    },

    mounted () {

    },

    watch : {}
}
</script>