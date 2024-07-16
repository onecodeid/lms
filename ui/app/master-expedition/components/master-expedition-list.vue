<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Masterdata Ekspedisi</h3>
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

                            <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add" disabled>
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
                :items="expeditions"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ExpeditionCode }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)">{{ props.item.M_ExpeditionName }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.M_ExpeditionIsCOD }}</td>
                    <td class="text-xs-center pa-2" @click="select(props.item)">{{ props.item.M_ExpeditionCODRate }} %</td>
                    
                    <td class="text-xs-center pa-0" @click="select(props.item)">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
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
        
        <common-dialog-delete :data="expedition_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete.vue")
    },

    data () {
        return {
            headers: [
                {
                    text: "KODE",
                    align: "left",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "center",
                    sortable: false,
                    width: "69%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "COD ?",
                    align: "center",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "COD RATE",
                    align: "left",
                    sortable: false,
                    width: "7%",
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
        expeditions () {
            return this.$store.state.expedition.expeditions
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        expedition_id () {
            return this.$store.state.expedition.selected_expedition.M_ExpeditionID
        },

        query : {
            get () { return this.$store.state.expedition.search },
            set (v) { this.$store.commit('expedition/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.expedition.current_page },
            set (v) { this.$store.commit('expedition/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.expedition.total_expedition_page
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('expedition_new/set_common', ['edit', false])
            this.$store.commit('expedition_new/set_common', ['expedition_name', ''])
            this.$store.commit('expedition_new/set_common', ['expedition_code', ''])
            this.$store.commit('expedition_new/set_common', ['expedition_cod', 'N'])
            this.$store.commit('expedition_new/set_common', ['expedition_cod_rate', '0'])
            this.$store.commit('expedition_new/set_common', ['dialog_new', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('expedition_new/set_common', ['edit', true])
            this.$store.commit('expedition_new/set_common', ['expedition_name', sc.M_ExpeditionName])
            this.$store.commit('expedition_new/set_common', ['expedition_code', sc.M_ExpeditionCode])
            this.$store.commit('expedition_new/set_common', ['expedition_cod', sc.M_ExpeditionIsCOD])
            this.$store.commit('expedition_new/set_common', ['expedition_cod_rate', sc.M_ExpeditionCODRate])

            this.$store.commit('expedition_new/set_selected_cod_rate_type', null)
            if (sc.M_ExpeditionIsCOD=="Y") {
                for (let t of this.$store.state.expedition_new.cod_rate_types)
                    if (t.id == sc.M_ExpeditionCODRateItemsOnly)
                        this.$store.commit('expedition_new/set_selected_cod_rate_type', t)
            }
            
            this.$store.commit('expedition_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('expedition/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('expedition/set_selected_expedition', x)
        },

        search () {
            return this.$store.dispatch('expedition/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('expedition/search', {})
        }
    }
}
</script>