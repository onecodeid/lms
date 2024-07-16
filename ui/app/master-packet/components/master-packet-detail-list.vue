<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="headline font-weight-light zalfa-text-title">Detail <span class="font-weight-regular">{{ packet_name }}</span></h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <v-btn color="primary" v-show="packet_id != false" class="ma-0" @click="go_to_fee">Setting Komisi</v-btn>
                    <v-btn color="success" class="ma-0 btn-icon" @click="add" :disabled="!packet_id">
                        <v-icon>add</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers_price"
                :items="items"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ItemName }}</td>

                    <td class="text-xs-right pa-1" @click="select(props.item)" v-for="(h,n) in headers_pricex" :key="props.item.M_ItemID+'-'+n">
                        <v-text-field
                            solo
                            :value="props.item.prices[h.M_CustomerLevelCode] ? props.item.prices[h.M_CustomerLevelCode] : 0"
                            :hint="'# ' + (props.item.prices_n[h.M_CustomerLevelCode] ? one_money(props.item.prices_n[h.M_CustomerLevelCode]) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price(h.M_CustomerLevelCode, props.index, $event)"
                        ></v-text-field>
                    </td>

                    <!-- <td class="text-xs-right pa-1" @click="select(props.item)">
                        <v-text-field
                            solo
                            :value="props.item.prices['CUST.DISTRIBUTOR'] ? props.item.prices['CUST.DISTRIBUTOR'] : 0"
                            :hint="'# ' + (props.item.prices_n['CUST.DISTRIBUTOR'] ? one_money(props.item.prices_n['CUST.DISTRIBUTOR']) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price('CUST.DISTRIBUTOR', props.index, $event)"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-right pa-1" @click="select(props.item)">
                        <v-text-field
                            solo
                            :value="props.item.prices['CUST.FAMILY'] ? props.item.prices['CUST.FAMILY'] : 0"
                            :hint="'# ' + (props.item.prices_n['CUST.FAMILY'] ? one_money(props.item.prices_n['CUST.FAMILY']) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price('CUST.FAMILY', props.index, $event)"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-right pa-1" @click="select(props.item)">
                        <v-text-field
                            solo
                            :value="props.item.prices['CUST.AGENCY'] ? props.item.prices['CUST.AGENCY'] : 0"
                            :hint="'# ' + (props.item.prices_n['CUST.AGENCY'] ? one_money(props.item.prices_n['CUST.AGENCY']) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price('CUST.AGENCY', props.index, $event)"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-right pa-1" @click="select(props.item)">
                        <v-text-field
                            solo
                            :value="props.item.prices['CUST.RESELLER'] ? props.item.prices['CUST.RESELLER'] : 0"
                            :hint="'# ' + (props.item.prices_n['CUST.RESELLER'] ? one_money(props.item.prices_n['CUST.RESELLER']) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price('CUST.RESELLER', props.index, $event)"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-right pa-1" @click="select(props.item)">
                        <v-text-field
                            solo
                            :value="props.item.prices['CUST.ENDUSER'] ? props.item.prices['CUST.ENDUSER'] : 0"
                            :hint="'# ' + (props.item.prices_n['CUST.ENDUSER'] ? one_money(props.item.prices_n['CUST.ENDUSER']) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price('CUST.ENDUSER', props.index, $event)"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-right pa-1" @click="select(props.item)">
                        <v-text-field
                            solo
                            :value="props.item.prices['CUST.MEMBER'] ? props.item.prices['CUST.MEMBER'] : 0"
                            :hint="'# ' + (props.item.prices_n['CUST.MEMBER'] ? one_money(props.item.prices_n['CUST.MEMBER']) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price('CUST.MEMBER', props.index, $event)"
                        ></v-text-field>
                    </td>
                    <td class="text-xs-right pa-1" @click="select(props.item)">
                        <v-text-field
                            solo
                            :value="props.item.prices['CUST.OTHER'] ? props.item.prices['CUST.OTHER'] : 0"
                            :hint="'# ' + (props.item.prices_n['CUST.OTHER'] ? one_money(props.item.prices_n['CUST.OTHER']) : 0)"
                            persistent-hint
                            reverse
                            @input="change_price('CUST.OTHER', props.index, $event)"
                        ></v-text-field>
                    </td> -->

                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="green" dark class="btn-icon ma-0" small @click="save_price(props.item)"><v-icon>save</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            

            <v-layout row wrap>
                <div style="width:27%" class="pa-2">Total Harga Normal</div>
                <div style="width:9%" class="pa-2 text-xs-right">{{ one_money(price_total.normal['CUST.DISTRIBUTOR']) }}</div>
                <div style="width:9%" class="pa-2 text-xs-right">{{ one_money(price_total.normal['CUST.FAMILY']) }}</div>
                <div style="width:9%" class="pa-2 text-xs-right">{{ one_money(price_total.normal['CUST.AGENCY']) }}</div>
                <div style="width:9%" class="pa-2 text-xs-right">{{ one_money(price_total.normal['CUST.RESELLER']) }}</div>
                <div style="width:9%" class="pa-2 text-xs-right">{{ one_money(price_total.normal['CUST.ENDUSER']) }}</div>
                <div style="width:9%" class="pa-2 text-xs-right">{{ one_money(price_total.normal['CUST.MEMBER']) }}</div>
                <div style="width:9%" class="pa-2 text-xs-right">{{ one_money(price_total.normal['CUST.OTHER']) }}</div>
                <div style="width:10%">&nbsp;</div>
            </v-layout>

            <v-layout row wrap>
                <div style="width:27%" class="pa-2"><h4>Total Harga PAKET</h4></div>
                <div style="width:9%" class="pa-2 text-xs-right"><h4>{{ one_money(price_total.sale['CUST.DISTRIBUTOR']) }}</h4></div>
                <div style="width:9%" class="pa-2 text-xs-right"><h4>{{ one_money(price_total.sale['CUST.FAMILY']) }}</h4></div>
                <div style="width:9%" class="pa-2 text-xs-right"><h4>{{ one_money(price_total.sale['CUST.AGENCY']) }}</h4></div>
                <div style="width:9%" class="pa-2 text-xs-right"><h4>{{ one_money(price_total.sale['CUST.RESELLER']) }}</h4></div>
                <div style="width:9%" class="pa-2 text-xs-right"><h4>{{ one_money(price_total.sale['CUST.ENDUSER']) }}</h4></div>
                <div style="width:9%" class="pa-2 text-xs-right"><h4>{{ one_money(price_total.sale['CUST.MEMBER']) }}</h4></div>
                <div style="width:9%" class="pa-2 text-xs-right"><h4>{{ one_money(price_total.sale['CUST.OTHER']) }}</h4></div>
                <div style="width:10%">&nbsp;</div>
            </v-layout>

            <v-layout row wrap v-show="!!packet_id">
                <v-flex xs12><v-divider class="mt-2 mb-4"></v-divider></v-flex>
                <v-flex xs4>
                    <h3 class="title mt-4">Harga Promo</h3>
                </v-flex>
                <v-flex xs2 mb-2>
                    <common-datepicker
                        label="Tanggal berlaku"
                        :date="promo_sdate"
                        data="0"
                        @change="change_promo_sdate"
                        classs="mt-0 input-dense"
                        hints=""
                        :details="false"
                        :solo="false"
                        v-if="trigger_date"
                    ></common-datepicker>
                </v-flex>

                <v-flex xs2 mb-2>
                    <common-datepicker
                        label="Sampai Tanggal"
                        :date="promo_edate"
                        data="0"
                        @change="change_promo_edate"
                        classs="mt-0 ml-1 input-dense"
                        hints=""
                        :details="false"
                        :solo="false"
                        v-if="trigger_date"
                    ></common-datepicker>
                </v-flex>
                <v-flex xs4 class="text-xs-right">
                    <v-btn color="green" dark class="btn-icon ma-3" small @click="save_promo"><v-icon>save</v-icon></v-btn>
                </v-flex>
                <v-flex xs12>
                    <div class="elevation-1">
                        <div class="v-table__overflow">
                            <table class="v-datatable v-table theme--light">
                                <thead>
                                    <tr>
                                        <th 
                                            role="columnheader" scope="col" width="16.6%" 
                                            aria-label="LEVEL: Not sorted." aria-sort="none" 
                                            class="column text-xs-center pa-2 zalfa-bg-purple lighten-3 white--text"
                                            v-for="(level, n) of promos" v-bind:key="n">
                                            {{level.M_CustomerLevelName.toUpperCase()}}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td v-for="(level, n) of promos" v-bind:key="n" class="pa-1">
                                            <v-text-field
                                                solo
                                                hide-details
                                                reverse
                                                class="input-dense"
                                                :value="level.M_PacketSaleAmount"
                                                @change="change_promo(n, $event)"
                                            ></v-text-field>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>  
                </v-flex>
            </v-layout>

        </v-card-text>
        
        <common-dialog-delete-x :data="item_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete-x>
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
</style>

<script>
module.exports = {
    components : {
        "common-dialog-delete-x" : httpVueLoader("../../common/components/common-dialog-delete.vue"),
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return {
            curr_page: 1,
            xtotal_page: 1,
            headers: [
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "27%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "10%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],
            trigger_promo_date: false
        }
    },

    computed : {
        ...Vuex.mapState({
            levels: s => s.master_level.levels,
            headers_pricex: s => s.packetdetail.headers_price
        }),

        items () {
            let items = this.$store.state.packetdetail.items
            
            for (let i in items) {
                let prices = {}
                let prices_n = {}
                for (let h of this.$store.state.packetdetail.headers_price) {
                    prices[h.M_CustomerLevelCode] = 0
                    prices_n[h.M_CustomerLevelCode] = 0

                    for (let p of items[i].prices)
                        if (p.M_CustomerLevelCode == h.M_CustomerLevelCode) {
                            prices[h.M_CustomerLevelCode] = p.M_PacketPriceSale
                        }
                }

                items[i]['prices'] = prices
                items[i]['prices_n'] = prices_n
            }
            
            return items
        },

        promos () {
            return this.$store.state.packet_sale.prices
        },

        promo_sdate () {
            return this.$store.state.packet_sale.sdate
        },

        promo_edate () {
            return this.$store.state.packet_sale.edate
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        item_id () {
            return this.$store.state.packetdetail.selected_item.M_PacketDetailID
        },

        packet_id () {
            if (this.$store.state.packet.selected_packet)
                return this.$store.state.packet.selected_packet.M_PacketID

            return false
        },

        packet_name () {
            if (this.$store.state.packet.selected_packet)
                return this.$store.state.packet.selected_packet.M_PacketName

            return "-"
        },

        headers_price () {
            let h = this.headers
            let i = this.$store.state.packetdetail.headers_price

            let hx = h.splice(1)
            if (i)
                for (let x of i)
                    h.push({
                        text: x['M_CustomerLevelName'],
                        align: "right",
                        sortable: false,
                        width: "9%",
                        class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                    })
            h.push(hx[0])
            return h
        },

        headers_fee () {
            return this.headers_price.splice(1, 7)
        },

        price_total () {
            let totals = {'normal':{}, 'sale':{}}
            for (let item of this.items) {
                
                for (let p in item.prices) {
                    if (typeof(totals.sale[p]) == 'undefined') {
                        totals.normal[p] = Math.round(item.prices_n[p])
                        totals.sale[p] = Math.round(item.prices[p])
                    }
                    else {
                        totals.normal[p] = Math.round(totals.normal[p]) + Math.round(item.prices_n[p])
                        totals.sale[p] = Math.round(totals.sale[p]) + Math.round(item.prices[p])
                    }  
                    
                }
            }

            return totals
        },

        selected_packet () {
            return this.$store.state.packet.selected_packet
        },

        trigger_date () {
            return this.$store.state.packet_sale.trigger_date
        }
    },

    methods : {
        add () {
            // this.$store.commit('item_new/set_common', ['edit', false])
            this.$store.dispatch('packetdetail/search_av_item', {})
            this.$store.commit('packetdetail/set_common', ['dialog_item_av', true])
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
            this.confirm_del(x)
            // this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('packetdetail/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('packetdetail/set_selected_item', x)
        },

        one_money (x) {
            return window.one_money(x)
        },

        set_price (x) {
            this.select(x)
            this.$store.dispatch('packetdetail/search_detail_price', {})
            this.$store.commit('packetdetail/set_common', ['dialog_price', true])
        },

        save_price (x) {
            this.select(x)
            this.$store.dispatch('packetdetail/save_price', {})
        },

        change_price(type, i, v) {
            let x = this.items
            x[i][`prices`][type] = v

            this.$store.commit('packetdetail/update_items', x)
        },

        go_to_fee () {
            this.$store.commit('packetdetail/set_common', ['selected_tab', 2])
        },

        change_promo(i, v) {
            let x = this.promos
            x[i].M_PacketSaleAmount = v
            this.$store.commit('packet_sale/set_prices', x)
        },

        change_promo_sdate(v) {
            this.$store.commit('packet_sale/set_common', ['sdate', v.new_date])
        },

        change_promo_edate(v) {
            this.$store.commit('packet_sale/set_common', ['edate', v.new_date])
        },

        save_promo () {
            this.$store.dispatch('packet_sale/save')
        }
    },

    mounted () {
        this.$store.dispatch('packetdetail/search_header_price', {})
    },

    watch : {
        selected_packet (v, o) {
            if (v != o) {
                this.trigger_promo_date = false
                var x = this
                setTimeout(function() {
                    x.trigger_promo_date = true
                }, 500)
            }
        }
    }
}
</script>