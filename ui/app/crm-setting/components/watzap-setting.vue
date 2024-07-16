<template>
    <v-card>
        <!-- <v-card-title class="cyan lighten-4 py-2">
            <v-layout row wrap>
                <v-flex xs6>
                    <h3 class="display-1 font-weight-light zalfa-text-pink">SYSTEM SETTINGS</h3>
                </v-flex>
                <v-flex xs6 class="text-xs-right">
                    <v-btn color="primary" @click="save_setting" class="ma-0">Simpan</v-btn>
                </v-flex>
            </v-layout>
        </v-card-title> -->

        <v-card-text class="pt-2">
            <v-layout row wrap>
                <v-flex xs12 sm4 md3 :class="{'pr-4':$vuetify.breakpoint.smAndUp}">
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-textarea
                                label="ID"
                                readonly
                                :value="setting.id"
                            ></v-textarea>
                            <v-text-field
                                label="Nama"
                                :value="setting.name"
                                readonly
                            ></v-text-field>
                        </v-flex>

                        <v-flex xs12>
                            <v-text-field
                                label="Telepon Perusahaan"

                            ></v-text-field>
                        </v-flex>

                        <v-flex xs12>
                            <v-list two-line>
                                <template v-for="(item, index) in numbers">
                                    <!-- <v-subheader
                                    v-if="item.header"
                                    :key="item.header"
                                    >
                                    {{ item.header }}
                                    </v-subheader> -->

                                    <v-list-tile
                                    :key="item.key"
                                    avatar
                                    @click=""
                                    >
                                        <v-list-tile-content>
                                            <v-list-tile-title v-html="item.wa_number" :class="item.is_connected?'green--text':'red--text'"></v-list-tile-title>
                                            <v-list-tile-sub-title v-html="item.key"></v-list-tile-sub-title>
                                        </v-list-tile-content>
                                    </v-list-tile>

                                    <v-divider
                                        :key="index"
                                        :inset="item.inset"
                                    ></v-divider>

                                </template>
                                </v-list>
                        </v-flex>
                    </v-layout>
                </v-flex>
                
                <v-flex xs12 sm4 md3 :class="{'pr-4':$vuetify.breakpoint.smAndUp}">
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-select
                                :items="numbers"
                                item-value="key"
                                item-text="wa_number"
                                label="Nomor untuk Kirim Invoice"
                                return-object
                                v-model="invoice_number"
                                clearable
                            ></v-select>
                        </v-flex>
                    </v-layout>
                </v-flex>

                <v-flex xs12 sm0 md3 :class="{'pr-4':$vuetify.breakpoint.smAndUp}">
                </v-flex>

                <v-flex xs12 sm4 md3 :class="{'pr-4':$vuetify.breakpoint.smAndUp}">
                    <v-layout row wrap>
                        <v-flex xs12>
                            <v-btn color="success" class="justify-center" @click="refresh">Refresh API</v-btn>
                            <v-btn color="primary" class="justify-center" @click="save_conf">Simpan</v-btn>
                        </v-flex>
                    </v-layout>
                </v-flex>
            </v-layout>
        </v-card-text>

        <v-snackbar
            v-model="snackbar"
            multi-line
            :timeout="6000"
            top
            vertical
            >
            {{ snackbar_text }}
            <v-btn
                color="pink"
                flat
                @click="snackbar = false"
            >
                Tutup
            </v-btn>
        </v-snackbar>
    </v-card>
</template>

<style>
.v-card__title.cyan.lighten-4 {
    border-bottom: solid 2px cyan !important;
    border-bottom-color: cyan !important;
}
</style>

<script>
module.exports = {
    computed : {
        
        setting : {
            get () { return this.$store.state.setting.setting },
            // set (v) { this.$store.commit('setting/set_common', ['company_name', v]) }
        },

        numbers () {
            if (!!this.setting)
                return this.setting.licenses_key
            return []
        },

        invoice_number : {
            get () { return this.$store.state.setting.invoice_number },
            set (v) { this.$store.commit('setting/set_object', ['invoice_number', v]) }
        },

        snackbar : {
            get () { return this.$store.state.setting.snackbar },
            set (v) { this.$store.commit('setting/set_common', ['snackbar', v]) }
        },

        snackbar_text () {
            return this.$store.state.setting.snackbar_text
        }
    },

    methods : {
        save_conf () {
            this.$store.dispatch('setting/save_conf')
        },

        refresh () {
            this.$store.dispatch('setting/refresh')
        }
    },

    mounted () {
        this.$store.dispatch('setting/get_conf')
    }
}
</script>
