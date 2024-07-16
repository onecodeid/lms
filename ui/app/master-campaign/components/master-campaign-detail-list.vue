<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="headline font-weight-light zalfa-text-title">Detail <span class="font-weight-regular">{{ campaign_name }}</span></h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <!-- <v-btn color="primary" v-show="campaign_id != false" class="ma-0" @click="go_to_fee">Setting Komisi</v-btn> -->
                    <v-btn color="success" class="ma-0 btn-icon" @click="add" :disabled="!campaign_id">
                        <v-icon>add</v-icon>
                    </v-btn>
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
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.packet_name }}</td>
                    <td class="text-xs-center pa-1" @click="select(props.item)">{{ props.item.is_packet == "Y" ? "PAKET" : "ITEM" }}</td>
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            

            

            <v-card flat class="pa-0 mt-4">
                <v-card-title primary-title class="pa-0">
                    <v-layout row wrap>
                        <v-flex xs9>
                            <h3 class="headline font-weight-light zalfa-text-title">Link Embed</h3>
                        </v-flex>
                        <v-flex xs3 class="text-xs-right">
                        </v-flex>
                    </v-layout>
                </v-card-title>
                <v-card-text class="pa-0 pt-2">
                    <v-textarea rows="3" readonly solo :value="embed_code" class="embed-box"></v-textarea>
                </v-card-text>
                
                <common-dialog-delete :data="item_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
            </v-card>
            
        </v-card-text>
        
        <common-dialog-delete :data="item_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
    </v-card>
</template>

<style scoped>
.v-text-field__details {
    padding: 0px !important;
    text-align: right;
}

.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}

.embed-box {
    font-family: 'Courier New', Courier, monospace;
}
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            curr_page: 1,
            xtotal_page: 1,
            headers: [
                {
                    text: "NAMA ITEM / PAKET",
                    align: "left",
                    sortable: false,
                    width: "70%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "JENIS",
                    align: "center",
                    sortable: false,
                    width: "20%",
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
            let items = this.$store.state.campaigndetail.items
            
            // for (let i in items) {
            //     let prices = {}
            //     let prices_n = {}
            //     for (let h of this.$store.state.campaigndetail.headers_price) {
            //         prices[h.M_CustomerLevelCode] = 0
            //         prices_n[h.M_CustomerLevelCode] = 0

            //         for (let p of items[i].prices)
            //             if (p.M_CustomerLevelCode == h.M_CustomerLevelCode) {
            //                 prices[h.M_CustomerLevelCode] = p.M_CampaignPriceSale
            //                 prices_n[h.M_CustomerLevelCode] = p.M_CampaignPriceNormal
            //             }
            //     }

            //     items[i]['prices'] = prices
            //     items[i]['prices_n'] = prices_n
            // }
            
            return items
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        item_id () {
            return this.$store.state.campaigndetail.selected_item.campaign_d_id
        },

        campaign_id () {
            if (this.$store.state.campaign.selected_campaign)
                return this.$store.state.campaign.selected_campaign.campaign_id

            return false
        },

        campaign_name () {
            if (this.$store.state.campaign.selected_campaign)
                return this.$store.state.campaign.selected_campaign.campaign_name

            return "-"
        },

        embed_code () {
            let sc = this.$store.state.campaign.selected_campaign
            if (sc)
                return `<iframe src="http://seller.zalfa.id/ui/app/sales-order-end-user/?c=${sc.campaign_code}" scrolling="no" frameborder="0" sandbox="allow-top-navigation allow-scripts allow-same-origin" height="988"></iframe>`
            else
                return ''
        }

        // headers_price () {
        //     let h = this.headers
        //     let i = this.$store.state.campaigndetail.headers_price

        //     let hx = h.splice(1)
        //     if (i)
        //         for (let x of i)
        //             h.push({
        //                 text: x['M_CustomerLevelName'],
        //                 align: "right",
        //                 sortable: false,
        //                 width: "11%",
        //                 class: "pa-2 zalfa-bg-purple lighten-3 white--text"
        //             })
        //     h.push(hx[0])
        //     return h
        // },

        // headers_fee () {
        //     return this.headers_price.splice(1, 7)
        // },

        // price_total () {
        //     let totals = {'normal':{}, 'sale':{}}
        //     for (let item of this.items) {
                
        //         for (let p in item.prices) {
        //             if (typeof(totals.sale[p]) == 'undefined') {
        //                 totals.normal[p] = Math.round(item.prices_n[p])
        //                 totals.sale[p] = Math.round(item.prices[p])
        //             }
        //             else {
        //                 totals.normal[p] = Math.round(totals.normal[p]) + Math.round(item.prices_n[p])
        //                 totals.sale[p] = Math.round(totals.sale[p]) + Math.round(item.prices[p])
        //             }  
                    
        //         }
        //     }

        //     return totals
        // }
    },

    methods : {
        add () {
            // this.$store.commit('item_new/set_common', ['edit', false])
            this.$store.commit('newitem/set_common', ['dialog_items', true])
            // this.$store.commit('campaigndetail/set_common', ['dialog_item_av', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('item_new/set_common', ['edit', true])
            this.$store.commit('item_new/set_common', ['item_name', sc.M_ItemName])
            this.$store.commit('item_new/set_common', ['item_code', sc.M_ItemCode])

            this.$store.commit('item_new/set_dialog_new', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('campaigndetail/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('campaigndetail/set_selected_item', x)
        },

        one_money (x) {
            return window.one_money(x)
        },

        // set_price (x) {
        //     this.select(x)
        //     this.$store.dispatch('campaigndetail/search_detail_price', {})
        //     this.$store.commit('campaigndetail/set_common', ['dialog_price', true])
        // },

        // save_price (x) {
        //     this.select(x)
        //     this.$store.dispatch('campaigndetail/save_price', {})
        // },

        // change_price(type, i, v) {
        //     let x = this.items
        //     x[i][`prices`][type] = v

        //     this.$store.commit('campaigndetail/update_items', x)
        // },

        // go_to_fee () {
        //     this.$store.commit('campaigndetail/set_common', ['selected_tab', 2])
        // }
    },

    mounted () {
        // this.$store.dispatch('campaigndetail/search_header_price', {})
    }
}
</script>