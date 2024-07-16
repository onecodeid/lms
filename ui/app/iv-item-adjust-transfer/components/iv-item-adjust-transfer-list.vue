<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Item Stock</h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <v-text-field solo hide-details placeholder="Pencarian" v-model="query" @change="search">
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search">
                                <v-icon>search</v-icon>
                            </v-btn>
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table :items="items" :loading="false" hide-actions class="elevation-1" hide-default-header>
                <template slot="headers">
                    <tr>
                        <th role="columnheader" scope="col" width="5%" aria-label="#: Not sorted." aria-sort="none"
                            class="column text-xs-left pa-2 zalfa-bg-purple lighten-3 white--text">#</th>
                        <th role="columnheader" scope="col" width="5%" aria-label="KODE: Not sorted." aria-sort="none"
                            class="column text-xs-center pa-2 zalfa-bg-purple lighten-3 white--text">KODE</th>
                        <th role="columnheader" scope="col" width="24%" aria-label="NAMA: Not sorted." aria-sort="none"
                            class="column text-xs-left pa-2 zalfa-bg-purple lighten-3 white--text">NAMA</th>
                        <th role="columnheader" scope="col" width="10%" aria-label="KATEGORI: Not sorted."
                            aria-sort="none" class="column text-xs-left pa-2 zalfa-bg-purple lighten-3 white--text">
                            KATEGORI</th>
                        
                        <template v-for="(w,n) in warehouses">
                            <th role="columnheader" scope="col" :width="(40/warehouses.length)+'%'" aria-label="STOK: Not sorted." aria-sort="none"
                            class="column text-xs-center pa-2 zalfa-bg-purple lighten-3 white--text">{{ w.warehouse_name }}</th>
                        </template>

                        <th role="columnheader" scope="col" width="16%" aria-label="STOK: Not sorted." aria-sort="none"
                            class="column text-xs-center pa-2 zalfa-bg-purple lighten-3 white--text">STOK TOTAL</th>
                        
                    </tr>
                    <tr class="v-datatable__progress">
                        <th colspan="5" class="column"></th>
                    </tr>
                </template>
                <template slot="items" slot-scope="props">
                    <tr @click="select(props.item)">
                        <td class="text-xs-left pa-0 pt-1 pl-1" @click="select(props.item)">
                            <img :src="props.item.img_url" height="60" />
                        </td>

                        <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.item_code }}</td>
                        <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.item_name }}</td>
                        <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.category_name }}</td>
                        <td v-for="(s,n) in props.item.stocks">
                            <v-layout v-show="!s.adjust&&!s.transfer">
                                <v-text-field hide-details label="Stok" dense :value="s.stock_qty" solo :suffix="s.unit_name" readonly flat>
                                    <template slot="append-outer">
                                        <v-btn color="orange" class="ma-0 btn-icon mr-1" @click="adjustItem(props.index, n)" v-show="n==0" 
                                            title="Penyesuaian Stok" :disabled="!!inAction">
                                            <v-icon>swap_vert</v-icon>
                                        </v-btn>
                                        <v-btn color="lime" class="ma-0 btn-icon" @click="transferItem(props.index, n)" 
                                            title="Transfer Stok" :disabled="!!inAction">
                                            <v-icon>swap_horiz</v-icon>
                                        </v-btn>
                                    </template>
                                </v-text-field>
                            </v-layout>
                            
                            <v-layout v-show="!!s.adjust">
                                <v-flex xs2 class="d-flex align-center grey--text">[Adjust]</v-flex>
                                <v-flex xs2 class="d-flex align-center primary--text subheading pl-2">{{ one_money(s.stock_qty) }}</v-flex>
                                <v-flex xs8>
                                    <v-text-field hide-details label="Stok" dense v-model="adjustQty" solo :suffix="s.unit_name" prefix="+">
                                        <template slot="append-outer">
                                            <v-btn color="success" class="ma-0 btn-icon" @click="doAdjust(s)">
                                                <v-icon>save</v-icon>
                                            </v-btn>
                                            <v-btn color="red" class="ma-0 btn-icon" @click="adjustItem(props.index, n)" dark>
                                                <v-icon>close</v-icon>
                                            </v-btn>
                                        </template>
                                    </v-text-field>
                                </v-flex>
                            </v-layout>

                            <v-layout row wrap v-show="!!s.transfer">
                                <v-flex xs12 class="my-2">
                                    <v-select :items="warehouses" item-value="warehouse_id" item-text="warehouse_name" :item-disabled="[s.warehouse_id]" 
                                        return-object hide-details prefix="Tujuan" solo v-model="selectedWarehouse">
                                        <template slot="selection" slot-scope="data"><v-icon class="mr-1" small>forward</v-icon>{{ data.item.warehouse_name }}</template>
                                        <template slot="item" slot-scope="data">
                                            <v-icon class="mr-1" small>
                                            forward</v-icon>{{ data.item.warehouse_name }}
                                        </template>
                                    </v-select>
                                </v-flex>
                                <v-flex xs2 class="d-flex align-center grey--text">[Stok]</v-flex>
                                <v-flex xs2 class="d-flex align-center primary--text subheading pl-2">{{ one_money(s.stock_qty) }}</v-flex>
                                <v-flex xs8>
                                    <v-text-field hide-details label="Stok" dense v-model="transferQty" solo :suffix="s.unit_name" prefix="-">
                                        <template slot="append-outer">
                                            <v-btn color="success" class="ma-0 btn-icon" @click="doTransfer(s)">
                                                <v-icon>save</v-icon>
                                            </v-btn>
                                            <v-btn color="red" class="ma-0 btn-icon" @click="transferItem(props.index, n)" dark>
                                                <v-icon>close</v-icon>
                                            </v-btn>
                                        </template>
                                    </v-text-field>
                                </v-flex>
                            </v-layout>
                            <!-- <v-layout>
                                <v-flex xs6>{{ s.stock_qty }}</v-flex>
                                <v-flex xs6>anu</v-flex>
                            </v-layout> -->
                            
                        </td>
                        <td>
                            <v-layout>
                                <v-flex xs6 class="text-xs-right pr-1">{{ stockTotal(props.item.stocks) }}</v-flex>
                                <v-flex xs6 class="pl-1">{{ props.item.unit_name }}</v-flex>
                            </v-layout>
                        </td>

                        <!-- <td class="text-xs-center pa-0" @click="select(props.item)">
                            <v-btn color="orange" dark class="btn-icon ma-0" small @click="print_stock_card(props.item)" title="Cetak Kartu Stok"><v-icon>print</v-icon></v-btn>
                            <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                            <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                        </td> -->
                    </tr>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination style="margin-top:10px;margin-bottom:10px" v-model="curr_page" :length="xtotal_page"
                @input="change_page"></v-pagination>
        </v-card-text>
        <dialog-adjust></dialog-adjust>
        <dialog-transfer></dialog-transfer>
    </v-card>
</template>

<style>
.v-text-field.v-text-field--solo .v-input__control {
    min-height: 36px;
}
.v-text-field.v-text-field--solo .v-input__append-outer {
    margin-top: 0px;
    margin-left: 0px;
}
</style>

<script>
let t = '?t='+(Math.random() * 1e6);
module.exports = {
    components: {
        "dialog-adjust": httpVueLoader("./iv-item-adjust-transfer-dialog-adjust.vue"+t),
        "dialog-transfer": httpVueLoader("./iv-item-adjust-transfer-dialog-transfer.vue"+t)
    },

    data() {
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
                    width: "24%",
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
                    text: "STOK",
                    align: "center",
                    sortable: false,
                    width: "56%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                // {
                //     text: "ACTION",
                //     align: "center",
                //     sortable: false,
                //     width: "10%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // }
            ]
        }
    },

    computed: {
        ...Vuex.mapState({
            items: s => s.adjusttransfer.items,
            warehouses: s => s.adjusttransfer.warehouses,
            inAction: s => s.adjusttransfer.inAction
        }),

        query: {
            get() { return this.$store.state.adjusttransfer.search },
            set(v) { this.$store.commit('adjusttransfer/update_search', v) }
        },

        selectedWarehouse: {
            get() { return this.$store.state.adjusttransfer.selectedWarehouse },
            set(v) { this.$store.commit('adjusttransfer/set_object', ['selectedWarehouse', v]) }
        },

        transferQty: {
            get() { return this.$store.state.adjusttransfer.transferQty },
            set(v) { this.$store.commit('adjusttransfer/set_object', ['transferQty', v]) }
        },

        adjustQty: {
            get() { return this.$store.state.adjusttransfer.adjustQty },
            set(v) { this.$store.commit('adjusttransfer/set_object', ['adjustQty', v]) }
        },

        curr_page: {
            get() { return this.$store.state.adjusttransfer.current_page },
            set(v) { this.$store.commit('adjusttransfer/update_current_page', v) }
        },

        xtotal_page() {
            return this.$store.state.adjusttransfer.total_item_page
        }
    },

    methods: {
        __c (a, b) {
            return this.$store.commit("adjusttransfer/set_object", [a, b])
        },

        one_money(x) {
            return window.one_money(x)
        },

        select(x) {
            this.$store.commit('adjusttransfer/set_object', ['selected_item', x])
        },

        search() {
            return this.$store.dispatch('adjusttransfer/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('adjusttransfer/search', {})
        },

        stockTotal(x) {
            let y = 0
            for (let xx of x) y += parseFloat(xx.stock_qty)
            return y
        },

        adjustItem(x, y) {
            let items = JSON.parse(JSON.stringify(this.items))
            items[x].stocks[y].adjust = !items[x].stocks[y].adjust
            this.$store.commit("adjusttransfer/set_object", ["items", items])

            this.adjustQty = 1;

            this.__c("inAction", !this.inAction)
        },

        transferItem(x, y) {
            let items = JSON.parse(JSON.stringify(this.items))
            items[x].stocks[y].transfer = !items[x].stocks[y].transfer
            this.$store.commit("adjusttransfer/set_object", ["items", items])

            this.transferQty = items[x].stocks[y].stock_qty
            this.selectedWarehouse = null

            this.__c("inAction", !this.inAction)
        },

        doTransfer(x) {
            let y = this.selectedWarehouse 
            if (!y)
                return alert('Pilih gudang tujuan !')
            if (x.warehouse_id == y.warehouse_id) {
                return alert('Gudang tujuan tidak boleh sama dengan gudang asal !')
            } 
            
            this.$store.dispatch("adjusttransfer/transferItem", x).then(d => {
                console.log(d)
            })
        },

        doAdjust(x) {
            this.$store.dispatch("adjusttransfer/adjustItem", x).then(d => {
                console.log(d)
            })
        }
    },

    mounted() {
        this.$store.dispatch('adjusttransfer/searchWarehouse').then(d => {
            this.$store.dispatch('adjusttransfer/search', {})
        })
    }
}