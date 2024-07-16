<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="1000px"
        :fullscreen="$vuetify.breakpoint.smAndDown"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="pa-1 blue">
                <v-btn flat color="primary" class="ma-0 btn-icon mr-2 hidden-md-and-up" @click="dialog=!dialog" style="float:left">
                    <v-icon class="white--text" medium>arrow_back</v-icon>
                </v-btn>

                <h3 class="headline white--text pt-1" v-show="!edit">general BARU</h3>
                <h3 class="headline white--text pt-1" v-show="edit">UBAH DATA general</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12 sm6 md6 :class="{'pr-4':$vuetify.breakpoint.mdAndUp,'pr-2':$vuetify.breakpoint.smOnly}">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field
                                    label="Nama general"
                                    
                                    v-model="general_name"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Alamat general"
                                    v-model="general_address"
                                    prepend-inner-icon="place"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-autocomplete
                                    label="Propinsi"
                                    v-model="selected_province"
                                    :items="provinces"
                                    auto-select-first
                                    return-object
                                    clearable
                                    item-text="M_ProvinceName"
                                    item-value="M_ProvinceID"
                                    placeholder="Pilih Propinsi"
                                    >
                                    <template
                                        slot="item"
                                        slot-scope="{ item }"
                                        >
                                        <v-list-tile-content>
                                        <v-list-tile-title v-text="item.M_ProvinceName"></v-list-tile-title>
                                        <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                                        </v-list-tile-content>
                                    </template>

                                </v-autocomplete>
                            </v-flex>

                            <v-flex xs12 sm6 md6 :class="{'pl-2':$vuetify.breakpoint.smAndUp}">
                                <v-autocomplete
                                    label="Kota"
                                    v-model="selected_city"
                                    :items="cities"
                                    auto-select-first
                                    return-object
                                    clearable
                                    item-text="M_CityName"
                                    item-value="M_CityID"
                                    placeholder="Pilih Kota"
                                    :disabled="selected_province == null"
                                    >
                                    <template
                                        slot="item"
                                        slot-scope="{ item }"
                                        >
                                        <v-list-tile-content>
                                        <v-list-tile-title v-text="item.M_CityName"></v-list-tile-title>
                                        <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                                        </v-list-tile-content>
                                    </template>

                                </v-autocomplete>
                            </v-flex>

                            

                            
                            
                            <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-text-field
                                    label="Telepon"
                                    v-model="general_phone"
                                    prepend-inner-icon="phonelink_ring"
                                    placeholder="08X-"
                                ></v-text-field>

                            </v-flex>

                            <v-flex xs12 pr-2>
                                <v-text-field
                                    label="Email"
                                    v-model="general_email"
                                    prepend-inner-icon="email"
                                    placeholder="ex : someone@gmail.com"
                                ></v-text-field>

                            </v-flex>

                        </v-layout>
                    </v-flex>
                    <v-flex xs12 sm6 md6 :class="{'pl-4':$vuetify.breakpoint.mdAndUp,'pl-2':$vuetify.breakpoint.smOnly}">
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-textarea
                                    label="Catatan"
                                    placeholder=" "
                                    rows="2"
                                    v-model="general_note"
                                ></v-textarea>
                            </v-flex>

                        </v-layout>                        
                    </v-flex>
                </v-layout>
                
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>                
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save" 
                    :disabled="error_password || 
                                error_password_confirm">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
module.exports = {
    data () {
        return {
            
        }
    },

    computed : {
        edit () {
            return this.$store.state.general_new.edit
        },

        dialog : {
            get () { return this.$store.state.general_new.dialog_new },
            set (v) { this.$store.commit('general_new/set_common', ['dialog_new', v]) }
        },

        search_city : {
            get () { return this.$store.state.general_new.search_city },
            set (v) { this.$store.commit('general_new/set_common', ["search_city", v]) }
        },

        selected_city : {
            get () { return this.$store.state.general_new.selected_city },
            set (v) { 
                this.$store.commit('general_new/set_selected_city', v) 
                // this.$store.dispatch('general_new/search_district', {})
            }
        },

        cities () {
            return this.$store.state.general_new.cities
        },

        general_name : {
            get () { return this.$store.state.general_new.general_name },
            set (v) { this.$store.commit('general_new/set_common', ['general_name', v]) }
        },

        general_address : {
            get () { return this.$store.state.general_new.general_address },
            set (v) { this.$store.commit('general_new/set_common', ['general_address', v]) }
        },

        general_phone : {
            get () { return this.$store.state.general_new.general_phone },
            set (v) { this.$store.commit('general_new/set_common', ['general_phone', v]) }
        },

        general_note : {
            get () { return this.$store.state.general_new.general_note },
            set (v) { this.$store.commit('general_new/set_common', ['general_note', v]) }
        },

        general_email : {
            get () { return this.$store.state.general_new.general_email },
            set (v) { this.$store.commit('general_new/set_common', ['general_email', v]) }
        },

        provinces () {
            return this.$store.state.general_new.provinces
        },

        selected_province : {
            get () { return this.$store.state.general_new.selected_province },
            set (v) { 
                this.$store.commit('general_new/set_selected_province', v)
                this.$store.dispatch('general_new/search_city', {})
            }
        }
    },

    methods : {
        thr_search: _.debounce( function () {
            // if (!window.one_token()) {
            //     this.$store.commit('update_message_error', 'Maaf, koneksi Anda sempat terputus silahkan Log Out dan Login kembali')
            //     this.$store.commit('update_dialog_error', true)
            //     return
            // }
            
            this.$store.commit("general_new/set_common", ['search_city_status', 1]) // LOADING
            this.$store.dispatch("general_new/search_city", [])
        }, 700),

        save () {
            this.$store.dispatch('general_new/save')
        },

        revoke_user () {
            this.$store.dispatch('general_new/revoke_user')
        }
    },

    mounted () {
        this.$store.dispatch('general_new/search_province', {})
        this.$store.dispatch('general_new/search_general_level', {})
    },

    watch : {
        search_city(val, old) { 
            console.log(`val:${val}, old:${old}`)
            console.log('edit:'+this.$store.state.general_new.edit)
            if (this.$store.state.general_new.edit) return
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.general_new.search_city_status == 1 ) return  
            
            this.$store.commit("general_new/set_common", ["search_city", val])
            this.thr_search()
        }
    }
}
</script>