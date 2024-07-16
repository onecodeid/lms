<template>
    <div>
        <v-data-table 
                :headers="headers"
                :items="pendings"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-2" @click="select(props.index)">
                        <div>{{ reverse_date(props.item.L_SoDate) }}</div>
                        <div>{{ props.item.L_SoNumber }}</div>
                    </td>
                    
                    <td class="text-xs-left pa-2" @click="select(props.index)">
                        <div><b>{{ props.item.M_CustomerName }}</b></div>
                        <div>{{ props.item.city_name }} ─ {{ props.item.customer_phone }}</div>
                        
                    </td>
                    
                    <td class="text-xs-right pa-2" @click="select(props.index)">{{ one_money(props.item.L_SoTotal) }}</td>
                    
                    <td class="text-xs-right pa-2" @click="select(props.index)">
                        {{ one_money(props.item.L_SoExpCost) }}</td>
                </template>
            </v-data-table>    
    </div>
</template>

<script>
module.exports = {
    data () {
        return {
            headers: [
                {
                    text: "TANGGAL",
                    align: "left",
                    width: "25%",
                    sortable:false,
                    class: "pa-2 info lighten-1 lighten-3 white--text"
                },
                {
                    text: "NAMA CUSTOMER",
                    align: "left",
                    sortable: false,
                    width: "45%",
                    class: "pa-2 info lighten-1 lighten-3 white--text"
                },
                {
                    text: "TAGIHAN",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 info lighten-1 lighten-3 white--text"
                },
                {
                    text: "ONGKIR",
                    align: "right",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 info lighten-1 lighten-3 white--text"
                }
            ]
        }
    },

    computed : {
        pendings () {
            return this.$store.state.cod_new.pendings
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        select (i) {
            let items = this.$store.state.cod.items
            items.push(this.pendings[i])

            this.$store.commit('cod_new/set_items', items)
            
            let p = this.pendings
            p.splice(i, 1)

            this.$store.commit('cod_new/set_pendings', p)
            this.$store.commit('cod_new/set_common', ['total_pending', p.length])
        },

        reverse_date (x) {
            return x.split('-').reverse().join('-')
        }
    },

    mounted () {
        this.$store.dispatch('cod_new/search_pending')
    }
}
</script>