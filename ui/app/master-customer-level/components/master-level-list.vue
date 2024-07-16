<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Manajemen Level Customer</h3>
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

                            <!-- <v-btn color="success" class="ma-0 ml-2 btn-icon" @click="add">
                                <v-icon>add</v-icon>
                            </v-btn>  -->
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="levels"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    
                    <td class="text-xs-left pa-2" @click="select(props.item)" :class="props.item.level_active!='Y'?inactive_bg:''"><b>{{ props.item.M_CustomerLevelName }}</b></td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" :class="props.item.level_active!='Y'?inactive_bg:''">
                        <v-layout row wrap>
                            <v-flex>Min</v-flex>
                            <v-flex class="text-xs-right red--text">Rp {{ one_money(props.item.level_monthly_min) }}</v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex>Max</v-flex>
                            <v-flex class="text-xs-right">Rp {{ one_money(props.item.level_monthly_max) }}</v-flex>
                        </v-layout>
                    </td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" :class="props.item.level_active!='Y'?inactive_bg:''">
                        <v-layout row wrap>
                            <v-flex>Min</v-flex>
                            <v-flex class="text-xs-right red--text">Rp {{ one_money(props.item.level_3month_min) }}</v-flex>
                        </v-layout>
                        <v-layout row wrap>
                            <v-flex>Max</v-flex>
                            <v-flex class="text-xs-right">Rp {{ one_money(props.item.level_3month_max) }}</v-flex>
                        </v-layout>
                    </td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" :class="props.item.level_active!='Y'?inactive_bg:''">
                        <v-icon class="red--text mr-1" small v-show="props.item.level_downgrade_id!=0">arrow_downward</v-icon>
                        {{ props.item.level_downgrade_name }}
                    </td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" :class="props.item.level_active!='Y'?inactive_bg:''">
                        <v-icon class="blue--text mr-1" small v-show="props.item.level_upgrade_id!=0">arrow_upward</v-icon>
                        {{ props.item.level_upgrade_name }}
                    </td>
                    <td class="text-xs-center pa-0" @click="select(props.item)" :class="props.item.level_active!='Y'?inactive_bg:''">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)" v-show="props.item.level_active=='Y'"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)" v-show="props.item.level_active=='Y'"><v-icon>delete</v-icon></v-btn>
                        <v-btn color="success" dark class="btn-icon ma-0" small @click="restore(props.item)" v-show="props.item.level_active!='Y'"><v-icon>refresh</v-icon></v-btn>
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
        
        <common-dialog-delete :data="level_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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
                    text: "NAMA",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "OMZET PER BULAN",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "OMZET PER 3 BULAN",
                    align: "center",
                    sortable: false,
                    width: "15%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "LEVEL DOWNGRADE",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "LEVEL UPGRADE",
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
            ],

            inactive_bg: 'red lighten-4'
        }
    },

    computed : {
        levels () {
            return this.$store.state.level.levels
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        level_id () {
            return this.$store.state.level.selected_level.M_CustomerLevelID
        },

        query : {
            get () { return this.$store.state.level.search },
            set (v) { this.$store.commit('level/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.level.current_page },
            set (v) { this.$store.commit('level/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.level.total_level_page
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('level_new/set_common', ['edit', false])
            this.$store.commit('level_new/set_common', ['level_name', ''])
            this.$store.commit('level_new/set_common', ['level_code', ''])
            this.$store.commit('level_new/set_level_upgrade', null)
            this.$store.commit('level_new/set_level_downgrade', null)
            this.$store.commit('level_new/set_common', ['level_monthly_min', 0])
            this.$store.commit('level_new/set_common', ['level_monthly_max', 0])
            this.$store.commit('level_new/set_common', ['level_3month_min', 0])
            this.$store.commit('level_new/set_common', ['level_3month_max', 0])
            this.$store.commit('level_new/set_common', ['dialog_new', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('level_new/set_common', ['edit', true])
            this.$store.commit('level_new/set_common', ['level_name', sc.M_CustomerLevelName])
            this.$store.commit('level_new/set_common', ['level_code', sc.M_CustomerLevelCode])
            this.$store.commit('level_new/set_level_upgrade', null)
            this.$store.commit('level_new/set_level_downgrade', null)
            this.$store.commit('level_new/set_common', ['level_monthly_min', sc.level_monthly_min])
            this.$store.commit('level_new/set_common', ['level_monthly_max', sc.level_monthly_max])
            this.$store.commit('level_new/set_common', ['level_3month_min', sc.level_3month_min])
            this.$store.commit('level_new/set_common', ['level_3month_max', sc.level_3month_max])

            for (let l of this.levels) {
                if (l.level_id == sc.level_upgrade_id) {
                    this.$store.commit('level_new/set_level_upgrade', l)
                }

                if (l.level_id == sc.level_downgrade_id) {
                    this.$store.commit('level_new/set_level_downgrade', l)
                }
            }

            this.$store.commit('level_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        restore (x) {
            this.select(x)
            this.$store.dispatch('level/restore', {id:x.data})
            // this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('level/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('level/set_selected_level', x)
        },

        search () {
            return this.$store.dispatch('level/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('level/search', {})
        }
    }
}
</script>