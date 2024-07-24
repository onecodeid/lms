<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Masterdata Kursus</h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <!-- <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn> -->

                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian" v-model="query"
                        @change="search"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>      

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="items"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <tr :class="[props.item.M_ItemIsPublished!='Y'?'red lighten-4':'']">
                        <td class="text-xs-left pa-0 pt-1 pl-1" @click="select(props.item)">
                            <img :src="props.item.img_url" height="60" /></td>
                        
                        <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.M_ItemCode }}</td>
                        <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ItemName }}</td>
                        <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.category_name }}</td>
                        <td class="text-xs-right pa-2" @click="select(props.item)">{{ one_money(props.item.M_ItemWeight) }}x Pertemuan</td>
                        

                        <td class="text-xs-center pa-2" @click="select(props.item)">/</td>
                        
                        
                        <td class="text-xs-center pa-0" @click="select(props.item)">
                            <v-btn color="orange" dark class="btn-icon ma-0" small @click="print_stock_card(props.item)" title="Cetak Kartu Stok"><v-icon>print</v-icon></v-btn>
                            <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                            <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                        </td>
                    </tr>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
        </v-card-text>

        <common-dialog-print :report_url="report_url" v-if="dialog_report"></common-dialog-print>
        <common-dialog-delete :data="item_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
        <master-item-stock-card-print></master-item-stock-card-print>

    </v-card>
</template>

<style scoped>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        "common-dialog-print" : httpVueLoader("../../common/components/common-dialog-print.vue"),
        "master-item-stock-card-print" : httpVueLoader("./master-item-stock-card-print.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "#",
                    align: "left",
                    sortable: false,
                    width: "5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KODE",
                    align: "center",
                    sortable: false,
                    width: "5%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "34%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KATEGORI",
                    align: "left",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "LAMA KURSUS",
                    align: "right",
                    sortable: false,
                    width: "18%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                
                {
                    text: "Biaya",
                    align: "center",
                    sortable: false,
                    width: "18%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        items () {
            return this.$store.state.item.items
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        item_id () {
            return this.$store.state.item.selected_item.M_ItemID
        },

        query : {
            get () { return this.$store.state.item.search },
            set (v) { this.$store.commit('item/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.item.current_page },
            set (v) { this.$store.commit('item/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.item.total_item_page
        },

        dialog_report : {
            get () { return this.$store.state.dialog_print },
            set (v) { this.$store.commit('set_dialog_print', v) }
        },

        report_url () {
            return this.$store.state.report_url
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.dispatch('item_new/search_level_price', {})
            this.$store.commit('item_new/set_common', ['edit', false])
            this.$store.commit('item_new/set_common', ['item_name', ''])
            this.$store.commit('item_new/set_common', ['item_code', ''])
            this.$store.commit('item_new/set_common', ['item_weight', 0])
            this.$store.commit('item_new/set_common', ['item_img', ''])
            this.$store.commit('item_new/set_common', ['item_min', 0])
            this.$store.commit('item_new/set_common', ['item_hpp', 0])
            this.$store.commit('item_new/set_common', ['item_publish', 'Y'])
            this.$store.commit('item_new/set_selected_unit', null)
            this.$store.commit('item_new/set_selected_category', null)
            this.$store.commit('item_new/set_common', ['sdate', new Date().toISOString().substr(0, 10)])
            this.$store.commit('item_new/set_common', ['edate', new Date().toISOString().substr(0, 10)])
            this.$store.commit('item_new/set_dialog_new', true)
            this.$store.commit('item_new/set_object', ['schedules', []])

            let levels = this.$store.state.item_new.levels
            let fees = []
            for(let l of levels) {
                fees.push({
                    M_CustomerLevelCode:l.M_CustomerLevelCode,
                    M_CustomerLevelID:l.M_CustomerLevelID,
                    M_CustomerLevelName:l.M_CustomerLevelName,
                    M_FeeAmount:0,
                    M_FeeID:0,
                    M_FeePointAmount:0,
                    M_FeeRewardAmount:0
                })
            }
            this.$store.commit('item_new/set_fees', fees)
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('item_new/set_common', ['edit', true])
            this.$store.commit('item_new/set_common', ['item_name', sc.M_ItemName])
            this.$store.commit('item_new/set_common', ['item_code', sc.M_ItemCode])
            this.$store.commit('item_new/set_common', ['item_weight', sc.M_ItemWeight])
            this.$store.commit('item_new/set_common', ['item_img', sc.img_uri])
            this.$store.commit('item_new/set_common', ['item_min', sc.M_ItemMinStock])
            this.$store.commit('item_new/set_common', ['item_hpp', sc.M_ItemHPP])
            this.$store.commit('item_new/set_common', ['item_publish', sc.M_ItemIsPublished])
            this.$store.commit('item_new/set_object', ['schedules', sc.schedules])

            // UNIT
            let u = this.$store.state.item_new.units
            this.$store.commit('item_new/set_selected_unit', null)
            for (let x of u)
                if (x.M_UnitID == sc.M_ItemM_UnitID)
                    this.$store.commit('item_new/set_selected_unit', x)

            // CATEGORY
            u = this.$store.state.item_new.categories
            this.$store.commit('item_new/set_selected_category', null)
            for (let x of u)
                if (x.M_CategoryID == sc.M_ItemM_CategoryID)
                    this.$store.commit('item_new/set_selected_category', x)

            this.$store.commit('item_new/set_levels', x.prices)
            this.$store.commit('item_new/set_fees', x.fees)

            let sdate = new Date().toISOString().substr(0, 10)
            let edate = new Date().toISOString().substr(0, 10)
            for (let price of x.prices) {
                if (price.M_PriceSaleStartDate != null)
                    sdate = price.M_PriceSaleStartDate
                if (price.M_PriceSaleEndDate != null)
                    edate = price.M_PriceSaleEndDate
            }
            this.$store.commit('item_new/set_common', ['sdate', sdate])
            this.$store.commit('item_new/set_common', ['edate', edate])

            // this.$store.commit('item_new/set_common', ['item_', sc.full_address])
            // this.$store.commit('item_new/set_selected_city', {kelurahan_id:sc.kelurahan_id,full_address:sc.full_address})
            this.$store.commit('item_new/set_dialog_new', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('item/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('item/set_selected_item', x)
        },

        search () {
            return this.$store.dispatch('item/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('item/search', {})
        },

        print_stock_card (x) {
            this.$store.commit('item/set_common', ['dialog_stock_card', true])
            // alert(this.report_url)
            // this.select(x)
            // this.dialog_report = true
        }
    }
}
</script>