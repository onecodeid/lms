<template>
    <v-card>
        <v-card-title primary-title class="pt-2 pb-0">
            <v-layout row wrap>
                <v-flex xs9>
                    <h3 class="display-1 font-weight-light zalfa-text-title">Masterdata Paket</h3>
                </v-flex>
                <v-flex xs3 class="text-xs-right">
                    <v-btn color="success" class="ma-0 btn-icon" @click="add">
                        <v-icon>add</v-icon>
                    </v-btn>
                </v-flex>
            </v-layout>
        </v-card-title>
        <v-card-text class="pt-2">
            <v-data-table 
                :headers="headers"
                :items="packets"
                :loading="false"
                hide-actions
                class="elevation-1">
                <template slot="items" slot-scope="props">
                    <td class="text-xs-left pa-0 pt-1 pl-1" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'',props.item.M_PacketIsPublished=='N'?'red--text':'']">
                        {{ props.item.M_PacketCode }}</td>
                    <td class="text-xs-left pa-2" @click="select(props.item)" v-bind:class="[is_selected(props.item)?class_selected:'',props.item.M_PacketIsPublished=='N'?'red--text':'']">{{ props.item.M_PacketName }}</td>
                    
                    
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
        
        <common-dialog-delete :data="1" @confirm="confirmDel()" v-show="dialog_delete"></common-dialog-delete>
    </v-card>
</template>

<script>
module.exports = {
    components : {
        "common-dialog-delete" : httpVueLoader("../../common/components/common-dialog-delete-2.vue?t=1s23")
    },

    data () {
        return {
            headers: [
                {
                    text: "KODE",
                    align: "left",
                    sortable: false,
                    width: "20%",
                    class: "pa-2 zalfa-bg-purple lighten-3 white--text"
                },
                {
                    text: "NAMA",
                    align: "left",
                    sortable: false,
                    width: "60%",
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
        packets () {
            return this.$store.state.packet.packets
        },

        dialog_delete () {
            return this.$store.state.dialog_delete
        },

        packet_id () {
            if (this.$store.state.packet.selected_packet)
                return this.$store.state.packet.selected_packet.M_PacketID
            return 0
        },

        selected_packet () {
            return this.$store.state.packet.selected_packet
        },

        xtotal_page () {
            return this.$store.state.packet.total_packet_page
        },

        curr_page : {
            get () { return this.$store.state.packet.current_page },
            set (v) { 
                this.$store.commit('packet/update_current_page', v) 
                this.$store.dispatch('packet/search', {})
            }
        } 
    },

    methods : {
        add () {
            this.$store.commit('packet_new/set_common', ['edit', false])
            this.$store.commit('packet_new/set_common', ['packet_name', ''])
            this.$store.commit('packet_new/set_common', ['packet_code', ''])
            this.$store.commit('packet_new/set_common', ['packet_publish', 'Y'])
            this.$store.commit('packet_new/set_common', ['packet_img', ''])
            this.$store.commit('packet_new/set_dialog_new', true)
        },

        edit (x) {
            this.select(x)
            let sc = x
            this.$store.commit('packet_new/set_common', ['edit', true])
            this.$store.commit('packet_new/set_common', ['packet_name', sc.M_PacketName])
            this.$store.commit('packet_new/set_common', ['packet_code', sc.M_PacketCode])
            this.$store.commit('packet_new/set_common', ['packet_publish', sc.M_PacketIsPublished])
            this.$store.commit('packet_new/set_common', ['packet_img', sc.packet_img])

            this.$store.commit('packet_new/set_dialog_new', true)
        },

        del (x) {
            console.log("Selecting packet ...")
            this.select(x)
            this.$store.commit('set_dialog_delete', true)
        },

        confirmDel () {
            console.log("Preparing to delete ...")
            this.$store.dispatch('packet/del')
        },

        select (x) {
            this.$store.commit('packet/set_selected_packet', x)
            this.$store.dispatch('packetdetail/search', {})
            this.$store.dispatch('packet_sale/search', {})
        },

        is_selected (x) {
            if (this.selected_packet)
                if (this.selected_packet.M_PacketID == x.M_PacketID)
                    return true
            return false
        }
    },

    mounted () {
        this.$store.dispatch("master_level/search", {})
    }
}
</script>