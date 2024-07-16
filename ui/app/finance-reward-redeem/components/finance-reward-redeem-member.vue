<template>
    <v-card>
        <!-- <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs12>
                    <h3 class="display-1 font-weight-light zalfa-text-title">MEMBER</h3>
                </v-flex>
            </v-layout>
        </v-card-title> -->
        <v-card-text class="pt-2">
            <v-layout row wrap>
                <v-flex xs12>
                    <v-text-field
                        solo
                        hide-details
                        placeholder="Pencarian customer" v-model="query"
                        @change="search_customer"
                    >
                        <template v-slot:append-outer>
                            <v-btn color="primary" class="ma-0 btn-icon" @click="search_customer">
                                <v-icon>search</v-icon>
                            </v-btn> 
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
            <v-layout row wrap>
                <v-flex xs12>
                    <v-data-table 
                        :headers="headers"
                        :items="customers"
                        :loading="false"
                        hide-actions
                        class="elevation-1">
                        <template slot="items" slot-scope="props">
                            <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                <v-layout row wrap>
                                    <v-flex xs12>
                                        {{ props.item.M_CustomerName }}
                                    </v-flex>
                                    <v-flex xs12>
                                        <b>{{ props.item.M_CustomerCode }}</b>        
                                    </v-flex>
                                </v-layout>
                                 
                            </td>
                            <td class="text-xs-right pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                                {{ one_money(props.item.point_qty) }}
                            </td>
                            
                            <!-- <td class="text-xs-center pa-0" v-bind:class="level_color(props.item.M_CustomerLevelCode)" @click="select(props.item)">
                                <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                                <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                            </td> -->
                            <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                            <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                        </template>
                    </v-data-table>
                </v-flex>
                <v-flex xs12>
                    <v-divider></v-divider>
                    <v-pagination
                        style="margin-top:10px;margin-bottom:10px"
                        v-model="curr_page"
                        :length="xtotal_page"
                        @input="change_page"
                    ></v-pagination>
                </v-flex>
            </v-layout>
            
        </v-card-text>
        
        <common-dialog-delete :data="reward_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
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
                // {
                //     text: "NOMOR",
                //     align: "left",
                //     sortable: false,
                //     width: "8%",
                //     class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                // },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "80%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "POIN",
                    align: "right",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],
            class_selected: 'cyan lighten-4'
        }
    },

    computed : {
        customers () {
            return this.$store.state.reward.customers
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        reward_id () {
            return this.$store.state.reward.selected_reward.M_RewardID
        },

        query : {
            get () { return this.$store.state.reward.search_customer },
            set (v) { this.$store.commit('reward/set_common', ['search_customer', v]) }
        },

        curr_page : {
            get () { return this.$store.state.reward.current_customer_page },
            set (v) { this.$store.commit('reward/set_common', ['current_customer_page', v]) }
        },

        xtotal_page () {
            return this.$store.state.reward.total_customer_page
        },

        selected_customer () {
            return this.$store.state.reward.selected_customer
        }
    },

    methods : {
        one_money (x) {
            return window.one_money(x)
        },

        add () {
            this.$store.commit('reward_new/set_common', ['edit', false])
            this.$store.commit('reward_new/set_common', ['reward_name', ''])
            this.$store.commit('reward_new/set_common', ['reward_code', ''])
            this.$store.commit('reward_new/set_common', ['reward_point', 0])
            this.$store.commit('reward_new/set_common', ['reward_quota', 10])
            this.$store.commit('reward_new/set_common', ['dialog_new', true])
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('reward_new/set_common', ['edit', true])
            this.$store.commit('reward_new/set_common', ['reward_name', sc.M_RewardName])
            this.$store.commit('reward_new/set_common', ['reward_code', sc.M_RewardCode])
            this.$store.commit('reward_new/set_common', ['reward_point', sc.M_RewardPoint])
            this.$store.commit('reward_new/set_common', ['reward_quota', sc.M_RewardQuota])

            this.$store.commit('reward_new/set_common', ['dialog_new', true])
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('reward/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('reward/set_selected_customer', x)
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('reward/search_customer', {})
        },

        search_customer () {
            return this.$store.dispatch('reward/search_customer', {})
        },

        is_selected (x) {
            if (this.selected_customer)
                if (this.selected_customer.M_CustomerID == x.M_CustomerID)
                    return true
            return false
        }
    },

    mounted () {
        this.$store.dispatch('reward/search_customer', {})
    }
}
</script>