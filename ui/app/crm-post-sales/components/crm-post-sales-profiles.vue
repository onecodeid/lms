<template>
    <v-card>
        <v-card-text>
            <v-layout row wrap>
                <v-flex xs9>
                    
                </v-flex>
            </v-layout>

            <v-data-table 
                :headers="headers"
                :items="profiles"
                :loading="false"
                hide-actions
                class="elevation-1">
                
                <template slot="items" slot-scope="props">
                    <tr :class="is_selected(props.item)?class_selected:''">
                        <td class="text-xs-left pa-2">
                            {{props.item.profile_name}}</td>

                        <td class="text-xs-center pa-0">
                            <v-btn color="red" class="btn-icon ma-0 white--text" depressed small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                            <v-btn color="success" class="btn-icon ma-0" depressed small @click="load(props.item)"><v-icon>file_upload</v-icon></v-btn>
                        </td>
                    </tr>
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
            
            <common-dialog-delete :data="0" @confirm_del="confirm_del" v-if="dialog_delete" 
                :text="'Apakah anda yakin akan menghapus Profil '+tmp_profile.profile_name+' ?'"></common-dialog-delete>
        </v-card-text>
    </v-card>
</template>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "NAMA PROFIL",
                    align: "left",
                    sortable: false,
                    width: "75%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },

                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "25%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],
            class_selected:'cyan lighten-4',
            tmp_profile: null
        }
    },

    computed : {
        profiles() {
            return this.$store.state.post_sales.profiles
        },

        selected_profile: {
            get () { return this.$store.state.post_sales.selected_profile },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_profile', v]) }
        },

        curr_page : {
            get () { return this.$store.state.post_sales.profile_current_page },
            set (v) { 
                this.$store.commit('post_sales/set_common', ['profile_current_page', v])
                this.$store.dispatch('post_sales/search_profile')
            }
        },

        xtotal_page () {
            return this.$store.state.post_sales.profile_total_page
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        }
    },

    methods: {
        one_money (x) {
            return window.one_money(x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('post_sales/search_profile')
        },

        select (x) {
            this.$store.commit('post_sales/set_object', ['selected_profile', x])
        },

        load (x) {
            this.select(x)
            let y = JSON.parse(x.profile_json)
            let z = this.$store
            let state = z.state.post_sales

            /** Initialize */
            let customer_type = null
            let sales_type = null
            let salesdur = null
            let leadsource = null
            let provinces = []
            let cities = []
            let items = []
            let packets = []
            let expeditions = []
            let services = []
            
            for (let st of state.sales_types) {
                if (st.id == y.sales_status)
                    sales_type = st
            }

            for (let ct of state.customer_types) {
                if (ct.id == y.customer_status)
                    customer_type = ct
            }

            for (let sd of state.salesdurs) {
                if (sd.salesdur_id == y.period.id)
                    salesdur = sd
            }

            for (let ls of state.leadsources) {
                if (ls.leadsource_id == y.leadsource)
                    leadsource = ls
            }

            if (y.provinces.length > 0) {
                for (let pv of state.provinces)
                    if (y.provinces.indexOf(pv.M_ProvinceID) > -1)
                        provinces.push(pv)
            }

            if (y.cities.length > 0) {
                for (let pv of state.cities)
                    if (y.cities.indexOf(pv.M_CityID) > -1)
                        cities.push(pv)
            }

            if (y.expeditions.length > 0) {
                for (let ex of state.expeditions)
                    if (y.expeditions.indexOf(ex.M_ExpeditionID) > -1)
                        expeditions.push(ex)
            }

            if (y.services.length > 0) {
                for (let sv of state.services)
                    if (y.services.indexOf(sv.service) > -1)
                        services.push(sv)
            }

            if (y.items != "") {
                let its = y.items.split(",")
                for (let it of state.items)
                    if (its.indexOf(it.M_ItemID) > -1)
                        items.push(it)
            }

            if (y.packets != "") {
                let its = y.packets.split(",")
                for (let it of state.packets)
                    if (its.indexOf(it.M_PacketID) > -1)
                        packets.push(it)
            }

            /** Do the rest */
            z.commit("post_sales/set_common", ["profile_id", x.profile_id])
            z.commit("post_sales/set_object", ["selected_province", provinces])
            z.commit("post_sales/set_object", ["selected_city", cities])
            z.commit("post_sales/set_object", ["selected_expedition", expeditions])
            z.commit("post_sales/set_object", ["selected_service", services])
            z.commit("post_sales/set_object", ["selected_item", items])
            z.commit("post_sales/set_object", ["selected_packet", packets])
            z.commit("post_sales/set_object", ["selected_sales_type", sales_type])
            z.commit("post_sales/set_object", ["selected_customer_type", customer_type])
            z.commit("post_sales/set_object", ["selected_salesdur", salesdur])
            z.commit("post_sales/set_object", ["selected_leadsource", leadsource])
            z.commit("post_sales/set_object", ["custom_date", y.period.custom.state])
            if (!!y.period.custom.state) {
                z.commit("post_sales/set_common", ["custom_sdate", y.period.custom.sdate])
                z.commit("post_sales/set_common", ["custom_edate", y.period.custom.edate])
            }

            /** Select Tab */
            z.commit("post_sales/set_object", ["selected_main_tab", 0])
        },

        del (x) {
            this.tmp_profile = x
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('post_sales/del_profile', {id:this.tmp_profile.profile_id})
        },

        is_selected (x) {
            if (!this.selected_profile)
                return false
            if (this.selected_profile.profile_id == x.profile_id)
                return true
            return false
        }
    },

    mounted () {
        this.$store.dispatch('post_sales/search_watemplate')
    }
}
</script>