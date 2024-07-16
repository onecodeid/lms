<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs10>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Master Campaign</h3>
                </v-flex>
                <v-flex xs2 class="text-xs-right">
                    <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="campaigns"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-0 pt-1 pl-1" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                        {{ props.item.campaign_name }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">{{ props.item.exp_name }}</td>
                    
                    
                    <td class="text-xs-left pa-0" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'']">
                        <v-btn color="primary" class="btn-icon ma-0" small @click="edit(props.item)"><v-icon>create</v-icon></v-btn>
                        <v-btn color="red" dark class="btn-icon ma-0" small @click="del(props.item)"><v-icon>delete</v-icon></v-btn>
                    </td>
                    <!-- <td class="text-xs-center pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.M_DoctorHP}}</td>
                    <td class="text-xs-left pa-2" v-bind:class="{'amber lighten-4':isSelected(props.item)}" @click="selectMe(props.item)">{{ props.item.status}}</td> -->
                </template>
            </v-data-table>
            <v-divider></v-divider>
            <v-pagination
                style="margin-top:10px;margin-bottom:10px"
                v-model="curr_page"
                :length="xtotal_page"
            ></v-pagination>
        </v-card-text>
        
        <common-dialog-delete :data="campaign_id" @confirm_del="confirm_del" v-if="dialog_delete"></common-dialog-delete>
    </v-card>
</template>

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
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "50%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "EKSPEDISI",
                    align: "left",
                    sortable: false,
                    width: "30%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "ACTION",
                    align: "center",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                }
            ],
            class_selected: 'cyan lighten-4'
        }
    },

    computed : {
        campaigns () {
            return this.$store.state.campaign.campaigns
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        campaign_id () {
            if (this.$store.state.campaign.selected_campaign)
                return this.$store.state.campaign.selected_campaign.M_CampaignID
            return 0
        },

        selected_campaign () {
            return this.$store.state.campaign.selected_campaign
        }
    },

    methods : {
        add () {
            this.$store.commit('campaign_new/set_common', ['edit', false])
            this.$store.commit('campaign_new/set_common', ['campaign_name', ''])
            this.$store.commit('campaign_new/set_selected_expedition', null)
            this.$store.commit('campaign_new/set_services', [])
            this.$store.commit('campaign_new/set_selected_service', null)
            this.$store.commit('campaign_new/set_dialog_new', true)
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('campaign_new/set_common', ['edit', true])
            this.$store.commit('campaign_new/set_common', ['campaign_name', sc.campaign_name])
            this.$store.commit('campaign_new/set_selected_campaign', sc)

            let exp = this.$store.state.campaign_new.expeditions
            for (let i in exp) {
                if (exp[i].M_ExpeditionROCode == sc.exp_code) {
                    this.$store.commit('campaign_new/set_selected_expedition', exp[i])
                    this.$store.dispatch('campaign_new/search_service', {})
                }
                    
            }

            this.$store.commit('campaign_new/set_dialog_new', true)
        },

        del (x) {
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirm_del (x) {
            this.$store.dispatch('campaign/del', {id:x.data})
        },

        select (x) {
            this.$store.commit('campaign/set_selected_campaign', x)
            this.$store.dispatch('campaigndetail/search', {})
        },

        is_selected (x) {
            if (this.selected_campaign)
                if (this.selected_campaign.campaign_id == x.campaign_id)
                    return true
            return false
        }
    }
}
</script>