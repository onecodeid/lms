<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9 pt-1>
                    <h3 class="headline font-weight-light zalfa-text-title">DAFTAR REWARD</h3>
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
                            </v-btn>   -->
                        </template>
                    </v-text-field>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-layout row wrap>
                <v-flex xs3 v-for="(reward, n) in rewards" :key="n" px-1 pb-2>
                    <v-card @click="select(reward)">
                        <v-img
                        src="https://static-01.daraz.com.bd/p/3445157d9d491db9f9891ed04218fff6.jpg"
                        contain
                        ></v-img>
                        <v-card-title primary-title class="pa-2" v-bind:class="[is_selected(reward)?class_selected:'']">
                            <v-layout row wrap>
                                <v-flex xs12 style="min-height:50px; align-items:start">
                                    {{reward.M_RewardName}}
                                </v-flex>
                                <v-flex xs6 class="red--text">
                                    <b>{{one_money(reward.M_RewardPoint)}} POIN</b>
                                </v-flex>
                                <v-flex xs6 class="text-xs-right grey--text pa-0">
                                    <v-btn color="primary" small class="ma-0" :disabled="!selected_customer || !!redeemx" @click="redeem(reward)">Tukarkan</v-btn>
                                </v-flex>
                            </v-layout>
                            
                        </v-card-title>
                    </v-card>
                </v-flex>
            </v-layout>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
                @input="change_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="reward_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
        <common-dialog-confirm
            text="Anda akan menukarkan poin dengan hadiah tersebut?"
            btn_cancel="Tidak, saya mau yang lainnya"
            btn_confirm="Tukar"
            :data="1"
            @confirm="do_redeem"
        ></common-dialog-confirm>
        <v-snackbar
            v-model="snackbar"
            :timeout=3000
            top
            >
            {{redeem_text}}
            <v-btn
                color="pink"
                flat
                @click="snackbar = false"
            >
                Tutup
            </v-btn>
        </v-snackbar>
    </v-card>
</template>)

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
        "common-dialog-confirm" : httpVueLoader("../../common/components/common-dialog-confirm.vue")
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
                    align: "left",
                    sortable: false,
                    width: "66%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "POINT",
                    align: "left",
                    sortable: false,
                    width: "7%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "KUOTA / DIGUNAKAN",
                    align: "left",
                    sortable: false,
                    width: "10%",
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
            class_selected: 'cyan lighten-4'
        }
    },

    computed : {
        rewards () {
            return this.$store.state.reward.rewards
        },

        selected_reward: {
            get () { return this.$store.state.reward.selected_reward },
            set (v) { this.$store.commit('reward/set_selected_reward', v) }
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        reward_id () {
            return this.$store.state.reward.selected_reward.M_RewardID
        },

        query : {
            get () { return this.$store.state.reward.search },
            set (v) { this.$store.commit('reward/update_search', v) }
        },

        curr_page : {
            get () { return this.$store.state.reward.current_page },
            set (v) { this.$store.commit('reward/update_current_page', v) }
        },

        xtotal_page () {
            return this.$store.state.reward.total_reward_page
        },

        selected_customer () {
            return this.$store.state.reward.selected_customer
        },

        snackbar: {
            get () { return this.$store.state.reward.snackbar },
            set (v) { this.$store.commit('reward/set_common', ['snackbar', v]) }            
        },

        redeem_text () {
            return this.$store.state.reward.redeem_text
        },

        redeemx () {
            return this.$store.state.reward.redeem
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
            this.$store.commit('reward/set_selected_reward', x)
        },

        search () {
            return this.$store.dispatch('reward/search', {})
        },

        change_page(x) {
            this.curr_page = x
            this.$store.dispatch('reward/search', {})
        },

        redeem (x) {
            this.select(x)
            this.$store.commit('set_dialog_confirm', true)
        },

        do_redeem () {
            this.$store.dispatch('reward/redeem_reward', {})
        },

        is_selected (x) {
            if (this.selected_reward)
                if (this.selected_reward.M_RewardID == x.M_RewardID)
                    return true
            return false
        }
    }
}
</script>