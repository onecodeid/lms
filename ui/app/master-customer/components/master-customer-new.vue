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
            <v-card-title primary-title class="py-2 blue">
                <v-btn flat color="primary" class="ma-0 btn-icon mr-2 hidden-md-and-up" @click="dialog=!dialog" style="float:left">
                    <v-icon class="white--text" medium>arrow_back</v-icon>
                </v-btn>

                <h3 class="headline white--text pt-1" v-show="!edit">SISWA BARU</h3>
                <h3 class="headline white--text pt-1" v-show="edit">UBAH DATA SISWA</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12 sm6 md6 :class="{'pr-4':$vuetify.breakpoint.mdAndUp,'pr-2':$vuetify.breakpoint.smOnly}">
                        <v-layout row wrap>
                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Customer"
                                    v-model="customer_name"
                                    :error="customer_name==''"
                                    :error-count="customer_name==''?1:0"
                                    :error-messages="customer_name==''?['Wajib diisi']:[]"
                                ></v-text-field>
                            </v-flex>                            

                            <v-flex xs12>
                                <v-text-field
                                    label="Alamat Customer"
                                    v-model="customer_address"
                                    prepend-inner-icon="place"
                                    :error="customer_address.length<10"
                                    :error-count="customer_address.length<10?1:0"
                                    :error-messages="customer_address.length<10?['Wajib diisi, Minimal 10 karakter']:[]"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-select :items="sexes" v-model="selected_sex"
                                    return-object clearable item-text="text" item-value="id" label="Jenis Kelamin" placeholder="Belum terdefinisi">

                                </v-select>
                            </v-flex>

                            <v-flex xs12 sm6 md6 :class="{'pl-2':$vuetify.breakpoint.smAndUp}">
                                <common-datepicker-2
                                    label="Tanggal Lahir"
                                    :date="customer_dob"
                                    data="0"
                                    @change="change_dob"
                                    classs="ml-1"
                                    hints=""
                                    :details="true"
                                    v-if="dialog"
                                    :solo="false"
                                ></common-datepicker-2>
                            </v-flex>
                            
                            <!-- <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
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
                                    :error="!selected_province"
                                    :error-count="!selected_province?1:0"
                                    :error-messages="!selected_province?['Pilih salah satu']:[]"
                                    >
                                    <template
                                        slot="item"
                                        slot-scope="{ item }"
                                        >
                                        <v-list-tile-content>
                                        <v-list-tile-title v-text="item.M_ProvinceName"></v-list-tile-title>
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
                                        </v-list-tile-content>
                                    </template>

                                </v-autocomplete>
                            </v-flex> -->
                            
                            <!-- <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-text-field
                                    label="Kode Pos"
                                    v-model="customer_postcode"
                                    placeholder=""
                                ></v-text-field>

                            </v-flex> -->

                            <v-flex xs12 sm6 md6 class="d-none d-sm-none">
                                <!-- <v-text-field
                                    label="Telepon"
                                    v-model="customer_phone"
                                    prepend-inner-icon="phonelink_ring"
                                    placeholder="08X-"
                                ></v-text-field> -->

                            </v-flex>

                            <v-flex xs12 sm6 md6 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-text-field
                                    label="No HP"
                                    v-model="customer_phone"
                                    prepend-inner-icon="phonelink_ring"
                                    placeholder="08X-"
                                ></v-text-field>

                            </v-flex>

                            <v-flex xs12 sm6 md6>
                                <v-text-field
                                    label="No HP (opsional)"
                                    v-model="customer_phone2"
                                    prepend-inner-icon="phonelink_ring"
                                    placeholder="08X-"
                                ></v-text-field>

                            </v-flex>

                            <v-flex xs8 pr-2>
                                <v-text-field
                                    label="Email"
                                    v-model="customer_email"
                                    prepend-inner-icon="email"
                                    placeholder="ex : someone@gmail.com"
                                    :error="!email_validate"
                                    :error-count="email_validate?0:1"
                                    :error-messages="['Email harus diisi, Format email harus benar']"
                                ></v-text-field>
                            </v-flex>
                            <v-flex xs4>
                                <!-- <common-datepicker
                                    label="Tanggal Bergabung"
                                    :date="customer_join_date"
                                    data="0"
                                    @change="change_join_date"
                                    classs="ml-1"
                                    hints=""
                                    :details="true"
                                    v-if="dialog"
                                    :solo="false"
                                ></common-datepicker> -->
                                &nbsp;
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
                                >
                                </v-select>
                            </v-flex>
                            <v-flex xs6>&nbsp;</v-flex>

                            <v-flex xs12 sm6 md4 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-text-field @click="openDate('j')" :value="customer_join_date" readonly label="Tanggal Mulai Kursus"></v-text-field>
                                <!-- <common-datepicker
                                    label="Tanggal Mulai Kursus"
                                    :date="customer_join_date"
                                    data="0"
                                    @change="change_join_date"
                                    classs="ml-1"
                                    hints=""
                                    :details="true"
                                    v-if="dialog"
                                    :solo="false"
                                ></common-datepicker> -->
                            </v-flex>

                            <v-flex xs12 sm6 md4 :class="{'pr-2':$vuetify.breakpoint.smAndUp}">
                                <v-text-field @click="openDate('e')" :value="customer_end_date" readonly label="Tanggal Selesai Kursus"></v-text-field>
                                <!-- <common-datepicker
                                    label="Tanggal Selesai Kursus"
                                    :date="customer_end_date"
                                    data="0"
                                    @change="change_end_date"
                                    classs="ml-1"
                                    hints=""
                                    :details="true"
                                    v-if="dialog"
                                    :solo="false"
                                ></common-datepicker> -->
                            </v-flex>

                            <v-flex xs12 mb-3>
                                
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

                        <v-layout row wrap v-if="edit && user_customer_id == 0 && ['CUST.ENDUSER','CUST.FAMILY'].indexOf(selected_customer_level.M_CustomerLevelCode) < 0">
                            <v-flex xs12>
                                <v-btn color="primary" block large @click="do_grant_user">G R A N T &nbsp;  U S E R</v-btn>
                            </v-flex>    
                        </v-layout>

                        <v-layout row wrap>
                            <!-- <v-flex xs12 sm6 md6 pr-2>
                                <v-card class="pa-1 orange lighten-4" depressed>
                                    <v-card-title class="pt-2 px-2 pb-0">
                                        <h3>AKUN MP</h3>
                                        <v-spacer></v-spacer>
                                        <v-btn color="success" small class="ma-0 btn-icon" @click="mp_add"><v-icon>add</v-icon></v-btn>
                                    </v-card-title>
                                    <v-card-text class="pa-2">
                                        <v-divider class="mb-3"></v-divider>
                                        
                                        <div class="text-xs-center" v-show="customer_mps.length<1">
                                            Belum ada akun marketplace, silahkan tambahkan</div>
                                        <v-text-field
                                            :label="mp.mp_name"
                                            placeholder="@account.id"
                                            v-for="(mp, n) in customer_mps" :key="n"
                                            :value="mp.mp_account_name"
                                            readonly
                                        >
                                            <template slot="append">
                                                <div>
                                                    <v-img :src="'../assets/img/'+mp.mp_logo" width="25"></v-img>
                                                </div>
                                            </template>

                                            <template slot="append-outer">
                                                <div>
                                                    <v-btn color="red" icon small class="ma-0" flat @click="mp_del(n)"><v-icon>clear</v-icon></v-btn>
                                                </div>
                                            </template>
                                        </v-text-field>
                                    </v-card-text>
                                </v-card>
                            </v-flex> -->

                            <v-flex xs12 sm6 md6>
                                <div v-show="!edit">
                                    <v-checkbox v-if="!edit" v-model="user_customer" hide-details true-value="Y" false-value="N">
                                        <template slot="label">
                                            <span class="red--text font-weight-bold">Buatkan Akun User ?</span>
                                        </template>
                                    </v-checkbox>
                                </div>

                                <v-card class="pa-1" v-if="edit && user_customer_id != 0">
                                    <v-card-title class="pa-2 primary white--text">
                                        <v-checkbox label="Buatkan Akun User ?" v-model="user_customer"
                                            true-value="Y"
                                            false-value="N" hide-details class="mt-0 white--text"
                                            :disabled="!grant_user || (edit && user_customer_id != 0)"
                                            v-show="grant_user && (!edit || (edit && user_customer_id == 0))"></v-checkbox>
                                            
                                        <v-btn color="red" dark v-show="edit && user_customer_id != 0" 
                                            @click="revoke_user" block depressed class="my-0"
                                            >Revoke User</v-btn>
                                    </v-card-title>
                                    <v-card-text class="pa-2">
                                        <v-layout row wrap>
                                    
                                            
                                            <v-flex  xs12>
                                                
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

                            <v-flex xs12 sm6 md6>
                                
                            </v-flex>
                        </v-layout>
                        

                        
                    </v-flex>
                </v-layout>
                
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>                
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save" 
                    :disabled="customer_name == '' ||
                                customer_address.length<10">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
        <customer-mp></customer-mp>
        <datex @change="setDate"></datex>
    </v-dialog>
</template>

<script>
module.exports = {
    components : {
        'customer-mp' : httpVueLoader('./master-customer-mp.vue'),
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue'),
        'common-datepicker-2' : httpVueLoader('../../common/components/common-datepicker-null.vue'),
        datex: httpVueLoader("../../common/components/common-date-dialog-x.vue")
    },

    data () {
        return {
            whichDate: 'j'
        }
    },

    computed : {
        URL () {
            return this.$store.state.customer_new.URL
        },

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

        customer_phone2 : {
            get () { return this.$store.state.customer_new.customer_phone2 },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_phone2', v]) }
        },

        customer_phone3 : {
            get () { return this.$store.state.customer_new.customer_phone3 },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_phone3', v]) }
        },

        customer_note : {
            get () { return this.$store.state.customer_new.customer_note },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_note', v]) }
        },

        customer_email : {
            get () { return this.$store.state.customer_new.customer_email },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_email', v]) }
        },

        customer_postcode : {
            get () { return this.$store.state.customer_new.customer_postcode },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_postcode', v]) }
        },

        customer_auto : {
            get () { return this.$store.state.customer_new.customer_auto },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_auto', v]) }
        },

        customer_due_payment : {
            get () { return this.$store.state.customer_new.customer_due_payment },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_due_payment', v]) }
        },

        customer_join_date : {
            get () { return this.$store.state.customer_new.customer_join_date },
            set (v) { this.$store.commit('customer_new/set_common', ['customer_join_date', v]) }
        },

        customer_end_date : {
            get () { return this.$store.state.customer_new.customer_end_date },
            set (v) { this.$store.commit('customer_new/set_object', ['customer_end_date', v]) } },

        customer_mps () {
            return this.$store.state.customer_new.customer_mps
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

        sexes () {
            return this.$store.state.customer_new.sexes
        },

        selected_sex : {
            get () { return this.$store.state.customer_new.selected_sex },
            set (v) { 
                this.$store.commit('customer_new/set_object', ['selected_sex', v])
            }
        },

        customer_dob : {
            get () { return this.$store.state.customer_new.customer_dob },
            set (v) { 
                this.$store.commit('customer_new/set_object', ['customer_dob', v])
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
            if (this.selected_customer_level.M_CustomerLevelCode != 'CUST.DISTRIBUTOR'
                && this.selected_customer_level.M_CustomerLevelCode != 'CUST.AGENCY'
                && this.selected_customer_level.M_CustomerLevelCode != 'CUST.RESELLER') return false
            return true
        },

        change_password : {
            get () { return this.$store.state.customer_new.change_password },
            set (v) { this.$store.commit('customer_new/set_common', ['change_password', v]) }
        },

        email_validate (x) {
            const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            return re.test(String(this.customer_email).toLowerCase())
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
        },

        do_grant_user () {
            this.$store.dispatch('customer_new/grant_user')
        },

        mp_add () {
            this.$store.commit('customer_new/set_common', ['mp_account_name', ''])
            this.$store.commit('customer_new/set_common', ['mp_account_url', ''])
            this.$store.commit('customer_new/set_selected_mp', null)
            this.$store.commit('customer_new/set_common', ['dialog_mp', true])
        },

        mp_del (n) {
            let x = this.customer_mps
            x.splice(n, 1)
            this.$store.commit('customer_new/set_customer_mps', x)
        },

        change_join_date(x) {
            this.customer_join_date = x.new_date
        },

        change_end_date(x) {
            this.customer_end_date = x.new_date
        },

        change_dob(x) {
            this.customer_dob = x.new_date
        },

        openDate(x) {
            // if (!this.enabled) return false
            if (!!x) this.whichDate = x

            let __c = function (x, a, b) { x.$store.commit("xdate/SET_OBJECT", [a, b]) }
            __c(this, "dateXDialog", true)
        },

        setDate(x) {
            
            if (this.whichDate == 'j')
                this.customer_join_date = x

            if (this.whichDate == 'e')
                this.customer_end_date = x
            // if (!!this.whichDate.match(/(r)[0-9]+/)) {
            //     let i = this.whichDate.replace(/(r)/, "")
            //     this.setRevisionData(i, "rev_date", x)
            // }
        }
    },

    mounted () {
        this.$store.dispatch('customer_new/search_province', {})
        this.$store.dispatch('customer_new/search_customer_level', {})
        this.$store.dispatch('customer_new/search_mp')
    },

    watch : {
        search_city(val, old) { 
            
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