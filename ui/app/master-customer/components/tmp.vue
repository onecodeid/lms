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

                <h3 class="headline white--text pt-1" v-show="!edit">CUSTOMER BARU</h3>
                <h3 class="headline white--text pt-1" v-show="edit">UBAH DATA CUSTOMER</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12 sm6 md6 :class="{'pr-4':$vuetify.breakpoint.mdAndUp,'pr-2':$vuetify.breakpoint.smOnly}">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Customer"
                                    
                                    v-model="customer_name"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Alamat Customer"
                                    v-model="customer_address"
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
                                <v-autocomplete
                                    label="Kecamatan"
                                    v-model="selected_district"
                                    :items="districts"
                                    auto-select-first
                                    return-object
                                    clearable
                                    item-text="M_DistrictName"
                                    item-value="M_DistrictID"
                                    placeholder="Pilih Kecamatan"
                                    :disabled="selected_city == null"
                                    >
                                    <template
                                        slot="item"
                                        slot-scope="{ item }"
                                        >
                                        <v-list-tile-content>
                                        <v-list-tile-title v-text="item.M_DistrictName"></v-list-tile-title>
                                        <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                                        </v-list-tile-content>
                                    </template>

                                </v-autocomplete>
                            </v-flex>

                            <v-flex xs12 sm6 md6 :class="{'pl-2':$vuetify.breakpoint.smAndUp}">
                                <v-autocomplete
                                    label="Kelurahan"
                                    v-model="selected_village"
                                    :items="villages"
                                    auto-select-first
                                    return-object
                                    clearable
                                    item-text="M_KelurahanName"
                                    item-value="M_KelurahanID"
                                    placeholder="Pilih Kelurahan"
                                    :disabled="selected_district == null"
                                    >
                                    <template
                                        slot="item"
                                        slot-scope="{ item }"
                                        >
                                        <v-list-tile-content>
                                        <v-list-tile-title v-text="item.M_KelurahanName"></v-list-tile-title>
                                        <!-- <v-list-tile-sub-title v-text="getAddress(item)"></v-list-tile-sub-title> -->
                                        </v-list-tile-content>
                                    </template>

                                </v-autocomplete>
                            </v-flex>
                            
                            <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-text-field
                                    label="Telepon"
                                    v-model="customer_phone"
                                    prepend-inner-icon="phonelink_ring"
                                    placeholder="08X-"
                                ></v-text-field>

                            </v-flex>

                            <v-flex xs12 pr-2>
                                <v-text-field
                                    label="Email"
                                    v-model="customer_email"
                                    prepend-inner-icon="email"
                                    placeholder="ex : someone@gmail.com"
                                ></v-text-field>

                            </v-flex>

                        </v-layout>
                    </v-flex>
                    <v-flex xs12 sm6 md6 :class="{'pl-4':$vuetify.breakpoint.mdAndUp,'pl-2':$vuetify.breakpoint.smOnly}">
                        <v-layout row wrap>
                            <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-select
                                    :items="customer_levels"
                                    v-model="selected_customer_level"
                                    return-object
                                    item-text="M_CustomerLevelName"
                                    item-value="M_CustomerLevelID"
                                    label="Jenis Customer"
                                ></v-select>
                            </v-flex>

                            <v-flex xs12>
                                <v-textarea
                                    label="Catatan"
                                    placeholder=" "
                                    rows="2"
                                    v-model="customer_note"
                                ></v-textarea>
                            </v-flex>

                        </v-layout>

                        <v-card class="pa-1" v-show="current_user.customer_level_code=='CUST.DISTRIBUTOR'">
                            <v-card-title class="pa-2 primary white--text">
                                <v-checkbox label="Grant User (Level Distributor)" v-model="user_customer"
                                    true-value="Y"
                                    false-value="N" hide-details class="mt-0 white--text"
                                    :disabled="!grant_user || (edit && user_customer_id != 0)"></v-checkbox>
                                    <v-spacer></v-spacer>
                                    <v-btn color="red" dark small v-show="edit && user_customer_id != 0" @click="revoke_user" >Revoke User</v-btn>
                            </v-card-title>
                            <v-card-text class="pa-2">
                                <v-layout row wrap>
                            
                                    
                                    <v-flex  xs12 sm6 md6>
                                        
                                        <v-text-field
                                            label="Username"
                                            v-model="user_customer_username"
                                            :disabled="user_customer!='Y' || !grant_user || (edit && user_customer_id != 0)"
                                        ></v-text-field>

                                        <v-text-field
                                            label="Password"
                                            v-model="user_customer_password"
                                            type="password"
                                            :error="error_password"
                                            :error-count="error_password ? 1 : 0"
                                            :error-messages="error_password ? ['Minimal 5 huruf, HARUS terdapat angka DAN perpaduan huruf besar kecil'] : []"
                                            :disabled="user_customer!='Y' || !grant_user || (edit && user_customer_id != 0)"
                                            v-show="!edit || user_customer_id == 0"
                                        ></v-text-field>

                                        <v-text-field
                                            label="Konfirmasi Password"
                                            v-model="user_customer_password_confirm"
                                            type="password"
                                            :error="error_password_confirm"
                                            :error-count="error_password_confirm ? 1 : 0"
                                            :error-messages="error_password_confirm ? ['Password tidak sama'] : []"
                                            :disabled="user_customer!='Y' || !grant_user || (edit && user_customer_id != 0)"
                                            v-show="!edit || user_customer_id == 0"
                                        ></v-text-field>

                                        <!-- CHANGE PASSWORD -->
                                        <v-text-field
                                            label="Password"
                                            v-model="user_customer_password"
                                            type="password"
                                            :error="error_password"
                                            :error-count="error_password ? 1 : 0"
                                            :error-messages="error_password ? ['Minimal 5 huruf, HARUS terdapat angka DAN perpaduan huruf besar kecil'] : []"
                                            v-show="edit && user_customer_id != 0 && change_password"
                                        ></v-text-field>

                                        <v-text-field
                                            label="Konfirmasi Password"
                                            v-model="user_customer_password_confirm"
                                            type="password"
                                            :error="error_password_confirm"
                                            :error-count="error_password_confirm ? 1 : 0"
                                            :error-messages="error_password_confirm ? ['Password tidak sama'] : []"
                                            v-show="edit && user_customer_id != 0 && change_password"
                                        ></v-text-field>
                                        <!-- END OF CHANGE PASSWORD -->

                                        <v-btn color="primary" block v-show="!change_password && user_customer_id != 0" @click="change_password=!change_password">Ubah Password</v-btn>
                                    </v-flex>
                                </v-layout>
                            </v-card-text>
                        </v-card>

                        
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
            return this.$store.state.customer_new.edit
        },

        dialog : {
            get () { return this.$store.state.customer_new.dialog_new },
            set (v) { this.$store.commit('customer_new/set_common', ['dialog_new', v]) }
        },

        search_city : {
            get () { return this.$store.state.customer_new.search_city },
            set (v) { this.$store.commit('customer_new/set_common', ["search_city", v]) }
        },

        selected_city : {
            get () { return this.$store.state.customer_new.selected_city },
            set (v) { 
                this.$store.commit('customer_new/set_selected_city', v) 
                this.$store.dispatch('customer_new/search_district', {})
            }
        },

        cities () {
            return this.$store.state.customer_new.cities
        },

        customer_name : {
            get () { return this.$store.state.customer_new.customer_name },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_name', v]) }
        },

        customer_address : {
            get () { return this.$store.state.customer_new.customer_address },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_address', v]) }
        },

        customer_phone : {
            get () { return this.$store.state.customer_new.customer_phone },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_phone', v]) }
        },

        customer_email : {
            get () { return this.$store.state.customer_new.customer_email },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_email', v]) }
        },

        customer_note : {
            get () { return this.$store.state.customer_new.customer_note },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_note', v]) }
        },

        provinces () {
            return this.$store.state.customer_new.provinces
        },

        selected_province : {
            get () { return this.$store.state.customer_new.selected_province },
            set (v) { 
                this.$store.commit('customer_new/set_selected_province', v)
                this.$store.dispatch('customer_new/search_city', {})
            }
        },

        districts () {
            return this.$store.state.customer_new.districts
        },

        selected_district : {
            get () { return this.$store.state.customer_new.selected_district },
            set (v) { 
                this.$store.commit('customer_new/set_selected_district', v)
                this.$store.dispatch('customer_new/search_village', {})
            }
        },

        villages () {
            return this.$store.state.customer_new.villages
        },

        selected_village : {
            get () { return this.$store.state.customer_new.selected_village },
            set (v) { 
                this.$store.commit('customer_new/set_selected_village', v)
            }
        },

        customer_levels () {
            return this.$store.state.customer_new.customer_levels
        },

        selected_customer_level : {
            get () { return this.$store.state.customer_new.selected_customer_level },
            set (v) { 
                this.$store.commit('customer_new/set_selected_customer_level', v)
            }
        },

        user_customer : {
            get () { return this.$store.state.customer_new.user_customer },
            set (v) { this.$store.commit('customer_new/set_common', ['user_customer', v]) }
        },

        user_customer_id : {
            get () { return this.$store.state.customer_new.user_customer_id },
            set (v) { this.$store.commit('customer_new/set_common', ['user_customer_id', v]) }
        },

        user_customer_username : {
            get () { return this.$store.state.customer_new.user_customer_username },
            set (v) { this.$store.commit('customer_new/set_common', ['user_customer_username', v]) }
        },

        user_customer_password : {
            get () { return this.$store.state.customer_new.user_customer_password },
            set (v) { this.$store.commit('customer_new/set_common', ['user_customer_password', v]) }
        },

        user_customer_password_confirm : {
            get () { return this.$store.state.customer_new.user_customer_password_confirm },
            set (v) { this.$store.commit('customer_new/set_common', ['user_customer_password_confirm', v]) }
        },

        error_password_confirm () {
            if (this.user_customer == 'N')
                return false

            if (this.user_customer_password != this.user_customer_password_confirm)
                return true
            return false
        },

        error_password () {
            if (this.user_customer == 'N')
                return false

            if (this.user_customer_password == '' && this.user_customer_password_confirm == '')
                return false

            if (this.user_customer_password.length < 5)
                return true

            if (!this.user_customer_password.match(/[a-z]/) || 
                !this.user_customer_password.match(/[A-Z]/) ||
                !this.user_customer_password.match(/[0-9]/))
                return true

            return false
        },

        grant_user () {
            if (!this.selected_customer_level)  return false
            if (this.selected_customer_level.M_CustomerLevelCode != 'CUST.AGENCY' &&
                this.selected_customer_level.M_CustomerLevelCode != 'CUST.RESELLER') return false
            return true
        },

        change_password : {
            get () { return this.$store.state.customer_new.change_password },
            set (v) { this.$store.commit('customer_new/set_common', ['change_password', v]) }
        },

        current_user () {
            return this.$store.state.customer_new.current_user
        }
    },

    methods : {
        thr_search: _.debounce( function () {
            // if (!window.one_token()) {
            //     this.$store.commit('update_message_error', 'Maaf, koneksi Anda sempat terputus silahkan Log Out dan Login kembali')
            //     this.$store.commit('update_dialog_error', true)
            //     return
            // }
            
            this.$store.commit("customer_new/set_common", ['search_city_status', 1]) // LOADING
            this.$store.dispatch("customer_new/search_city", [])
        }, 700),

        save () {
            this.$store.dispatch('customer_new/save')
        },

        revoke_user () {
            this.$store.dispatch('customer_new/revoke_user')
        }
    },

    mounted () {
        this.$store.dispatch('customer_new/search_province', {})
        this.$store.dispatch('customer_new/search_customer_level', {})
    },

    watch : {
        search_city(val, old) { 
            console.log(`val:${val}, old:${old}`)
            console.log('edit:'+this.$store.state.customer_new.edit)
            if (this.$store.state.customer_new.edit) return
            if (val == null || typeof val == 'undefined') val = ""
            if (val == old ) return
            if (this.$store.state.customer_new.search_city_status == 1 ) return  
            
            this.$store.commit("customer_new/set_common", ["search_city", val])
            this.thr_search()
        }
    }
}
</script>